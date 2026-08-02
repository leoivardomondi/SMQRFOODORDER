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
        ];
    }
}
