<template>
    <LoadingComponent :props="loading" />
    <section class="pt-7 pb-16">
        <div class="container max-w-[550px]">
            <router-link :to="{ name: 'frontend.home' }" class="mb-3 inline-flex items-center gap-2 text-primary">
                <i class="lab lab-undo lab-font-size-16"></i>
                <span class="text-xs font-medium leading-6">{{ $t('label.back_to_home') }}</span>
            </router-link>
            <div class="py-6 p-4 sm:px-6 shadow-xs rounded-2xl bg-white">
                <h2 class="capitalize mb-6 text-left text-[22px] font-semibold leading-[34px] text-heading">
                    {{ $t('menu.edit_profile') }}
                </h2>

                <div class="mb-6 p-4 rounded-xl border bg-gradient-to-r from-blue-50 to-indigo-50 border-indigo-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs uppercase font-semibold text-indigo-500 tracking-wider block">Customer Trust Score</span>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-2xl font-extrabold text-indigo-900">{{ profile.trust_metrics?.trust_score ?? profile.order ?? 0 }}</span>
                            <span class="text-xs text-indigo-600 font-medium">({{ profile.trust_metrics?.successful_orders ?? 0 }} Delivered Orders)</span>
                        </div>
                    </div>
                    <div>
                        <span v-if="profile.can_pay_on_delivery" class="px-3 py-1.5 rounded-full text-xs font-bold bg-green-100 text-green-700 inline-flex items-center gap-1">
                            <i class="fa-solid fa-circle-check"></i> Pay on Delivery Eligible
                        </span>
                        <span v-else class="px-3 py-1.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 inline-flex items-center gap-1">
                            <i class="fa-solid fa-lock"></i> Pay Online Only
                        </span>
                    </div>
                </div>
                <form @submit.prevent="save" id="formElem">
                    <div class="row">
                        <div class="col-12 sm:col-6">
                            <label for="first_name" class="text-xs capitalize mb-1 text-heading">{{ $t('label.first_name') }}</label>
                            <input id="first_name" type="text" v-model="form.first_name" :class="errors.first_name ? 'invalid' : ''"
                                class="w-full h-12 text-sm rounded-lg border px-4 text-heading border-[#D9DBE9]">
                            <small class="db-field-alert" v-if="errors.first_name">
                                {{ errors.first_name[0] }}
                            </small>
                        </div>
                        <div class="col-12 sm:col-6">
                            <label for="last_name" class="text-xs capitalize mb-1 text-heading">{{ $t('label.last_name') }}</label>
                            <input id="last_name" type="text" v-model="form.last_name" :class="errors.last_name ? 'invalid' : ''"
                                class="w-full h-12 text-sm rounded-lg border px-4 text-heading border-[#D9DBE9]">
                            <small class="db-field-alert" v-if="errors.last_name">
                                {{ errors.last_name[0] }}
                            </small>
                        </div>
                        <div class="col-12 sm:col-6">
                            <label for="email" class="text-xs capitalize mb-1 text-heading">{{ $t('label.email') }}</label>
                            <input id="email" type="email" v-model="form.email" :class="errors.email ? 'invalid' : ''"
                                class="w-full h-12 text-sm rounded-lg border px-4 text-heading border-[#D9DBE9]">
                            <small class="db-field-alert" v-if="errors.email">
                                {{ errors.email[0] }}
                            </small>
                        </div>
                        <div class="col-12 sm:col-6">
                            <label for="phone" class="text-xs capitalize mb-1 text-heading">{{ $t('label.phone') }}</label>
                            <div class="w-full h-12 rounded-lg border px-4 flex items-center border-[#D9DBE9]" :class="errors.phone ? 'invalid' : ''">
                                <div class="w-fit flex-shrink-0 dropdown-group">
                                    <button type="button" class="flex items-center gap-1 dropdown-btn">
                                        {{ flag }}
                                        <span class="whitespace-nowrap flex-shrink-0 text-xs">
                                            {{ form.country_code }}
                                        </span>
                                        <input type="hidden" v-model="form.country_code">
                                    </button>
                                </div>
                                <input id="phone" type="text" v-on:keypress="phoneNumber($event)" v-model="form.phone"
                                    class="pl-4 text-sm w-full h-full text-heading">
                            </div>
                            <small class="db-field-alert" v-if="errors.phone">
                                {{ errors.phone[0] }}
                            </small>
                        </div>
                        <div class="col-12">
                            <button
                                class="w-full h-12 text-center capitalize font-medium rounded-3xl text-white bg-primary">
                                {{ $t('button.update_profile') }}
                            </button>
                        </div>
                    </div>
                </form>

                <div class="mt-8 pt-6 border-t border-[#EFF0F6]">
                    <h3 class="capitalize mb-3 text-base font-semibold text-heading flex items-center gap-2">
                        <i class="fa-solid fa-palette text-primary"></i>
                        <span>Appearance / Theme</span>
                    </h3>
                    <p class="text-xs text-paragraph mb-4">Choose your preferred display theme for Bwibo Restaurant.</p>
                    <div class="grid grid-cols-2 gap-4">
                        <button type="button" @click="setThemeMode('light')"
                            :class="activeTheme === 'light' ? 'border-primary bg-amber-50/60 text-primary shadow-xs' : 'border-gray-200 bg-white text-heading hover:bg-gray-50'"
                            class="p-4 rounded-xl border-2 transition-all flex flex-col items-center justify-center gap-2 font-semibold text-sm">
                            <i class="fa-solid fa-sun text-2xl text-amber-500"></i>
                            <span>Light Theme</span>
                            <span v-if="activeTheme === 'light'" class="text-[11px] font-bold text-primary flex items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> Active
                            </span>
                        </button>
                        <button type="button" @click="setThemeMode('dark')"
                            :class="activeTheme === 'dark' ? 'border-primary bg-indigo-950/40 text-primary shadow-xs' : 'border-gray-200 bg-white text-heading hover:bg-gray-50'"
                            class="p-4 rounded-xl border-2 transition-all flex flex-col items-center justify-center gap-2 font-semibold text-sm">
                            <i class="fa-solid fa-moon text-2xl text-indigo-400"></i>
                            <span>Dark Theme</span>
                            <span v-if="activeTheme === 'dark'" class="text-[11px] font-bold text-primary flex items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> Active
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import alertService from "../../../../services/alertService";
import appService from "../../../../services/appService";
import LoadingComponent from "../../components/LoadingComponent";

