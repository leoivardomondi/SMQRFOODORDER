<template>
    <LoadingComponent :props="loading" />
    <section class="pt-6 pb-24 sm:pt-8 sm:pb-16">
        <div class="w-auto max-w-[360px] mx-2 sm:mx-auto py-6 p-4 mb-6 sm:px-6 shadow-xs rounded-2xl bg-white">
            <h2 class="capitalize mb-6 text-center text-[22px] font-semibold leading-[34px] text-heading">
                {{ carts.length > 0 ? 'Checkout Details' : $t('label.welcome_back') }}
            </h2>
            <div v-if="errors.validation"
                class="bg-red-100 border border-red-400 text-red-700 px-3 py-3 mb-5 rounded relative flex items-start gap-2"
                role="alert">
                <span class="block sm:inline text-sm flex-auto">{{ errors.validation }}</span>
                <button type="button" @click="close" class="leading-none">
                    <i class="lab lab-close-circle-line"></i>
                </button>
            </div>
            
            <form @submit.prevent="save">
                <div class="mb-4">
                    <label class="text-sm capitalize mb-1 text-heading">{{ $t('label.name') }}</label>
                    <input type="text" :class="errors.name ? 'invalid' : ''" v-model="props.form.name"
                        class="w-full h-12 rounded-lg border px-4 border-[#D9DBE9]" id="formName" placeholder="John Doe">
                    <small class="db-field-alert" v-if="errors.name">{{ errors.name[0] }}</small>
                </div>

                <div class="mb-6">
                    <label class="text-sm capitalize mb-1 text-heading">{{ $t('label.mobile_number') }}</label>
                    <div class="w-full h-12 rounded-lg border px-4 flex items-center border-[#D9DBE9]">
                        <div class="w-fit flex-shrink-0 dropdown-group">
                            <button type="button" class="flex items-center gap-1">
                                {{ flag }}
                                <span class="whitespace-nowrap flex-shrink-0 text-sm">{{ props.form.code }}</span>
                                <input type="hidden" v-model="props.form.code">
                            </button>
                        </div>
                        <input v-model="props.form.phone" v-on:keypress="phoneNumber($event)" v-on:keyup.enter="save"
                            :class="errors.phone
                                ? 'invalid' : ''" type="tel" inputmode="numeric" autocomplete="tel-national"
                            :placeholder="phonePlaceholder" :maxlength="phoneDigitLength" id="phone"
                            class="pl-4 text-sm w-full h-full text-heading placeholder:text-gray-400" />
                    </div>
                    <small class="db-field-alert" v-if="errors.phone">
                        {{ errors.phone[0] }}
                    </small>
                </div>
                
                <button type="submit"
                    class="w-full h-12 text-center capitalize font-medium rounded-3xl mb-6 text-white bg-primary">
                    Guest Login
                </button>

                <div class="flex items-center justify-center gap-2 mb-4">
                    <button type="button" @click="showStaffLogin = !showStaffLogin" class="text-xs font-medium text-primary">
                        {{ showStaffLogin ? 'Hide Staff Login' : 'Staff / Admin Login' }}
                    </button>
                </div>
            </form>

            <form v-if="showStaffLogin" @submit.prevent="login" class="pt-4 border-t border-gray-200 mt-4">
                <div class="mb-4">
                    <label for="formEmail" class="text-sm capitalize mb-1 text-heading">{{ $t('label.email') }}</label>
                    <input type="text" :class="errors.email ? 'invalid' : ''" v-model="form.email"
                        class="w-full h-12 rounded-lg border px-4 border-[#D9DBE9]" id="formEmail">
                    <small class="db-field-alert" v-if="errors.email">{{ errors.email[0] }}</small>
                </div>
                <div class="mb-4">
                    <label for="formPassword" class="text-sm capitalize mb-1 text-heading">{{
                        $t('label.password')
                        }}</label>
                    <input autocomplete="off" type="password" :class="errors.password ? 'invalid' : ''"
                        v-model="form.password" class="w-full h-12 rounded-lg border px-4 border-[#D9DBE9]"
                        id="formPassword">
                    <small class="db-field-alert" v-if="errors.password">{{ errors.password[0] }}</small>
                </div>
                <div class="flex items-center justify-between mb-6">
                    <div class="db-field-checkbox p-0">
                        <div class="custom-checkbox w-3 h-3">
                            <input type="checkbox" id="checkbox2" class="custom-checkbox-field">
                            <i
                                class="fa-solid fa-check custom-checkbox-icon leading-[9px] text-[9px] rounded-[3px] border-[#6E7191]"></i>
                        </div>
                        <label for="checkbox2" class="db-field-label text-xs text-heading">
                            {{ $t('label.remember_me') }}
                        </label>
                    </div>
                    <router-link :to="{ name: 'auth.forgetPassword' }"
                        class="capitalize text-xs font-medium transition text-primary hover:underline">
                        {{ $t('button.forget_password') }}
                    </router-link>
                </div>
                <button type="submit"
                    class="w-full h-12 text-center capitalize font-medium rounded-3xl mb-6 text-white bg-primary">
                    {{ $t('button.login') }}
                </button>
            </form>
        </div>

        <div v-if="demo === 'true' || demo === 'TRUE' || demo === 'True' || demo === '1' || demo === 1"
            class="container max-w-[360px] py-6 p-4 sm:px-6 shadow-xs rounded-2xl bg-white">
            <h2 class="mb-6 text-center text-lg font-medium text-heading">{{ $t('message.for_quick_demo') }}</h2>
            <nav class="grid grid-cols-2 gap-3">
                <button @click.prevent="setupCredit('admin')"
                    class="click-to-prop w-full h-10 leading-10 rounded-lg text-center text-sm capitalize text-white bg-orange-500"
                    id="adminClick">
                    {{ $t('label.admin') }}
                </button>
                <button @click.prevent="setupCredit('customer')"
                    class="click-to-prop w-full h-10 leading-10 rounded-lg text-center text-sm capitalize text-white bg-emerald-500"
                    id="customerClick">
                    {{ $t('label.customer') }}
                </button>
                <button @click.prevent="setupCredit('branchManager')"
                    class="click-to-prop w-full h-10 leading-10 rounded-lg text-center text-sm capitalize text-white bg-sky-600"
                    id="branchManagerClick">
                    {{ $t('label.branch_manager') }}
                </button>
                <button @click.prevent="setupCredit('posOperator')"
                    class="click-to-prop w-full h-10 leading-10 rounded-lg text-center text-sm capitalize text-white bg-purple-500"
                    id="posOperatorClick">
                    {{ $t('label.pos_operator') }}
                </button>
                <button @click.prevent="setupCredit('chef')"
                    class="click-to-prop w-full h-10 leading-10 rounded-lg text-center text-sm capitalize text-white bg-blue-500"
                    id="chefClick">
                    {{ $t('label.chef_kitchen') }}
                </button>
            </nav>
        </div>
    </section>
