<template>
    <LoadingComponent :props="loading" />

    <div id="company" class="db-card db-tab-div active">
        <div class="db-card-header">
            <h3 class="db-card-title">{{ $t("menu.theme") }}</h3>
        </div>
        <div class="db-card-body">
            <form @submit.prevent="save">
                <div class="form-row">
                    <div class="form-col-12 sm:form-col-6">
                        <label for="theme_logo" class="db-field-title">
                            {{ $t("label.logo") }} (128px,43px)
                        </label>
                        <input @change="changeLogo" v-bind:class="errors.theme_logo ? 'invalid' : ''" id="theme_logo"
                            type="file" class="db-field-control" ref="themeLogoProperty"
                            accept="image/png, image/jpeg, image/jpg" />
                        <small class="db-field-alert" v-if="errors.theme_logo">{{
                            errors.theme_logo[0]
                        }}</small>
                        <img class="w-[150px] h-[120px] object-fill rounded-lg mt-2" alt="logo" v-if="theme_logo_reader"
                            :src="theme_logo_reader" />
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="fav_icon" class="db-field-title">
                            {{ $t("label.fav_icon") }} (120px,120px)
                        </label>
                        <input @change="changeFavIcon" v-bind:class="errors.theme_favicon_logo ? 'invalid' : ''"
                            id="fav_icon" type="file" class="db-field-control" ref="themeFaviconLogoProperty"
                            accept="image/png, image/jpeg, image/jpg" />
                        <small class="db-field-alert" v-if="errors.theme_favicon_logo">{{
                            errors.theme_favicon_logo[0]
                        }}</small>

                        <img class="w-[120px] h-[120px] object-fill rounded-lg mt-2" alt="logo"
                            v-if="theme_favicon_logo_reader" :src="theme_favicon_logo_reader" />
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label for="footer_logo" class="db-field-title">
                            {{ $t("label.footer_logo") }} (144px,48px)
                        </label>
                        <input @change="changeFooterLogo" v-bind:class="errors.theme_footer_logo ? 'invalid' : ''"
                            id="fav_icon" type="file" class="db-field-control" ref="themeFooterLogoProperty"
                            accept="image/png, image/jpeg, image/jpg" />
                        <small class="db-field-alert" v-if="errors.theme_footer_logo">{{
                            errors.theme_footer_logo[0]
                        }}</small>

                        <img class="w-[150px] h-[120px] object-fill rounded-lg mt-2" alt="logo"
                            v-if="theme_footer_logo_reader" :src="theme_footer_logo_reader" />
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="theme_font_family" class="db-field-title">Body font</label>
                        <select id="theme_font_family" v-model="design.theme_font_family" class="db-field-control">
                            <option v-for="font in fontOptions" :key="font.value" :value="font.value">{{ font.label }}</option>
                        </select>
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label for="theme_heading_font_family" class="db-field-title">Heading font</label>
                        <select id="theme_heading_font_family" v-model="design.theme_heading_font_family" class="db-field-control">
                            <option v-for="font in fontOptions" :key="font.value" :value="font.value">{{ font.label }}</option>
                        </select>
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label for="theme_color_mode" class="db-field-title">Color mode</label>
                        <select id="theme_color_mode" v-model="design.theme_color_mode" class="db-field-control">
                            <option value="light">Light</option>
                            <option value="dark">Dark</option>
                        </select>
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label for="theme_border_radius" class="db-field-title">Corner radius</label>
                        <select id="theme_border_radius" v-model="design.theme_border_radius" class="db-field-control">
                            <option v-for="radius in radiusOptions" :key="radius" :value="radius">{{ radius }}</option>
                        </select>
                    </div>

                    <div class="form-col-12">
                        <div class="theme-color-heading">
                            <div>
                                <h4>Storefront colors</h4>
                                <p>Customize backgrounds, text, borders, and buttons across the customer UI.</p>
                            </div>
                            <button type="button" class="theme-reset-btn" @click="resetColors">Reset defaults</button>
                        </div>
                    </div>
                    <div class="form-col-12 lg:form-col-7">
                        <div class="theme-color-groups">
                            <section v-for="group in colorGroups" :key="group.key" class="theme-color-group">
                                <div class="theme-color-group-heading">
                                    <h5>{{ group.label }}</h5>
                                    <p>{{ group.description }}</p>
                                </div>
                                <div class="theme-color-grid">
                                    <label v-for="field in group.fields" :key="field.key" class="theme-color-field">
                                        <span>{{ field.label }}</span>
                                        <div class="theme-color-control">
                                            <input v-if="field.key !== 'theme_modal_overlay_color'" type="color" v-model="colors[field.key]" :aria-label="field.label" />
                                            <span v-else class="theme-overlay-swatch" :style="{ background: colors[field.key] }" aria-hidden="true"></span>
                                            <input type="text" v-model="colors[field.key]"
                                                :maxlength="field.key === 'theme_modal_overlay_color' ? 32 : 7"
                                                :pattern="field.key === 'theme_modal_overlay_color' ? '^rgba?\\([^)]+\\)$' : '^#[0-9A-Fa-f]{6}$'" class="db-field-control"
                                                :class="errors[field.key] ? 'invalid' : ''" />
                                        </div>
                                        <small class="db-field-alert" v-if="errors[field.key]">{{ errors[field.key][0] }}</small>
                                    </label>
                                </div>
                            </section>
                        </div>
                    </div>
                    <div class="form-col-12 lg:form-col-5">
                        <div class="theme-preview" :style="previewStyle">
                            <div class="theme-preview-header">
                                <img :src="theme_logo_reader" alt="Store logo preview" />
                                <span :style="{ color: colors.theme_category_color }">Home</span><span :style="{ color: colors.theme_category_color }">Menu</span><span :style="{ color: colors.theme_category_color }">Offers</span>
                            </div>
                            <div class="theme-preview-body">
                                <p class="theme-preview-kicker">Live preview</p>
                                <h4>Your storefront</h4>
                                <p>See how the palette works across content, cards, and actions.</p>
                                <div class="theme-preview-card">
                                    <div><strong>Signature item</strong><small>Freshly prepared</small><em>Ksh 850</em></div>
                                    <button type="button">Add to cart</button>
                                </div>
                            </div>
                            <div class="theme-preview-footer">Footer background</div>
                        </div>
                    </div>

                    <div class="form-col-12">
                        <button type="submit" class="db-btn text-white bg-primary">
                            <i class="lab lab-save"></i>
                            <span>{{ $t("button.save") }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../components/LoadingComponent";
import alertService from "../../../../services/alertService";

export default {
    name: "ThemeComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            theme_logo: "",
            theme_logo_reader: "",
            theme_favicon_logo: "",
            theme_favicon_logo_reader: "",
            theme_footer_logo: "",
            theme_footer_logo_reader: "",
            colors: {},
            design: {},
            errors: {},
        };
    },
    computed: {
        colorDefaults() {
            return {
                theme_primary_color: "#0f766e", theme_primary_hover_color: "#115e59",
                theme_button_text_color: "#ffffff", theme_page_background: "#f7f7fc",
                theme_surface_color: "#ffffff", theme_header_background: "#ffffff",
                theme_footer_background: "#0f172a", theme_heading_color: "#1f1f39",
                theme_body_text_color: "#6e7191", theme_border_color: "#d9dbe9",
                theme_muted_surface_color: "#f8fafc", theme_input_background_color: "#ffffff",
                theme_muted_text_color: "#64748b", theme_modal_overlay_color: "rgba(15, 23, 42, 0.48)",
                theme_item_name_color: "#1f1f39", theme_item_description_color: "#6e7191",
                theme_item_price_color: "#115e59", theme_item_old_price_color: "#6e7191",
                theme_category_color: "#6e7191", theme_icon_color: "#0f766e",
                theme_offer_title_color: "#1f1f39", theme_offer_description_color: "#6e7191",
                theme_nav_background_color: "#ffffff", theme_nav_text_color: "#334155", theme_nav_active_color: "#115e59",
                theme_nav_icon_color: "#475569", theme_nav_active_icon_color: "#115e59",
            };
        },
        designDefaults() {
            return {
                theme_font_family: "Inter, sans-serif",
                theme_heading_font_family: "Inter, sans-serif",
                theme_color_mode: "light",
                theme_border_radius: "12px",
            };
        },
        fontOptions() {
            return [
                { label: "Inter", value: "Inter, sans-serif" },
                { label: "Rubik", value: "Rubik, sans-serif" },
                { label: "Poppins", value: "Poppins, sans-serif" },
                { label: "Open Sans", value: "'Open Sans', sans-serif" },
                { label: "Lato", value: "Lato, sans-serif" },
                { label: "System default", value: "system-ui, sans-serif" },
            ];
        },
        radiusOptions() {
            return ["0px", "6px", "12px", "18px", "24px"];
        },
        colorGroups() {
            const field = (key, label) => ({ key, label });
            return [
                { key: "surfaces", label: "Surfaces & structure", description: "Set the page, cards, header, footer, borders, and primary action colors.", fields: [
                    field("theme_primary_color", "Primary buttons"), field("theme_primary_hover_color", "Links & hover"),
                    field("theme_button_text_color", "Button text"), field("theme_page_background", "Page background"),
                    field("theme_surface_color", "Cards & panels"), field("theme_muted_surface_color", "Muted surfaces"),
                    field("theme_input_background_color", "Input background"), field("theme_header_background", "Header"),
                    field("theme_footer_background", "Footer"), field("theme_border_color", "Card borders"),
                    field("theme_modal_overlay_color", "Modal overlay"),
                ]},
                { key: "content", label: "General content", description: "Control the default colors used by headings, body copy, categories, and icons.", fields: [
                    field("theme_heading_color", "Headings"), field("theme_body_text_color", "Body text"), field("theme_muted_text_color", "Muted text"),
                    field("theme_category_color", "Category tabs"), field("theme_icon_color", "General icons"),
                ]},
                { key: "items", label: "Item cards", description: "Make item names, descriptions, current prices, and old prices readable independently.", fields: [
                    field("theme_item_name_color", "Item names"), field("theme_item_description_color", "Item descriptions"),
                    field("theme_item_price_color", "Item prices"), field("theme_item_old_price_color", "Old prices"),
                ]},
                { key: "offers", label: "Offers", description: "Set offer titles and descriptions separately from the rest of the storefront.", fields: [
                    field("theme_offer_title_color", "Offer titles"), field("theme_offer_description_color", "Offer descriptions"),
                ]},
                { key: "navigation", label: "Navigation & mobile bar", description: "Customize navigation background, inactive and active text, and both icon states.", fields: [
                    field("theme_nav_background_color", "Navigation background"), field("theme_nav_text_color", "Navigation text"),
                    field("theme_nav_active_color", "Active navigation text"), field("theme_nav_icon_color", "Navigation icons"),
                    field("theme_nav_active_icon_color", "Active navigation icons"),
                ]},
            ];
        },
        previewStyle() {
            return {
                "--preview-primary": this.colors.theme_primary_color, "--preview-hover": this.colors.theme_primary_hover_color,
                "--preview-button-text": this.colors.theme_button_text_color, "--preview-page": this.colors.theme_page_background,
                "--preview-surface": this.colors.theme_surface_color, "--preview-header": this.colors.theme_header_background,
                "--preview-footer": this.colors.theme_footer_background, "--preview-heading": this.colors.theme_heading_color,
                "--preview-body": this.colors.theme_body_text_color, "--preview-border": this.colors.theme_border_color,
                "--preview-item-name": this.colors.theme_item_name_color, "--preview-item-description": this.colors.theme_item_description_color,
                "--preview-item-price": this.colors.theme_item_price_color,
                "--preview-offer-title": this.colors.theme_offer_title_color,
                "--preview-offer-description": this.colors.theme_offer_description_color,
            };
        },
    },
    created() {
        this.colors = { ...this.colorDefaults };
        this.design = { ...this.designDefaults };
    },
    mounted() {
        this.list();
    },
    methods: {
        changeLogo: function (e) {
            this.theme_logo = e.target.files[0];
        },
        changeFavIcon: function (e) {
            this.theme_favicon_logo = e.target.files[0];
        },
        changeFooterLogo: function (e) {
            this.theme_footer_logo = e.target.files[0];
        },
        list: function () {
            this.loading.isActive = true;
            this.$store
                .dispatch("theme/lists")
                .then((res) => {
                    this.theme_logo_reader = res.data.data.theme_logo;
                    this.theme_favicon_logo_reader = res.data.data.theme_favicon_logo;
                    this.theme_footer_logo_reader = res.data.data.theme_footer_logo;
                    Object.keys(this.colorDefaults).forEach((key) => {
                        this.colors[key] = res.data.data[key] || this.colorDefaults[key];
                    });
                    Object.keys(this.designDefaults).forEach((key) => {
                        this.design[key] = res.data.data[key] || this.designDefaults[key];
                    });
                    this.loading.isActive = false;
                })
                .catch((err) => {
                    this.loading.isActive = false;
                });
        },
        save: function () {
            try {
                const fd = new FormData();
                if (this.theme_logo) {
                    fd.append("theme_logo", this.theme_logo);
                }
                if (this.theme_favicon_logo) {
                    fd.append("theme_favicon_logo", this.theme_favicon_logo);
                }
                if (this.theme_footer_logo) {
                    fd.append("theme_footer_logo", this.theme_footer_logo);
                }
                Object.keys(this.colorDefaults).forEach((key) => fd.append(key, this.colors[key]));
                Object.keys(this.designDefaults).forEach((key) => fd.append(key, this.design[key]));
                this.loading.isActive = true;
                this.$store
                    .dispatch("theme/save", {
                        form: fd,
                    })
                    .then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(1, this.$t("menu.theme"));
                        this.list();
                        this.theme_logo = "";
                        this.theme_favicon_logo = "";
                        this.theme_footer_logo = "";
                        this.errors = {};
                        this.$refs.themeLogoProperty.value = null;
                        this.$refs.themeFaviconLogoProperty.value = null;
                        this.$refs.themeFooterLogoProperty.value = null;
                        this.$store.dispatch("frontendSetting/lists").catch(() => {});
                    })
                    .catch((err) => {
                        this.loading.isActive = false;
                        this.errors = err.response.data.errors;
                    });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        resetColors: function () {
            this.colors = { ...this.colorDefaults };
            this.design = { ...this.designDefaults };
        },
    },
};
</script>

