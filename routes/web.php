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

//Route::get('/company/{company}', 'HomeController@companytest')->middleware('company');
Route::resource('/companies', 'CompanyController')->middleware('can:create,App\Company');

Route::get('/companies/{company}/dashboard', 'CompanyController@dashboard')->middleware('can:view,company')->name('companies.dashboard');

Route::get('/companies/{company}/users', 'CompanyController@users')->middleware('can:manage,company')->name('companies.getusers');
Route::get('/companies/{company}/campaigns', 'CompanyController@campaigns')->middleware('can:manage,company')->name('companies.getcampaigns');
Route::post('/companies/{company}/users', 'CompanyController@storeuser')->middleware('can:manage,company')->name('companies.adduser');

Route::resource('/users', 'UserController')->middleware('can:create,App\User');
Route::resource('/campaigns', 'CampaignController')->middleware('can:create,App\Campaign');
