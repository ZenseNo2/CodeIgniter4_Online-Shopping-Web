<?php

use CodeIgniter\Router\RouteCollection;

/**
 * Peta URL ShopEase.
 *
 * Filter:
 *   auth                  → harus login
 *   auth:customer         → hanya customer
 *   auth:admin            → hanya admin
 *   auth:admin,operator   → admin atau operator
 *   guest                 → hanya untuk yang belum login
 *
 * @var RouteCollection $routes
 */

// ------------------------------------------------------------ Halaman publik
$routes->get('/', 'HomeController::index');
$routes->get('about', 'HomeController::about');
$routes->get('contact', 'HomeController::contact');
$routes->get('catalog', 'ProductController::catalog');
$routes->get('product/(:num)', 'ProductController::detail/$1');

// ------------------------------------------------------------ Authentication
$routes->group('', ['filter' => 'guest'], static function ($routes) {
    $routes->get('register', 'AuthController::register');
    $routes->post('register', 'AuthController::store');

    $routes->get('login', 'AuthController::login');
    $routes->post('login', 'AuthController::authenticate');
});
$routes->post('logout', 'AuthController::logout');

// ------------------------------------------------------------ Customer
$routes->group('', ['filter' => 'auth:customer'], static function ($routes) {
    // Keranjang
    $routes->get('cart', 'CartController::index');
    $routes->post('cart/add', 'CartController::add');
    $routes->post('cart/update/(:num)', 'CartController::update/$1');
    $routes->post('cart/remove/(:num)', 'CartController::remove/$1');

    // Checkout
    $routes->get('checkout', 'CheckoutController::index');
    $routes->post('checkout', 'CheckoutController::process');

    // Pesanan, pembayaran, pelacakan
    $routes->get('orders', 'OrderController::index');
    $routes->get('orders/(:segment)', 'OrderController::show/$1');
    $routes->post('orders/(:segment)/payment', 'OrderController::uploadPayment/$1');
    $routes->get('orders/(:segment)/proof', 'OrderController::proof/$1');
    $routes->post('orders/(:segment)/cancel', 'OrderController::cancel/$1');
    $routes->post('orders/(:segment)/received', 'OrderController::received/$1');

    // Dashboard customer
    $routes->get('account', 'AccountController::index');
    $routes->post('account/profile', 'AccountController::updateProfile');
    $routes->post('account/password', 'AccountController::updatePassword');
    $routes->post('account/addresses', 'AccountController::addAddress');
    $routes->post('account/addresses/(:num)/default', 'AccountController::defaultAddress/$1');
    $routes->post('account/addresses/(:num)/delete', 'AccountController::deleteAddress/$1');
});

// ------------------------------------------------------------ Dashboard per role
$routes->group('admin', ['filter' => 'auth:admin'], static function ($routes) {
    $routes->get('/', 'Admin\DashboardController::index');
});

$routes->group('operator', ['filter' => 'auth:operator'], static function ($routes) {
    $routes->get('/', 'Operator\DashboardController::index');
});

// ------------------------------------------------------------ Backend admin & operator
$routes->group('admin', ['filter' => 'auth:admin,operator', 'namespace' => 'App\Controllers\Admin'], static function ($routes) {
    // Kategori (Praktikum 4)
    $routes->get('categories', 'CategoryController::index');
    $routes->get('categories/create', 'CategoryController::create');
    $routes->post('categories/store', 'CategoryController::store');
    $routes->get('categories/(:num)/edit', 'CategoryController::edit/$1');
    $routes->post('categories/(:num)/update', 'CategoryController::update/$1');
    $routes->post('categories/(:num)/delete', 'CategoryController::delete/$1');

    // Produk (Praktikum 5)
    $routes->get('products', 'ProductController::index');
    $routes->get('products/create', 'ProductController::create');
    $routes->post('products/store', 'ProductController::store');
    $routes->get('products/(:num)/edit', 'ProductController::edit/$1');
    $routes->post('products/(:num)/update', 'ProductController::update/$1');
    $routes->post('products/(:num)/toggle', 'ProductController::toggle/$1');
    $routes->post('products/(:num)/delete', 'ProductController::delete/$1');

    // Pesanan (Praktikum 9)
    $routes->get('orders', 'OrderController::index');
    $routes->get('orders/(:num)', 'OrderController::show/$1');
    $routes->post('orders/(:num)/cancel', 'OrderController::cancel/$1');
    $routes->post('orders/(:num)/complete', 'OrderController::complete/$1');

    // Pembayaran & pengiriman (Praktikum 10)
    $routes->get('payments', 'PaymentController::index');
    $routes->get('payments/(:num)/proof', 'PaymentController::proof/$1');
    $routes->post('payments/(:num)/approve', 'PaymentController::approve/$1');
    $routes->post('payments/(:num)/reject', 'PaymentController::reject/$1');
    $routes->get('shipments', 'ShipmentController::index');
    $routes->post('shipments/(:num)/ship', 'ShipmentController::ship/$1');

    // Laporan (Praktikum 11)
    $routes->get('reports', 'ReportController::index');
    $routes->get('reports/export', 'ReportController::export');
});

// Manajemen pengguna & role: khusus admin
$routes->group('admin', ['filter' => 'auth:admin', 'namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('users', 'UserController::index');
    $routes->post('users/(:num)/role', 'UserController::updateRole/$1');
});
