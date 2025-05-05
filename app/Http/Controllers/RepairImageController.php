<?php

namespace App\Http\Controllers;

use App\Models\RepairImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RepairImageController extends Controller
{
    /**
     * Remove the specified repair image.
     */
    public function destroy(RepairImage $repairImage)
    {
        // Check if user has permission to delete this image
        if (!auth()->user()->can('delete', $repairImage)) {
            return redirect()->back()->with('error', 'You do not have permission to delete this image.');
        }

        // Delete the image file from storage
        if (Storage::disk('public')->exists($repairImage->image_path)) {
            Storage::disk('public')->delete($repairImage->image_path);
        }

        // Delete the database record
        $repairImage->delete();

        return redirect()->back()->with('success', 'Image deleted successfully.');
    }
} 