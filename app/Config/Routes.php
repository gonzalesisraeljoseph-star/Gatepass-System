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
$routes->get('api/hardware', '\App\Controllers\Api\Hardware::getHardware');
$routes->get('api/hardware/mine', 'Api\HardwareController::mine');

/** User Management  */
$routes->get('/user-management', 'Pages\UserManagement::index');
$routes->get('user-management/list', 'Pages\UserManagement::list');
$routes->get('usermanagement/saveUser/(:num)', 'Pages\UserManagement::edituser/$1');
$routes->post('usermanagement/saveUser', 'Pages\UserManagement::saveUser');
$routes->post('usermanagement/deleteUser/(:num)', 'Pages\UserManagement::deleteUser/$1');
$routes->post('users/toggleStatus/(:num)', 'Pages\UserManagement::toggleStatus/$1');
$routes->get('users/sync', 'Pages\UserManagement::syncUsers');

// ---- Approvals (any logged-in approver) ----
// ---- Approvals (any logged-in approver) ----
    $routes->get('approvals', 'Approval\GatepassApprovals::inbox');
    $routes->post('approvals/act', 'Approval\GatepassApprovals::act');
    $routes->get('approvals/floating', 'Approval\GatepassApprovals::floating');
    $routes->post('approvals/override', 'Approval\GatepassApprovals::override');


    $routes->get('approving-sequence', 'Workflow\WorkflowBuilder::index');
$routes->get('approving-sequence/(:num)', 'Workflow\WorkflowBuilder::show/$1');
$routes->post('approving-sequence/save', 'Workflow\WorkflowBuilder::save');
$routes->post('approving-sequence/validate', 'Workflow\WorkflowBuilder::validateGraph');
$routes->post('approving-sequence/delete/(:num)', 'Workflow\WorkflowBuilder::destroy/$1');

if (ENVIRONMENT === 'development') {
    $routes->get('dev-login-as/(:segment)', 'Auth::devLoginAs/$1');
}