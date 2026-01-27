
<?php
// Archivo de rutas para la API
// Aquí se definen las rutas públicas y protegidas de la aplicación

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Authentication routes
// Rutas para registro y login de usuarios
Route::post('/register', [AuthController::class, 'createUser']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
// Rutas protegidas que requieren autenticación con Sanctum
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Your other protected API routes go here
});
