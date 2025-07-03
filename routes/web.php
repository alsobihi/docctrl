<?php

/*
|--------------------------------------------------------------------------
| 2. Update Web Routes
|--------------------------------------------------------------------------
|
| Open `routes/web.php` and replace the existing dashboard route
| with a new one that points to our `DashboardController`.
|
*/

// File: routes/web.php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController; // <-- Add this line
use App\Http\Controllers\PlantController; // <-- Add this line
use App\Http\Controllers\EmployeeController; // <-- Add this line
use App\Http\Controllers\ProjectController; // <-- Add this line
use App\Http\Controllers\DocumentTypeController; // <-- Add this line
use App\Http\Controllers\EmployeeDocumentController; // <-- Add this line
use App\Http\Controllers\WorkflowController; // <-- Add this line
use App\Http\Controllers\WorkflowStepController; // <-- Add this line
use App\Http\Controllers\ProcessWorkflowController; // <-- Add this line
use App\Http\Controllers\ProjectTeamController; // <-- Add this line
use App\Http\Controllers\Api\WorkflowApiController; // <-- Add this if not present
use App\Http\Controllers\ReportController; // <-- Add this line
use App\Http\Controllers\InProgressWorkflowController; // <-- Add this
use App\Http\Controllers\DocumentTemplateController;
use App\Http\Controllers\UserController;


use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // By default, you can redirect to the login page
    return redirect()->route('login');
});

// This is our new main route for the dashboard after a user logs in.
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

     // Add this line for Plant Management
    Route::resource('plants', PlantController::class);
     Route::resource('employees', EmployeeController::class); // <-- Add this line
     Route::resource('projects', ProjectController::class); // <-- Add this line
     Route::resource('document-types', DocumentTypeController::class); // <-- Add this line
     Route::resource('employees.documents', EmployeeDocumentController::class)->shallow()->only(['index', 'create', 'store', 'destroy']);
    Route::resource('workflows', WorkflowController::class);
    // Routes for managing steps within a workflow
    Route::post('workflows/{workflow}/steps', [WorkflowStepController::class, 'store'])->name('workflows.steps.store');
    Route::delete('workflow-steps/{step}', [WorkflowStepController::class, 'destroy'])->name('workflows.steps.destroy');



Route::get('process-workflow', [ProcessWorkflowController::class, 'create'])->name('process-workflow.create');
Route::post('process-workflow', [ProcessWorkflowController::class, 'show'])->name('process-workflow.show');


Route::get('projects/{project}/team', [ProjectTeamController::class, 'index'])->name('projects.team.index');
Route::post('projects/{project}/team', [ProjectTeamController::class, 'store'])->name('projects.team.store');
Route::delete('projects/{project}/team/{employee}', [ProjectTeamController::class, 'destroy'])->name('projects.team.destroy');



Route::get('/employees/{employee}/relevant-workflows', [WorkflowApiController::class, 'getRelevantWorkflows'])
     ->name('api.employees.workflows');



Route::get('reports/expiring-documents', [ReportController::class, 'expiringDocumentsForm'])->name('reports.expiring-documents.form');
Route::get('reports/generate/expiring-documents', [ReportController::class, 'generateExpiringDocumentsReport'])->name('reports.expiring-documents.generate');






Route::get('in-progress-workflows', [InProgressWorkflowController::class, 'index'])->name('workflows.in-progress');



Route::get('process-workflow/start', [ProcessWorkflowController::class, 'create'])->name('process-workflow.create');
Route::get('employees/{employee}/workflows/{workflow}', [ProcessWorkflowController::class, 'show'])->name('process-workflow.show');
Route::post('process-workflow/show', [ProcessWorkflowController::class, 'redirectToShow'])->name('process-workflow.redirect');
// API route for dynamic filtering
Route::get('/employees/{employee}/relevant-workflows', [WorkflowApiController::class, 'getRelevantWorkflows'])->name('api.employees.workflows');





Route::resource('document-templates', DocumentTemplateController::class);

Route::resource('users', UserController::class)->middleware('can:admin');







});

require __DIR__.'/auth.php';
