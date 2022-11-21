<?php

use App\Http\Controllers\AuthController;
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

Route::get('', [AuthController::class, 'home'])->name('home');
Route::get('info', [AuthController::class, 'info'])->name('info')->middleware('auth');
Route::get('register', [AuthController::class, 'register'])->name('register')->middleware('app');
Route::get('login', [AuthController::class, 'login'])->name('login')->middleware('app');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');
Route::post('handle-register', [AuthController::class, 'handleRegister'])->name('handleRegister');
Route::post('handle-login', [AuthController::class, 'handleLogin'])->name('handleLogin');
Route::get('error', [AuthController::class, 'error'])->name('error');
