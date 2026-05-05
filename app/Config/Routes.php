<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'WisataController::index');
$routes->get('wisata/(:num)', 'WisataController::detail/$1');
$routes->get('galeri/(:num)', 'WisataController::galeri/$1');

$routes->group('api', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('wisata', 'ApiController::list');
    $routes->get('wisata/(:num)', 'ApiController::show/$1');
    $routes->post('rekomendasi', 'ApiController::nearest');
    $routes->get('galeri/(:num)', 'ApiController::galeri/$1');
    $routes->get('kategori', 'ApiController::kategori');
});

$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('loginProcess', 'Auth::loginProcess');
    $routes->get('logout', 'Auth::logout');
    
    $routes->group('', ['filter' => 'adminauth'], function($routes) {
        $routes->get('dashboard', 'Dashboard::index');
        
        // Kategori CRUD
        $routes->get('kategori', 'KategoriController::index');
        $routes->get('kategori/create', 'KategoriController::create');
        $routes->post('kategori/store', 'KategoriController::store');
        $routes->get('kategori/edit/(:num)', 'KategoriController::edit/$1');
        $routes->post('kategori/update/(:num)', 'KategoriController::update/$1');
        $routes->get('kategori/delete/(:num)', 'KategoriController::delete/$1');

        // Wisata CRUD
        $routes->get('wisata', 'WisataController::index');
        $routes->get('wisata/create', 'WisataController::create');
        $routes->post('wisata/store', 'WisataController::store');
        $routes->get('wisata/edit/(:num)', 'WisataController::edit/$1');
        $routes->post('wisata/update/(:num)', 'WisataController::update/$1');
        $routes->get('wisata/delete/(:num)', 'WisataController::delete/$1');

        // Galeri CRUD
        $routes->get('galeri', 'GaleriController::index');
        $routes->get('galeri/create', 'GaleriController::create');
        $routes->post('galeri/store', 'GaleriController::store');
        $routes->get('galeri/edit/(:num)', 'GaleriController::edit/$1');
        $routes->post('galeri/update/(:num)', 'GaleriController::update/$1');
        $routes->get('galeri/delete/(:num)', 'GaleriController::delete/$1');
    });
});

