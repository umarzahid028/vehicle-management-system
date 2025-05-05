<?php

namespace App\Http\Controllers;

use App\Models\Transporter;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Notifications\LoginCredentials;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TransporterController extends Controller
{
    /**
     * Display a listing of the transporters.
     */
    public function index(Request $request): View
    {
        $query = Transporter::query()->latest();

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
        }

        // Active status filter
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        }

        $transporters = $query->paginate(10);
        return view('transporters.index', compact('transporters'));
    }

    /**
     * Show the form for creating a new transporter.
     */
    public function create(): View
    {
        return view('transporters.create');
    }

    /**
     * Store a newly created transporter in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Double-check if a user with this email already exists
        if (User::where('email', $validated['email'])->exists()) {
            return redirect()->back()->withInput()
                ->with('error', 'A user with this email already exists.');
        }

        // Set is_active to true if not provided
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }
        
        // Store the password before removing it from the transporter data
        $password = $validated['password'];
        
        try {
            DB::beginTransaction();
            
            // Create the transporter - the observer will create the user automatically
            $transporter = Transporter::create($validated);
            
            // Update the user's password since they provided a custom one
            $user = User::where('email', $validated['email'])->first();
            if ($user) {
                $user->password = Hash::make($password);
                $user->save();
                
                // No need to send notification since user set their own password
                // We could send a custom "welcome" notification here if needed
            }
            
            DB::commit();
            
            return redirect()->route('transporters.index')
                ->with('success', 'Transporter created successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            
            // Check specifically for duplicate entry error
            if ($e->errorInfo[1] == 1062) {
                return redirect()->back()->withInput()
                    ->with('error', 'A user with this email already exists.');
            }
            
            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while creating the transporter: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while creating the transporter: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified transporter.
     */
    public function show(Transporter $transporter): View
    {
        $transporter->load('transports.vehicle');
        return view('transporters.show', compact('transporter'));
    }

    /**
     * Show the form for editing the specified transporter.
     */
    public function edit(Transporter $transporter): View
    {
        return view('transporters.edit', compact('transporter'));
    }

    /**
     * Update the specified transporter in storage.
     */
    public function update(Request $request, Transporter $transporter): RedirectResponse
    {
        // Check if email is being changed
        $emailChanged = $request->email !== $transporter->email;

        $rules = [
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ];

        // Only add email validation rules if email is being changed
        if ($emailChanged) {
            $rules['email'] = [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email,' . $transporter->user?->id,
                'unique:transporters,email,' . $transporter->id,
            ];
        } else {
            $rules['email'] = 'required|string|email|max:255';
        }

        $validated = $request->validate($rules);

        // Handle the checkbox for is_active
        $validated['is_active'] = $request->has('is_active');

        // Remove password from transporter data if it exists
        if (isset($validated['password'])) {
            $password = $validated['password'];
            unset($validated['password']);
        }

        // Update transporter
        $transporter->update($validated);

        // Find existing user by email or the one associated with transporter
        $user = $transporter->user ?? User::where('email', $validated['email'])->first();

        if ($user) {
            // Update existing user
            $userData = [
                'name' => $validated['contact_person'] ?? $validated['name'],
                'email' => $validated['email'],
                'transporter_id' => $transporter->id,
            ];

            // Only update password if provided
            if (isset($password)) {
                $userData['password'] = Hash::make($password);
            }

            $user->update($userData);

            // Ensure user has Transporter role
            if (!$user->hasRole('Transporter')) {
                $user->assignRole('Transporter');
            }
        } else {
            // Create new user only if one doesn't exist
            $user = User::create([
                'name' => $validated['contact_person'] ?? $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($password ?? Str::random(10)),
                'transporter_id' => $transporter->id,
            ]);
            
            // Assign transporter role
            $user->assignRole('Transporter');
        }

        return redirect()->route('transporters.index')
                         ->with('success', 'Transporter updated successfully.');
    }

    /**
     * Remove the specified transporter from storage.
     */
    public function destroy(Transporter $transporter): RedirectResponse
    {
        // Check if the transporter has any transports
        if ($transporter->transports()->exists()) {
            return redirect()->route('transporters.index')
                             ->with('error', 'Cannot delete transporter with associated transports.');
        }

        $transporter->delete();
        return redirect()->route('transporters.index')
                         ->with('success', 'Transporter removed successfully.');
    }
} 