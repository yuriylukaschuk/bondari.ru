<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OutputController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [OutputController::class, 'index']);
Route::get('/welcome', [OutputController::class, 'index']);
Route::post('/welcome/feedbacksend', [OutputController::class,'feedbackSend'])->name('feedback.send');
Route::get('/production', [OutputController::class, 'index']);
Route::get('/directory', [OutputController::class, 'index']);
Route::get('/price-list', [OutputController::class, 'priceList']);
Route::get('/our-clients', [OutputController::class, 'outerClient']);
Route::get('/reviews', [OutputController::class, 'reviews']);
Route::get('/production/{slug?}', [OutputController::class, 'production']);
Route::get('/directory/{slug?}', [OutputController::class, 'directory']);

Route::middleware(['auth', 'verified', 'permission:dashboard-login'])->group(function () {
    Route::get('/profile', [
        ProfileController::class, 'edit'
    ])->middleware(['role:web-developer', 'permission:dashboard-changes'])->name('profile.edit');
    Route::patch('/profile', [
        ProfileController::class, 'update'
    ])->middleware(['role:web-developer', 'permission:dashboard-changes'])->name('profile.update');
    Route::delete('/profile', [
        ProfileController::class, 'destroy'
    ])->middleware(['role:web-developer', 'permission:dashboard-changes'])->name('profile.destroy');
});

// Основной маршрут для обработки ошибок
Route::fallback(function () {
    return Inertia::render('Error', [
        'status' => 404,
        'message' => 'Страница не найдена'
    ]);
})->name('error');

require __DIR__.'/auth.php';
require __DIR__.'/dashboard.php';
