<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->add('/login', 'Auth::index');
$routes->add('/dashboard', 'Pages\Dashboard::index');
