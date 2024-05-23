<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('', ['filter' => 'jwt'], function ($routes) {
  $routes->group('students', static function ($routes) {
    $routes->get('', 'StudentController::showAll');
    $routes->get('(:num)', 'StudentController::show/$1');
    $routes->post('', 'StudentController::create');
    $routes->delete('(:num)', 'StudentController::delete/$1');
    $routes->post('(:num)', 'StudentController::update/$1');
    $routes->get('photo/(:num)', 'StudentController::getPhoto/$1');
  });
});

$routes->group('users', static function ($routes) {
  $routes->post('', 'UserController::create');
});

$routes->group('auth', static function ($routes) {
  $routes->post('login', 'AuthController::store');
});