<template>
    <aside @click="closeBackdrop($event)" id="cart"
        class="w-screen h-full fixed top-[58px] lg:top-[74px] left-0 z-60 opacity-0 invisible bg-black/60 transition">
        <div class="max-w-sm w-full h-screen absolute top-0 right-0 translate-x-full bg-white transition">
            <div :class="carts.length === 0 || orderType === null ? 'flex items-center justify-center flex-col text-center overflow-y-auto' : 'thin-scrolling'"
                class="h-[calc(96vh-200px)] lg:h-[calc(100vh-220px)] p-4 relative">
                <h3 :class="carts.length === 0 || orderType === null ? 'mb-16' : 'mb-5'"
                    class="text-xl font-semibold capitalize text-center">
                    {{ $t('label.my_cart') }}
                </h3>
                <button @click.prevent="closeCanvas('cart')"
                    class="fa-solid fa-xmark absolute top-2 rtl:left-3 ltr:right-3 text-white bg-[#FB4E4E] xmark-btn"></button>

                <div v-if="(carts.length === 0 || orderType === null) || (setting.order_setup_delivery === activityEnum.DISABLE && setting.order_setup_takeaway === activityEnum.DISABLE)"
                    class="flex items-center justify-center flex-col text-center flex-col text-center overflow-y-auto">
                    <img class="w-40 mb-12" :src="setting.image_cart" alt="gif">
                    <p v-if="orderType === null || (setting.order_setup_delivery === activityEnum.DISABLE && setting.order_setup_takeaway === activityEnum.DISABLE)"
                        class="text-sm max-w-xs">{{ $t('message.delivery_and_takeaway') }}</p>
                    <p v-else class="text-sm max-w-xs">{{ $t('message.empty_cart') }}</p>
                </div>

                <div v-if="carts.length > 0 && orderType !== null && (setting.order_setup_delivery === activityEnum.ENABLE || setting.order_setup_takeaway === activityEnum.ENABLE)"
                    class="flex items-center rounded-2xl w-fit mx-auto mb-6 text-[#008BBA] bg-[#BDEFFF]">
                    <div v-if="setting.order_setup_delivery === activityEnum.ENABLE" class="relative cursor-pointer">
                        <input @change="changeOrderType(orderTypeEnum.DELIVERY)" type="radio"
                            :value="orderTypeEnum.DELIVERY" :checked="orderType === orderTypeEnum.DELIVERY"
                            id="delivery"
                            class="cart-switch w-full h-full absolute top-0 left-0 opacity-0 cursor-pointer">
                        <label for="delivery"
                            class="py-1.5 px-3.5 rounded-2xl text-xs font-medium capitalize transition cursor-pointer">{{
                                $t('label.delivery')
                            }}</label>
                    </div>
                    <div v-if="setting.order_setup_takeaway === activityEnum.ENABLE" class="relative cursor-pointer">
                        <input @change="changeOrderType(orderTypeEnum.TAKEAWAY)" type="radio"
                            :value="orderTypeEnum.TAKEAWAY" :checked="orderType === orderTypeEnum.TAKEAWAY"
                            id="takeaway"
                            class="cart-switch w-full h-full absolute top-0 left-0 opacity-0 cursor-pointer">
                        <label for="takeaway"
                            class="py-1.5 px-3.5 rounded-2xl text-xs font-medium capitalize transition cursor-pointer">{{
                                $t('label.takeaway')
                            }}</label>
                    </div>
                </div>
                <div v-if="carts.length > 0 && orderType !== null && (setting.order_setup_delivery === activityEnum.ENABLE || setting.order_setup_takeaway === activityEnum.ENABLE)"
                    class="mb-5">
                    <div v-for="(cart, index) in carts"
                        class="mb-4 pb-4 border-b last:mb-0 last:pb-0 last:border-b-0 border-gray-100">
                        <div class="flex items-start gap-4">
                            <img class="w-20 h-20 rounded-xl object-cover flex-shrink-0" :src="cart.image" alt="thumbnail">
                            <div class="flex-1">
                                <a href="#" class="text-base font-bold text-gray-900 capitalize hover:underline mb-1 block">
                                    {{ cart.name }}
                                </a>
                                <p v-if="Object.keys(cart.item_variations.variations).length !== 0"
                                    class="capitalize text-xs text-gray-500 mb-1">
                                    <span v-for="(variation, variationName) in cart.item_variations.names">
                                        {{ variationName }}: {{ variation }}, &nbsp;
                                    </span>
                                </p>
                                <h3 class="text-sm font-bold text-gray-900 mb-2">{{
                                    currencyFormat(cart.total, setting.site_digit_after_decimal_point,
                                        setting.site_default_currency_symbol, setting.site_currency_position)
                                }}</h3>
                                
                                <div class="flex items-center gap-4 px-3 py-1.5 w-fit rounded-full bg-gray-100">
                                    <button @click.prevent="quantityDecrement(index)"
                                        :class="cart.quantity === 1 ? 'fa-trash-can text-red-500' : 'fa-minus text-gray-500'"
                                        class="fa-solid text-sm hover:text-black transition"></button>
                                    <input v-on:keypress="onlyNumber($event)" v-on:keyup="quantityUp(index, $event)"
                                        type="number" :value="cart.quantity"
                                        class="text-center w-6 text-sm font-bold bg-transparent text-gray-900 outline-none select-none pointer-events-none">
                                    <button @click.prevent="quantityIncrement(index)"
                                        class="fa-solid fa-plus text-sm text-gray-500 hover:text-black transition"></button>
                                </div>
                            </div>
                        </div>

                        <ul v-if="cart.item_extras.extras.length > 0 || cart.instruction !== ''"
                            class="flex flex-col gap-1.5">
                            <li v-if="cart.item_extras.extras.length > 0" class="flex gap-1">
                                <h3 class="capitalize text-xs w-fit whitespace-nowrap">{{ $t('label.extras') }}:</h3>
                                <p class="text-xs">
                                    <span v-for="extra in cart.item_extras.names">
                                        {{ extra }}, &nbsp;
                                    </span>
                                </p>
                            </li>

                            <li v-if="cart.instruction !== ''" class="flex gap-1">
                                <h3 class="capitalize text-xs w-fit whitespace-nowrap">
                                    {{ $t('label.instruction') }}:
                                </h3>
                                <p class="text-xs">{{ cart.instruction }}</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div v-if="carts.length > 0 && orderType !== null && (setting.order_setup_delivery === activityEnum.ENABLE || setting.order_setup_takeaway === activityEnum.ENABLE)"
                class="p-4 bg-white border-t border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="capitalize text-lg font-bold text-gray-900">{{ $t('label.subtotal') }}</h3>
                    <h4 class="text-lg font-bold text-gray-900">
                        {{
                            currencyFormat(subtotal, setting.site_digit_after_decimal_point,
                                setting.site_default_currency_symbol, setting.site_currency_position)
                        }}
                    </h4>
                </div>
                <router-link @click.prevent="closeSidebar" :to="{ name: 'frontend.checkout' }"
                    class="block text-center capitalize text-lg font-bold py-4 rounded-full w-full text-white bg-primary hover:bg-primary-dark transition shadow-lg">
                    {{ $t('button.proceed_checkout') }}
                </router-link>
            </div>
        </div>
    </aside>
