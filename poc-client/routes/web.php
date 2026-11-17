<?php

use App\Http\Controllers\SimulationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SimulationController::class, 'home'])->name('home');
Route::get('/symmetric', [SimulationController::class, 'symmetric'])->name('symmetric');
Route::post('/symmetric/send', [SimulationController::class, 'symmetricSend'])->name('symmetric.send');
Route::get('/asymmetric', [SimulationController::class, 'asymmetric'])->name('asymmetric');
Route::post('/asymmetric/send', [SimulationController::class, 'asymmetricSend'])->name('asymmetric.send');
Route::get('/data', [SimulationController::class, 'data'])->name('data');
