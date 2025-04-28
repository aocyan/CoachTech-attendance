<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthenticatedSessionController;
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
Route::post('/register/store', [UserController::class, 'store']) -> name('user.store');

Route::middleware('auth') -> group(function () {
    Route::get('/attendance', [UserController::class, 'attend']) -> name('user.attend');
    Route::get('/attendance/list', [UserController::class, 'index']) -> name('user.index');
    Route::get('/attendance/default', [UserController::class, 'defaultAttend']) -> name('user.default');
    Route::post('/attendance/clock/in', [UserController::class, 'clockIn']) -> name('user.clockIn');
    Route::post('/attendance/clock/out', [UserController::class, 'clockOut']) -> name('user.clockOut');
    Route::post('/attendance/interval/in', [UserController::class, 'intervalIn']) -> name('user.intervalIn');
    Route::post('/attendance/interval/out', [UserController::class, 'intervalOut']) -> name('user.intervalOut');  
    Route::post('/attendance/correction/{id}', [UserController::class, 'correction']) -> name('user.correction');
});


Route::middleware('multiAuth') -> group(function () {
    Route::get('/stamp_correction_request/list', [UserController::class, 'apply']) -> name('user.apply');
    Route::post('/stamp_correction_request/list/search', [UserController::class, 'apply']) -> name('user.search');
    Route::get('/attendance/{id}', [UserController::class, 'detail'] )-> name('user.detail');
});


Route::prefix('admin') -> name('admin.')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        -> middleware('guest:admin')
        -> name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        -> middleware('guest:admin')
        -> name('login');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        -> middleware('auth:admin')
        -> name('logout');
    Route::get('attendance/list', [AdminController::class, 'index'])
        -> middleware('auth:admin')
        -> name('attendance.list');
});

Route::middleware('auth:admin') -> group(function () {
    Route::get('/admin/staff/list', [StaffController::class, 'index'])->name('staff.index');
    Route::post('/attendance/list/search', [AdminController::class, 'indexSearch']) -> name('admin.index.search');
    Route::post('/attendance/admin/correction/{id}', [AdminController::class, 'correction']) -> name('admin.correction');

    Route::get('/admin/attendance/staff/{id}', [StaffController::class, 'attendList']) -> name('staff.attendance');
    Route::get('/admin/attendance/staff/{id}/csv', [StaffController::class, 'csv']) -> name('staff.attendance.csv');
    Route::get('/stamp_correction_request/approve/{attendance_correct_request}', [StaffController::class, 'detail']) -> name('staff.detail');
    Route::post('/stamp_correction_request/approve/correction/{attendance_correct_request}', [StaffController::class, 'correction']) -> name('staff.correction');
});