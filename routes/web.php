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
Route::resource('/users', 'UserController')->middleware('can:create,App\User');
