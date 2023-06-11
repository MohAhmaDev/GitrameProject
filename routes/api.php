<?php

use App\Http\Controllers\Api\AdmissionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CreanceController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DetteController;
use App\Http\Controllers\Api\EmployeController;
use App\Http\Controllers\Api\EntrepriseController;
use App\Http\Controllers\Api\FilialeController;
use App\Http\Controllers\Api\FinanceController;
use App\Http\Controllers\Api\FormationController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\StagiareController;
use App\Http\Controllers\api\StartController;
use App\Http\Controllers\Api\UserController;
use App\Http\Resources\FinanceResource;
use App\Models\Admission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

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
    Route::post('/roles/{id}', [RoleController::class, 'store']);
    Route::put('/roles/{id}', [RoleController::class, 'update'])->whereNumber('id');

    Route::get('/admission', [AdmissionController::class, 'index']);
    Route::post('/admission/{id}', [AdmissionController::class, 'store']);
    Route::put('/admission/{id}', [AdmissionController::class, 'update'])->whereNumber('id');

    Route::get('/filiale', [FilialeController::class, 'index']);
    Route::post('/filiale/{id}', [FilialeController::class, 'store']);
    Route::put('/filiale/{id}', [FilialeController::class, 'update'])->whereNumber('id');

    Route::get('/agregat/{type_agregat}', [FilialeController::class, 'get_agregat']);
    Route::get('/finance_years', [FilialeController::class, 'get_years']);

    Route::get('/entreprise', [EntrepriseController::class, 'index']);
    Route::post('/entreprise', [EntrepriseController::class, 'store']);
    Route::apiResource('/employes', EmployeController::class);
    Route::apiResource('/stagiares', StagiareController::class);
    Route::apiResource('/finances', FinanceController::class);
    Route::apiResource('/dettes', DetteController::class);
    Route::apiResource('/creances', CreanceController::class);
    Route::apiResource('/formations', FormationController::class);
    Route::post('/dash', [DashboardController::class, 'get_Employes_dash']);
    Route::get('/dash-line', [DashboardController::class, 'get_FFinances']);
    Route::get('/dash-ca', [DashboardController::class, 'get_ca']);
    Route::post('/finance_dashboard', [DashboardController::class, 'get_dashboard_finance']);
    Route::post('/rhs_dashboard', [DashboardController::class, 'get_dashboard_rhs']);
    Route::post('/dash_creance_dettes', [DashboardController::class, 'get_fcreance_dette']);
    Route::get('date_fcreance_dettes', [DashboardController::class, 'get_Mois']);
});

Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);



