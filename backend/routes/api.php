<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NoteController;

// Rutas de autenticación
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('validate-token', [AuthController::class, 'validateToken']);

// Rutas protegidas para el CRUD de notas
Route::middleware('auth:api')->group(function () {
    Route::get('/notes/{user}', [NoteController::class, 'index']);
    Route::post('/notes-store', [NoteController::class, 'store']);
    Route::put('/notes/{note}', [NoteController::class, 'update']);
    Route::delete('/notes/{note}', [NoteController::class, 'destroy']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
