<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::index');

/** Authentication */
$routes->post('authentication', 'Auth::login');
$routes->get('logout', 'Auth::logout');


/** Dashboard */
$routes->get('/dashboard', 'Pages\Dashboard::index');

/** Accessories */

$routes->get('/accessories', 'Pages\Accessories::index');

/** Gatepass Request  */
$routes->get('gatepass', 'Pages\Gatepass::index');
$routes->get('gatepass/list', 'Pages\Gatepass::list');
$routes->post('gatepass/store', 'Pages\Gatepass::store');


/** API */
$routes->get('api/hardware', 'Api\Hardware::getHardware');

/** User Management  */
$routes->get('/user-management', 'Pages\UserManagement::index');
$routes->get('user-management/list', 'Pages\Gatepass::list');
$routes->post('user-management/store', 'Pages\Gatepass::store');