<?php

use Illuminate\Database\Migrations\Migration;
use Smartisan\Settings\Facades\Settings;

return new class extends Migration
{
    private const DEFAULTS = [
        'theme_primary_color'       => '#c6a15b',
        'theme_primary_hover_color' => '#e2c986',
        'theme_button_text_color'   => '#080808',
        'theme_page_background'     => '#080808',
        'theme_surface_color'       => '#111111',
        'theme_header_background'   => '#0b0b0b',
        'theme_footer_background'   => '#050505',
        'theme_heading_color'       => '#ffffff',
        'theme_body_text_color'     => '#a8a8ad',
        'theme_border_color'        => '#332b1e',
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
