<template>
  <div :dir="direction">
    <div v-if="theme === 'frontend' && frontendReady" class="frontend-theme min-h-screen" :data-theme-mode="themeMode" :style="frontendThemeStyle">
      <FrontendNavbarComponent v-if="!focusedLayout" :theme-mode="themeMode" @toggle-theme="toggleTheme" />
      <FrontendCartComponent />
      <router-view></router-view>
      <FrontendMobileNavBarComponent v-if="!focusedLayout" />
      <FrontendMobileAccountComponent />
      <FrontendCookiesComponent />
      <FrontendFooterComponent v-if="!focusedLayout" />
    </div>

    <div v-else-if="theme === 'frontend'" class="min-h-screen bg-[#080808]" aria-hidden="true"></div>

    <div v-if="theme === 'backend'">
      <main class="db-main" v-if="logged">
        <BackendNavbarComponent />
        <BackendMenuComponent />
        <router-view></router-view>
      </main>

      <div v-if="!logged">
        <router-view></router-view>
      </div>
    </div>
  </div>
</template>

<script>
import BackendNavbarComponent from "./layouts/backend/BackendNavbarComponent";
import BackendMenuComponent from "./layouts/backend/BackendMenuComponent";
import FrontendNavbarComponent from "./layouts/frontend/FrontendNavBarComponent";
import FrontendFooterComponent from "./layouts/frontend/FrontendFooterComponent";
import FrontendMobileNavBarComponent from "./layouts/frontend/FrontendMobileNavBarComponent";
import FrontendMobileAccountComponent from "./layouts/frontend/FrontendMobileAccountComponent";
import FrontendCartComponent from "./layouts/frontend/FrontendCartComponent";
import FrontendCookiesComponent from "./layouts/frontend/FrontendCookiesComponent";
import displayModeEnum from "../enums/modules/displayModeEnum";
import env from "../config/env";
import { initCartAbandonmentTracker } from "../services/cartAbandonmentService";


