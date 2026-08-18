<?php

namespace App\Http\Resources;


use App\Models\ThemeSetting;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
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
    public function toArray($request): array
    {
        return [
            'company_name'                         => $this->info['company_name'],
            'company_email'                        => $this->info['company_email'],
            'company_phone'                        => $this->info['company_phone'],
            'company_address'                      => $this->info['company_address'],
            'company_country_code'                 => $this->info['company_country_code'],
            'site_default_branch'                  => $this->info['site_default_branch'],
            'site_default_language'                => $this->info['site_default_language'],
            'site_android_app_link'                => $this->info['site_android_app_link'],
            'site_ios_app_link'                    => $this->info['site_ios_app_link'],
            'site_copyright'                       => $this->info['site_copyright'],
            'site_currency_position'               => $this->info['site_currency_position'],
            'site_digit_after_decimal_point'       => $this->info['site_digit_after_decimal_point'],
            'site_default_currency_symbol'         => $this->info['site_default_currency_symbol'],
            'site_phone_verification'              => $this->info['site_phone_verification'],
            'site_language_switch'                 => $this->info['site_language_switch'],
            'site_online_payment_gateway'          => $this->info['site_online_payment_gateway'],
            "site_guest_login"                     => $this->info['site_guest_login'],
            'theme_logo'                           => $this->themeImage('theme_logo')->logo,
            'theme_footer_logo'                    => $this->themeImage('theme_footer_logo')->footerLogo,
            'theme_favicon_logo'                   => $this->themeImage('theme_favicon_logo')->faviconLogo,
            'theme_primary_color'                  => $this->info['theme_primary_color'] ?? '#0f766e',
            'theme_primary_hover_color'            => $this->info['theme_primary_hover_color'] ?? '#115e59',
            'theme_button_text_color'              => $this->info['theme_button_text_color'] ?? '#ffffff',
            'theme_page_background'                => $this->info['theme_page_background'] ?? '#f7f7fc',
            'theme_surface_color'                  => $this->info['theme_surface_color'] ?? '#ffffff',
            'theme_header_background'              => $this->info['theme_header_background'] ?? '#ffffff',
            'theme_footer_background'              => $this->info['theme_footer_background'] ?? '#0f172a',
            'theme_heading_color'                  => $this->info['theme_heading_color'] ?? '#1f1f39',
            'theme_body_text_color'                => $this->info['theme_body_text_color'] ?? '#6e7191',
            'theme_border_color'                   => $this->info['theme_border_color'] ?? '#d9dbe9',
            'theme_item_name_color'                => $this->info['theme_item_name_color'] ?? ($this->info['theme_heading_color'] ?? '#1f1f39'),
            'theme_item_description_color'         => $this->info['theme_item_description_color'] ?? ($this->info['theme_body_text_color'] ?? '#6e7191'),
            'theme_item_price_color'               => $this->info['theme_item_price_color'] ?? ($this->info['theme_primary_hover_color'] ?? '#115e59'),
            'theme_item_old_price_color'           => $this->info['theme_item_old_price_color'] ?? ($this->info['theme_body_text_color'] ?? '#6e7191'),
            'theme_category_color'                 => $this->info['theme_category_color'] ?? ($this->info['theme_body_text_color'] ?? '#6e7191'),
            'theme_icon_color'                     => $this->info['theme_icon_color'] ?? ($this->info['theme_primary_color'] ?? '#0f766e'),
            'theme_offer_title_color'              => $this->info['theme_offer_title_color'] ?? ($this->info['theme_heading_color'] ?? '#1f1f39'),
            'theme_offer_description_color'        => $this->info['theme_offer_description_color'] ?? ($this->info['theme_body_text_color'] ?? '#6e7191'),
            'theme_nav_background_color'           => $this->info['theme_nav_background_color'] ?? ($this->info['theme_header_background'] ?? '#ffffff'),
            'theme_nav_text_color'                 => $this->info['theme_nav_text_color'] ?? ($this->info['theme_body_text_color'] ?? '#6e7191'),
            'theme_nav_active_color'               => $this->info['theme_nav_active_color'] ?? ($this->info['theme_primary_hover_color'] ?? '#115e59'),
            'theme_nav_icon_color'                 => $this->info['theme_nav_icon_color'] ?? ($this->info['theme_nav_text_color'] ?? '#6e7191'),
            'theme_nav_active_icon_color'          => $this->info['theme_nav_active_icon_color'] ?? ($this->info['theme_nav_active_color'] ?? '#115e59'),
            'theme_font_family'                    => $this->info['theme_font_family'] ?? 'Inter, sans-serif',
            'theme_heading_font_family'            => $this->info['theme_heading_font_family'] ?? 'Inter, sans-serif',
            'theme_color_mode'                     => $this->info['theme_color_mode'] ?? 'light',
            'theme_border_radius'                  => $this->info['theme_border_radius'] ?? '12px',
            'otp_type'                             => $this->info['otp_type'],
            'otp_digit_limit'                      => $this->info['otp_digit_limit'],
            'otp_expire_time'                      => $this->info['otp_expire_time'],
            'social_media_facebook'                => $this->info['social_media_facebook'],
            'social_media_instagram'               => $this->info['social_media_instagram'],
            'social_media_twitter'                 => $this->info['social_media_twitter'],
            'social_media_youtube'                 => $this->info['social_media_youtube'],
            'order_setup_food_preparation_time'    => $this->info['order_setup_food_preparation_time'],
            'order_setup_takeaway'                 => $this->info['order_setup_takeaway'],
            'order_setup_delivery'                 => $this->info['order_setup_delivery'],
            'order_setup_free_delivery_kilometer'  => $this->info['order_setup_free_delivery_kilometer'],
            'order_setup_basic_delivery_charge'    => $this->info['order_setup_basic_delivery_charge'],
            'order_setup_charge_per_kilo'          => $this->info['order_setup_charge_per_kilo'],
            'order_setup_max_delivery_kilometer'   => $this->info['order_setup_max_delivery_kilometer'] ?? "0",
            'order_setup_trust_score_enable'     => $this->info['order_setup_trust_score_enable'] ?? \App\Enums\Activity::ENABLE,
            'order_setup_trust_score_min_orders' => $this->info['order_setup_trust_score_min_orders'] ?? "1",
            'cookies_details_page_id'              => $this->info['cookies_details_page_id'],
            'cookies_summary'                      => $this->info['cookies_summary'],
            'notification_fcm_api_key'             => $this->info['notification_fcm_api_key'],
            'notification_fcm_auth_domain'         => $this->info['notification_fcm_auth_domain'],
            'notification_fcm_project_id'          => $this->info['notification_fcm_project_id'],
            'notification_fcm_storage_bucket'      => $this->info['notification_fcm_storage_bucket'],
            'notification_fcm_messaging_sender_id' => $this->info['notification_fcm_messaging_sender_id'],
            'notification_fcm_app_id'              => $this->info['notification_fcm_app_id'],
            'notification_fcm_measurement_id'      => $this->info['notification_fcm_measurement_id'],
            'notification_fcm_public_vapid_key'    => $this->info['notification_fcm_public_vapid_key'],
            'notification_audio'                   => asset('/audio/notification.mp3'),
            'image_cart'                           => asset('/images/cart/empty-cart.gif'),
            'image_confirm'                        => asset('/images/cart/confirm.gif'),
            'image_vag'                            => asset('/images/item-type/veg.png'),
            'image_non_vag'                        => asset('/images/item-type/non-veg.png'),
            'image_app_store'                      => asset('/images/store/app-store.png'),
            'image_play_store'                     => asset('/images/store/play-store.png'),
            'image_order_track'                    => asset('/images/order/track.png'),
            'image_order_placed'                   => asset('/images/order/placed.gif'),
            'image_order_complete'                 => asset('/images/order/complete.gif'),
            'image_order_delivered'                => asset('/images/order/delivered.gif'),
            'image_order_preparing'                => asset('/images/order/preparing_order.gif'),
            'image_order_prepared'                 => asset('/images/order/prepared_order.gif'),
            'image_order_out_for_delivery'         => asset('/images/order/out_for_delivery.gif'),
            'image_order_rejected'                 => asset('/images/order/rejected.gif'),
            'image_order_canceled'                 => asset('/images/order/canceled.gif'),
            'image_order_returned'                 => asset('/images/order/returned.gif'),
            'image_four_zero_four_page'            => asset('/images/accessible/404.gif'),
            'image_four_zero_three_page'           => asset('/images/accessible/403.gif'),
            'payment_gateway_cash_on_delivery'     => \App\Models\PaymentGateway::where('slug', 'cash-on-delivery')->value('status'),
        ];
    }

    public function themeImage($key)
    {
        return ThemeSetting::where(['key' => $key])->first();
    }
}
