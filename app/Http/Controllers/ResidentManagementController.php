<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ResidentManagementController extends Controller
{
    /**
     * Show the resident registration and monitoring page.
     */
    public function residents(): View
    {
        return view('resident-management.residents');
    }

    /**
     * Show the unit assignment page.
     */
    public function units(): View
    {
        return view('resident-management.units');
    }

    /**
     * Show the move-in and move-out operations page.
     */
    public function moveInOut(): View
    {
        return view('resident-management.move-in-out');
    }

    /**
     * Show the family member management page.
     */
    public function familyMembers(): View
    {
        return view('resident-management.family-members');
    }

    /**
     * Show the vehicle and parking management page.
     */
    public function vehicles(): View
    {
        return view('resident-management.vehicles');
    }
}
