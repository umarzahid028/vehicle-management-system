<?php

use App\Settings\GeneralSettings;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateGeneralSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', config('app.name'));
        $this->migrator->add('general.contact_email', config('mail.from.address', ''));
        $this->migrator->add('general.timezone', config('app.timezone'));
        $this->migrator->add('general.date_format', 'Y-m-d');
        $this->migrator->add('general.time_format', 'H:i');

        $this->migrator->add('general.csv_monitor_path', storage_path('app/imports'));
        $this->migrator->add('general.auto_process_files', false);
        $this->migrator->add('general.archive_processed_files', true);
        $this->migrator->add('general.file_pattern', '*.csv');
    }

    public function down(): void
    {
        $this->migrator->delete('general.site_name');
        $this->migrator->delete('general.contact_email');
        $this->migrator->delete('general.timezone');
        $this->migrator->delete('general.date_format');
        $this->migrator->delete('general.time_format');

        $this->migrator->delete('general.csv_monitor_path');
        $this->migrator->delete('general.auto_process_files');
        $this->migrator->delete('general.archive_processed_files');
        $this->migrator->delete('general.file_pattern');
    }
} 