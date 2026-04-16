<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $query = User::with('creator')->latest();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Shop owners can only see sellers
        if (auth()->user()->isShopOwner()) {
            $query->where('role', 'seller');
        }

        $users = $query->paginate(15)->withQueryString();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = $this->getAllowedRoles();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => ['required', Password::min(8)->mixedCase()->numbers()],
            'role'     => 'required|in:' . implode(',', $this->getAllowedRoles()),
            'phone'    => 'nullable|string|max:20',
        ];

        $validated = $request->validate($rules);
        $validated['password']   = Hash::make($validated['password']);
        $validated['created_by'] = auth()->id();

        $user = User::create($validated);

        return redirect()->route('users.index')
            ->with('success', "User '{$user->name}' created successfully.");
    }

    public function show(User $user)
    {
        $user->load(['sales.items', 'creator', 'createdUsers']);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorizeUserEdit($user);
        $roles = $this->getAllowedRoles();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeUserEdit($user);

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'role'      => 'required|in:' . implode(',', $this->getAllowedRoles()),
            'phone'     => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'password'  => ['nullable', Password::min(8)->mixedCase()->numbers()],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', "User '{$user->name}' updated successfully.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('users.index')
            ->with('success', "User '{$user->name}' deleted successfully.");
    }

    private function getAllowedRoles(): array
    {
        $authUser = auth()->user();
        if ($authUser->isSuperAdmin()) {
            return ['super_admin', 'shop_owner', 'seller'];
        }
        // Shop owner can only create sellers
        return ['seller'];
    }

    private function authorizeUserEdit(User $user): void
    {
        $authUser = auth()->user();
        // Shop owners can only edit sellers
        if ($authUser->isShopOwner() && !$user->isSeller()) {
            abort(403, 'You can only manage sellers.');
        }
    }
}
