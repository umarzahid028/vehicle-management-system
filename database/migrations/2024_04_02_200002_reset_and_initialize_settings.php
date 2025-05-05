<?php

use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

class ResetAndInitializeSettings extends SettingsMigration
{
    public function up(): void
    {
        // First, clear all existing settings
        DB::table('settings')->where('group', 'general')->delete();

        // General Settings
        $this->migrator->add('general.site_name', config('app.name'));
        $this->migrator->add('general.contact_email', config('mail.from.address', ''));
        $this->migrator->add('general.timezone', config('app.timezone'));
        $this->migrator->add('general.date_format', 'Y-m-d');
        $this->migrator->add('general.time_format', 'H:i');

        // CSV Import Settings
        $this->migrator->add('general.csv_monitor_path', storage_path('app/imports'));
        $this->migrator->add('general.auto_process_files', false);
        $this->migrator->add('general.archive_processed_files', true);
        $this->migrator->add('general.file_pattern', '*.csv');
    }

    public function down(): void
    {
        DB::table('settings')->where('group', 'general')->delete();
    }
} 