export default {
    name: "EditProfileComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            form: {
                first_name: "",
                last_name: "",
                email: "",
                phone: "",
                country_code: ""
            },
            flag: "",
            errors: {},
        };
    },
    mounted() {
        try {
            this.loading.isActive = true;
            const profile = this.$store.getters.authInfo;
            const countryCode = this.$store.getters['frontendCountryCode/show'];
            this.form = {
                first_name: profile.first_name,
                last_name: profile.last_name,
                email: profile.email,
                phone: profile.phone,
                country_code: countryCode.calling_code,
            };
            this.flag = countryCode.flag_emoji;
            this.loading.isActive = false;
        } catch (err) {
            this.loading.isActive = false;
            alertService.error(err);
        }
    },
    computed: {
        profile: function () {
            return this.$store.getters.authInfo || {};
        },
        countryCode: function () {
            return this.$store.getters['frontendCountryCode/show'];
        },
        activeTheme: function () {
            const globalState = this.$store.getters['globalState/lists'] || {};
            return globalState.theme_mode || window.localStorage.getItem('store_theme_mode') || 'light';
        }
    },
    methods: {
        setThemeMode(mode) {
            window.localStorage.setItem('store_theme_mode', mode);
            this.$store.dispatch('globalState/set', { theme_mode: mode });
            alertService.success("Theme updated to " + mode + " mode.");
        },
        phoneNumber(e) {
            return appService.phoneNumber(e);
        },
        save: function () {
            try {
                this.loading.isActive = true;
                this.$store.dispatch("frontendEditProfile/updateProfile", this.form).then((res) => {
                    this.$store.dispatch('updateAuthInfo', res.data.data).then(res => {
                        this.loading.isActive = false;
                        alertService.successFlip(1, this.$t("menu.profile"));
                        this.errors = {};
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err);
                    });
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
    },
    watch: {
        countryCode: {
            deep: true,
            handler(country) {
                this.flag = country.flag_emoji;
                this.form.country_code = country.calling_code;
            }
        }
    }
}
</script>
