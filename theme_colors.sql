-- ========================================================
-- Theme Colors & Appearance SQL Export
-- Database: gponzghq_bwibo | Table: settings
-- Generated: 2026-08-31 02:21:39
-- ========================================================

-- 1. Standard INSERT INTO ... ON DUPLICATE KEY UPDATE
INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (61, 'theme', 'theme_logo', '{\"$value\":{},\"$cast\":null}', NULL, NULL, '2026-01-28 18:15:59', '2026-01-28 18:15:59')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":{},\"$cast\":null}', `group` = 'theme', `updated_at` = '2026-01-28 18:15:59';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (62, 'theme', 'theme_favicon_logo', '{\"$value\":{},\"$cast\":null}', NULL, NULL, '2026-01-28 18:15:25', '2026-01-28 18:15:25')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":{},\"$cast\":null}', `group` = 'theme', `updated_at` = '2026-01-28 18:15:25';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (63, 'theme', 'theme_footer_logo', '{\"$value\":{},\"$cast\":null}', NULL, NULL, '2026-01-28 18:15:42', '2026-01-28 18:15:42')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":{},\"$cast\":null}', `group` = 'theme', `updated_at` = '2026-01-28 18:15:42';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (71, 'theme', 'theme_primary_color', '{\"$value\":\"#c6a15b\",\"$cast\":null}', NULL, NULL, '2026-08-02 19:51:05', '2026-08-02 19:51:05')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#c6a15b\",\"$cast\":null}', `group` = 'theme', `updated_at` = '2026-08-02 19:51:05';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (72, 'theme', 'theme_primary_hover_color', '{\"$value\":\"#e2c986\",\"$cast\":null}', NULL, NULL, '2026-08-02 19:51:05', '2026-08-02 19:51:05')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#e2c986\",\"$cast\":null}', `group` = 'theme', `updated_at` = '2026-08-02 19:51:05';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (73, 'theme', 'theme_button_text_color', '{\"$value\":\"#ffffff\",\"$cast\":null}', NULL, NULL, '2026-08-02 19:51:05', '2026-08-02 19:51:05')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#ffffff\",\"$cast\":null}', `group` = 'theme', `updated_at` = '2026-08-02 19:51:05';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (74, 'theme', 'theme_page_background', '{\"$value\":\"#080808\",\"$cast\":null}', NULL, NULL, '2026-08-02 19:51:05', '2026-08-02 19:51:05')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#080808\",\"$cast\":null}', `group` = 'theme', `updated_at` = '2026-08-02 19:51:05';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (75, 'theme', 'theme_surface_color', '{\"$value\":\"#111111\",\"$cast\":null}', NULL, NULL, '2026-08-02 19:51:05', '2026-08-02 19:51:05')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#111111\",\"$cast\":null}', `group` = 'theme', `updated_at` = '2026-08-02 19:51:05';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (76, 'theme', 'theme_header_background', '{\"$value\":\"#0b0b0b\",\"$cast\":null}', NULL, NULL, '2026-08-02 19:51:05', '2026-08-02 19:51:05')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#0b0b0b\",\"$cast\":null}', `group` = 'theme', `updated_at` = '2026-08-02 19:51:05';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (77, 'theme', 'theme_footer_background', '{\"$value\":\"#1c1712\",\"$cast\":null}', NULL, NULL, '2026-08-04 20:14:09', '2026-08-04 20:14:09')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#1c1712\",\"$cast\":null}', `group` = 'theme', `updated_at` = '2026-08-04 20:14:09';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (78, 'theme', 'theme_heading_color', '{\"$value\":\"#ffffff\",\"$cast\":null}', NULL, NULL, '2026-08-02 19:51:05', '2026-08-02 19:51:05')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#ffffff\",\"$cast\":null}', `group` = 'theme', `updated_at` = '2026-08-02 19:51:05';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (79, 'theme', 'theme_body_text_color', '{\"$value\":\"#e2e8f0\",\"$cast\":null}', NULL, NULL, '2026-08-02 19:51:05', '2026-08-02 19:51:05')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#e2e8f0\",\"$cast\":null}', `group` = 'theme', `updated_at` = '2026-08-02 19:51:05';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (80, 'theme', 'theme_border_color', '{\"$value\":\"#332b1e\",\"$cast\":null}', NULL, NULL, '2026-08-02 19:51:05', '2026-08-02 19:51:05')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#332b1e\",\"$cast\":null}', `group` = 'theme', `updated_at` = '2026-08-02 19:51:05';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (81, NULL, 'theme_primary_hover_color', '{\"$value\":\"#e2c986\",\"$cast\":null}', NULL, NULL, '2026-08-14 06:32:30', '2026-08-14 06:32:30')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#e2c986\",\"$cast\":null}', `group` = NULL, `updated_at` = '2026-08-14 06:32:30';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (82, NULL, 'theme_button_text_color', '{\"$value\":\"#080808\",\"$cast\":null}', NULL, NULL, '2026-08-14 06:32:30', '2026-08-14 06:32:30')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#080808\",\"$cast\":null}', `group` = NULL, `updated_at` = '2026-08-14 06:32:30';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (83, NULL, 'theme_page_background', '{\"$value\":\"#080808\",\"$cast\":null}', NULL, NULL, '2026-08-14 06:32:30', '2026-08-14 06:32:30')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#080808\",\"$cast\":null}', `group` = NULL, `updated_at` = '2026-08-14 06:32:30';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (84, NULL, 'theme_surface_color', '{\"$value\":\"#111111\",\"$cast\":null}', NULL, NULL, '2026-08-14 06:32:30', '2026-08-14 06:32:30')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#111111\",\"$cast\":null}', `group` = NULL, `updated_at` = '2026-08-14 06:32:30';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (85, NULL, 'theme_header_background', '{\"$value\":\"#0b0b0b\",\"$cast\":null}', NULL, NULL, '2026-08-14 06:32:30', '2026-08-14 06:32:30')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#0b0b0b\",\"$cast\":null}', `group` = NULL, `updated_at` = '2026-08-14 06:32:30';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (86, NULL, 'theme_footer_background', '{\"$value\":\"#050505\",\"$cast\":null}', NULL, NULL, '2026-08-14 06:32:30', '2026-08-14 06:32:30')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#050505\",\"$cast\":null}', `group` = NULL, `updated_at` = '2026-08-14 06:32:30';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (87, NULL, 'theme_heading_color', '{\"$value\":\"#ffffff\",\"$cast\":null}', NULL, NULL, '2026-08-14 06:32:30', '2026-08-14 06:32:30')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#ffffff\",\"$cast\":null}', `group` = NULL, `updated_at` = '2026-08-14 06:32:30';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (88, NULL, 'theme_body_text_color', '{\"$value\":\"#a8a8ad\",\"$cast\":null}', NULL, NULL, '2026-08-14 06:32:30', '2026-08-14 06:32:30')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#a8a8ad\",\"$cast\":null}', `group` = NULL, `updated_at` = '2026-08-14 06:32:30';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (89, NULL, 'theme_border_color', '{\"$value\":\"#332b1e\",\"$cast\":null}', NULL, NULL, '2026-08-14 06:32:30', '2026-08-14 06:32:30')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#332b1e\",\"$cast\":null}', `group` = NULL, `updated_at` = '2026-08-14 06:32:30';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (90, NULL, 'theme_font_family', '{\"$value\":\"Inter, sans-serif\",\"$cast\":null}', NULL, NULL, '2026-08-17 09:59:59', '2026-08-17 09:59:59')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"Inter, sans-serif\",\"$cast\":null}', `group` = NULL, `updated_at` = '2026-08-17 09:59:59';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (91, NULL, 'theme_heading_font_family', '{\"$value\":\"Inter, sans-serif\",\"$cast\":null}', NULL, NULL, '2026-08-17 09:59:59', '2026-08-17 09:59:59')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"Inter, sans-serif\",\"$cast\":null}', `group` = NULL, `updated_at` = '2026-08-17 09:59:59';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (92, NULL, 'theme_color_mode', '{\"$value\":\"light\",\"$cast\":null}', NULL, NULL, '2026-08-17 09:59:59', '2026-08-17 09:59:59')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"light\",\"$cast\":null}', `group` = NULL, `updated_at` = '2026-08-17 09:59:59';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (93, NULL, 'theme_border_radius', '{\"$value\":\"12px\",\"$cast\":null}', NULL, NULL, '2026-08-17 09:59:59', '2026-08-17 09:59:59')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"12px\",\"$cast\":null}', `group` = NULL, `updated_at` = '2026-08-17 09:59:59';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (94, NULL, 'theme_item_name_color', '{\"$value\":\"#1f1f39\",\"$cast\":null}', NULL, NULL, '2026-08-18 07:24:33', '2026-08-18 07:24:33')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#1f1f39\",\"$cast\":null}', `group` = NULL, `updated_at` = '2026-08-18 07:24:33';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (95, NULL, 'theme_item_description_color', '{\"$value\":\"#6e7191\",\"$cast\":null}', NULL, NULL, '2026-08-18 07:24:33', '2026-08-18 07:24:33')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#6e7191\",\"$cast\":null}', `group` = NULL, `updated_at` = '2026-08-18 07:24:33';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (96, NULL, 'theme_item_price_color', '{\"$value\":\"#115e59\",\"$cast\":null}', NULL, NULL, '2026-08-18 07:24:33', '2026-08-18 07:24:33')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#115e59\",\"$cast\":null}', `group` = NULL, `updated_at` = '2026-08-18 07:24:33';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (97, NULL, 'theme_item_old_price_color', '{\"$value\":\"#6e7191\",\"$cast\":null}', NULL, NULL, '2026-08-18 07:24:33', '2026-08-18 07:24:33')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#6e7191\",\"$cast\":null}', `group` = NULL, `updated_at` = '2026-08-18 07:24:33';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (98, NULL, 'theme_category_color', '{\"$value\":\"#6e7191\",\"$cast\":null}', NULL, NULL, '2026-08-18 07:24:33', '2026-08-18 07:24:33')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#6e7191\",\"$cast\":null}', `group` = NULL, `updated_at` = '2026-08-18 07:24:33';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (99, NULL, 'theme_icon_color', '{\"$value\":\"#0f766e\",\"$cast\":null}', NULL, NULL, '2026-08-18 07:24:33', '2026-08-18 07:24:33')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#0f766e\",\"$cast\":null}', `group` = NULL, `updated_at` = '2026-08-18 07:24:33';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (100, NULL, 'theme_nav_background_color', '{\"$value\":\"#ffffff\",\"$cast\":null}', NULL, NULL, '2026-08-18 08:04:28', '2026-08-18 08:04:28')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#ffffff\",\"$cast\":null}', `group` = NULL, `updated_at` = '2026-08-18 08:04:28';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (101, NULL, 'theme_nav_text_color', '{\"$value\":\"#6e7191\",\"$cast\":null}', NULL, NULL, '2026-08-18 08:04:28', '2026-08-18 08:04:28')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#6e7191\",\"$cast\":null}', `group` = NULL, `updated_at` = '2026-08-18 08:04:28';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (102, NULL, 'theme_nav_active_color', '{\"$value\":\"#115e59\",\"$cast\":null}', NULL, NULL, '2026-08-18 08:04:28', '2026-08-18 08:04:28')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#115e59\",\"$cast\":null}', `group` = NULL, `updated_at` = '2026-08-18 08:04:28';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (103, NULL, 'theme_nav_icon_color', '{\"$value\":\"#6e7191\",\"$cast\":null}', NULL, NULL, '2026-08-18 08:05:47', '2026-08-18 08:05:47')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#6e7191\",\"$cast\":null}', `group` = NULL, `updated_at` = '2026-08-18 08:05:47';

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`)
VALUES (104, NULL, 'theme_nav_active_icon_color', '{\"$value\":\"#115e59\",\"$cast\":null}', NULL, NULL, '2026-08-18 08:05:47', '2026-08-18 08:05:47')
ON DUPLICATE KEY UPDATE `payload` = '{\"$value\":\"#115e59\",\"$cast\":null}', `group` = NULL, `updated_at` = '2026-08-18 08:05:47';

