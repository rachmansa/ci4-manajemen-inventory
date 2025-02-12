<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');
$routes->get('/', 'Auth::login');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');


$routes->group('', ['filter' => 'auth'], function ($routes) {
    
    $routes->get('/dashboard', 'Dashboard::index');

    // Lab
    $routes->get('/lab', 'LabController::index');
    $routes->get('/lab/create', 'LabController::create');
    $routes->post('/lab/store', 'LabController::store');
    $routes->get('/lab/edit/(:num)', 'LabController::edit/$1');
    $routes->post('/lab/update/(:num)', 'LabController::update/$1');
    $routes->post('/lab/delete/(:num)', 'LabController::delete/$1');

    // SATUAN BARANG
    $routes->get('/satuan-barang', 'SatuanBarangController::index');
    $routes->get('/satuan-barang/create', 'SatuanBarangController::create');
    $routes->post('/satuan-barang/store', 'SatuanBarangController::store');
    $routes->get('/satuan-barang/edit/(:num)', 'SatuanBarangController::edit/$1');
    $routes->post('/satuan-barang/update/(:num)', 'SatuanBarangController::update/$1');
    $routes->post('/satuan-barang/delete/(:num)', 'SatuanBarangController::delete/$1');

    // JENIS PENGGUNAAN
    $routes->get('/jenis-penggunaan', 'JenisPenggunaanController::index');
    $routes->get('/jenis-penggunaan/create', 'JenisPenggunaanController::create');
    $routes->post('/jenis-penggunaan/store', 'JenisPenggunaanController::store');
    $routes->get('/jenis-penggunaan/edit/(:num)', 'JenisPenggunaanController::edit/$1');
    $routes->post('/jenis-penggunaan/update/(:num)', 'JenisPenggunaanController::update/$1');
    $routes->post('/jenis-penggunaan/delete/(:num)', 'JenisPenggunaanController::delete/$1');

    // JENIS BARANG
    $routes->get('/jenis-barang', 'JenisBarangController::index');
    $routes->get('/jenis-barang/create', 'JenisBarangController::create');
    $routes->post('/jenis-barang/store', 'JenisBarangController::store');
    $routes->get('/jenis-barang/edit/(:num)', 'JenisBarangController::edit/$1');
    $routes->post('/jenis-barang/update/(:num)', 'JenisBarangController::update/$1');
    $routes->post('/jenis-barang/delete/(:num)', 'JenisBarangController::delete/$1');

    // PEGAWAI UNIT
    $routes->get('/pegawai-unit', 'PegawaiUnitController::index');
    $routes->get('/pegawai-unit/create', 'PegawaiUnitController::create');
    $routes->post('/pegawai-unit/store', 'PegawaiUnitController::store');
    $routes->get('/pegawai-unit/edit/(:num)', 'PegawaiUnitController::edit/$1');
    $routes->post('/pegawai-unit/update/(:num)', 'PegawaiUnitController::update/$1');
    $routes->post('/pegawai-unit/delete/(:num)', 'PegawaiUnitController::delete/$1');

    // BARANG
    $routes->get('/barang', 'BarangController::index');
    $routes->get('/barang/create', 'BarangController::create');
    $routes->post('/barang/store', 'BarangController::store');
    $routes->get('/barang/edit/(:num)', 'BarangController::edit/$1');
    $routes->post('/barang/update/(:num)', 'BarangController::update/$1');
    $routes->post('/barang/delete/(:num)', 'BarangController::delete/$1');
    $routes->get('barang/generate-kode/(:num)', 'BarangController::generateKode/$1');

    // BARANG MASUK
    $routes->get('/barang-masuk', 'BarangMasukController::index');
    $routes->get('/barang-masuk/create', 'BarangMasukController::create');
    $routes->post('/barang-masuk/store', 'BarangMasukController::store');
    $routes->get('/barang-masuk/edit/(:num)', 'BarangMasukController::edit/$1');
    $routes->post('/barang-masuk/update/(:num)', 'BarangMasukController::update/$1');
    $routes->post('/barang-masuk/delete/(:num)', 'BarangMasukController::delete/$1');

    // BARANG DETAIL
    $routes->get('/barang-detail', 'BarangDetailController::index');
    $routes->get('/barang-detail/create', 'BarangDetailController::create');
    $routes->post('/barang-detail/store', 'BarangDetailController::store');
    $routes->get('/barang-detail/edit/(:num)', 'BarangDetailController::edit/$1');
    $routes->post('/barang-detail/update/(:num)', 'BarangDetailController::update/$1');
    $routes->post('/barang-detail/delete/(:num)', 'BarangDetailController::delete/$1');

    // BARANG LAB
    $routes->get('/barang-lab', 'BarangLabController::index');
    $routes->get('/barang-lab/create', 'BarangLabController::create');
    $routes->post('/barang-lab/store', 'BarangLabController::store');
    $routes->get('/barang-lab/edit/(:num)', 'BarangLabController::edit/$1');
    $routes->post('/barang-lab/update/(:num)', 'BarangLabController::update/$1');
    $routes->post('/barang-lab/delete/(:num)', 'BarangLabController::delete/$1');
    $routes->get('/barang-lab/get-serials/(:num)', 'BarangLabController::getSerials/$1');
    $routes->post('/barang-lab/get-barang-info', 'BarangLabController::getBarangInfo');

    
});

$routes->group('', ['filter' => 'auth', 'filter' => 'role'], function ($routes) {
    $routes->get('/user', 'User::index');
    $routes->get('/user/create', 'User::create');
    $routes->post('/user/create', 'User::create');
    $routes->get('/user/edit/(:num)', 'User::edit/$1');
    $routes->post('/user/edit/(:num)', 'User::edit/$1');
    $routes->post('/user/delete/(:num)', 'User::delete/$1');

    $routes->get('admin/activity-logs', 'Admin\ActivityLogController::index', ['filter' => 'auth']);

});



$routes->get('/logout', 'Auth::logout');

