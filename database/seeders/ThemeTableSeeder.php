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
            'theme_primary_color'        => '#0f766e',
            'theme_primary_hover_color'  => '#115e59',
            'theme_button_text_color'    => '#ffffff',
            'theme_page_background'      => '#f7f7fc',
            'theme_surface_color'        => '#ffffff',
            'theme_header_background'    => '#ffffff',
            'theme_footer_background'    => '#0f172a',
            'theme_heading_color'        => '#1f1f39',
            'theme_body_text_color'      => '#6e7191',
            'theme_border_color'         => '#d9dbe9',
            'theme_item_name_color'       => '#1f1f39',
            'theme_item_description_color' => '#6e7191',
            'theme_item_price_color'      => '#115e59',
            'theme_item_old_price_color'  => '#6e7191',
            'theme_category_color'        => '#6e7191',
            'theme_icon_color'            => '#0f766e',
            'theme_nav_background_color'  => '#ffffff',
            'theme_nav_text_color'        => '#6e7191',
            'theme_nav_active_color'      => '#115e59',
            'theme_nav_icon_color'        => '#6e7191',
            'theme_nav_active_icon_color' => '#115e59',
            'theme_font_family'          => 'Inter, sans-serif',
            'theme_heading_font_family'  => 'Inter, sans-serif',
            'theme_color_mode'           => 'light',
            'theme_border_radius'        => '12px',
        ]);
    }
}
