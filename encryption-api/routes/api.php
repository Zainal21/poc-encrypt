<?php

use App\Http\Controllers\Api\AsymmetricController;
use App\Http\Controllers\Api\PersonalDataController;
use App\Http\Controllers\Api\SymmetricController;
use Illuminate\Support\Facades\Route;

Route::post('/symmetric/encrypt', [SymmetricController::class, 'encrypt']);
Route::post('/symmetric/decrypt', [SymmetricController::class, 'decrypt']);

Route::post('/asymmetric/generate-keys', [AsymmetricController::class, 'generateKeys']);
Route::post('/asymmetric/encrypt', [AsymmetricController::class, 'encrypt']);
Route::post('/asymmetric/decrypt', [AsymmetricController::class, 'decrypt']);

Route::post('/personal-data', [PersonalDataController::class, 'store']);
Route::get('/personal-data', [PersonalDataController::class, 'index']);
Route::get('/personal-data/{id}', [PersonalDataController::class, 'show']);
