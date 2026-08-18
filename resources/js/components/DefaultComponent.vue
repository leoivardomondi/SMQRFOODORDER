<template>
  <div :dir="direction">
    <div v-if="theme === 'frontend' && frontendReady" class="frontend-theme min-h-screen" :style="frontendThemeStyle">
      <FrontendNavbarComponent v-if="!focusedLayout" />
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
    };
  },
  computed: {
    frontendThemeStyle: function () {
      const settings = this.$store.getters['frontendSetting/lists'] || {};
      return {
        '--store-primary': settings.theme_primary_color || '#0f766e',
        '--store-primary-hover': settings.theme_primary_hover_color || '#115e59',
        '--store-button-text': settings.theme_button_text_color || '#ffffff',
        '--store-page-bg': settings.theme_page_background || '#f7f7fc',
        '--store-surface': settings.theme_surface_color || '#ffffff',
        '--store-header-bg': settings.theme_header_background || '#ffffff',
        '--store-footer-bg': settings.theme_footer_background || '#0f172a',
        '--store-heading': settings.theme_heading_color || '#1f1f39',
        '--store-body-text': settings.theme_body_text_color || '#6e7191',
        '--store-border': settings.theme_border_color || '#d9dbe9',
        '--store-item-name': settings.theme_item_name_color || settings.theme_heading_color || '#1f1f39',
        '--store-item-description': settings.theme_item_description_color || settings.theme_body_text_color || '#6e7191',
        '--store-item-price': settings.theme_item_price_color || settings.theme_primary_hover_color || '#115e59',
        '--store-item-old-price': settings.theme_item_old_price_color || settings.theme_body_text_color || '#6e7191',
        '--store-category': settings.theme_category_color || settings.theme_body_text_color || '#6e7191',
        '--store-icon': settings.theme_icon_color || settings.theme_primary_color || '#0f766e',
        '--store-font': settings.theme_font_family || 'Inter, sans-serif',
        '--store-heading-font': settings.theme_heading_font_family || 'Inter, sans-serif',
        '--store-color-scheme': settings.theme_color_mode || 'light',
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
