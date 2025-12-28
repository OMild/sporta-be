<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class OwnerController extends Controller
{
    /**
     * Display a listing of venue owners.
     */
    public function index()
    {
        $owners = User::where('role', User::ROLE_VENUE_OWNER)
            ->withCount('venues')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.owners.index', compact('owners'));
    }

    /**
     * Show the form for creating a new venue owner.
     */
    public function create()
    {
        return view('admin.owners.create');
    }

    /**
     * Store a newly created venue owner in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $owner = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_VENUE_OWNER,
        ]);

        return redirect()->route('admin.owners.index')
            ->with('success', 'Venue owner created successfully.');
    }

    /**
     * Display the specified venue owner.
     */
    public function show(User $owner)
    {
        // Ensure the user is actually a venue owner
        if (!$owner->isVenueOwner()) {
            abort(404);
        }

        $owner->load(['venues' => function ($query) {
            $query->withCount('bookings');
        }]);

        return view('admin.owners.show', compact('owner'));
    }

    /**
     * Show the form for editing the specified venue owner.
     */
    public function edit(User $owner)
    {
        // Ensure the user is actually a venue owner
        if (!$owner->isVenueOwner()) {
            abort(404);
        }

        return view('admin.owners.edit', compact('owner'));
    }

    /**
     * Update the specified venue owner in storage.
     */
    public function update(Request $request, User $owner)
    {
        // Ensure the user is actually a venue owner
        if (!$owner->isVenueOwner()) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($owner->id)],
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ];

        // Only update password if provided
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $owner->update($updateData);

        return redirect()->route('admin.owners.index')
            ->with('success', 'Venue owner updated successfully.');
    }

    /**
     * Remove the specified venue owner from storage.
     */
    public function destroy(User $owner)
    {
        // Ensure the user is actually a venue owner
        if (!$owner->isVenueOwner()) {
            abort(404);
        }

        // Check if owner has venues - prevent deletion if they do
        if ($owner->venues()->count() > 0) {
            return redirect()->route('admin.owners.index')
                ->with('error', 'Cannot delete venue owner who has registered venues.');
        }

        $owner->delete();

        return redirect()->route('admin.owners.index')
            ->with('success', 'Venue owner deleted successfully.');
    }
}