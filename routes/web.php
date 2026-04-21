<?php

use App\Models\Contact;
use App\Models\FollowUp;
use App\Models\Intake;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\IntakeController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\DashboardController;


Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('intakes', [IntakeController::class, 'index'])->name('intakes.index');
    Route::get('intakes/create', [IntakeController::class, 'create'])->name('intakes.create');
    Route::post('intakes', [IntakeController::class, 'store'])->name('intakes.store');
    Route::post('intakes/{intake}/follow-ups', [IntakeController::class, 'storeFollowUp'])->name('intakes.follow-ups.store');
    Route::get('intakes/{intake}', [IntakeController::class, 'show'])->name('intakes.show');
    Route::patch('intakes/{intake}', [IntakeController::class, 'update'])->name('intakes.update');

    Route::get('follow-ups/queue', [FollowUpController::class, 'queue'])->name('follow-ups.queue');
    Route::patch('follow-ups/queue/intakes/{intake}/reassign', [FollowUpController::class, 'reassign'])
        ->name('follow-ups.queue.reassign');
    Route::post('follow-ups/queue/intakes/{intake}/log', [FollowUpController::class, 'logFromQueue'])
        ->name('follow-ups.queue.log');
});

require __DIR__ . '/settings.php';
