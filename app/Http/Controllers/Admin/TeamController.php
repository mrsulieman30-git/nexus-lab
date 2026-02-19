<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; // Import Base Controller
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    /**
     * Security Constructor:
     * Restricts access to Lab Managers and Super Admins only.
     */
    public function __construct()
    {
        // Note: Middleware-based role checks are recommended; constructor kept minimal.
    }

    /**
     * List all team members
     */
    public function index()
    {
        // Security Check (Redundant if middleware works, but safe to keep)
        // Deny access for lab technicians
        if (Auth::user()->role === config('roles.keys.lab_tech')) {
            abort(403, 'Unauthorized. Only Managers/Admins can view the team list.');
        }

        // Only show users belonging to THIS lab
        $users = User::where('laboratory_id', Auth::user()->laboratory_id)
            ->where('id', '!=', Auth::id()) // Don't show yourself in the list
            ->where('role', '!=', config('roles.keys.super_admin')) // Hide super admin
            ->latest()
            ->get();

        return Inertia::render('Teams/Index', [
            'users' => $users
        ]);
    }

    /**
     * Add a new team member
     */
    public function store(Request $request)
    {
        // Security Check
        if (Auth::user()->role === config('roles.keys.lab_tech')) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,lab_manager,lab_tech',
        ]);

        // Auto-generate a password (they can change it later)
        // You might want to email this to them in a real app
        $tempPassword = Str::random(8);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($tempPassword),
            'laboratory_id' => Auth::user()->laboratory_id,
            'email_verified_at' => now(), // Auto-verify since admin created it
        ]);

        return back()->with('message', "User created! Temporary Password: $tempPassword");
    }

    /**
     * Remove a team member
     */
    public function destroy(User $team) // Route param is 'team', explicitly mapped to User model
    {
        // Security Check
        if (Auth::user()->role === config('roles.keys.lab_tech')) {
            abort(403, 'Unauthorized.');
        }

        // Ensure you can't delete users from OTHER labs
        if ($team->laboratory_id !== Auth::user()->laboratory_id) {
            abort(403, 'You do not have permission to delete this user.');
        }

        $team->delete();

        return back()->with('message', 'Team member removed successfully.');
    }
}
