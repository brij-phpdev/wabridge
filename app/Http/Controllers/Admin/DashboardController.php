<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        // Real KPIs (organizations count, subscription/credit summaries) are
        // wired in the Commercial foundation stage. This confirms the
        // platform-admin shell, route guard, and Inertia render work
        // end-to-end before any real data exists to show.
        return Inertia::render('Admin/Dashboard');
    }
}
