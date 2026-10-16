<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EmployeeDashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\StockAdjustmentController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Área protegida
Route::middleware('auth')->group(function () {

    // Dashboard admin — solo admin
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('role:admin');

    // Inventario — accesible para admin y empleado
    Route::resource('articles', ArticleController::class)->middleware('role:admin,empleado');

    // Categorías — solo admin
    Route::resource('categories', CategoryController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->middleware('role:admin');

    // Reservas — admin y empleado
    Route::middleware('role:admin,empleado')->group(function () {
        Route::get('/reservas/verificar-disponibilidad', [ReservationController::class, 'verificarDisponibilidad'])->name('reservas.verificar');
        Route::get('/reservas/nueva',           [ReservationController::class, 'create'])->name('reservas.create');
        Route::post('/reservas',                [ReservationController::class, 'store'])->name('reservas.store');
        Route::get('/reservas',                 [ReservationController::class, 'index'])->name('reservas.index');
        Route::get('/reservas/{reserva}/despacho',    [ReservationController::class, 'despacho'])->name('reservas.despacho');
        Route::post('/reservas/{reserva}/despachar',  [ReservationController::class, 'despachar'])->name('reservas.despachar');
        Route::post('/reservas/{reserva}/cancelar',   [ReservationController::class, 'cancelar'])->name('reservas.cancelar');
        Route::get('/reservas/{reserva}/comprobante', [ReservationController::class, 'comprobante'])->name('reservas.comprobante');
    });

    // Retornos — solo admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/retornos',                   [ReturnController::class, 'index'])->name('retornos.index');
        Route::get('/retornos/{reserva}/crear',   [ReturnController::class, 'create'])->name('retornos.create');
        Route::post('/retornos/{reserva}',        [ReturnController::class, 'store'])->name('retornos.store');
    });

    // Ajustes de Stock — solo admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/inventario/ajustes',  [StockAdjustmentController::class, 'index'])->name('stock.ajustes');
        Route::post('/inventario/ajustes', [StockAdjustmentController::class, 'store'])->name('stock.ajustes.store');
    });

    // Clientes — admin y empleado
    Route::middleware('role:admin,empleado')->group(function () {
        Route::get('/clientes/buscar',          [ClientController::class, 'buscar'])->name('clients.buscar');
        Route::get('/clientes',                 [ClientController::class, 'index'])->name('clients.index');
        Route::post('/clientes',                [ClientController::class, 'store'])->name('clients.store');
        Route::get('/clientes/{client}',        [ClientController::class, 'show'])->name('clients.show');
        Route::get('/clientes/{client}/editar', [ClientController::class, 'edit'])->name('clients.edit');
        Route::put('/clientes/{client}',        [ClientController::class, 'update'])->name('clients.update');
        Route::delete('/clientes/{client}',     [ClientController::class, 'destroy'])->name('clients.destroy');
    });

    // Control de Pagos — solo admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/pagos',                         [PaymentController::class, 'index'])->name('pagos.index');
        Route::get('/pagos/{reserva}',               [PaymentController::class, 'show'])->name('pagos.show');
        Route::post('/pagos/{reserva}',              [PaymentController::class, 'store'])->name('pagos.store');
        Route::post('/pagos/{reserva}/liquidar',     [PaymentController::class, 'liquidar'])->name('pagos.liquidar');
    });

    // Reporte de Disponibilidad — solo admin
    Route::get('/disponibilidad', [AvailabilityController::class, 'index'])
        ->name('disponibilidad.index')
        ->middleware('role:admin');

    // Panel del empleado
    Route::prefix('empleado')->middleware('role:admin,empleado')->group(function () {
        Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('employee.dashboard');
        Route::get('/catalogo',  [EmployeeDashboardController::class, 'catalogo'])->name('employee.catalogo');
        Route::get('/nueva-reserva', fn () => redirect()->route('reservas.create'))->name('employee.reserva.placeholder');
    });
});

// Raíz → redirección inteligente según sesión activa
Route::get('/', function () {
    if (\Illuminate\Support\Facades\Auth::check()) {
        return redirect()->route(\Illuminate\Support\Facades\Auth::user()->rol === 'admin' ? 'dashboard' : 'employee.dashboard');
    }
    return redirect()->route('login');
});
