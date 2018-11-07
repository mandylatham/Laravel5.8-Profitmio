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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::middleware('signed', 'justinvited')->group(function() {
    Route::get('/registration/complete', 'Auth\CompleteController@show')->name('registration.complete');
    Route::post('/registration/complete', 'Auth\CompleteController@set');
});

//Route::get('/company/{company}', 'HomeController@companytest')->middleware('company');
Route::resource('/companies', 'CompanyController')->middleware('can:create,App\Company');

Route::get('/companies/{company}/dashboard', 'CompanyController@dashboard')->middleware('can:view,company')->name('companies.dashboard');
Route::get('/companies/{company}/preferences', 'CompanyController@preferences')->middleware('can:viewforpreferences,company')->name('companies.preferences');
Route::post('/companies/{company}/preferences', 'CompanyController@setpreferences')->middleware('can:viewforpreferences,company')->name('companies.setpreferences');

Route::get('/companies/{company}/adduser', 'CompanyController@createuser')->middleware('can:manage,company')->name('companies.createuser');
Route::post('/companies/{company}/adduser', 'CompanyController@storeuser')->middleware('can:manage,company')->name('companies.storeuser');

Route::get('/companies/{company}/campaign/{campaign}', 'CompanyController@campaignaccess')->middleware('can:manage,campaign')->name('companies.campaignaccess');
Route::post('/companies/{company}/campaign/{campaign}', 'CompanyController@setcampaignaccess')->middleware('can:manage,campaign')->name('companies.setcampaignaccess');

Route::get('/companies/{company}/user/{user}', 'CompanyController@useraccess')->middleware('can:manage,company')->name('companies.useraccess');
Route::post('/companies/{company}/user/{user}', 'CompanyController@setuseraccess')->middleware('can:manage,company')->name('companies.setuseraccess');

Route::resource('/users', 'UserController')->middleware('can:create,App\User');
Route::get('/impersonateas/{user}', 'Auth\ImpersonateController@login')->middleware('can:create,App\User')->name('auth.impersonate');
Route::get('/leaveimpersonating', 'Auth\ImpersonateController@leave')->name('auth.leaveimpersonate');
Route::resource('/campaigns', 'CampaignController')->middleware('can:create,App\Campaign');
