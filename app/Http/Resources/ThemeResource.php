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
            "theme_primary_color"       => $this->info['theme_primary_color'] ?? '#0f766e',
            "theme_primary_hover_color" => $this->info['theme_primary_hover_color'] ?? '#115e59',
            "theme_button_text_color"   => $this->info['theme_button_text_color'] ?? '#ffffff',
            "theme_page_background"     => $this->info['theme_page_background'] ?? '#f7f7fc',
            "theme_surface_color"       => $this->info['theme_surface_color'] ?? '#ffffff',
            "theme_header_background"   => $this->info['theme_header_background'] ?? '#ffffff',
            "theme_footer_background"   => $this->info['theme_footer_background'] ?? '#0f172a',
            "theme_heading_color"       => $this->info['theme_heading_color'] ?? '#1f1f39',
            "theme_body_text_color"     => $this->info['theme_body_text_color'] ?? '#6e7191',
            "theme_border_color"        => $this->info['theme_border_color'] ?? '#d9dbe9',
            "theme_font_family"         => $this->info['theme_font_family'] ?? 'Inter, sans-serif',
            "theme_heading_font_family" => $this->info['theme_heading_font_family'] ?? 'Inter, sans-serif',
            "theme_color_mode"          => $this->info['theme_color_mode'] ?? 'light',
            "theme_border_radius"       => $this->info['theme_border_radius'] ?? '12px',
        ];
    }

    public function themeImage($key)
    {
        return ThemeSetting::where(['key' => $key])->first();
    }
}