export default {
  name: "DefaultComponent",
  components: {
    FrontendCartComponent,
    FrontendMobileAccountComponent,
    FrontendMobileNavBarComponent,
    FrontendCookiesComponent,
    FrontendFooterComponent,
    FrontendNavbarComponent,
    BackendNavbarComponent,
    BackendMenuComponent
  },
  data() {
    return {
      theme: "frontend",
      frontendReady: false,
      themeOverride: null,
    };
  },
  computed: {
    themeMode: function () {
      const globalState = this.$store.getters['globalState/lists'] || {};
      if (globalState.theme_mode === 'light' || globalState.theme_mode === 'dark') {
        return globalState.theme_mode;
      }
      if (this.themeOverride === 'light' || this.themeOverride === 'dark') {
        return this.themeOverride;
      }
      return 'light';
    },
    frontendThemeStyle: function () {
      const settings = this.$store.getters['frontendSetting/lists'] || {};
      const luminance = (hex) => {
        const value = String(hex || '').replace('#', '');
        if (value.length !== 6) return 1;
        const channels = [0, 2, 4].map((index) => parseInt(value.slice(index, index + 2), 16) / 255);
        return channels.reduce((sum, channel, channelIndex) => sum + (channel <= 0.03928 ? channel / 12.92 : Math.pow((channel + 0.055) / 1.055, 2.4)) * [0.2126, 0.7152, 0.0722][channelIndex], 0);
      };
      const configuredPageBackground = settings.theme_page_background;
      const configuredSurfaceBackground = settings.theme_surface_color;
      const looksDark = (color) => typeof color === 'string' && color.startsWith('#') && luminance(color) < 0.25;
      // Older saved themes can have dark surfaces while the mode field is empty
      // or still set to light. Infer the active mode so the header/navigation
      // cannot remain white on an otherwise dark storefront.
      const isDark = this.themeMode === 'dark';
      const palette = isDark ? {
        page: '#0b1220', surface: '#111827', header: '#0f172a', footer: '#020617',
        heading: '#f8fafc', body: '#cbd5e1', border: '#334155', navText: '#cbd5e1', navIcon: '#94a3b8',
        mutedSurface: '#1e293b', input: '#0f172a',
      } : {
        page: '#f7f8fa', surface: '#ffffff', header: '#ffffff', footer: '#0f172a',
        heading: '#111827', body: '#475569', border: '#e2e8f0', navText: '#334155', navIcon: '#64748b',
        mutedSurface: '#f8fafc', input: '#ffffff',
      };
      const readableColor = (value, fallback, background) => {
        const foreground = luminance(value);
        const surface = luminance(background);
        const contrast = (Math.max(foreground, surface) + 0.05) / (Math.min(foreground, surface) + 0.05);
        return contrast >= 3 ? value : fallback;
      };
      const pageBackground = isDark
        ? (looksDark(configuredPageBackground) ? configuredPageBackground : palette.page)
        : (!looksDark(configuredPageBackground) ? (configuredPageBackground || palette.page) : palette.page);
      const surfaceBackground = isDark
        ? (looksDark(configuredSurfaceBackground) ? configuredSurfaceBackground : palette.surface)
        : (!looksDark(configuredSurfaceBackground) ? (configuredSurfaceBackground || palette.surface) : palette.surface);
      // A saved light header must not leak into dark mode. Structural surfaces
      // follow the selected mode; brand/action colors remain customizable.
      const headerBackground = isDark
        ? (looksDark(settings.theme_header_background) ? settings.theme_header_background : palette.header)
        : (!looksDark(settings.theme_header_background) ? (settings.theme_header_background || palette.header) : palette.header);
      const heading = isDark
        ? readableColor(settings.theme_heading_color, palette.heading, surfaceBackground)
        : (!looksDark(settings.theme_heading_color) ? readableColor(settings.theme_heading_color, palette.heading, surfaceBackground) : palette.heading);
      const body = isDark
        ? readableColor(settings.theme_body_text_color, palette.body, surfaceBackground)
        : (!looksDark(settings.theme_body_text_color) ? readableColor(settings.theme_body_text_color, palette.body, surfaceBackground) : palette.body);
      const mutedText = isDark
        ? readableColor(settings.theme_muted_text_color, '#94a3b8', surfaceBackground)
        : (!looksDark(settings.theme_muted_text_color) ? readableColor(settings.theme_muted_text_color, '#64748b', surfaceBackground) : '#64748b');
      const inputBackground = isDark
        ? (looksDark(settings.theme_input_background_color) ? settings.theme_input_background_color : palette.input)
        : (!looksDark(settings.theme_input_background_color) ? (settings.theme_input_background_color || palette.input) : palette.input);
      const border = isDark
        ? (settings.theme_border_color || palette.border)
        : (looksDark(settings.theme_border_color) ? palette.border : (settings.theme_border_color || palette.border));
      return {
        '--store-primary': settings.theme_primary_color || '#0f766e',
        '--store-primary-hover': settings.theme_primary_hover_color || '#115e59',
        '--store-button-text': settings.theme_button_text_color || '#ffffff',
        '--store-page-bg': pageBackground,
        '--store-surface': surfaceBackground,
        '--store-header-bg': headerBackground,
        '--store-footer-bg': palette.footer,
        '--store-heading': heading,
        '--store-body-text': body,
        '--store-border': border,
        '--store-item-name': readableColor(settings.theme_item_name_color, heading, surfaceBackground),
        '--store-item-description': readableColor(settings.theme_item_description_color, body, surfaceBackground),
        '--store-item-price': readableColor(settings.theme_primary_hover_color, palette.navText, surfaceBackground),
        '--store-item-old-price': readableColor(settings.theme_item_old_price_color, body, surfaceBackground),
        '--store-category': readableColor(settings.theme_category_color, body, surfaceBackground),
        '--store-icon': settings.theme_primary_color || '#0f766e',
        '--store-offer-title': readableColor(settings.theme_offer_title_color, heading, surfaceBackground),
        '--store-offer-description': readableColor(settings.theme_offer_description_color, body, surfaceBackground),
        '--store-nav-bg': headerBackground,
        '--store-nav-text': readableColor(settings.theme_nav_text_color, palette.navText, headerBackground),
        '--store-nav-active': readableColor(settings.theme_primary_hover_color, palette.navText, headerBackground),
        '--store-nav-icon': readableColor(settings.theme_nav_icon_color, palette.navIcon, headerBackground),
        '--store-nav-active-icon': settings.theme_primary_hover_color || '#115e59',
        '--store-muted-surface': isDark
          ? (looksDark(settings.theme_muted_surface_color) ? settings.theme_muted_surface_color : palette.mutedSurface)
          : (!looksDark(settings.theme_muted_surface_color) ? (settings.theme_muted_surface_color || palette.mutedSurface) : palette.mutedSurface),
        '--store-input-bg': inputBackground,
        '--store-muted-text': mutedText,
        '--store-modal-overlay': settings.theme_modal_overlay_color || (isDark ? 'rgba(0, 0, 0, 0.72)' : 'rgba(15, 23, 42, 0.48)'),
        '--store-icon-muted': readableColor(settings.theme_icon_color, isDark ? '#cbd5e1' : '#64748b', surfaceBackground),
        '--store-font': settings.theme_font_family || 'Inter, sans-serif',
        '--store-heading-font': settings.theme_heading_font_family || 'Inter, sans-serif',
        '--store-color-scheme': isDark ? 'dark' : 'light',
        '--store-radius': settings.theme_border_radius || '12px',
      };
    },
    focusedLayout: function () {
      return this.$route.meta.focusedLayout === true;
    },
    direction: function () {
      return this.$store.getters['frontendLanguage/show'].display_mode === displayModeEnum.RTL ? 'rtl' : 'ltr';
    },
    logged: function () {
      return this.$store.getters.authStatus;
    },
  },
  beforeMount() {
    this.themeOverride = window.localStorage.getItem('store_theme_mode');
    this.$store
      .dispatch("frontendSetting/lists")
      .then((res) => {
        this.$store.dispatch("globalState/init", {
          branch_id: res.data.data.site_default_branch,
          language_id: res.data.data.site_default_language,
        });
      })
      .catch()
      .finally(() => {
        // Do not render the storefront until its theme settings are available.
        // This prevents the default installer theme from flashing on first load.
        this.frontendReady = true;
      });

    if (env.DEMO === "true" || env.DEMO === true || env.DEMO === "1" || env.DEMO === 1) {
      this.$store.dispatch("authcheck").then(res => {
        if (res.data.status === false) {
          this.$router.push({ name: "frontend.home" });
        };
      }).catch();
    }
  },
  mounted() {
    initCartAbandonmentTracker(this.$store);
  },
  methods: {
    toggleTheme: function () {
      this.themeOverride = this.themeMode === 'dark' ? 'light' : 'dark';
      window.localStorage.setItem('store_theme_mode', this.themeOverride);
    },
  },
  watch: {
    $route(e) {
      if (e.meta.isFrontend === true) {
        this.theme = "frontend";
      } else {
        this.theme = "backend";
      }
    },
  },
};
</script>
