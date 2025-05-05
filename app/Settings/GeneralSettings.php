<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    // General Settings
    public string $site_name;
    public string $contact_email;
    public string $timezone;
    public string $date_format;
    public string $time_format;

    // CSV Import Settings
    public string $csv_monitor_path;
    public bool $auto_process_files;
    public bool $archive_processed_files;
    public string $file_pattern;

    // Vehicle Import Settings
    public string $vehicle_imports_path;

    public static function group(): string
    {
        return 'general';
    }

    public static function default(): array
    {
        return [
            // General Settings
            'site_name' => config('app.name'),
            'contact_email' => config('mail.from.address', ''),
            'timezone' => config('app.timezone'),
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',

            // CSV Import Settings
            'csv_monitor_path' => storage_path('app/imports'),
            'auto_process_files' => false,
            'archive_processed_files' => true,
            'file_pattern' => '*.csv',

            // Vehicle Import Settings
            'vehicle_imports_path' => storage_path('app/vehicle-imports'),
        ];
    }
} 