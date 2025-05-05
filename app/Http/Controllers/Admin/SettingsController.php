<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Display the system settings page.
     */
    public function index()
    {
        $settings = Cache::get('system_settings', [
            'csv_monitor_path' => storage_path('app/imports'),
            'auto_process_files' => true,
            'archive_processed_files' => true,
            'file_pattern' => '*.csv'
        ]);

        return view('settings.index', compact('settings'));
    }

    /**
     * Update the system settings.
     */
    public function update(Request $request)
    {
        $this->authorize('edit users');
        
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'notification_email' => 'required|email|max:255',
            'enable_notifications' => 'boolean',
            'enable_auto_assignments' => 'boolean',
        ]);

        Cache::forever('system_settings', $validated);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    /**
     * Update the CSV import settings.
     */
    public function updateCsvSettings(Request $request)
    {
        $validated = $request->validate([
            'csv_monitor_path' => ['required', 'string'],
            'file_pattern' => ['required', 'string'],
            'auto_process_files' => ['boolean'],
            'archive_processed_files' => ['boolean'],
        ]);

        // Ensure the directory exists and is writable
        if (!file_exists($validated['csv_monitor_path'])) {
            if (!mkdir($validated['csv_monitor_path'], 0755, true)) {
                return back()->with('error', 'Unable to create the specified directory.');
            }
        }

        if (!is_writable($validated['csv_monitor_path'])) {
            return back()->with('error', 'The specified directory is not writable.');
        }

        // Get existing settings and merge with new ones
        $settings = Cache::get('system_settings', []);
        $settings = array_merge($settings, [
            'csv_monitor_path' => $validated['csv_monitor_path'],
            'file_pattern' => $validated['file_pattern'],
            'auto_process_files' => $request->boolean('auto_process_files'),
            'archive_processed_files' => $request->boolean('archive_processed_files'),
        ]);

        // Save settings
        Cache::forever('system_settings', $settings);

        return back()->with('success', 'CSV import settings updated successfully.');
    }
}
