<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PruebasController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*
Route::get('/', function () {
    return view('welcome');
});

Route::get('/testorm',[PruebasController::class,'testOrm']);

Route::get('/usuario/pruebas',[UserController::class,'pruebas']);
*/


//RUTAS API

/*
Metodos http comunes

Get     - Conseguir datos o recursos
Post    - Guardar datos o recursos. o logica y devolver datos
Put     - Actualizar datos
Delete  - Eliminar datos
*/

//Rutas API Usuarios
Route::post('/api/register',[UserController::class,'register']);
Route::post('/api/login',[UserController::class,'login']);
Route::put('/api/user/update',[UserController::class,'update']);
Route::post('/api/user/upload',[UserController::class,'upload'])->middleware('apiauth');
Route::get('/api/user/avatar/{filename}',[UserController::class,'getImage']);
Route::get('/api/user/detail/{id}',[UserController::class,'detail']);

//Rutas Api Categories
Route::resource('/api/category', CategoryController::class);

//Rutas Api Posts
Route::resource('/api/post', PostController::class);
Route::post('/api/post/upload', [PostController::class,'upload']);
Route::get('/api/post/image/{filename}',[PostController::class,'getImage']);
Route::get('/api/post/category/{id}',[PostController::class,'getPostsByCategory']);
Route::get('/api/post/user/{id}',[PostController::class,'getPostsByUser']);