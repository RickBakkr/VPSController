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
    if(Auth::check()) {
        return redirect()->to('/home');  
    }
    return redirect()->to('/login');
});

Auth::routes();

Route::get('/home', [
  'uses' => 'HomeController@index',
  'as' => 'home'
]);
Route::get('/server/load/{serverId}', [
  'uses' => 'ServerController@loadServers',
  'as' => 'server.load'
]);
Route::get('/server/{id}/action/{action}', [
  'uses' => 'ServerController@action',
  'as' => 'server.action'
]);
Route::get('/server/{id}/delete', [
  'uses' => 'ServerController@delete',
  'as' => 'server.delete'
]);
Route::get('/server/add/{type}', [
  'uses' => 'ServerController@getAdd',
  'as' => 'server.add'
]);
Route::post('/server/add/{type}', [
  'uses' => 'ServerController@postAdd',
  'as' => 'server.add'
]);

Route::get('/server/get_virtualizor_servers', [
  'uses' => 'ServerController@getVirtualizorServers',
  'as' => 'server.getVirtServers'
]);