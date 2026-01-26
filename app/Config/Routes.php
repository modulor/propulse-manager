<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('/frameio', 'FrameIO::index');
$routes->get('/frameio/login', 'FrameIO::login');
$routes->get('/frameio/callback', 'FrameIO::callback');
$routes->get('/frameio/logout', 'FrameIO::logout');

$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/dashboard/workspaces/(:segment)', 'Dashboard::workspaces/$1');
$routes->get('/dashboard/workspace/(:segment)/(:segment)', 'Dashboard::workspace/$1/$2');
