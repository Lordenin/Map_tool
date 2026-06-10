<?php

use App\Http\Controllers\AnaliseController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PontoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('clientes', ClienteController::class);

    // Análises são listadas dentro do cliente (show), por isso o resource é shallow
    // e sem 'index': create/store ficam sob o cliente; o resto usa só a análise.
    Route::resource('clientes.analises', AnaliseController::class)
        ->shallow()
        ->except(['index']);

    // Pontos (estabelecimento + concorrentes) vivem dentro da análise e são listados
    // no seu show. Sem 'index'/'show' próprios; create/store ficam sob a análise.
    Route::resource('analises.pontos', PontoController::class)
        ->shallow()
        ->except(['index', 'show']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
