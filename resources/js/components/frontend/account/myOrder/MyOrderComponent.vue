<template>
    <LoadingComponent :props="loading" />
    <section class="orders-page pt-6 pb-24 sm:pt-8 sm:pb-16">
        <div class="container max-w-3xl">
            <router-link :to="{ name: 'frontend.home' }" class="mb-3 inline-flex items-center gap-2 text-primary">
                <i class="lab lab-undo lab-font-size-16"></i>
                <span class="text-xs font-medium leading-6">{{ $t('label.back_to_home') }}</span>
            </router-link>
            <div class="flex items-start flex-col md:flex-row gap-6">
                <div class="w-full">
                    <h3 class="capitalize font-medium text-[26px] leading-[40px] mb-4 pl-5 md:pl-0 text-[#008BBA]">
                        {{ $t('label.active_orders') }}
                    </h3>
                    <ul class="w-full p-4 rounded-2xl shadow-xs flex flex-col gap-4 bg-white"
                        v-if="activeOrders.length > 0">
                        <li class="w-full rounded-2xl bg-white" v-for="activeOrder in activeOrders" :key="activeOrder">
                            <div class="w-full rounded-lg py-2 px-3 flex items-center gap-5 border border-[#EFF0F6]">
                                <i class="lab lab-reserve lab-font-size-32 lab-color-blue"></i>
                                <div class="w-full">
                                    <div class="flex flex-wrap items-center gap-y-1 gap-x-3">
                                        <p class="text-sm leading-6 font-rubik">{{ $t("label.order_id") }}:
                                            <span class="text-heading">#
                                                {{ activeOrder.order_serial_no }}
                                            </span>
                                        </p>
                                        <span :class="orderStatusClass(activeOrder.status)">
                                            {{ enums.orderStatusEnumArray[activeOrder.status] }}
                                        </span>
                                    </div>
                                    <p class="text-xs font-light font-rubik mb-1">{{
                                        activeOrder.order_datetime
                                    }}
                                    </p>
                                    <p class="text-sm font-normal font-rubik capitalize mb-2 text-[#00749B]">
                                        {{ enums.orderTypeEnumArray[activeOrder.order_type] }}
                                    </p>
                                    <div class="flex flex-wrap gap-3 items-center justify-between">
                                        <p class="text-sm leading-6 font-rubik capitalize text-heading">{{
                                            $t("label.total")
                                        }}: <span class="font-medium">{{ activeOrder.total_currency_price }}</span>
                                        </p>
                                        <router-link
                                            :to="{ name: 'frontend.myOrder.details', params: { id: activeOrder.id } }"
                                            class="text-[10px] leading-4 font-medium font-rubik flex items-center gap-1.5 text-primary">
                                            {{ $t("label.see_details") }}
                                            <i class="lab lab-arrow-right rtl:rotate-180 lab-font-size-13"></i>
                                        </router-link>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="w-full">
                    <h3 class="capitalize font-medium text-[26px] leading-[40px] mb-4 pl-5 md:pl-0 text-[#008BBA]">
                        {{ $t('label.previous_orders') }}
                    </h3>
                    <ul class="w-full p-4 rounded-2xl shadow-xs flex flex-col gap-4 bg-white"
                        v-if="previousOrders.length > 0">
                        <li class="w-full rounded-lg py-2 px-3 flex items-center gap-5 border border-[#EFF0F6]"
                            v-for="previousOrder in previousOrders" :key="previousOrder">
                            <i class="lab lab-reserve lab-font-size-32 lab-color-blue"></i>
                            <div class="w-full">
                                <div class="flex flex-wrap items-center gap-y-1 gap-x-3">
                                    <p class="text-sm leading-6 font-rubik">{{ $t("label.order_id") }}: <span
                                            class="text-heading">#{{
                                                previousOrder.order_serial_no
                                            }}</span></p>
                                    <span :class="orderStatusClass(previousOrder.status)">
                                        {{ enums.orderStatusEnumArray[previousOrder.status] }}
                                    </span>
                                </div>
                                <p class="text-xs font-light font-rubik mb-1">{{
                                    previousOrder.order_datetime
                                }}</p>
                                <p class="text-sm font-normal font-rubik capitalize mb-2 text-[#00749B]">
                                    {{ enums.orderTypeEnumArray[previousOrder.order_type] }}
                                </p>
                                <div class="flex flex-wrap gap-3 items-center justify-between">
                                    <p class="text-sm leading-6 font-rubik capitalize text-heading">{{
                                        $t("label.total")
                                    }}:
                                        <span class="font-medium">{{ previousOrder.total_currency_price }}</span>
                                    </p>
                                    <router-link
                                        :to="{ name: 'frontend.myOrder.details', params: { id: previousOrder.id } }"
                                        class="text-[10px] leading-4 font-medium font-rubik flex items-center gap-1.5
                                                                                                                                                                                                                        text-primary">
                                        {{ $t("label.see_details") }}
                                        <i class="lab lab-arrow-right lab-font-size-13"></i>
                                    </router-link>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="flex items-center justify-between border-gray-200 bg-white px-4 py-6">
                        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                            <PaginationBox :pagination="pagination" :method="previousOrderList" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>