<style scoped>
.theme-overlay-swatch{width:42px;height:42px;flex:none;border:1px solid #d9dbe9;border-radius:9px;background:#fff}
.theme-color-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-top:12px;padding-top:24px;border-top:1px solid #e5e7eb}.theme-color-heading h4{font-size:18px;font-weight:700;color:#1f1f39}.theme-color-heading p{margin-top:4px;font-size:13px;color:#6e7191}.theme-reset-btn{flex-shrink:0;padding:8px 12px;border:1px solid #d9dbe9;border-radius:8px;color:#6e7191;font-size:13px;font-weight:600}.theme-color-groups{display:grid;gap:18px}.theme-color-group{padding:16px;border:1px solid #e5e7eb;border-radius:12px;background:#fafbfc}.theme-color-group-heading{margin-bottom:12px}.theme-color-group-heading h5{margin:0;color:#1f1f39;font-size:14px;font-weight:700}.theme-color-group-heading p{margin:3px 0 0;color:#6e7191;font-size:12px;line-height:1.45}.theme-color-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.theme-color-field>span{display:block;margin-bottom:7px;color:#1f1f39;font-size:13px;font-weight:600}.theme-color-control{display:flex;align-items:center;gap:8px}.theme-color-control input[type=color]{width:42px;height:42px;flex:none;padding:3px;border:1px solid #d9dbe9;border-radius:9px;background:#fff;cursor:pointer}.theme-color-control .db-field-control{height:42px;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;text-transform:uppercase}.theme-preview{position:sticky;top:20px;overflow:hidden;min-height:360px;border:1px solid var(--preview-border);border-radius:14px;color:var(--preview-body);background:var(--preview-page);box-shadow:0 14px 38px rgb(31 31 57 / 12%)}.theme-preview-header{display:flex;align-items:center;gap:14px;min-height:62px;padding:12px 16px;color:var(--preview-heading);background:var(--preview-header);border-bottom:1px solid var(--preview-border);font-size:11px}.theme-preview-header img{width:72px;max-height:30px;object-fit:contain;margin-right:auto}.theme-preview-body{padding:26px 20px}.theme-preview-kicker{color:var(--preview-hover);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em}.theme-preview-body h4{margin-top:7px;color:var(--preview-heading);font:600 24px/1.2 Georgia,serif}.theme-preview-body>p:not(.theme-preview-kicker){margin-top:8px;font-size:12px;line-height:1.6}.theme-preview-card{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:22px;padding:15px;border:1px solid var(--preview-border);border-radius:10px;background:var(--preview-surface)}.theme-preview-card strong,.theme-preview-card small{display:block}.theme-preview-card strong{color:var(--preview-item-name)}.theme-preview-card small{margin-top:3px;color:var(--preview-item-description);font-size:10px}.theme-preview-card em{display:block;margin-top:7px;color:var(--preview-item-price);font-size:12px;font-style:normal;font-weight:700}.theme-preview-card button{padding:9px 12px;border-radius:7px;color:var(--preview-button-text);background:var(--preview-primary);font-size:10px;font-weight:700}.theme-preview-card button:hover{background:var(--preview-hover)}.theme-preview-footer{padding:13px 20px;color:var(--preview-body);background:var(--preview-footer);border-top:1px solid var(--preview-border);font-size:10px}@media(max-width:640px){.theme-color-grid{grid-template-columns:1fr}.theme-color-heading{flex-direction:column}}
.theme-preview-card strong{color:var(--preview-item-name)}.theme-preview-card small{color:var(--preview-item-description)}.theme-preview-card em{display:block;margin-top:7px;color:var(--preview-item-price);font-size:12px;font-style:normal;font-weight:700}
</style>
