<?php

use Illuminate\Support\Facades\Route;

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

//Seccion de archivos
Route::get('/', function () {
    return view('welcome');
});
Route::get('/getarchivos',["uses"=>"Controller@getArchivos"]);
Route::post('/crearcarpeta',["uses"=>"Controller@crearCarpeta"]);
Route::post('/openfolder',["uses"=>"Controller@openFolder"]);
Route::post('/upload',["uses"=>"Controller@upload"]);
Route::get('/download',["uses"=>"Controller@download"]);
Route::post('/eliminar', ["uses"=>"Controller@eliminar"]);
Route::post('/eliminarcarpeta', ["uses"=>"Controller@eliminaCarpeta"]);

//Seccion de video
Route::get('/video',function () {
    return view('video');
});

Route::get('/st/{file}', "Controller@videoStream")->name('st');
