<?php

namespace App\Http\Controllers;

use App\Models\InspectionItem;
use App\Models\InspectionStage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InspectionItemController extends Controller
{
    /**
     * Display a listing of the inspection items.
     */
    public function index(Request $request)
    {
        $stageId = $request->query('stage_id');
        $query = InspectionItem::with('inspectionStage');
        
        if ($stageId) {
            $query->where('inspection_stage_id', $stageId);
        }
        
        $items = $query->orderBy('name')->get();
        $stages = InspectionStage::orderBy('order')->pluck('name', 'id');
        
        return view('inspection.items.index', compact('items', 'stages', 'stageId'));
    }

    /**
     * Show the form for creating a new inspection item.
     */
    public function create(Request $request)
    {
        $stageId = $request->query('stage_id');
        $stages = InspectionStage::where('is_active', true)
            ->orderBy('order')
            ->pluck('name', 'id');
            
        return view('inspection.items.create', compact('stages', 'stageId'));
    }

    /**
     * Store a newly created inspection item in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'inspection_stage_id' => 'required|exists:inspection_stages,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('inspection_items')
                    ->where('inspection_stage_id', $request->inspection_stage_id),
            ],
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'order' => 'nullable|integer|min:1',
            'vendor_required' => 'nullable|boolean',
            'cost_tracking' => 'nullable|boolean',
        ]);

        // Generate slug from name
        $validated['slug'] = Str::slug($validated['name']);
        
        // Set is_active default
        $validated['is_active'] = $validated['is_active'] ?? true;
        
        // Set vendor_required and cost_tracking defaults
        $validated['vendor_required'] = $validated['vendor_required'] ?? false;
        $validated['cost_tracking'] = true; // Always enable cost tracking
        
        // Set order default (highest order in its stage + 1)
        if (empty($validated['order'])) {
            $maxOrder = InspectionItem::where('inspection_stage_id', $validated['inspection_stage_id'])
                ->max('order') ?? 0;
            $validated['order'] = $maxOrder + 1;
        }

        InspectionItem::create($validated);

        return redirect()->route('inspection.items.index', ['stage_id' => $validated['inspection_stage_id']])
            ->with('success', 'Inspection item created successfully.');
    }

    /**
     * Display the specified inspection item.
     */
    public function show(InspectionItem $item)
    {
        $item->load('inspectionStage');
        return view('inspection.items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified inspection item.
     */
    public function edit(InspectionItem $item)
    {
        $stages = InspectionStage::where('is_active', true)
            ->orderBy('order')
            ->pluck('name', 'id');
        
        return view('inspection.items.edit', compact('item', 'stages'));
    }

    /**
     * Update the specified inspection item in storage.
     */
    public function update(Request $request, InspectionItem $item)
    {
        $validated = $request->validate([
            'inspection_stage_id' => 'required|exists:inspection_stages,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('inspection_items')
                    ->where('inspection_stage_id', $request->inspection_stage_id)
                    ->ignore($item->id),
            ],
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'order' => 'nullable|integer|min:1',
            'vendor_required' => 'nullable|boolean',
            'cost_tracking' => 'nullable|boolean',
        ]);

        // Generate slug from name if changed
        if ($validated['name'] !== $item->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        // Set is_active default
        $validated['is_active'] = $validated['is_active'] ?? false;
        
        // Set vendor_required and cost_tracking defaults
        $validated['vendor_required'] = $validated['vendor_required'] ?? false;
        $validated['cost_tracking'] = $validated['cost_tracking'] ?? false;
        
        // Set order default if not provided
        if (empty($validated['order'])) {
            $validated['order'] = $item->order ?? 1;
        }

        $item->update($validated);

        return redirect()->route('inspection.items.index', ['stage_id' => $validated['inspection_stage_id']])
            ->with('success', 'Inspection item updated successfully.');
    }

    /**
     * Remove the specified inspection item from storage.
     */
    public function destroy(InspectionItem $item)
    {
        // Check if the item has any associated results
        if ($item->itemResults()->count() > 0) {
            return redirect()->route('inspection.items.index', ['stage_id' => $item->inspection_stage_id])
                ->with('error', 'Cannot delete an item with associated inspection results.');
        }

        $stageId = $item->inspection_stage_id;
        $item->delete();

        return redirect()->route('inspection.items.index', ['stage_id' => $stageId])
            ->with('success', 'Inspection item deleted successfully.');
    }

    /**
     * Toggle active status of the inspection item.
     */
    public function toggleActive(InspectionItem $item)
    {
        $item->update(['is_active' => !$item->is_active]);

        return redirect()->route('inspection.items.index', ['stage_id' => $item->inspection_stage_id])
            ->with('success', 'Inspection item status updated.');
    }
} 