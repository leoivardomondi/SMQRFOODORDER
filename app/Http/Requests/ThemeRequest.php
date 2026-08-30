<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThemeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $color = ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'];

        return [
            'theme_logo'         => ['nullable', 'file', 'mimes:jpg,jpeg,png'],
            'theme_favicon_logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png'],
            'theme_footer_logo'  => ['nullable', 'file', 'mimes:jpg,jpeg,png'],
            'theme_primary_color'       => $color,
            'theme_primary_hover_color' => $color,
            'theme_button_text_color'   => $color,
            'theme_page_background'     => $color,
            'theme_surface_color'        => $color,
            'theme_header_background'   => $color,
            'theme_footer_background'   => $color,
            'theme_heading_color'       => $color,
            'theme_body_text_color'     => $color,
            'theme_border_color'        => $color,
            'theme_muted_surface_color' => $color,
            'theme_input_background_color' => $color,
            'theme_muted_text_color'    => $color,
            'theme_modal_overlay_color' => ['nullable', 'regex:/^rgba?\([^)]+\)$/'],
            'theme_item_name_color'    => $color,
            'theme_item_description_color' => $color,
            'theme_item_price_color'   => $color,
            'theme_item_old_price_color' => $color,
            'theme_category_color'     => $color,
            'theme_icon_color'         => $color,
            'theme_offer_title_color'  => $color,
            'theme_offer_description_color' => $color,
            'theme_nav_background_color' => $color,
            'theme_nav_text_color'       => $color,
            'theme_nav_active_color'     => $color,
            'theme_nav_icon_color'        => $color,
            'theme_nav_active_icon_color' => $color,
            'theme_font_family'         => ['nullable', 'string', 'max:120'],
            'theme_heading_font_family' => ['nullable', 'string', 'max:120'],
            'theme_color_mode'          => ['nullable', 'in:light,dark'],
            'theme_border_radius'       => ['nullable', 'in:0px,6px,12px,18px,24px'],
        ];
    }
}
