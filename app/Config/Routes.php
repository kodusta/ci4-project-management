<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

// Auth Routes
$routes->get('login', 'Auth::index');
$routes->post('auth/login', 'Auth::login');
$routes->get('auth/logout', 'Auth::logout');

// Test Password Route (Sadece geliştirme aşamasında kullanılmalıdır)
$routes->get('auth/test-password', 'Auth::testPassword');

// Dashboard Route (after login)
$routes->get('dashboard', 'Home::index');

// Default route
$routes->get('/', 'Home::index');

// Müşteri işlemleri
$routes->get('customers', 'Customers::index');
$routes->get('customers/create', 'Customers::create');
$routes->post('customers/store', 'Customers::store');
$routes->get('customers/edit/(:num)', 'Customers::edit/$1');
$routes->post('customers/update/(:num)', 'Customers::update/$1');
$routes->get('customers/delete/(:num)', 'Customers::delete/$1');
$routes->get('customers/projects/(:num)', 'Customers::projects/$1');

// Proje route'ları
$routes->get('projects', 'Projects::index');
$routes->get('projects/create', 'Projects::create');
$routes->post('projects/store', 'Projects::store');
$routes->get('projects/edit/(:num)', 'Projects::edit/$1');
$routes->post('projects/update/(:num)', 'Projects::update/$1');
$routes->post('projects/delete/(:num)', 'Projects::delete/$1');
$routes->get('projects/view/(:num)', 'Projects::view/$1');
$routes->post('projects/update-field', 'Projects::updateField');
$routes->get('projects/notes/(:num)', 'Projects::notes/$1');
$routes->post('projects/add-note', 'Projects::addNote');
$routes->post('projects/delete-note/(:num)', 'Projects::deleteNote/$1');
$routes->post('projects/updatePriority', 'Projects::updatePriority');
$routes->post('projects/updateStatus', 'Projects::updateStatus');

// Ödemeler
$routes->get('payments', 'Payments::index');
$routes->get('payments/create', 'Payments::create');
$routes->post('payments/store', 'Payments::store');
$routes->get('payments/edit/(:num)', 'Payments::edit/$1');
$routes->post('payments/update/(:num)', 'Payments::update/$1');
$routes->post('payments/delete/(:num)', 'Payments::delete/$1');
$routes->get('payments/project/(:num)', 'Payments::projectPayments/$1');

// Raporlama route'ları
$routes->get('reports', 'Reports::index');
$routes->get('reports/archive', 'Reports::archive');
$routes->get('reports/downloadArchive/(:segment)', 'Reports::downloadArchive/$1');
$routes->get('reports/exportPDF', 'Reports::exportPDF');
$routes->get('reports/exportExcel', 'Reports::exportExcel');
$routes->post('reports/filter', 'Reports::filter');

// Kategoriler
$routes->get('categories', 'Categories::index');
$routes->get('categories/create', 'Categories::create');
$routes->post('categories/store', 'Categories::store');
$routes->get('categories/edit/(:num)', 'Categories::edit/$1');
$routes->post('categories/update/(:num)', 'Categories::update/$1');
$routes->get('categories/delete/(:num)', 'Categories::delete/$1');
