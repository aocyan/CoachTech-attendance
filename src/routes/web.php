<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\RequestController;

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

Route::post('/register', [UserController::class, 'store'])->name('user.store');
Route::get('/attendance/default', [UserController::class, 'defaultAttend'])->name('user.default');
Route::middleware('auth')->group(function () {
    Route::get('/attendance', [UserController::class, 'attend'])->name('user.attend');
});
Route::post('/attendance/clock/in', [UserController::class, 'clockIn'])->name('user.clockIn');
Route::post('/attendance/clock/out', [UserController::class, 'clockOut'])->name('user.clockOut');
Route::post('/attendance/interval/in', [UserController::class, 'intervalIn'])->name('user.intervalIn');
Route::post('/attendance/interval/out', [UserController::class, 'intervalOut'])->name('user.intervalOut');

Route::get('/attendance/list', [UserController::class, 'index'])->name('user.index');
Route::get('/attendance/{id}', [UserController::class, 'detail'])->name('user.detail');
Route::post('/attendance/correction/{id}', [UserController::class, 'correction'])->name('user.correction');
Route::get('/stamp_correction_request/list', [UserController::class, 'apply'])->name('user.apply');



//Route::get('/admin/login', [AdminController::class, 'login'])->name('admin.login');
//Route::get('/admin/attendance/list', [AdminController::class, 'index'])->name('admin.index');
//Route::get('/attendance/detail', [AdminController::class, 'admin'])->name('admin.detail');



//Route::get('/admin/staff/list', [StaffController::class, 'staff'])->name('staff.index');
//::get('/admin/attendance/staff/detail', [StaffController::class, 'attendList'])->name('staff.attendList');



//Route::get('/stamp_correction_request/list', [RequestController::class, 'apply'])->name('admin.apply');
//Route::get('/stamp_correction_request/approve/detail', [RequestController::class, 'correction'])->name('admin.correct');
