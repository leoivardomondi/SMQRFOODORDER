<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;
use Smartisan\Settings\Facades\Settings;

class ThemeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Settings::group('theme')->set([
            'theme_logo'         => "",
            'theme_favicon_logo' => "",
            'theme_footer_logo'  => "",
            'theme_primary_color'        => '#c6a15b',
            'theme_primary_hover_color'  => '#e2c986',
            'theme_button_text_color'    => '#080808',
            'theme_page_background'      => '#080808',
            'theme_surface_color'         => '#111111',
            'theme_header_background'    => '#0b0b0b',
            'theme_footer_background'    => '#1c1712',
            'theme_heading_color'        => '#ffffff',
            'theme_body_text_color'      => '#a8a8ad',
            'theme_border_color'         => '#332b1e',
        ]);
    }
}
