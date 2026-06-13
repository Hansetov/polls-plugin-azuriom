<?php

use Azuriom\Plugin\Polls\Http\Controllers\PollController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', [PollController::class, 'index'])->name('index');
    Route::get('/{poll}', [PollController::class, 'show'])->name('show');
    Route::post('/{poll}/vote', [PollController::class, 'vote'])->name('vote');
});
