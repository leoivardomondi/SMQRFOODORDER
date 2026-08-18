<?php

use Illuminate\Database\Migrations\Migration;
use Smartisan\Settings\Facades\Settings;

return new class extends Migration
{
    private const DEFAULTS = [
        'theme_item_name_color' => '#1f1f39',
        'theme_item_description_color' => '#6e7191',
        'theme_item_price_color' => '#115e59',
        'theme_item_old_price_color' => '#6e7191',
        'theme_category_color' => '#6e7191',
        'theme_icon_color' => '#0f766e',
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
