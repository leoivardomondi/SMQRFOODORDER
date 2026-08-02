<?php

namespace App\Http\Resources;


use App\Models\ThemeSetting;
use Illuminate\Http\Resources\Json\JsonResource;

class ThemeResource extends JsonResource
{

    public array $info;

    public function __construct($info)
    {
        parent::__construct($info);
        $this->info = $info;
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) : array
    {
        return [
            "theme_logo"         => $this->themeImage('theme_logo')->logo,
            "theme_favicon_logo" => $this->themeImage('theme_favicon_logo')->faviconLogo,
            "theme_footer_logo"  => $this->themeImage('theme_footer_logo')->footerLogo,
            "theme_primary_color"       => $this->info['theme_primary_color'] ?? '#c6a15b',
            "theme_primary_hover_color" => $this->info['theme_primary_hover_color'] ?? '#e2c986',
            "theme_button_text_color"   => $this->info['theme_button_text_color'] ?? '#080808',
            "theme_page_background"     => $this->info['theme_page_background'] ?? '#080808',
            "theme_surface_color"        => $this->info['theme_surface_color'] ?? '#111111',
            "theme_header_background"   => $this->info['theme_header_background'] ?? '#0b0b0b',
            "theme_footer_background"   => $this->info['theme_footer_background'] ?? '#050505',
            "theme_heading_color"       => $this->info['theme_heading_color'] ?? '#ffffff',
            "theme_body_text_color"     => $this->info['theme_body_text_color'] ?? '#a8a8ad',
            "theme_border_color"        => $this->info['theme_border_color'] ?? '#332b1e',
        ];
    }

    public function themeImage($key)
    {
        return ThemeSetting::where(['key' => $key])->first();
    }
}
