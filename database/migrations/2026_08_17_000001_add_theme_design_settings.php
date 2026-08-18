<?php

use Illuminate\Database\Migrations\Migration;
use Smartisan\Settings\Facades\Settings;

return new class extends Migration
{
    private const DEFAULTS = [
        'theme_font_family'         => 'Inter, sans-serif',
        'theme_heading_font_family' => 'Inter, sans-serif',
        'theme_color_mode'          => 'light',
        'theme_border_radius'       => '12px',
    ];

    public function up(): void
    {
        $theme = Settings::group('theme');

        foreach (self::DEFAULTS as $key => $value) {
            if ($theme->get($key) === null) {
                $theme->set($key, $value);
            }
        }
    }

    public function down(): void
    {
        $theme = Settings::group('theme');

        foreach (array_keys(self::DEFAULTS) as $key) {
            $theme->forget($key);
        }
    }
};
