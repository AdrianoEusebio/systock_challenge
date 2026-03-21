<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\SqlReportController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('check.jwt')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'userProfile']);
    });
});

Route::middleware('check.jwt')->prefix('usuarios')->group(function () {
    Route::get('/', [UsuarioController::class, 'index']);
    Route::post('/', [UsuarioController::class, 'store']);
    Route::get('/{id}', [UsuarioController::class, 'show']);
    Route::put('/{id}', [UsuarioController::class, 'update']);
    Route::delete('/{id}', [UsuarioController::class, 'destroy']);
});

Route::middleware('check.jwt')->prefix('produtos')->group(function () {
    Route::get('/', [ProdutoController::class, 'index']);
    Route::post('/', [ProdutoController::class, 'store']);
    Route::get('/{id}', [ProdutoController::class, 'show']);
    Route::put('/{id}', [ProdutoController::class, 'update']);
    Route::delete('/{id}', [ProdutoController::class, 'destroy']);
    Route::get('/usuario/{usuarioId}', [ProdutoController::class, 'findByUsuarioId']);
});

// Relatórios
Route::middleware('check.jwt')->prefix('relatorio')->group(function () {
    Route::get('/', [SqlReportController::class, 'relatorioSql']);
    Route::get('/maiores-estoques', [SqlReportController::class, 'maioresEstoques']);
    Route::get('/produtos-mais-caros', [SqlReportController::class, 'produtosMaisCaros']);
    Route::get('/faixas-precos', [SqlReportController::class, 'faixasPrecos']);
});
