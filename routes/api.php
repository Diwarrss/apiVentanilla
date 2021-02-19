<?php

use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//Ruta para Login
Route::post('v1/login', 'Auth\LoginController@login');
Route::middleware('auth:sanctum')->get('v1/user', function (Request $request) {
  return $request->user();
});

//Logout Sesion
Route::group(['prefix' => 'v1'], function () {
  Route::post('auth/logout', 'Auth\LoginController@logout');
});

//Router for User Auth Success
Route::middleware(['auth:sanctum'])->group(function () {
  //Create prefix api/v1
  Route::group(['prefix' => 'v1'], function () {
    //info roles and permissions user Auth
    Route::get('permissions', 'PermissionController');
    Route::get('roles', 'RoleController');

    //DependenceController
    Route::apiResource('dependences', 'API\DependenceController');
    Route::put('dependences-state/{id}', 'API\DependenceController@updateState');
    Route::get('dependence-export', 'API\DependenceController@dataExport'); //export xlsx

    //TypeDocumentController
    Route::apiResource('type-documents', 'API\TypeDocumentController');
    Route::put('type-documents-state/{id}', 'API\TypeDocumentController@updateState');
    Route::get('type-document-export', 'API\TypeDocumentController@dataExport'); //export xlsx
    //Route::get('type-documents/export', 'API\TypeDocumentController@export'); //for download excel o csv

    //PriorityController
    Route::apiResource('priorities', 'API\PriorityController');
    Route::put('priorities-state/{id}', 'API\PriorityController@updateState' );
    Route::get('priority-export', 'API\PriorityController@dataExport'); //export xlsx

    //PersonController
    Route::apiResource('people', 'API\PersonController');
    Route::put('people-state/{id}', 'API\PersonController@updateState');
    Route::get('person-export', 'API\PersonController@dataExport'); //export xlsx

    //ContextTypeController context-type-export
    Route::apiResource('context-types', 'API\ContextTypeController');
    Route::put('context-types-state/{id}', 'API\ContextTypeController@updateState');
    Route::get('context-type-export', 'API\ContextTypeController@dataExport'); //export xlsx

    //TypeIdentificationController
    Route::apiResource('type-identifications', 'API\TypeIdentificationController');
    Route::put('type-identifications-state/{id}', 'API\TypeIdentificationController@updateState');
    Route::get('type-identification-export', 'API\TypeIdentificationController@dataExport'); //export xlsx

    //GenderController
    Route::apiResource('genders', 'API\GenderController');
    Route::put('genders-state/{id}', 'API\GenderController@updateState');
    Route::get('gender-export', 'API\GenderController@dataExport'); //export xlsx

    //EntryFilingController
    Route::apiResource('entry-filing', 'API\EntryFilingController');
    Route::post('entry-filing-cancel/{id}', 'API\EntryFilingController@cancelFiling');
    Route::post('entry-filing/upload-temp-files', 'API\EntryFilingController@uploadTempFiles');
    Route::post('entry-filing/delete-files/{id}', 'API\EntryFilingController@deleteFile');
    //Route::get('entry-filing/download-files/{id}', 'API\EntryFilingController@downloadFile');
    Route::get('template-entry-filing', 'API\EntryFilingController@generateTemplate');
    Route::get('entryfiling/export', 'API\EntryFilingController@export'); //export xlsx

    //OutgoingFilingController
    Route::apiResource('outgoing-filing', 'API\OutgoingFilingController');
    Route::post('outgoing-filing-cancel/{id}', 'API\OutgoingFilingController@cancelFiling');
    Route::post('outgoing-filing/upload-temp-files', 'API\OutgoingFilingController@uploadTempFiles');
    Route::post('outgoing-filing/upload-guide', 'API\OutgoingFilingController@uploadTempFilesGuide');
    Route::post('outgoing-filing/delete-files/{id}', 'API\OutgoingFilingController@deleteFile');
    //Route::get('outgoing-filing/download-files/{id}', 'API\OutgoingFilingController@downloadFile');
    Route::get('template-outgoing-filing', 'API\OutgoingFilingController@generateTemplate');
    Route::get('outgoingfiling/export', 'API\OutgoingFilingController@export'); //export xlsx

    //RolHasPermissionController, PermisionController and RolController
    Route::apiResource('all-rol', 'API\RolController');
    Route::apiResource('all-permissions', 'API\PermisionController');
    Route::apiResource('role-has-permissions', 'API\RoleHasPermissionController');
    Route::post('delete-role-has-permissions', 'API\RoleHasPermissionController@destroy');

    //UserController all data of roles and permissions
    Route::apiResource('all-user', 'API\UserController');
    Route::post('all-user/{id}', 'API\UserController@update');
    Route::put('all-user-state/{id}', 'API\UserController@updateState');
    Route::post('change-password', 'API\UserController@changePassword');
    Route::post('change-data', 'API\UserController@changeData');
    Route::post('change-image', 'API\UserController@changeImage');

    //CancellationReasonController
    Route::apiResource('cancellation-reasons', 'API\CancellationReasonController');
    Route::put('cancellation-reasons-state/{id}', 'API\CancellationReasonController@updateState');
    Route::get('cancellation-reasons-export', 'API\CancellationReasonController@dataExport'); //export xlsx

    //UpFilesController
    Route::get('get-file/{type}/{filename}/{settled}', 'API\UpFilesController@getFile');

    //CompanyController
    Route::apiResource('company', 'API\CompanyController');

    //SearchFilingController
    Route::apiResource('result-filings', 'API\SearchFilingController');
    Route::get('searchfiling/export', 'API\SearchFilingController@export'); //export xlsx

    //CompanyController
    Route::post('change-logo', 'API\CompanyController@changeLogo');
  });
});
Route::group(['prefix' => 'v1'], function () {
  Route::apiResource('company', 'API\CompanyController');
});

