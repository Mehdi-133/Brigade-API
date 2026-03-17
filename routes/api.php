<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\PlatsController;
use App\Http\Controllers\ProfileController;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/test', [PlatsController::class, 'test']);


    Route::apiResource('category', CategoryController::class);
    Route::apiResource('plats', PlatsController::class);

    Route::apiResource('profile' , ProfileController::class);
    Route::apiResource('ingredient' , IngredientController::class);
});