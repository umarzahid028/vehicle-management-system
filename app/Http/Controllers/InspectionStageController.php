<?php

namespace App\Http\Controllers;

use App\Models\InspectionStage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InspectionStageController extends Controller
{
    /**
     * Display a listing of inspection stages.
     */
    public function index()
    {
        $stages = InspectionStage::orderBy('order')->get();
        return view('inspection.stages.index', compact('stages'));
    }

    /**
     * Show the form for creating a new inspection stage.
     */
    public function create()
    {
        return view('inspection.stages.create');
    }

    /**
     * Store a newly created inspection stage in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:inspection_stages',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Generate slug from name
        $validated['slug'] = Str::slug($validated['name']);
        
        // Set default order if not provided
        if (!isset($validated['order'])) {
            $maxOrder = InspectionStage::max('order') ?? 0;
            $validated['order'] = $maxOrder + 1;
        }

        // Set is_active default
        $validated['is_active'] = $validated['is_active'] ?? true;

        InspectionStage::create($validated);

        return redirect()->route('inspection.stages.index')
            ->with('success', 'Inspection stage created successfully.');
    }

    /**
     * Display the specified inspection stage.
     */
    public function show(InspectionStage $stage)
    {
        $items = $stage->inspectionItems()->orderBy('name')->get();
        return view('inspection.stages.show', compact('stage', 'items'));
    }

    /**
     * Show the form for editing the specified inspection stage.
     */
    public function edit(InspectionStage $stage)
    {
        return view('inspection.stages.edit', compact('stage'));
    }

    /**
     * Update the specified inspection stage in storage.
     */
    public function update(Request $request, InspectionStage $stage)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('inspection_stages')->ignore($stage->id),
            ],
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Generate slug from name if name has changed
        if ($validated['name'] !== $stage->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Set is_active default
        $validated['is_active'] = $validated['is_active'] ?? false;

        $stage->update($validated);

        return redirect()->route('inspection.stages.index')
            ->with('success', 'Inspection stage updated successfully.');
    }

    /**
     * Remove the specified inspection stage from storage.
     */
    public function destroy(InspectionStage $stage)
    {
        // Check if the stage has any associated items or inspections
        if ($stage->inspectionItems()->count() > 0 || $stage->vehicleInspections()->count() > 0) {
            return redirect()->route('inspection.stages.index')
                ->with('error', 'Cannot delete a stage with associated items or inspections.');
        }

        $stage->delete();

        return redirect()->route('inspection.stages.index')
            ->with('success', 'Inspection stage deleted successfully.');
    }

    /**
     * Reorder inspection stages.
     */
    public function reorder(Request $request)
    {
        $validatedData = $request->validate([
            'stages' => 'required|array',
            'stages.*' => 'required|integer|exists:inspection_stages,id',
        ]);

        foreach ($validatedData['stages'] as $order => $stageId) {
            InspectionStage::where('id', $stageId)->update(['order' => $order + 1]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Toggle active status of the inspection stage.
     */
    public function toggleActive(InspectionStage $stage)
    {
        $stage->update(['is_active' => !$stage->is_active]);

        return redirect()->route('inspection.stages.index')
            ->with('success', 'Inspection stage status updated.');
    }
} 