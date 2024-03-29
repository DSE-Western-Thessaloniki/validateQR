<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentGroupController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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
        // 'canRegister' => Route::has('register'),
        // 'laravelVersion' => Application::VERSION,
        // 'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['throttle:document'])
    ->resource('document', DocumentController::class)
    ->only(['show'])
    ->missing(function (Request $request) {
        return Inertia::render('Error/DocumentNotFound');
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
    Route::post('/document/storeManySigned', [DocumentController::class, 'storeManySigned'])->name('document.storeManySigned');
    Route::resource('document', DocumentController::class)->only(['store']);

    Route::post('/documentGroup/{documentGroup}/addQR', [DocumentGroupController::class, 'addQR'])->name('documentGroup.addQR');
    Route::get('/documentGroup/{documentGroup}/withQR', [DocumentGroupController::class, 'getQR'])->name('documentGroup.getQR');
    Route::resource('documentGroup', DocumentGroupController::class);

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'store'])->name('settings.store');
    Route::post('/user/{user}/toggleActive', [UserController::class, 'toggleActive'])
        ->middleware('can:update,App\Models\User')
        ->name('user.toggleActive');
    Route::resource('user', UserController::class)->except(['show']);
});
