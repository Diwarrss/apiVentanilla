<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//rutas para descargar excel o csv
Route::get('type-documents/export', 'API\TypeDocumentController@export'); //tipos de docuemnento
Route::get('priorities/export', 'API\PriorityController@export'); //tipos de docuemnento
Route::get('context-types/export', 'API\ContextTypeController@export'); //tipos de contexto
Route::get('type-identifications/export', 'API\TypeIdentificationController@export'); //tipos de identificacion
Route::get('genders/export', 'API\GenderController@export'); //generos
Route::get('cancellation-reason/export', 'API\CancellationReasonController@export'); //generos
Route::get('dependences/export', 'API\DependenceController@export'); //dependencias
//Route::get('entryfiling/export', 'API\EntryFilingController@export'); //entryfiling

Route::get('/', function () {
    return view('welcome');
});

//Route::post('login', 'Auth\LoginController@login');
//Route::post('logout', 'Auth\LoginController@logout');

//Clear route cache:
/* Route::get('/route-cache', function() {
  $exitCode = Artisan::call('route:cache');
  return 'Routes cache cleared';
}); */

//Clear config cache:
Route::get('/all-clear-cache', function() {
  $exitCode = Artisan::call('config:cache');
  $exitCode = Artisan::call('cache:clear');
  $exitCode = Artisan::call('view:clear');
  return 'All cache cleared';
});

/* // Clear application cache:
Route::get('/clear-cache', function() {
  $exitCode = Artisan::call('cache:clear');
  return 'Application cache cleared';
});

// Clear view cache:
Route::get('/view-clear', function() {
  $exitCode = Artisan::call('view:clear');
  return 'View cache cleared';
}); */
