<?php

use App\Livewire\Course;
use App\Livewire\Dashboard;
use App\Livewire\Faculty;
use App\Livewire\FacultySchedules;
use App\Livewire\MyProfile;
use App\Livewire\Rooms;
use App\Livewire\StudentSchedule;
use App\Livewire\Users;
use App\Http\Middleware\SuperAdminAccess;
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
    return view('auth.login');
});

Auth::routes();

Route::group(['middleware' => ['auth', 'superadmin']], function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/rooms', Rooms::class)->name('rooms');
    //// Route::get('/faculty', Faculty::class)->name('faculty');
    Route::get('/courses', Course::class)->name('courses');
    Route::get('/faculty-schedules', FacultySchedules::class)->name('faculty-schedules');
    Route::get('/student-schedules', StudentSchedule::class)->name('student-schedules');
    Route::get('/users', Users::class)->name('users');
    Route::get('/profile', MyProfile::class)->name('profile');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
});
