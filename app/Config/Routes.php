<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('/accounts', 'FrameTest::getAccounts');

$routes->get('/auth/login', 'Auth::login');
$routes->get('/auth/callback', 'Auth::callback');
$routes->get('/auth/logout', 'Auth::logout');
$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/dashboard/workspaces/(:segment)', 'Dashboard::workspaces/$1');
