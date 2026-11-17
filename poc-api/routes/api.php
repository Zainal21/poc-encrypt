<?php

use App\Http\Controllers\CryptoController;
use App\Http\Controllers\PersonalDataController;
use Illuminate\Support\Facades\Route;

Route::get('/crypto/server-public-key', [CryptoController::class, 'serverPublicKey']);
Route::post('/crypto/keys/register', [CryptoController::class, 'registerKey']);

Route::post('/personal-data/symmetric', [PersonalDataController::class, 'storeSymmetric']);
Route::get('/personal-data/symmetric', [PersonalDataController::class, 'indexSymmetric']);
Route::post('/personal-data/asymmetric', [PersonalDataController::class, 'storeAsymmetric']);
Route::get('/personal-data/asymmetric', [PersonalDataController::class, 'indexAsymmetric']);
