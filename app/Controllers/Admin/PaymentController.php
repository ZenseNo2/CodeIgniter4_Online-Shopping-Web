<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Controllers\OrderController as CustomerOrders;
use App\Libraries\OrderService;
use App\Models\PaymentModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;

/**
 * Verifikasi pembayaran manual (Praktikum 10).
 */
class PaymentController extends BaseController
{
    public function index()
    {
        $status = (string) ($this->request->getGet('status') ?? 'submitted');
        $model = (new PaymentModel())->withOrder();
        if ($status !== 'all' && isset(config('Shop')->paymentStatusLabels[$status])) {
            $model->where('payments.payment_status', $status);
        }

        return view('admin/payments/index', [
            'title'    => 'Verifikasi Pembayaran',
            'payments' => $model->orderBy('payments.submitted_at', 'DESC')->orderBy('payments.id', 'DESC')->paginate(12),
            'pager'    => $model->pager,
            'status'   => $status,
            'waiting'  => (new PaymentModel())->where('payment_status', 'submitted')->countAllResults(),
        ]);
    }

    /** Menampilkan file bukti (disimpan di writable/, tidak bisa diakses langsung dari URL publik). */
    public function proof(int $id)
    {
        $p = (new PaymentModel())->find($id) ?? throw new PageNotFoundException();

        return CustomerOrders::sendProof($this->response, $p['proof_file']);
    }

    public function approve(int $id)
    {
        return $this->decide($id, true);
    }

    public function reject(int $id)
    {
        if (! $this->validate(['note' => ['label' => 'Alasan penolakan', 'rules' => 'required|min_length[5]|max_length[255]']])) {
            return redirect()->back()->with('error', 'Tuliskan alasan penolakan (minimal 5 karakter) agar customer tahu apa yang harus diperbaiki.');
        }

        return $this->decide($id, false, trim($this->request->getPost('note')));
    }

    private function decide(int $id, bool $approve, ?string $note = null)
    {
        $p = (new PaymentModel())->find($id) ?? throw new PageNotFoundException();
        try {
            (new OrderService())->verifyPayment($p, $approve, $this->userId(), $note);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', $approve
            ? 'Pembayaran diterima. Pesanan masuk tahap Diproses.'
            : 'Pembayaran ditolak. Customer diminta mengunggah ulang bukti.');
    }
}
