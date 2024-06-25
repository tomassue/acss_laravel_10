<?php

use App\Livewire\Course;
use App\Livewire\Dashboard;
use App\Livewire\Faculty;
use App\Livewire\Rooms;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/rooms', Rooms::class)->name('rooms');
    Route::get('/faculty', Faculty::class)->name('faculty');
    Route::get('/courses', Course::class)->name('courses');
});
