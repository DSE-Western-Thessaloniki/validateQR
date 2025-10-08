<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentGroupController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

$missingDocumentHandler = function (Request $request) {
    $document_id = $request->route('document');
    $ip_address = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ?
        "{$_SERVER['HTTP_X_FORWARDED_FOR']} -> {$_SERVER['REMOTE_ADDR']}" :
        "{$_SERVER['REMOTE_ADDR']}";
    Log::info("Το έγγραφο με id '{document_id}' δεν βρέθηκε. [{ip_address}]", [
        'document_id' => $document_id,
        'ip_address' => $ip_address
    ]);
    return Inertia::render('Error/DocumentNotFound');
};

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
    ]);
})->name('home');

Route::middleware(['throttle:document'])
    ->get('/document/{document}/download', [DocumentController::class, 'download'])->name('document.download')
    ->missing($missingDocumentHandler);

Route::middleware(['throttle:document'])
    ->resource('document', DocumentController::class)
    ->only(['show'])
    ->missing($missingDocumentHandler);

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
    Route::get('/document/{document}/showAdmin', [DocumentController::class, 'adminShow'])->name('document.adminShow');
    Route::post('/document/{document}/cancel', [DocumentController::class, 'cancel'])->name('document.cancel');
    Route::post('/document/{document}/replaceWithId', [DocumentController::class, 'replaceWithId'])->name('document.replaceWithId');
    Route::post('/document/{document}/replaceWithFile', [DocumentController::class, 'replaceWithFile'])->name('document.replaceWithFile');
    Route::post('/document/{document}/restoreState', [DocumentController::class, 'restoreState'])->name('document.restoreState');
    Route::get('/document/{document}/withQR', [DocumentController::class, 'downloadWithQR'])->name('document.downloadWithQR');
    Route::post('/document/{document}/storeSigned', [DocumentController::class, 'storeSigned'])->name('document.storeSigned');
    Route::resource('document', DocumentController::class)->only(['index', 'store']);

    Route::post('/documentGroup/{documentGroup}/addQR', [DocumentGroupController::class, 'addQR'])->name('documentGroup.addQR');
    Route::get('/documentGroup/{documentGroup}/withQR', [DocumentGroupController::class, 'getQR'])->name('documentGroup.getQR');
    Route::post('/documentGroup/{documentGroup}/togglePublished', [DocumentGroupController::class,'togglePublished'])->name('documentGroup.togglePublished');
    Route::post('/documentGroup/{documentGroup}/confirmDelete', [DocumentGroupController::class,'confirmDelete'])->name('documentGroup.confirmDelete');
    Route::post('/documentGroup/{documentGroup}/cancelDocuments', [DocumentGroupController::class,'cancelDocuments'])->name('documentGroup.cancelDocuments');
    Route::resource('documentGroup', DocumentGroupController::class);

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'store'])->name('settings.store');
    Route::post('/user/{user}/toggleActive', [UserController::class, 'toggleActive'])
        ->middleware('can:update,App\Models\User')
        ->name('user.toggleActive');
    Route::resource('user', UserController::class)->except(['show']);
});
