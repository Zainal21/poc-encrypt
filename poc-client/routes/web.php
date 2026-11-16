<?php

use App\Http\Controllers\DemoController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('symmetric'));
Route::get('/symmetric', [DemoController::class, 'symmetric'])->name('symmetric');
Route::post('/symmetric/encrypt', [DemoController::class, 'symmetricEncrypt'])->name('symmetric.encrypt');
Route::post('/symmetric/decrypt', [DemoController::class, 'symmetricDecrypt'])->name('symmetric.decrypt');

Route::get('/asymmetric', [DemoController::class, 'asymmetric'])->name('asymmetric');
Route::post('/asymmetric/generate-keys', [DemoController::class, 'asymmetricGenerateKeys'])->name('asymmetric.generate');
Route::post('/asymmetric/encrypt', [DemoController::class, 'asymmetricEncrypt'])->name('asymmetric.encrypt');
Route::post('/asymmetric/decrypt', [DemoController::class, 'asymmetricDecrypt'])->name('asymmetric.decrypt');

Route::get('/personal-data', [DemoController::class, 'personalData'])->name('personal');
Route::post('/personal-data', [DemoController::class, 'personalDataStore'])->name('personal.store');
