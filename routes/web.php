<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentGroupController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        // 'laravelVersion' => Application::VERSION,
        // 'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::post('/document/storeMany', [DocumentController::class, 'storeMany'])->name('document.storeMany');
    Route::resource('document', DocumentController::class);

    Route::post('/documentGroup/{documentGroup}/addQR', [DocumentGroupController::class, 'addQR'])->name('documentGroup.addQR');
    Route::resource('documentGroup', DocumentGroupController::class);

    Route::get('/users', function () {
        return Inertia::render('Users');
    })->name('users');
});
