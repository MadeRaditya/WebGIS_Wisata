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
