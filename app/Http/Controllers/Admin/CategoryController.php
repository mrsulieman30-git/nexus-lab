<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; // Import Base Controller
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Store a new category.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50'
        ]);

        Category::create([
            'name' => $request->name,
            'laboratory_id' => Auth::user()->laboratory_id
        ]);

        return back()->with('message', 'Category added successfully.');
    }

    /**
     * Update a category.
     */
    public function update(Request $request, Category $category)
    {
        // Security Check: Ensure category belongs to user's lab
        if ($category->laboratory_id !== Auth::user()->laboratory_id) {
            abort(403);
        }

        $request->validate([
            'color_hex' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/'
        ]);

        $category->update($request->only(['color_hex']));

        return back()->with('message', 'Category updated successfully.');
    }
}
