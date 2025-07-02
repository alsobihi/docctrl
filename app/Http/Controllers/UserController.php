<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('plant')->latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $plants = Plant::orderBy('name')->get();
        return view('users.create', compact('plants'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', Rule::in(['admin', 'manager', 'viewer'])],
            'plant_id' => ['nullable', 'required_if:role,manager', 'exists:plants,id'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'plant_id' => $request->role === 'manager' ? $request->plant_id : null,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        $plants = Plant::orderBy('name')->get();
        return view('users.edit', compact('user', 'plants'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', Rule::in(['admin', 'manager', 'viewer'])],
            'plant_id' => ['nullable', 'required_if:role,manager', 'exists:plants,id'],
        ]);

        $userData = $request->only('name', 'email', 'role');
        $userData['plant_id'] = $request->role === 'manager' ? $request->plant_id : null;

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        // Prevent a user from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete yourself.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
