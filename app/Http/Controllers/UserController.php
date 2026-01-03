<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function __construct()
    {
        // Check admin access in each method instead of using middleware
    }

    /**
     * Check if user is admin, abort if not
     */
    private function checkAdmin()
    {
        if (!Auth::check() || !Auth::user()->hasRole('ADMIN')) {
            abort(403, 'Unauthorized action. Admin access required.');
        }
    }

    public function index(Request $request)
    {
        $this->checkAdmin();
        
        $query = User::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('department', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->checkAdmin();
        
        return view('users.create');
    }

    public function store(Request $request)
    {
        $this->checkAdmin();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|in:END_USER,PROCUREMENT_OFFICER,BAC_SECRETARIAT,BAC_CHAIR,BAC_MEMBER,CANVASSER,SUPPLIER,ADMIN',
            'department' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'department' => $validated['department'] ?? null,
            ]);

            activity_log('CREATED', 'USER', $user->id, [
                'email' => $user->email,
                'role' => $user->role,
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('users.show', $user)
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()]);
        }
    }

    public function show(User $user)
    {
        $this->checkAdmin();
        
        // Load user activity logs - activities performed by user OR activities on this user
        $activityLogs = \App\Models\ActivityLog::where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere(function($q) use ($user) {
                          $q->where('entity_type', 'USER')
                            ->where('entity_id', $user->id);
                      });
            })
            ->with('user')
            ->latest()
            ->limit(20)
            ->get();

        return view('users.show', compact('user', 'activityLogs'));
    }

    public function edit(User $user)
    {
        $this->checkAdmin();
        
        // Prevent editing yourself (use profile page instead)
        if ($user->id === Auth::id()) {
            return redirect()->route('profile.edit')
                ->with('info', 'Please use the Profile page to edit your own information.');
        }

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->checkAdmin();
        
        // Prevent editing yourself (use profile page instead)
        if ($user->id === Auth::id()) {
            return redirect()->route('profile.edit')
                ->with('info', 'Please use the Profile page to edit your own information.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:END_USER,PROCUREMENT_OFFICER,BAC_SECRETARIAT,BAC_CHAIR,BAC_MEMBER,CANVASSER,SUPPLIER,ADMIN',
            'department' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $oldData = $user->toArray();

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'department' => $validated['department'] ?? null,
            ]);

            activity_log('UPDATED', 'USER', $user->id, [
                'email' => $user->email,
                'role' => $user->role,
                'updated_by' => Auth::id(),
                'changes' => array_diff_assoc($user->toArray(), $oldData),
            ]);

            DB::commit();

            return redirect()->route('users.show', $user)
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to update user: ' . $e->getMessage()]);
        }
    }

    public function destroy(User $user)
    {
        $this->checkAdmin();
        
        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        DB::beginTransaction();
        try {
            $userId = $user->id;
            $userEmail = $user->email;

            activity_log('DELETED', 'USER', $userId, [
                'email' => $userEmail,
                'deleted_by' => Auth::id(),
            ]);

            $user->delete();

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete user: ' . $e->getMessage()]);
        }
    }

    public function resetPassword(Request $request, User $user)
    {
        $this->checkAdmin();
        
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        DB::beginTransaction();
        try {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            activity_log('PASSWORD_RESET', 'USER', $user->id, [
                'email' => $user->email,
                'reset_by' => Auth::id(),
            ]);

            DB::commit();

            return back()->with('success', 'Password reset successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to reset password: ' . $e->getMessage()]);
        }
    }
}