</template>

<script>
import router from "../../../router";
import LoadingComponent from "../components/LoadingComponent";
import alertService from "../../../services/alertService";
import activityEnum from "../../../enums/modules/activityEnum";
import ENV from "../../../config/env";
import { routes } from "../../../router";
import appService from "../../../services/appService";
import roleEnum from "../../../enums/modules/roleEnum";

export default {
    name: "LoginComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            form: {
                email: "",
                password: ""
            },
            props: {
                form: {
                    name: "",
                    phone: "",
                    code: "",
                },
            },
            flag: "",
            country_code: "",
            enums: {
                activityEnum: activityEnum
            },
            errors: {},
            permissions: {},
            firstMenu: null,
            demo: ENV.DEMO,
            showStaffLogin: false
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch('frontendSetting/lists').then(res => {
            this.defaultCountryCode = res.data.data.company_country_code;
            this.$store.dispatch('frontendCountryCode/show', this.defaultCountryCode).then(res => {
                this.props.form.code = res.data.data.calling_code;
                this.country_code = res.data.data.calling_code;
                this.flag = res.data.data.flag_emoji;
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        }).catch((err) => {
            this.loading.isActive = false;
        });
    },
    computed: {
        carts: function () {
            return this.$store.getters['frontendCart/lists'];
        },
        setting: function () {
            return this.$store.getters['frontendSetting/lists']
        },
        permission: function () {
            return this.$store.getters.authPermission;
        },
        countryCode: function () {
            return this.$store.getters['frontendCountryCode/show'];
        },
        phoneDigitLength: function () {
            const configuredLength = parseInt(this.setting.site_default_phone_digit_length, 10);
            return Number.isInteger(configuredLength) && configuredLength > 0 ? configuredLength : 10;
        },
        phonePlaceholder: function () {
            if (String(this.props.form.code).replace(/\D/g, '') === '254') {
                return '0712 345 678';
            }

            const sample = '123456789012345'.slice(0, this.phoneDigitLength);
            return sample.replace(/(.{3})/g, '$1 ').trim();
        },
    },
    methods: {
        phoneNumber(e) {
            return appService.phoneNumber(e);
        },
        save: function () {
            try {
                this.loading.isActive = true;
                if (this.setting.site_phone_verification === activityEnum.DISABLE) {
                    this.props.form.token = "1000";
                    this.$store.dispatch("GuestLoginVerify", this.props.form).then((LoginRes) => {
                        this.$store.dispatch("GuestSignup/reset").then().catch();
                        this.loading.isActive = false;
                        this.errors = {};
                        this.props.form = {
                            name: "",
                            phone: "",
                            code: this.country_code,
                        };
                        alertService.success(LoginRes.data.message);
                        if (this.carts.length > 0) {
                            this.$router.push({ name: "frontend.checkout" });
                        } else {
                            this.$router.push({ name: "frontend.home" });
                        }
                    }).catch((err) => {
                        this.loading.isActive = false;
                        if (err.response && err.response.data && err.response.data?.message) {
                            alertService.error(err.response.data.message);
                        } else if (err.response && err.response.data && err.response.data.errors) {
                            this.errors = err.response.data.errors;
                        } else {
                            alertService.error("An error occurred");
                        }
                    });
                } else {
                    this.$store.dispatch("GuestSignup/otp", this.props.form).then((res) => {
                        this.loading.isActive = false;
                        this.errors = {};
                        this.props.form = {
                            name: "",
                            phone: "",
                            code: this.country_code,
                        };
                        alertService.success(res.data.message, 'bottom-center');
                        this.$router.push({
                            name: "auth.guestLoginVerify",
                        });
                    }).catch((err) => {
                        this.loading.isActive = false;
                        if (err.response && err.response.data && err.response.data?.message) {
                            alertService.error(err.response.data.message);
                        } else if (err.response && err.response.data && err.response.data.errors) {
                            this.errors = err.response.data.errors;
                        } else {
                            alertService.error("An error occurred");
                        }
                    });
                }
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        login: async function () {
            try {
                this.loading.isActive = true;
                await this.$store.dispatch('login', this.form).then(async (res) => {
                    this.loading.isActive = false;
                    alertService.success(res.data.message);

                    // Refresh route permissions before navigating into the admin area.
                    appService.recursiveRouter(routes, this.permission);

                    if (this.$store.getters.authInfo.role_id === roleEnum.ADMIN) {
                        await router.replace({ name: "admin.order.list" });
                    } else if (this.$store.getters.authInfo.role_id === roleEnum.DELIVERY_BOY) {
                        await router.replace({ name: "frontend.rider.deliveries" });
                    } else if (this.carts.length > 0) {
                        await router.replace({ name: "frontend.checkout" });
                    } else {
                        await router.replace({ name: "frontend.home" });
                    }

                }).catch((err) => {
                    this.loading.isActive = false;
                    if (err?.response?.data?.errors) {
                        this.errors = err.response.data.errors;
                    } else if (!String(err?.message || err).match(/dynamically imported module|Loading chunk/i)) {
                        alertService.error('Unable to complete login. Please try again.');
                    }
                })
            } catch (err) {
                this.loading.isActive = false;
            }
        },
        close: function () {
            this.errors = {}
        },
        setupCredit: function (e) {
            if (e === 'admin') {
                this.form.email = 'admin@example.com';
                this.form.password = '123456';
            } else if (e === 'customer') {
                this.form.email = 'customer@example.com';
                this.form.password = '123456';
            } else if (e === 'branchManager') {
                this.form.email = 'branchmanager@example.com';
                this.form.password = '123456';
            } else if (e === 'posOperator') {
                this.form.email = 'posoperator@example.com';
                this.form.password = '123456';
            } else if (e === 'chef') {
                this.form.email = 'chef@example.com';
                this.form.password = '123456';
            }
        }
    }
}
</script>
