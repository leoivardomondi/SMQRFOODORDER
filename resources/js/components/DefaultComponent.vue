<template>
  <div :dir="direction">
    <div v-if="theme === 'frontend'" class="frontend-theme min-h-screen" :style="frontendThemeStyle">
      <FrontendNavbarComponent v-if="!focusedLayout" />
      <FrontendCartComponent />
      <router-view></router-view>
      <FrontendMobileNavBarComponent v-if="!focusedLayout" />
      <FrontendMobileAccountComponent />
      <FrontendCookiesComponent />
      <FrontendFooterComponent v-if="!focusedLayout" />
      <WhatsappSupportComponent v-if="!focusedLayout" />
    </div>

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
import WhatsappSupportComponent from "./layouts/frontend/WhatsappSupportComponent";
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
    BackendMenuComponent,
    WhatsappSupportComponent
  },
  data() {
    return {
      theme: "frontend",
    };
  },
  computed: {
    frontendThemeStyle: function () {
      const settings = this.$store.getters['frontendSetting/lists'] || {};
      return {
        '--store-primary': settings.theme_primary_color || '#c6a15b',
        '--store-primary-hover': settings.theme_primary_hover_color || '#e2c986',
        '--store-button-text': settings.theme_button_text_color || '#080808',
        '--store-page-bg': settings.theme_page_background || '#080808',
        '--store-surface': settings.theme_surface_color || '#111111',
        '--store-header-bg': settings.theme_header_background || '#0b0b0b',
        '--store-footer-bg': settings.theme_footer_background && settings.theme_footer_background !== '#050505' && settings.theme_footer_background !== '#000000' ? settings.theme_footer_background : '#1c1712',
        '--store-heading': settings.theme_heading_color || '#ffffff',
        '--store-body-text': settings.theme_body_text_color || '#a8a8ad',
        '--store-border': settings.theme_border_color || '#332b1e',
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
      .catch();

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
