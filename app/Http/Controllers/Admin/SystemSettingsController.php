<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SystemSettingsController extends Controller
{
    public function index(GeneralSettings $settings)
    {
        return view('admin.system-settings.index', [
            'settings' => $settings
        ]);
    }

    public function update(Request $request, GeneralSettings $settings)
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email'],
            'timezone' => ['required', 'string'],
            'date_format' => ['required', 'string'],
            'time_format' => ['required', 'string'],
            'vehicle_imports_path' => ['required', 'string'],
        ]);

        // Ensure the vehicle imports directory exists and is writable
        if (!File::exists($validated['vehicle_imports_path'])) {
            if (!File::makeDirectory($validated['vehicle_imports_path'], 0755, true)) {
                return back()->with('error', 'Unable to create the vehicle imports directory.');
            }
        }

        if (!File::isWritable($validated['vehicle_imports_path'])) {
            return back()->with('error', 'The vehicle imports directory is not writable.');
        }

        foreach ($validated as $key => $value) {
            $settings->$key = $value;
        }
        
        $settings->save();

        return back()->with('success', 'System settings updated successfully.');
    }
} 