</template>

<script>
import appService from "../../../services/appService";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum";
import activityEnum from "../../../enums/modules/activityEnum";

export default {
    name: "FrontendCartComponent",
    data() {
        return {
            orderTypeEnum: orderTypeEnum,
            activityEnum: activityEnum,
            localOrderType: null
        }
    },
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        carts: function () {
            return this.$store.getters['frontendCart/lists'];
        },
        subtotal: function () {
            return this.$store.getters['frontendCart/subtotal'];
        },
        orderType: function () {
            return this.$store.getters['frontendCart/orderType'];
        }
    },
    mounted() {
        window.setTimeout(() => {
            this.localOrderType = this.$store.getters['frontendCart/orderType'];
        }, 3000);
    },
    methods: {
        onlyNumber: function (e) {
            return appService.onlyNumber(e);
        },
        closeCanvas: function (id) {
            return appService.closeCanvas(id);
        },
        closeBackdrop: function (e) {
            return appService.closeBackdrop(e);
        },
        currencyFormat(amount, decimal, currency, position) {
            return appService.currencyFormat(amount, decimal, currency, position);
        },
        quantityUp: function (id, e) {
            if (e.target.value > 0) {
                this.$store.dispatch('frontendCart/quantity', { id: id, status: e.target.value }).then().catch();
            }
        },
        quantityIncrement: function (id) {
            this.$store.dispatch('frontendCart/quantity', { id: id, status: "increment" }).then().catch();
        },
        quantityDecrement: function (id) {
            this.$store.dispatch('frontendCart/quantity', { id: id, status: "decrement" }).then().catch();
        },
        closeSidebar: function () {
            const cart = document.getElementById('cart');
            const body = document.querySelector('body');
            cart?.classList?.remove('active');
            body.style.overflowY = "auto";
        },
        changeOrderType: function (e) {
            this.localOrderType = e;
            this.$store.dispatch('frontendCart/updateOrderType', this.localOrderType).then().catch();
        }
    }
}
</script>