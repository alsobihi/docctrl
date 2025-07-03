<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\EmployeeController;
use App\Http\Controllers\Api\V1\DocumentController;
use App\Http\Controllers\Api\V1\WorkflowController;
use App\Http\Controllers\Api\V1\PlantController;

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

// Public routes
Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
});

// Protected routes
Route::prefix('v1')->middleware(['auth:sanctum', 'api.response'])->group(function () {
    
    // Authentication
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [DashboardController::class, 'stats']);
        Route::get('/activities', [DashboardController::class, 'activities']);
    });

    // Plants
    Route::apiResource('plants', PlantController::class);
    Route::get('plants/{plant}/employees', [PlantController::class, 'employees']);

    // Employees
    Route::apiResource('employees', EmployeeController::class);
    Route::get('employees/{employee}/documents', [EmployeeController::class, 'documents']);
    Route::get('employees/{employee}/workflows', [EmployeeController::class, 'workflows']);

    // Documents
    Route::apiResource('documents', DocumentController::class);
    Route::get('documents/{document}/download', [DocumentController::class, 'download']);
    Route::get('reports/expiring-documents', [DocumentController::class, 'expiringReport']);

    // Workflows
    Route::apiResource('workflows', WorkflowController::class);
    Route::post('workflows/{workflow}/document-types', [WorkflowController::class, 'addDocumentType']);
    Route::delete('workflows/{workflow}/document-types/{documentType}', [WorkflowController::class, 'removeDocumentType']);
    Route::post('workflows/{workflow}/start', [WorkflowController::class, 'startForEmployee']);
    Route::get('workflows/{workflow}/employees/{employee}/checklist', [WorkflowController::class, 'getChecklist']);
    Route::get('workflows/in-progress', [WorkflowController::class, 'inProgress']);

    // Legacy route for existing frontend
    Route::get('/employees/{employee}/relevant-workflows', [App\Http\Controllers\Api\WorkflowApiController::class, 'getRelevantWorkflows']);
});

// Fallback for undefined API routes
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found'
    ], 404);
});