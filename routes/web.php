<?php

use App\Models\Contact;
use App\Models\FollowUp;
use App\Models\Intake;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\IntakeController;
use App\Http\Controllers\FollowUpController;


Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function (Request $request) {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $organizationId = $user->organization_id;

        $totalContacts = Contact::where('organization_id', $organizationId)->count();

        $totalIntakes = Intake::where('organization_id', $organizationId)->count();

        $newIntakes = Intake::where('organization_id', $organizationId)
            ->where('status', 'new')
            ->count();

        $contactedIntakes = Intake::where('organization_id', $organizationId)
            ->where('status', 'contacted')
            ->count();

        $totalFollowUps = FollowUp::where('organization_id', $organizationId)->count();

        $recentIntakes = Intake::with(['contact', 'assignedUser'])
            ->where('organization_id', $organizationId)
            ->latest()
            ->take(5)
            ->get();

        $overdueFollowUps = FollowUp::with(['intake.contact', 'intake.assignedUser', 'user'])
            ->where('organization_id', $organizationId)
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<', now())
            ->orderBy('next_follow_up_at')
            ->take(5)
            ->get();

        $needsAttentionIntakes = Intake::with(['contact', 'assignedUser'])
            ->where('organization_id', $organizationId)
            ->whereNotIn('status', ['won', 'lost'])
            ->where(function ($query) {
                $query->whereNull('assigned_user_id')
                    ->orWhere('last_activity_at', '<=', now()->subDays(2));
            })
            ->orderBy('last_activity_at')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalContacts',
            'totalIntakes',
            'newIntakes',
            'contactedIntakes',
            'totalFollowUps',
            'recentIntakes',
            'overdueFollowUps',
            'needsAttentionIntakes'
        ));
    })->name('dashboard');

    Route::get('intakes', [IntakeController::class, 'index'])->name('intakes.index');
    Route::get('intakes/create', [IntakeController::class, 'create'])->name('intakes.create');
    Route::post('intakes', [IntakeController::class, 'store'])->name('intakes.store');
    Route::post('intakes/{intake}/follow-ups', [IntakeController::class, 'storeFollowUp'])->name('intakes.follow-ups.store');
    Route::get('intakes/{intake}', [IntakeController::class, 'show'])->name('intakes.show');
    Route::patch('intakes/{intake}', [IntakeController::class, 'update'])->name('intakes.update');

    Route::get('follow-ups/queue', [FollowUpController::class, 'queue'])->name('follow-ups.queue');
    Route::patch('follow-ups/queue/intakes/{intake}/reassign', [FollowUpController::class, 'reassign'])
        ->name('follow-ups.queue.reassign');
});

require __DIR__ . '/settings.php';
