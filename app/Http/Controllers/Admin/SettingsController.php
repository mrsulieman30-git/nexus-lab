<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; // Import Base Controller
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Laboratory;
use App\Models\Category;

class SettingsController extends Controller
{
    /**
     * Security Constructor:
     * Restricts access to Lab Managers and Super Admins only.
     */
    public function __construct()
    {
        // Middleware is applied via routes in new Laravel versions usually,
        // but keeping this check here adds a second layer of security.
        // Note: In Laravel 11+, middleware in construct can sometimes be tricky depending on setup.
        // We will assume standard Jetstream setup here.
    }

    /**
     * Show the Settings Page
     */
    public function index()
    {
        // Authorization Check
        if (Auth::user()->role === 'technician') {
            abort(403, 'Unauthorized action. Only Managers can manage lab settings.');
        }

        $lab = Laboratory::findOrFail(Auth::user()->laboratory_id);

        // Append full logo URL so frontend can display it easily
        // Assumes you have a 'logo_url' accessor in your Laboratory model,
        // OR we manually create it here:
        $lab->logo_url = $lab->logo_path ? Storage::url($lab->logo_path) : null;

        // Fetch Categories
        $categories = Category::where('laboratory_id', Auth::user()->laboratory_id)->get();

        // Ensure preset arrays are always arrays for the frontend
        $lab->storage_locations = $lab->storage_locations ?? [];
        $lab->prebuilt_units = $lab->prebuilt_units ?? [];
        $lab->sample_types = $lab->sample_types ?? [];

        return Inertia::render('Settings/Index', [
            'lab' => $lab,
            'categories' => $categories
        ]);
    }

    /**
     * Update Laboratory Information (with Logo & Website)
     */
    public function update(Request $request)
    {
        // Authorization Check
        if (Auth::user()->role === 'technician') {
            abort(403, 'Unauthorized action.');
        }

        // Ensure user is updating their OWN lab
        $lab = Laboratory::findOrFail(Auth::user()->laboratory_id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048', // Max 2MB
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'license_number' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'financial_year_start' => 'nullable|date',
            'storage_locations_raw' => 'nullable|string',
            'prebuilt_units_raw' => 'nullable|string',
            'sample_types_raw' => 'nullable|string',
        ]);

        // Handle Logo Upload
        if ($request->hasFile('logo')) {
            // 1. Delete old logo if it exists
            if ($lab->logo_path) {
                Storage::disk('public')->delete($lab->logo_path);
            }

            // 2. Store new logo
            $path = $request->file('logo')->store('logos', 'public');
            $validated['logo_path'] = $path;
        }

        // Remove the raw 'logo' file object from array before updating DB
        unset($validated['logo']);

        // Update scalar fields first
        $lab->update($validated);

        // Parse and save presets if provided (comma-separated strings expected)
        if ($request->filled('storage_locations_raw')) {
            $locations = array_values(array_filter(array_map('trim', explode(',', $request->input('storage_locations_raw')))));
            $lab->storage_locations = $locations;
        }

        if ($request->filled('prebuilt_units_raw')) {
            $units = array_values(array_filter(array_map('trim', explode(',', $request->input('prebuilt_units_raw')))));
            $lab->prebuilt_units = $units;
        }

        if ($request->filled('sample_types_raw')) {
            $types = array_values(array_filter(array_map('trim', explode(',', $request->input('sample_types_raw')))));
            $lab->sample_types = $types;
        }

        $lab->save();

        return back()->with('message', 'Laboratory settings updated successfully.');
    }
}
