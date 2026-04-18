<?php

use App\Models\Contact;
use App\Models\FollowUp;
use App\Models\Intake;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\IntakeController;


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

        return view('dashboard', compact(
            'totalContacts',
            'totalIntakes',
            'newIntakes',
            'contactedIntakes',
            'totalFollowUps',
            'recentIntakes'
        ));
    })->name('dashboard');

    Route::get('intakes', [IntakeController::class, 'index'])->name('intakes.index');
    Route::get('intakes/create', [IntakeController::class, 'create'])->name('intakes.create');
    Route::post('intakes', [IntakeController::class, 'store'])->name('intakes.store');
});

require __DIR__ . '/settings.php';
