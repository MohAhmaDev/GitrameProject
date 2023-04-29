<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CreanceController;
use App\Http\Controllers\Api\DetteController;
use App\Http\Controllers\Api\EmployeController;
use App\Http\Controllers\Api\EntrepriseController;
use App\Http\Controllers\Api\FilialeController;
use App\Http\Controllers\Api\FinanceController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\StagiareController;
use App\Http\Controllers\api\StartController;
use App\Http\Controllers\Api\UserController;
use App\Http\Resources\FinanceResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [StartController::class, 'index']);

    Route::apiResource('/users', UserController::class);
    Route::get('/roles', [RoleController::class, 'index']);
    Route::put('/roles/{id}', [RoleController::class, 'store'])->whereNumber('id');
    Route::get('/filiale', [FilialeController::class, 'index']);
    Route::post('/filiale/{id}', [FilialeController::class, 'store']);
    Route::put('/filiale/{id}', [FilialeController::class, 'update'])->whereNumber('id');
    Route::get('/entreprise', [EntrepriseController::class, 'index']);
    Route::post('/entreprise', [EntrepriseController::class, 'store']);
    Route::apiResource('/employes', EmployeController::class);
    Route::apiResource('/stagiares', StagiareController::class);
    Route::apiResource('/finances', FinanceController::class);
    Route::apiResource('/dettes', DetteController::class);
    Route::apiResource('/creances', CreanceController::class);
});

Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);



