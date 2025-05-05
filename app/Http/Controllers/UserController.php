<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('view users');
        
        $query = User::query()
            ->with('roles')
            ->withCount('roles');
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('role')) {
            $roleId = $request->input('role');
            $query->whereHas('roles', function ($q) use ($roleId) {
                $q->where('id', $roleId);
            });
        }
        
        $users = $query->latest()->paginate(10)->withQueryString();
        $roles = Role::orderBy('name')->get();
        
        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create users');
        
        $roles = Role::all();
        
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create users');
        
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);
        
        // Double-check if the email already exists to prevent race conditions
        if (User::where('email', $request->input('email'))->exists()) {
            return redirect()->back()->withInput()
                ->with('error', 'A user with this email already exists.');
        }
        
        DB::beginTransaction();
        
        try {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
            ]);
            
            // Assign roles
            if ($request->has('roles')) {
                $roles = Role::whereIn('id', $request->input('roles'))->get();
                $user->assignRole($roles);
            }
            
            DB::commit();
            
            return redirect()->route('admin.users.index')
                ->with('success', 'User created successfully');
                
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            
            // Check specifically for duplicate entry error
            if ($e->errorInfo[1] == 1062) {
                return redirect()->back()->withInput()
                    ->with('error', 'A user with this email already exists.');
            }
            
            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while creating the user: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while creating the user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorize('view users');
        
        $user = User::with('roles')->findOrFail($id);
        
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->authorize('edit users');
        
        $user = User::findOrFail($id);
        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->toArray();
        
        return view('users.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Admin role can't have roles modified for security
        if (!$user->hasRole('admin')) {
            $user->syncRoles($request->input('roles', []));
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deletion of admin users
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Admin users cannot be deleted');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Verify the specified user's email.
     */
    public function verify(User $user)
    {
        $this->authorize('edit users');

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            
            return redirect()->back()
                ->with('success', 'User email verified successfully.');
        }

        return redirect()->back()
            ->with('info', 'User email is already verified.');
    }
}
