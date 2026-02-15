<?php

use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CompanySettingsController;
use App\Http\Controllers\UserStatsConttroller;

Route::prefix('api')->middleware(['web'])->group(function () {
    Route::post('/register', [AuthController::class, 'createUser']);
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::middleware(['auth'])->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/budget-items', [AuthController::class, 'getBudgetItems']);
        Route::get('/clients', [ClientController::class, 'index']);
        Route::get('/clients/{id}', [ClientController::class, 'show']);
        Route::post('/clients-new', [ClientController::class, 'storeClient']);
        Route::put('/clients/{id}', [ClientController::class, 'update']);
        Route::delete('/clients/{id}', [ClientController::class, 'destroy']);
        
        // Budget routes
        Route::get('/budgets', [BudgetController::class, 'index']);
        Route::post('/budgets-new', [BudgetController::class, 'store']);
        Route::get('/budgets-draft', [BudgetController::class, 'getDraft']);
        Route::post('/budgets-draft', [BudgetController::class, 'saveDraft']);
        Route::delete('/budgets-draft', [BudgetController::class, 'clearDraft']);
        Route::get('/budgets/{id}', [BudgetController::class, 'show']);
        Route::get('/budgets/{id}/pdf', [BudgetController::class, 'downloadPDF']);
        Route::put('/budgets/{id}', [BudgetController::class, 'update']);
        Route::delete('/budgets/{id}', [BudgetController::class, 'destroy']);
        Route::post('/budgets/{id}/send-email', [BudgetController::class, 'sendEmail']);
        
        // Company Settings routes
        Route::get('/company-settings', [CompanySettingsController::class, 'index']);
        Route::post('/company-settings', [CompanySettingsController::class, 'store']);

        // clientes y presupuestos
        Route::get('/user-stats', [UserStatsConttroller::class, 'index']);
    });
});

Route::get('/', function () {
    return view('app');
})->name('home');

// Catch-all route for Vue SPA
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api|sanctum).*$')->name('spa');

require __DIR__.'/settings.php';
