<?php

use Illuminate\Database\Migrations\Migration;
use Smartisan\Settings\Facades\Settings;

return new class extends Migration
{
    private const DEFAULTS = [
        'theme_nav_icon_color' => '#6e7191',
        'theme_nav_active_icon_color' => '#115e59',
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
