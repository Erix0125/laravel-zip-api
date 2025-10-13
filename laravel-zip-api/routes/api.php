<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CitiesController;
use App\Http\Controllers\CountiesController;

Route::get('/counties', [CountiesController::class, 'index']);
Route::post('/counties', [CountiesController::class, 'create'])->middleware('auth:sanctum');
Route::patch('/counties/{id}', [CountiesController::class, 'modify'])->middleware('auth:sanctum');
Route::delete('/counties/{id}', [CountiesController::class, 'delete'])->middleware('auth:sanctum');

Route::get('/counties/{county_id}/cities', [CitiesController::class, 'index']);
Route::post('/counties/{county_id}/cities', [CitiesController::class, 'create'])->middleware('auth:sanctum');
Route::patch('/counties/{county_id}/cities/{city_id}', [CitiesController::class, 'modify'])->middleware('auth:sanctum');
Route::delete('/counties/{county_id}/cities/{city_id}', [CitiesController::class, 'delete'])->middleware('auth:sanctum');

Route::post('/users/login', [UsersController::class, 'login']);
Route::get('/users', [UsersController::class, 'index'])->middleware('auth:sanctum');
