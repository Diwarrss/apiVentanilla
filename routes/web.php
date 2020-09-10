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
