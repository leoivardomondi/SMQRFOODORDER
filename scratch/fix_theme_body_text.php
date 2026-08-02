<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Smartisan\Settings\Facades\Settings;

echo "Updating theme settings in database...\n";
Settings::group('theme')->set([
    'theme_primary_color'       => '#c6a15b',
    'theme_primary_hover_color' => '#e2c986',
    'theme_button_text_color'   => '#ffffff',
    'theme_page_background'     => '#080808',
    'theme_surface_color'        => '#111111',
    'theme_header_background'   => '#0b0b0b',
    'theme_footer_background'   => '#050505',
    'theme_heading_color'       => '#ffffff',
    'theme_body_text_color'     => '#e2e8f0', // High contrast silver white!
    'theme_border_color'        => '#332b1e',
]);

echo "Theme body text color successfully updated to #e2e8f0!\n";
