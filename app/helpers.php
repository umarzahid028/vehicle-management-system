<?php

use App\Settings\GeneralSettings;

if (!function_exists('setting')) {
    function setting($key = null, $default = null)
    {
        $settings = app(GeneralSettings::class);

        if (is_null($key)) {
            return $settings;
        }

        if (is_array($key)) {
            foreach ($key as $k => $value) {
                $settings->$k = $value;
            }
            $settings->save();
            return true;
        }

        return $settings->$key ?? $default;
    }
} 