<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddVehicleImportsPathToGeneralSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.vehicle_imports_path', storage_path('app/vehicle-imports'));
    }

    public function down(): void
    {
        $this->migrator->delete('general.vehicle_imports_path');
    }
}
