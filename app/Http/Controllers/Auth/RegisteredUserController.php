<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Laboratory; // Import Laboratory model
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'laboratory_name' => 'nullable|string|max:255', // Optional: Allow creating a lab on signup
        ]);

        // Logic: First user is Super Admin
        $isFirstUser = User::count() === 0;
        $role = $isFirstUser ? 'super_admin' : 'technician'; // Default role

        // Handle Laboratory Creation (Optional for Super Admin, Required for others generally)
        $laboratoryId = null;

        if ($request->filled('laboratory_name')) {
            $lab = Laboratory::create([
                'name' => $request->laboratory_name,
                // Add defaults for new lab
                'subscription_status' => 'active'
            ]);
            $laboratoryId = $lab->id;

            // If they created a lab, they should be its manager (unless they are super admin)
            if (!$isFirstUser) {
                $role = 'lab_manager';
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
            'laboratory_id' => $laboratoryId,
            'is_active' => true,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
