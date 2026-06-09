<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */




/** Dashboard */
$routes->get('/', 'Pages\Dashboard::index');
$routes->get('/dashboard', 'Pages\Dashboard::index');





