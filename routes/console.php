<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
  

Schedule::command('import:vehicles-csv --directory=/home/u529019370/domains/recon.trevinosauto.com/inv_up_dc --archive')->everyMinute()->appendOutputTo(storage_path('logs/import-vehicles-csv.log'));