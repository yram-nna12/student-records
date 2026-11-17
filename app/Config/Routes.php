<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('admin/login', 'AdminAuthController::loginForm', ['filter'=>'csrf']);
$routes->get('admin/setup', 'InitialAdminSetupController::form', ['filter'=>'csrf']);
$routes->post('admin/setup', 'InitialAdminSetupController::create', ['filter'=>'csrf']);
$routes->post('admin/login', 'AdminAuthController::login', ['filter'=>['csrf','adminattempts']]);
$routes->post('admin/logout', 'AdminAuthController::logout', ['filter'=>['csrf','adminauth']]);
$routes->get('admin/dashboard', 'AdminDashboardController::index', ['filter'=>['csrf','adminauth']]);
$routes->get('admin/verifications', 'VerificationMonitoringController::index', ['filter'=>['csrf','adminauth']]);
$routes->group('admin/students', ['filter'=>['csrf','adminauth']], static function ($routes) { $routes->get('', 'StudentManagementController::index'); $routes->get('new', 'StudentManagementController::create'); $routes->post('', 'StudentManagementController::store'); $routes->get('(:num)', 'StudentManagementController::show/$1'); $routes->get('(:num)/edit', 'StudentManagementController::edit/$1'); $routes->post('(:num)/edit', 'StudentManagementController::update/$1'); });

$routes->get(
    'api/students/verify',
    'StudentVerificationController::verify'
);

