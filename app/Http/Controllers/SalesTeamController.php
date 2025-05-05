<?php

namespace App\Http\Controllers;

use App\Models\SalesTeam;
use App\Models\User;
use App\Http\Requests\SalesTeamRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Notifications\LoginCredentials;
use Illuminate\Support\Facades\DB;

class SalesTeamController extends Controller
{
    /**
     * Create a new controller instance.
     */


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $salesTeam = SalesTeam::with('manager')
            ->latest()
            ->paginate(10);

        return view('sales-team.index', compact('salesTeam'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $managers = User::role(['Admin', 'Sales Manager', 'Recon Manager'])->get();
        return view('sales-team.create', compact('managers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SalesTeamRequest $request)
    {
        $data = $request->validated();
        
        // Check if a user with this email already exists
        if (User::where('email', $data['email'])->exists()) {
            return redirect()->back()->withInput()
                ->with('error', 'A user with this email already exists.');
        }
        
        DB::beginTransaction();
        
        try {
            // Handle photo upload
            if ($request->hasFile('photo')) {
                $data['photo_path'] = $request->file('photo')->store('sales-team-photos', 'public');
            }

            // Handle password - if not provided, generate one
            if (empty($data['password'])) {
                $plainPassword = Str::random(10);
                session(['generated_password' => $plainPassword]);
                $data['password'] = $plainPassword;
            }

            // Create user account first
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']), // Will be hashed by User model
                'phone' => $data['phone'] ?? null,
            ]);

            // Assign sales team role
            $user->assignRole('Sales Team');

            // Create the sales team member (without password)
            $salesTeam = SalesTeam::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'position' => $data['position'],
                'bio' => $data['bio'] ?? null,
                'photo_path' => $data['photo_path'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'manager_id' => $data['manager_id'] ?? null,
            ]);
            
            DB::commit();

            // Send login credentials notification if password was generated
            if (session()->has('generated_password')) {
                $user->notify(new LoginCredentials(session('generated_password'), 'Sales Team'));
                session()->forget('generated_password');
            }
            
            return redirect()->route('sales-team.index')
                ->with('success', 'Team member added successfully.');
                
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            
            // Delete uploaded photo if exists
            if (isset($data['photo_path'])) {
                Storage::disk('public')->delete($data['photo_path']);
            }
            
            // Check specifically for duplicate entry error
            if ($e->errorInfo[1] == 1062) {
                return redirect()->back()->withInput()
                    ->with('error', 'A user with this email already exists.');
            }
            
            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while creating the team member: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded photo if exists
            if (isset($data['photo_path'])) {
                Storage::disk('public')->delete($data['photo_path']);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the team member: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SalesTeam $salesTeam)
    {
        return view('sales-team.show', compact('salesTeam'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalesTeam $salesTeam)
    {
        $managers = User::role(['Admin', 'Sales Manager', 'Recon Manager'])->get();
        return view('sales-team.edit', compact('salesTeam', 'managers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SalesTeamRequest $request, SalesTeam $salesTeam)
    {
        $data = $request->validated();
        
        DB::beginTransaction();
        
        try {
            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($salesTeam->photo_path) {
                    Storage::disk('public')->delete($salesTeam->photo_path);
                }
                $data['photo_path'] = $request->file('photo')->store('sales-team-photos', 'public');
            }

            // First update the sales team record
            $salesTeam->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? $salesTeam->phone,
                'position' => $data['position'],
                'bio' => $data['bio'] ?? $salesTeam->bio,
                'photo_path' => $data['photo_path'] ?? $salesTeam->photo_path,
                'is_active' => $data['is_active'] ?? $salesTeam->is_active,
                'manager_id' => $data['manager_id'] ?? $salesTeam->manager_id,
            ]);
            
            // Find and update the corresponding user
            $user = User::where('email', $salesTeam->getOriginal('email'))->first();
            
            if ($user) {
                $userUpdateData = [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? $user->phone,
                ];
                
                // Only update password if provided
                if (!empty($data['password'])) {
                    $userUpdateData['password'] = Hash::make($data['password']);
                }
                
                $user->update($userUpdateData);
            }
            
            DB::commit();
            
            return redirect()->route('sales-team.index')
                ->with('success', 'Team member updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded photo if exists and different from original
            if (isset($data['photo_path']) && $data['photo_path'] !== $salesTeam->getOriginal('photo_path')) {
                Storage::disk('public')->delete($data['photo_path']);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the team member: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesTeam $salesTeam)
    {
        try {
            // Delete photo if exists
            if ($salesTeam->photo_path) {
                Storage::disk('public')->delete($salesTeam->photo_path);
            }

            $salesTeam->delete();
            
            return redirect()->route('sales-team.index')
                ->with('success', 'Team member deleted successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the team member: ' . $e->getMessage());
        }
    }

    /**
     * Toggle the active status of the specified resource.
     */
    public function toggleActive(SalesTeamRequest $request, SalesTeam $salesTeam)
    {
        $salesTeam->update([
            'is_active' => !$salesTeam->is_active
        ]);

        return redirect()->route('sales-team.index')
            ->with('success', 'Team member status updated successfully.');
    }

    /**
     * Test the sales team creation flow.
     */
    public function testCreateFlow()
    {
        DB::beginTransaction();
        
        try {
            $testData = [
                'name' => 'Test Sales Member',
                'email' => 'test.sales@example.com',
                'password' => 'password123',
                'phone' => '1234567890',
                'position' => 'Sales Representative',
                'bio' => 'Test bio information',
                'is_active' => true,
            ];

            // Check if test user already exists and delete if it does
            if ($existingUser = User::where('email', $testData['email'])->first()) {
                $existingUser->delete();
            }
            if ($existingSalesTeam = SalesTeam::where('email', $testData['email'])->first()) {
                $existingSalesTeam->delete();
            }

            // Create user account first
            $user = User::create([
                'name' => $testData['name'],
                'email' => $testData['email'],
                'password' => $testData['password'],
                'phone' => $testData['phone'],
            ]);

            // Assign sales team role
            $user->assignRole('Sales Team');

            // Create the sales team member
            $salesTeam = SalesTeam::create([
                ...$testData,
                'password' => $user->password, // Use the already hashed password from user
            ]);

            $result = [
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'has_role' => $user->hasRole('Sales Team'),
                ],
                'sales_team' => [
                    'id' => $salesTeam->id,
                    'name' => $salesTeam->name,
                    'email' => $salesTeam->email,
                    'position' => $salesTeam->position,
                ],
            ];

            DB::rollBack(); // Roll back the test data

            return response()->json($result);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
