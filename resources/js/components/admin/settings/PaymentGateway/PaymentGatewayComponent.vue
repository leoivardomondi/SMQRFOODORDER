<template>
    <LoadingComponent :props="loading"/>
    <div id="payment" class="db-tab-div active pt-4 sm:pt-6">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 mb-5">
            <button @click="selectActive(index)"
                    class="db-tab-sub-btn w-full min-h-11 px-4 py-2.5 rounded-lg text-left transition bg-white hover:text-primary hover:bg-primary/5 flex items-center justify-between gap-2"
                    :data-tab="'#' + paymentGateway.slug" v-for="(paymentGateway, index) in paymentGateways.slice(0,3)"
                    :key="paymentGateway.slug" :class="index === selectIndex ? 'active' : ''">
                <span class="block capitalize whitespace-normal break-words text-[15px] leading-5">
                    {{ paymentGateway.name }}
                </span>
                <span v-if="paymentGateway.is_primary" class="text-amber-500 font-bold text-xs" title="Primary Gateway">★</span>
            </button>

            <div v-if="paymentGateways.length > 3" class="dropdown-group w-full">
                <button
                    class="dropdown-btn w-full flex items-center gap-3 h-10 px-4 rounded-lg transition bg-white hover:text-primary hover:bg-primary/5">
                    <i class="fa-solid fa-circle-chevron-down text-sm"></i>
                    <span class="capitalize whitespace-nowrap text-[15px]"> {{ $t('label.more_gateway') }}</span>
                </button>
                <div class="w-full dropdown-list absolute top-[42px] right-0 z-30 p-2 rounded-md bg-white shadow-lg">
                    <button @click="selectActive(index+3)"
                            class="db-tab-sub-btn w-full flex items-center whitespace-nowrap justify-between my-0.5 gap-2.5 pl-3 pr-4 py-1.5 text-sm rounded-md capitalize transition text-gray-500 hover:text-primary hover:bg-primary/5"
                            :data-tab="'#' + paymentGateway.slug"
                            v-for="(paymentGateway, index) in paymentGateways.slice(3, paymentGateways.length)"
                            :key="paymentGateway.slug" :class="index+3 === selectIndex ? 'active' : ''">
                        <span>{{ paymentGateway.name }}</span>
                        <span v-if="paymentGateway.is_primary" class="text-amber-500 font-bold text-xs">★ Primary</span>
                    </button>
                </div>
            </div>
        </div>
        <div v-if="!paymentGateways.length && !loading.isActive" class="py-10 text-center text-sm text-gray-500">
            No payment gateways found.
        </div>
        <div :id="paymentGateway.slug" class="db-card db-tab-sub-div" v-for="(paymentGateway, index) in paymentGateways"
             :key="paymentGateway.slug" :class="index === selectIndex ? 'active' : ''">
            <div class="db-card-header flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <h3 class="db-card-title">{{ paymentGateway.name }}</h3>
                    <span v-if="paymentGateway.is_primary" class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 border border-amber-300">
                        <i class="fa-solid fa-star text-amber-500 text-xs"></i>
                        Primary Gateway
                    </span>
                </div>
                <button v-if="!paymentGateway.is_primary && paymentGateway.slug !== 'cash-on-delivery'" 
                        type="button" 
                        @click="setPrimary(paymentGateway.slug)" 
                        class="px-3.5 py-1.5 text-xs font-semibold rounded-lg border border-primary text-primary hover:bg-primary hover:text-white transition-all flex items-center gap-1.5 shadow-2xs">
                    <i class="fa-regular fa-star"></i>
                    Set as Primary
                </button>
            </div>
            <div class="db-card-body">
                <form @submit.prevent="save(index)" :id="'formElem' + index">
                    <div class="form-row">
                        <input type="hidden" :value="paymentGateway.slug" name="payment_type">

                        <div v-if="paymentGateway.slug === 'cash-on-delivery'" class="form-col-12">
                            <input type="hidden" name="cash_on_delivery_status" :value="paymentGateway.status">
                            <div class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-[#DBDEE0] p-4">
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-heading">Enable Cash on Delivery</h4>
                                    <p class="mt-1 text-sm leading-5 text-paragraph">
                                        Show Pay on Delivery at checkout and allow customers to place cash orders.
                                    </p>
                                </div>
                                <label class="relative inline-flex shrink-0 cursor-pointer items-center">
                                    <input type="checkbox" class="peer sr-only"
                                        :checked="paymentGateway.status === enums.activityEnum.ENABLE"
                                        @change="paymentGateway.status = $event.target.checked ? enums.activityEnum.ENABLE : enums.activityEnum.DISABLE">
                                    <span class="h-7 w-12 rounded-full bg-gray-300 transition peer-checked:bg-primary
                                        after:absolute after:left-1 after:top-1 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-transform after:content-[''] peer-checked:after:translate-x-5"></span>
                                    <span class="ml-3 min-w-[62px] text-sm font-semibold text-heading">
                                        {{ paymentGateway.status === enums.activityEnum.ENABLE ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="form-col-12 sm:form-col-6" v-for="paymentGatewayOption in visibleOptions(paymentGateway)"
                             :key="paymentGatewayOption.option">
                            <label :for="paymentGatewayOption.option" class="db-field-title">
                                {{ $t("label." + paymentGatewayOption.option) }}
                            </label>
                            <input v-if="paymentGatewayOption.type === enums.inputTypeEnum.TEXT" type="text"
                                   :value="paymentGatewayOption.value"
                                   v-bind:class="errors[paymentGatewayOption.option] ? 'invalid' : ''"
                                   :name="paymentGatewayOption.option" :id="paymentGatewayOption.option"
                                   class="db-field-control"/>

                            <select v-else class="db-field-control" :id="paymentGatewayOption.option"
                                    :name="paymentGatewayOption.option"
                                    v-bind:class="errors[paymentGatewayOption.option] ? 'invalid' : ''">
                                <option :value="index" :selected="index === paymentGatewayOption.value"
                                        v-for="(activity, index) in paymentGatewayOption.activities">
                                    {{ $t("label." + activity) }}
                                </option>
                            </select>

                            <small class="db-field-alert" v-if="errors[paymentGatewayOption.option]">{{
                                    errors[paymentGatewayOption.option][0]
                                }}</small>
                        </div>
                        <div class="form-col-12">
                            <button type="submit" :id="'formButton' + index" class="db-btn text-white bg-primary">
                                <i class="lab lab-save"></i>
                                <span>{{ $t("button.save") }}</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../components/LoadingComponent";
import appService from "../../../../services/appService";
import alertService from "../../../../services/alertService";
import inputTypeEnum from "../../../../enums/modules/inputTypeEnum";
import activityEnum from "../../../../enums/modules/activityEnum";

export default {
    name: "PaymentGatewayComponent",
    components: {LoadingComponent},
    data() {
        return {
            loading: {
                isActive: false,
            },
            search: {
                paginate: 0,
                order_column: "id",
                order_type: "asc",
                excepts: "2"
            },
            selectIndex: 0,
            enums: {
                inputTypeEnum: inputTypeEnum,
                activityEnum: activityEnum
            },
            errors: {},

        };
    },
    computed: {
        paymentGateways: function () {
            let list = [...this.$store.getters["paymentGateway/lists"]];
            // Move primary gateway to position #2 (index 1) if it's not cash-on-delivery
            const primaryIndex = list.findIndex(g => g.is_primary && g.slug !== 'cash-on-delivery');
            if (primaryIndex > 1) {
                const [primary] = list.splice(primaryIndex, 1);
                list.splice(1, 0, primary);
            } else if (primaryIndex === -1) {
                const prefIndex = list.findIndex(g => (g.slug === 'pesapal' || g.slug === 'paystack') && g.slug !== 'cash-on-delivery');
                if (prefIndex > 1) {
                    const [pref] = list.splice(prefIndex, 1);
                    list.splice(1, 0, pref);
                }
            }
            return list;
        },
    },
    mounted() {
        try {
            this.loading.isActive = true;
            this.$store.dispatch("paymentGateway/lists", this.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        } catch (err) {
            this.loading.isActive = false;
            alertService.error(err);
        }
    },
    methods: {
        setPrimary: function (slug) {
            try {
                this.loading.isActive = true;
                axios.post('/admin/setting/payment-gateway/set-primary', { primary_gateway: slug }).then((res) => {
                    this.loading.isActive = false;
                    alertService.success(res.data.message);
                    this.$store.dispatch("paymentGateway/lists", this.search);
                }).catch((err) => {
                    this.loading.isActive = false;
                    alertService.error(err.response?.data?.message || "Failed to set primary gateway.");
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        visibleOptions: function (paymentGateway) {
            return paymentGateway.options.filter((option) => option.option !== 'cash_on_delivery_status');
        },
        save: function (index) {
            try {
                let form = document.getElementById("formElem" + index);
                let formDataObj = {};
                [...form.elements].filter((el) => el.tagName !== 'BUTTON' && el.name).forEach((item) => {
                    formDataObj[item.name] = item.value;
                });

                this.loading.isActive = true;
                this.$store.dispatch("paymentGateway/save", {form: formDataObj, search: this.search}).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(res.config.method === "put" ?? 0, this.$t("menu.payment_gateway"));
                    this.errors = {};
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        selectActive: function (index) {
            this.selectIndex = index;
        }
    }
};
</script>
