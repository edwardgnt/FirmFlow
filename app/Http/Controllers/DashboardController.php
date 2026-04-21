<?php

namespace App\Http\Controllers;

use App\Actions\Dashboard\BuildDashboardData;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, BuildDashboardData $buildDashboardData)
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        return view('dashboard', $buildDashboardData->handle($user));
    }
}
