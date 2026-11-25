<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Struktur database ShopEase (db_shopease).
 *
 *  users ──┬──< addresses
 *          ├──1 carts ──< cart_items >── products >── categories
 *          └──< orders ──< order_items >── products
 *                  ├──1 payments  (verified_by → users)
 *                  └──1 shipments
 *
 * Tabel users mengikuti modul Hands-On Lab Authentication (primary key id_user).
 * Jalankan: php spark migrate
 */
class CreateShopeaseTables extends Migration
{
    public function up()
    {
        // ------------------------------------------------------------------ users
        $this->forge->addField([
            'id_user'    => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'password'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'role'       => ['type' => 'ENUM', 'constraint' => ['customer', 'admin', 'operator'], 'default' => 'customer'],
            'phone'      => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'address'    => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_user', true);
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('users');

        // ------------------------------------------------------------- categories
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 120],
            'description' => ['type' => 'TEXT', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('categories');

        // --------------------------------------------------------------- products
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'category_id' => ['type' => 'INT', 'unsigned' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 150],
            'description' => ['type' => 'TEXT', 'null' => true],
            'price'       => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'stock'       => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'image'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status'      => ['type' => 'ENUM', 'constraint' => ['active', 'inactive'], 'default' => 'active'],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('products');

        // -------------------------------------------------------------- addresses
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'        => ['type' => 'INT', 'unsigned' => true],
            'label'          => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Rumah'],
            'recipient_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'phone'          => ['type' => 'VARCHAR', 'constraint' => 20],
            'address'        => ['type' => 'TEXT'],
            'city'           => ['type' => 'VARCHAR', 'constraint' => 100],
            'postal_code'    => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'is_default'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->createTable('addresses');

        // ------------------------------------------------------------------ carts
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('user_id');   // satu customer = satu keranjang aktif
        $this->forge->addForeignKey('user_id', 'users', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->createTable('carts');

        // ------------------------------------------------------------- cart_items
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'cart_id'    => ['type' => 'INT', 'unsigned' => true],
            'product_id' => ['type' => 'INT', 'unsigned' => true],
            'quantity'   => ['type' => 'INT', 'unsigned' => true, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['cart_id', 'product_id'], 'unique_cart_product');
        $this->forge->addForeignKey('cart_id', 'carts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('cart_items');

        // ----------------------------------------------------------------- orders
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'          => ['type' => 'INT', 'unsigned' => true],
            'order_number'     => ['type' => 'VARCHAR', 'constraint' => 50],
            'recipient_name'   => ['type' => 'VARCHAR', 'constraint' => 100],
            'phone'            => ['type' => 'VARCHAR', 'constraint' => 20],
            'shipping_address' => ['type' => 'TEXT'],
            'courier'          => ['type' => 'VARCHAR', 'constraint' => 50],
            'shipping_cost'    => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'subtotal'         => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'total'            => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'order_status'     => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'pending_payment'],
            'notes'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('order_number');
        $this->forge->addKey('order_status');
        $this->forge->addForeignKey('user_id', 'users', 'id_user', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('orders');

        // ------------------------------------------------------------ order_items
        // Menyimpan snapshot nama & harga produk saat transaksi.
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'order_id'     => ['type' => 'INT', 'unsigned' => true],
            'product_id'   => ['type' => 'INT', 'unsigned' => true],
            'product_name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'price'        => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'quantity'     => ['type' => 'INT', 'unsigned' => true],
            'subtotal'     => ['type' => 'DECIMAL', 'constraint' => '12,2'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('order_items');

        // --------------------------------------------------------------- payments
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'order_id'       => ['type' => 'INT', 'unsigned' => true],
            'method'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'amount'         => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'proof_file'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'payment_status' => ['type' => 'ENUM', 'constraint' => ['unpaid', 'submitted', 'verified', 'rejected'], 'default' => 'unpaid'],
            'note'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'submitted_at'   => ['type' => 'DATETIME', 'null' => true],
            'verified_by'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'verified_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('order_id');
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('verified_by', 'users', 'id_user', 'CASCADE', 'SET NULL');
        $this->forge->createTable('payments');

        // -------------------------------------------------------------- shipments
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'order_id'        => ['type' => 'INT', 'unsigned' => true],
            'courier'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'tracking_number' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'shipment_status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'pending'],
            'shipped_at'      => ['type' => 'DATETIME', 'null' => true],
            'delivered_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('order_id');
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('shipments');
    }

    public function down()
    {
        foreach (['shipments', 'payments', 'order_items', 'orders', 'cart_items', 'carts', 'addresses', 'products', 'categories', 'users'] as $t) {
            $this->forge->dropTable($t, true);
        }
    }
}
