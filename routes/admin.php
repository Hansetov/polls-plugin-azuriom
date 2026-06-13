<?php

use Azuriom\Plugin\Polls\Http\Controllers\Admin\PollController;
use Illuminate\Support\Facades\Route;

Route::middleware('can:polls.admin')->group(function () {
    Route::get('/', [PollController::class, 'index'])->name('index');
    Route::get('/create', [PollController::class, 'create'])->name('create');
    Route::post('/', [PollController::class, 'store'])->name('store');
    Route::get('/{poll}/edit', [PollController::class, 'edit'])->name('edit');
    Route::put('/{poll}', [PollController::class, 'update'])->name('update');
    Route::post('/{poll}/toggle', [PollController::class, 'toggleStatus'])->name('toggle');
    Route::delete('/{poll}', [PollController::class, 'destroy'])->name('destroy');
    Route::delete('/{poll}/options/{option}', [PollController::class, 'destroyOption'])->name('options.destroy');
});