import orderStatusEnum from "../../../../enums/modules/orderStatusEnum";
import appService from "../../../../services/appService";
import LoadingComponent from "../../components/LoadingComponent";
import orderTypeEnum from "../../../../enums/modules/orderTypeEnum";
import PaginationSMBox from "../../../admin/components/pagination/PaginationSMBox"
import PaginationBox from "../../../admin/components/pagination/PaginationBox"
import PaginationTextComponent from "../../../admin/components/pagination/PaginationTextComponent"
import activityEnum from "../../../../enums/modules/activityEnum";
import paymentStatusEnum from "../../../../enums/modules/paymentStatusEnum";


export default {
    name: "MyOrderComponent",
    components: {
        LoadingComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                activityEnum: activityEnum,
                paymentStatusEnum: paymentStatusEnum,
                orderStatusEnum: orderStatusEnum,
                orderTypeEnum: orderTypeEnum,
                orderStatusEnumArray: {
                    [orderStatusEnum.PENDING]: this.$t("label.pending"),
                    [orderStatusEnum.ACCEPT]: this.$t("label.accept"),
                    [orderStatusEnum.PREPARING]: this.$t("label.preparing"),
                    [orderStatusEnum.PREPARED]: this.$t("label.prepared"),
                    [orderStatusEnum.OUT_FOR_DELIVERY]: this.$t("label.out_for_delivery"),
                    [orderStatusEnum.DELIVERED]: this.$t("label.delivered"),
                    [orderStatusEnum.CANCELED]: this.$t("label.canceled"),
                    [orderStatusEnum.REJECTED]: this.$t("label.rejected"),
                    [orderStatusEnum.RETURNED]: this.$t("label.returned"),
                },
                orderTypeEnumArray: {
                    [orderTypeEnum.DELIVERY]: this.$t("label.delivery"),
                    [orderTypeEnum.TAKEAWAY]: this.$t("label.takeaway")
                },
            },
            active: {
                excepts: orderStatusEnum.DELIVERED + "|" + orderStatusEnum.CANCELED + "|" + orderStatusEnum.REJECTED + "|" + orderStatusEnum.RETURNED,
            },
            previous: {
                paginate: 1,
                page: 1,
                per_page: 5,
                excepts: orderStatusEnum.PENDING + "|" + orderStatusEnum.ACCEPT + "|" + orderStatusEnum.PREPARING + "|" + orderStatusEnum.PREPARED + "|" + orderStatusEnum.OUT_FOR_DELIVERY,
            }
        };
    },
    mounted() {
        try {
            this.loading.isActive = true;
            this.$store.dispatch('frontendOrder/activeOrder', {
                excepts: this.active.excepts,
            }).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });

            this.previousOrderList();

            if (Object.keys(this.$route.query).length > 0 && this.$route.query.id) {
                this.$router.push({ name: 'frontend.myOrder.details', params: { id: this.$route.query.id } });
            }
        } catch (err) {
            this.loading.isActive = false;
        }
    },
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        activeOrders: function () {
            return this.$store.getters['frontendOrder/activeOrder'];
        },
        previousOrders: function () {
            return this.$store.getters['frontendOrder/previousOrder'];
        },
        order: function () {
            return this.$store.getters['frontendOrder/show'];
        },
        pagination: function () {
            return this.$store.getters["frontendOrder/pagination"];
        },
        paginationPage: function () {
            return this.$store.getters["frontendOrder/page"];
        },
    },
    methods: {
        orderStatusClass: function (status) {
            return appService.orderStatusClass(status);
        },

        previousOrderList: function (page = 1) {
            this.loading.isActive = true;
            this.previous.page = page;
            this.$store.dispatch('frontendOrder/previousOrder', this.previous).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        }
    }
}
</script>
