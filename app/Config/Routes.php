<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

$routes->match(['get','post'], 'convert', 'Home::convert' );
$routes->match(['get','post'], 'home/convert', 'Home::convert' );

$routes->match(['get','post'], 'update', 'Home::update' );
$routes->match(['get','post'], 'home/update', 'Home::update' );

$routes->match(['get','post'], 'update-delCol', 'Home::delCol' );
$routes->match(['get','post'], 'home/update-delCol', 'Home::delCol' );

$routes->match(['get','post'], 'update-addCol', 'Home::addCol' );
$routes->match(['get','post'], 'home/update-addCol', 'Home::addCol' );

$routes->match(['get','post'], 'update-delLine', 'Home::delLine' );
$routes->match(['get','post'], 'home/update-delLine', 'Home::delLine' );

$routes->match(['get','post'], 'update-addLine', 'Home::addLine' );
$routes->match(['get','post'], 'home/update-addLine', 'Home::addLine' );

$routes->match(['get','post'], 'update-save', 'Home::save' );
$routes->match(['get','post'], 'home/update-save', 'Home::save' );

$routes->match(['get','post'], 'update-cancel', 'Home::cancel' );
$routes->match(['get','post'], 'home/update-cancel', 'Home::cancel' );

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
