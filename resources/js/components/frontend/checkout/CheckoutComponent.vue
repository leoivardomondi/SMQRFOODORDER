<template>
    <LoadingComponent :props="loading" />
    <section class="checkout-page py-4 sm:py-6">
        <div class="container max-w-[965px]">
            <router-link :to="{ name: 'frontend.home' }"
                class="text-xs font-medium inline-flex mb-3 items-center gap-2 text-primary">
                <i class="lab lab-undo lab-font-size-16"></i>
                <span>{{ $t('label.back_to_home') }}</span>
            </router-link>
            <div class="row">
                <div class="col-12 md:col-7">
                    <div class="p-4 mb-4 rounded-2xl shadow-xs bg-white">
                        <h3 class="text-base font-semibold mb-1 text-heading">Your Details</h3>
                        <p class="text-xs text-gray-500 mb-4">
                            Enter your details to continue. We will remember them and show your saved delivery addresses on this browser.
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="sm:col-span-2">
                                <label for="checkout-customer-name" class="block text-xs font-medium mb-1 text-heading">Full name</label>
                                <input id="checkout-customer-name" type="text" autocomplete="name"
                                    v-model.trim="checkoutProps.form.customer_name" @blur="ensureGuestSession"
                                    placeholder="Example: Jane Wanjiku"
                                    class="w-full h-12 px-4 rounded-lg border border-[#EFF0F6] bg-gray-50 focus:border-primary focus:bg-white text-gray-900 transition" />
                            </div>
                            <div>
                                <label for="checkout-customer-phone" class="block text-xs font-medium mb-1 text-heading">Phone number</label>
                                <input id="checkout-customer-phone" type="tel" inputmode="tel" autocomplete="tel"
                                    v-model.trim="checkoutProps.form.customer_phone" @input="onPhoneInput" @blur="ensureGuestSession"
                                    placeholder="Example: 0712 345 678"
                                    class="w-full h-12 px-4 rounded-lg border border-[#EFF0F6] bg-gray-50 focus:border-primary focus:bg-white text-gray-900 transition" />
                            </div>
                            <div>
                                <label for="checkout-customer-email" class="block text-xs font-medium mb-1 text-heading">Email (optional)</label>
                                <input id="checkout-customer-email" type="email" inputmode="email" autocomplete="email"
                                    v-model.trim="checkoutProps.form.customer_email" @blur="ensureGuestSession"
                                    placeholder="jane@example.com"
                                    class="w-full h-12 px-4 rounded-lg border border-[#EFF0F6] bg-gray-50 focus:border-primary focus:bg-white text-gray-900 transition" />
                            </div>
                        </div>
                        <p v-if="guestLoginInProgress" class="mt-3 text-xs text-primary">Loading your saved checkout details...</p>
                        <p v-else-if="authStatus" class="mt-3 text-xs text-green-600">Your details are saved on this browser.</p>
                    </div>
                    <div class="p-4 mb-6 rounded-2xl shadow-xs bg-white">
                        <!-- Live Branch Weather Widget & Rain Advisory -->
                        <div v-if="weather && weather.temp_c" 
                             class="p-4 mb-4 rounded-xl border transition-all shadow-xs"
                             :class="weather.is_raining ? 'border-amber-400/80 bg-amber-50 text-amber-900 ring-1 ring-amber-400/30' : 'border-blue-200 bg-blue-50/60 text-blue-900'">
                            <div class="flex items-start gap-3">
                                <div class="p-2.5 rounded-xl shrink-0 text-2xl flex items-center justify-center" 
                                     :class="weather.is_raining ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700'">
                                    <i class="fa-solid" :class="weather.fa_icon || 'fa-cloud-sun'"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center justify-between gap-1 mb-1">
                                        <h4 class="font-bold text-sm leading-snug">
                                            Live Weather at {{ weather.branch_name || 'Branch' }}: {{ weather.condition_text }} ({{ weather.temp_c }}°C)
                                        </h4>
                                        <span class="text-[11px] opacity-75 font-medium bg-white/60 px-2 py-0.5 rounded-full border border-gray-200">
                                            Updated {{ weather.fetched_at }}
                                        </span>
                                    </div>
                                    <p v-if="weather.is_raining" class="text-xs leading-relaxed font-semibold text-amber-900 mt-1">
                                        🌧️ <strong>Weather Notice:</strong> It is currently raining near <strong>{{ weather.branch_name }}</strong>. Delivery riders are taking extra precautions for safety, so your order delivery may take a few extra minutes. Thank you for understanding!
                                    </p>
                                    <p v-else class="text-xs leading-relaxed text-blue-800 mt-1">
                                        ☀️ Clear weather around {{ weather.branch_name }}. Ideal conditions for fast order preparation and delivery!
                                    </p>
                                </div>
                            </div>
                        </div>

                        <h3 v-if="branches.length > 0"
                            class="capitalize font-medium mb-3 text-heading">{{
                                $t('label.select_branch')
                            }}</h3>

                        <div v-if="branches.length > 0"
                            class="flex flex-col gap-2.5 mb-4">
                            <button v-for="branch in branches" :key="branch.id || branch"
                                :class="checkoutProps.form.branch_id === branch.id 
                                        ? 'border-2 border-[#b79a4b] bg-[#b79a4b]/20 text-[#b79a4b] font-bold shadow-md ring-1 ring-[#b79a4b]/40' 
                                        : 'border border-gray-700 bg-gray-900/70 text-gray-300 font-medium hover:border-[#b79a4b]/60 hover:text-white'"
                                :value="branch.id"
                                class="w-full flex items-center justify-between p-3.5 rounded-xl text-left text-sm transition-all duration-200 cursor-pointer"
                                @click.prevent="changeBranch(branch)">
                                <div class="flex items-center gap-3 min-w-0">
                                    <i :class="checkoutProps.form.branch_id === branch.id ? 'fa-solid fa-circle-check text-[#b79a4b] text-lg shrink-0' : 'fa-regular fa-circle text-gray-400 text-base shrink-0'"></i>
                                    <span class="font-medium text-sm leading-snug break-words">{{ branch.name }}</span>
                                </div>
                            </button>
                        </div>

                        <div v-if="!checkoutProps.form.branch_id"
                            class="p-3 mb-4 rounded-xl border border-amber-500/30 bg-amber-500/10 text-amber-300 text-xs flex items-center gap-2.5">
                            <i class="fa-solid fa-store text-sm text-amber-400"></i>
                            <span>Please select a branch above to proceed with your order.</span>
                        </div>

                        <MapComponent :key="mapKey"
                            v-if="mapShow && checkoutProps.form.order_type === orderTypeEnum.TAKEAWAY && checkoutProps.form.branch_id"
                            :location="location" :position="branchPosition"
                            :setting="{ autocomplete: false, mouseEvent: false, currentLocation: false }" />

                        <div v-if="checkoutProps.form.order_type === orderTypeEnum.TAKEAWAY && checkoutProps.form.branch_id && branchAddress"
                            class="flex items-center gap-2 mb-3 mt-6">
                            <i class="lab lab-location text-xl text-primary"></i>
                            <span class="text-sm text-heading">{{ branchAddress }}</span>
                        </div>

                        <div v-if="checkoutProps.form.order_type === orderTypeEnum.DELIVERY" class="mb-5">
                            <div class="flex flex-wrap justify-between gap-5 mb-2.5">
                                <h4 class="capitalize font-medium"> {{ $t('label.delivery_address') }} </h4>
                                <div class="flex gap-3">
                                    <button v-if="Object.keys(localAddress).length !== 0" @click="editAddress"
                                        type="button"
                                        class="group text-xs capitalize font-medium flex items-center rounded-3xl py-1.5 px-3 gap-1 text-[#00749B] bg-[#D6F5FF] transition hover:text-white hover:bg-[#00749B]">
                                        <i class="lab lab-edit-2 lab-font-size-13"></i>
                                        <span>{{ $t('button.edit') }}</span>
                                    </button>
                                    <AddressComponent :getLocation="updateAddress" :props="addressProps" />
                                </div>
                            </div>
                            <div v-if="addresses.length > 0" class="grid grid-cols-2 sm:grid-cols-3 gap-3 active-group">
                                <label @click="changeAddress($event, address)"
                                    :class="checkoutProps.form.address_id === address.id ? 'active' : ''"
                                    v-for="address in addresses" :key="address.id" :for="address.label"
                                    class="checkout-address-card p-3 rounded-lg w-full border cursor-pointer">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="checkout-address-title flex items-center gap-2 text-xs">
                                            <i class="icon-home"></i>
                                            <span class="font-medium">{{ address.label }}</span>
                                        </div>
                                        <div class="custom-radio sm">
                                            <input type="radio" :id="address.label"
                                                v-model="checkoutProps.form.address_id" :value="address.id"
                                                class="custom-radio-field">
                                            <span class="custom-radio-span"></span>
                                        </div>
                                    </div>
                                    <div class="checkout-address-detail text-xs flex gap-2">
                                        <i class="icon-location1 mt-0.5"></i>
                                        <span v-if="address.apartment">{{ address.apartment }}, {{
                                            address.address
                                        }}</span>
                                        <span v-else>{{ address.address }}</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <h4 v-if="checkoutProps.form.order_type === orderTypeEnum.DELIVERY"
                                class="font-medium mb-2.5">{{ $t('label.preferred_time') }}</h4>
                            <h4 v-else class="font-medium mb-2.5">{{ $t('label.preferred_time_takeaway') }}</h4>
                            <div class="flex flex-wrap items-start gap-4">
                                <label v-if="Object.keys(nowTimeSlot).length > 0" :for="dayTakeEnum.NOW"
                                    @click="selectNowDeliveryTime(nowTimeSlot)"
                                    :class="schedule === dayTakeEnum.NOW ? 'bg-primary/5 border-primary/30' : 'bg-white border-gray-100'"
                                    class="checkout-time-card w-fit py-2 px-3 rounded-lg flex items-start gap-5 cursor-pointer border transition-all duration-300">
                                    <dl class="flex-auto">
                                        <dt class="text-sm font-medium whitespace-nowrap mb-1.5 text-heading">
                                            {{ $t('label.now') }}
                                        </dt>
                                        <dd class="text-sm whitespace-nowrap text-heading">
                                            {{ setting.order_setup_food_preparation_time }} {{ $t('label.minute') }}
                                        </dd>
                                    </dl>
                                    <div class="custom-radio sm">
                                        <input type="radio" :id="dayTakeEnum.NOW" v-model="schedule"
                                            :value="dayTakeEnum.NOW" class="custom-radio-field">
                                        <span class="custom-radio-span"></span>
                                    </div>
                                </label>
                                <label @click="openTimeSlotModal" :for="dayTakeEnum.TOMORROW"
                                    :class="schedule === dayTakeEnum.TOMORROW ? 'bg-primary/5 border-primary/30' : 'bg-white border-gray-100'"
                                    class="checkout-time-card w-fit py-2 px-3 rounded-lg flex items-start gap-5 cursor-pointer border transition-all duration-300">
                                    <dl class="flex-auto">
                                        <dt class="text-sm font-medium whitespace-nowrap mb-1.5 text-heading">
                                            {{ $t('label.schedule_for_later') }}
                                        </dt>
                                        <dd class="text-sm whitespace-nowrap text-heading">
                                            {{ localDeliveryTimeLabel || $t('label.choose_a_time') }}
                                        </dd>
                                    </dl>
                                    <div class="custom-radio sm">
                                        <input type="radio" :id="dayTakeEnum.TOMORROW" v-model="schedule"
                                            :value="dayTakeEnum.TOMORROW" class="custom-radio-field">
                                        <span class="custom-radio-span"></span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 md:col-5">
                    <div class="rounded-2xl shadow-xs bg-white">
                        <div class="p-4 border-b">
                            <h3 class="capitalize font-medium mb-3 text-center">{{
                                $t('label.cart_summary')
                            }}</h3>
                            <div class="flex items-center rounded-2xl w-fit mx-auto mb-6 text-[#008BBA] bg-[#BDEFFF]">
                                <div v-if="setting.order_setup_delivery === activityEnum.ENABLE"
                                    class="relative cursor-pointer">
                                    <input @change="changeOrderType(orderTypeEnum.DELIVERY)" id="checkout-delivery"
                                        :checked="orderType === orderTypeEnum.DELIVERY" :value="orderTypeEnum.DELIVERY"
                                        class="cart-switch w-full h-full absolute top-0 left-0 opacity-0 cursor-pointer"
                                        type="radio">
                                    <label
                                        class="py-1.5 px-3.5 rounded-2xl text-xs font-medium capitalize transition cursor-pointer block"
                                        :style="orderType === orderTypeEnum.DELIVERY ? 'background-color: #000000 !important; color: #ffffff !important; font-weight: 700 !important;' : 'color: #374151 !important; font-weight: 600 !important;'"
                                        for="checkout-delivery">{{ $t('label.delivery') }}</label>
                                </div>
                                <div v-if="setting.order_setup_takeaway === activityEnum.ENABLE"
                                    class="relative cursor-pointer">
                                    <input @change="changeOrderType(orderTypeEnum.TAKEAWAY)" id="checkout-takeaway"
                                        :checked="orderType === orderTypeEnum.TAKEAWAY" :value="orderTypeEnum.TAKEAWAY"
                                        class="cart-switch w-full h-full absolute top-0 left-0 opacity-0 cursor-pointer"
                                        type="radio">
                                    <label
                                        class="py-1.5 px-3.5 rounded-2xl text-xs font-medium capitalize transition cursor-pointer block"
                                        :style="orderType === orderTypeEnum.TAKEAWAY ? 'background-color: #000000 !important; color: #ffffff !important; font-weight: 700 !important;' : 'color: #374151 !important; font-weight: 600 !important;'"
                                        for="checkout-takeaway">{{ $t('label.takeaway') }}</label>
                                </div>
                            </div>
                            <div class="pl-3">
                                <div v-for="cart in carts"
                                    class="mb-3 pb-3 border-b last:mb-0 last:pb-0 last:border-b-0 border-gray-2">
                                    <div class="flex items-center gap-3 relative">
                                        <b class="absolute top-5 -left-3 text-xs font-extrabold w-[24px] h-[24px] leading-[24px] text-center rounded-full shadow-xs flex items-center justify-center" style="background-color: #111827 !important; color: #ffffff !important; border: 2px solid #ffffff !important;">
                                            {{ cart.quantity }}</b>
                                        <img :src="cart.image" alt="thumbnail"
                                            class="w-16 h-16 rounded-lg flex-shrink-0">
                                        <div class="w-full">
                                            <span class="text-sm font-medium capitalize transition text-heading">
                                                {{ cart.name }}
                                            </span>
                                            <p v-if="Object.keys(cart.item_variations.variations).length !== 0"
                                                class="capitalize text-xs mb-1.5">
                                                <span v-for="(variation, variationName) in cart.item_variations.names">
                                                    {{ variationName }}: {{ variation }}, &nbsp;
                                                </span>
                                            </p>
                                            <h4 class="text-xs font-semibold">
                                                {{
                                                    currencyFormat(cart.total, setting.site_digit_after_decimal_point,
                                                        setting.site_default_currency_symbol, setting.site_currency_position)
                                                }}
                                            </h4>
                                        </div>
                                    </div>

                                    <ul v-if="cart.item_extras.extras.length > 0 || cart.instruction !== ''"
                                        class="flex flex-col gap-1.5 mt-2">
                                        <li v-if="cart.item_extras.extras.length > 0" class="flex gap-1">
                                            <h3 class="capitalize text-xs w-fit whitespace-nowrap">
                                                {{ $t('label.extras') }}:
                                            </h3>
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
                        
                        <div class="p-4 border-b border-[#EFF0F6]/20">
                            <h3 class="text-base font-semibold mb-4 text-white">Select your payment method</h3>
                            <div class="flex flex-col gap-3">
                                <!-- Online Payment -->
                                <label class="checkout-payment-option flex w-full min-w-0 items-center justify-between gap-3 p-3 rounded-lg border cursor-pointer transition bg-gray-900/50 border-gray-700"
                                    :class="checkoutProps.form.payment_method === enums.paymentTypeEnum.E_WALLET ? 'border-primary ring-1 ring-primary' : ''"
                                    @click="checkoutProps.form.payment_method = enums.paymentTypeEnum.E_WALLET">
                                    <div class="flex min-w-0 flex-1 items-center gap-3">
                                        <div class="w-8 h-8 shrink-0 rounded-lg overflow-hidden flex items-center justify-center bg-[#00A859] shadow-xs">
                                            <img src="/images/payment-gateway/mpesa.png" alt="M-Pesa" class="w-full h-full object-cover" />
                                        </div>
                                        <span class="min-w-0 font-medium leading-5 text-white">Pay Online (M-Pesa, Card)</span>
                                    </div>
                                    <div class="custom-radio sm shrink-0">
                                        <input type="radio" v-model="checkoutProps.form.payment_method" :value="enums.paymentTypeEnum.E_WALLET" class="custom-radio-field">
                                        <span class="custom-radio-span"></span>
                                    </div>
                                </label>

                                <!-- Cash -->
                                <label v-if="showCashOnDelivery"
                                    class="checkout-payment-option flex w-full min-w-0 items-center justify-between gap-3 p-3 rounded-lg border cursor-pointer bg-gray-900/50 border-gray-700 transition"
                                    :class="checkoutProps.form.payment_method === enums.paymentTypeEnum.CASH_ON_DELIVERY ? 'border-primary ring-1 ring-primary' : ''"
                                    @click="checkoutProps.form.payment_method = enums.paymentTypeEnum.CASH_ON_DELIVERY">
                                    <div class="flex min-w-0 flex-1 items-center gap-3">
                                        <div class="w-8 h-8 shrink-0 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                            <i class="fa-solid fa-money-bill"></i>
                                        </div>
                                        <span class="min-w-0 font-medium leading-5 text-white">Pay on Delivery (Cash)</span>
                                    </div>
                                    <div class="custom-radio sm shrink-0">
                                        <input type="radio" v-model="checkoutProps.form.payment_method" :value="enums.paymentTypeEnum.CASH_ON_DELIVERY" class="custom-radio-field">
                                        <span class="custom-radio-span"></span>
                                    </div>
                                </label>

                            </div>
                        </div>

                        <div class="p-4">
                            <CouponComponent :props="{ total: parseFloat(subtotal) }" :coupon="coupon" />

                            <div class="rounded-xl mb-6 border border-[#EFF0F6]">
                                <ul class="flex flex-col gap-2 p-3 border-b border-dashed border-[#EFF0F6]">
                                    <li class="flex items-center justify-between text-heading">
                                        <span class="text-sm leading-6 capitalize">
                                            {{ $t('label.subtotal') }}
                                        </span>
                                        <span class="text-sm leading-6 capitalize">
                                            {{
                                                currencyFormat(subtotal, setting.site_digit_after_decimal_point,
                                                    setting.site_default_currency_symbol, setting.site_currency_position)
                                            }}
                                        </span>
                                    </li>
                                    <li class="flex items-center justify-between text-heading">
                                        <span class="text-sm leading-6 capitalize">
                                            {{ $t('label.discount') }}
                                        </span>
                                        <span class="text-sm leading-6 capitalize">
                                            {{
                                                currencyFormat(checkoutProps.form.discount,
                                                    setting.site_digit_after_decimal_point,
                                                    setting.site_default_currency_symbol,
                                                    setting.site_currency_position)
                                            }}
                                        </span>
                                    </li>
                                    <li v-if="checkoutProps.form.order_type === orderTypeEnum.DELIVERY"
                                        class="flex items-center justify-between text-heading">
                                        <span class="text-sm leading-6 capitalize">
                                            {{ $t('label.delivery_charge') }}
                                        </span>
                                        <span class="text-sm leading-6 capitalize font-medium text-[#1AB759]">
                                            {{
                                                currencyFormat(checkoutProps.form.delivery_charge,
                                                    setting.site_digit_after_decimal_point,
                                                    setting.site_default_currency_symbol,
                                                    setting.site_currency_position)
                                            }}
                                        </span>
                                    </li>
                                </ul>
                                <div class="flex items-center justify-between p-3">
                                    <h4 class="text-sm leading-6 font-semibold capitalize">
                                        {{ $t('label.total') }}
                                    </h4>
                                    <h5 class="text-sm leading-6 font-semibold capitalize">
                                        {{
                                            currencyFormat(subtotal +
                                                checkoutProps.form.delivery_charge - checkoutProps.form.discount,
                                                setting.site_digit_after_decimal_point, setting.site_default_currency_symbol,
                                                setting.site_currency_position)
                                        }}
                                    </h5>
                                </div>
                            </div>
                            <button type="button"
                                :disabled="submittingOrder"
                                :class="submittingOrder ? 'opacity-60 cursor-not-allowed' : ''"
                                class="w-full rounded-3xl capitalize font-medium leading-6 py-3 text-white bg-primary"
                                @click="orderSubmit">
                                {{ checkoutProps.form.payment_method === enums.paymentTypeEnum.CASH_ON_DELIVERY
                                    ? $t('button.place_order')
                                    : $t('button.pay_now') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="time-schedule-modal" class="modal">
        <div class="modal-dialog">
            <div class="flex items-center justify-between gap-4 py-3.5 px-4 border-b border-slate-100">
                <h3 class="text-lg font-semibold capitalize">{{ $t('label.select_time_schedule') }}</h3>
                <button class="modal-close fa-regular fa-circle-xmark" @click="resetTimeSlotModal"></button>
            </div>

            <div v-if="todayTimeSlots.length > 0 || tomorrowTimeSlots.length > 0" class="p-4 border-b border-gray-100">
                <nav class="w-fit flex items-center rounded-full bg-primary/10">
                    <button data-tab="#time-slot-today-tab" v-if="todayTimeSlots.length > 0"
                        @click.prevent="changeTimeSlot(dayTakeEnum.TODAY)"
                        :class="scheduleTab === dayTakeEnum.TODAY && todayTimeSlots.length > 0 ? 'text-white bg-primary' : ''"
                        class="other-tabBtn text-sm font-medium capitalize h-10 px-4 rounded-full text-heading">
                        {{ $t('label.today') }}
                    </button>
                    <button data-tab="#time-slot-tomorrow-tab" v-if="tomorrowTimeSlots.length > 0"
                        @click.prevent="changeTimeSlot(dayTakeEnum.TOMORROW)"
                        :class="scheduleTab === dayTakeEnum.TOMORROW || (todayTimeSlots.length === 0 && tomorrowTimeSlots.length > 0) ? 'text-white bg-primary' : ''"
                        class="other-tabBtn text-sm font-medium capitalize h-10 px-4 rounded-full text-heading">
                        {{ $t('label.tomorrow') }}
                    </button>
                </nav>
            </div>

            <div v-if="todayTimeSlots.length > 0"
                :class="todayTimeSlots.length > 0 && scheduleTab === dayTakeEnum.TODAY ? 'active' : ''"
                id="time-slot-today-tab" class="data-tab">
                <ul v-if="todayTimeSlots.length > 0" class="p-4 grid grid-cols-2 gap-y-4 gap-6">
                    <li v-for="todayTimeSlot in todayTimeSlots" @click.prevent="selectDeliveryTime(todayTimeSlot)"
                        class="w-full h-10 leading-10 rounded-3xl text-center text-sm cursor-pointer border text-heading"
                        :class="timeSlot.is_advance_order === isAdvanceOrderEnum.NO && timeSlot.label === todayTimeSlot.label ? 'bg-primary/5 border-primary/40' : 'border-gray-100 bg-gray-100'">
                        {{ todayTimeSlot.label }}
                    </li>
                </ul>
            </div>

            <div v-if="tomorrowTimeSlots.length > 0" id="time-slot-tomorrow-tab"
                :class="(todayTimeSlots.length === 0 && tomorrowTimeSlots.length > 0) || scheduleTab === dayTakeEnum.TOMORROW ? 'active' : ''"
                class="data-tab">
                <ul v-if="tomorrowTimeSlots.length > 0" class="p-4 grid grid-cols-2 gap-y-4 gap-6">
                    <li v-for="tomorrowTimeSlot in tomorrowTimeSlots"
                        @click.prevent="selectDeliveryTime(tomorrowTimeSlot, isAdvanceOrderEnum.YES)"
                        class="w-full h-10 leading-10 rounded-3xl text-center text-sm cursor-pointer border text-heading"
                        :class="timeSlot.is_advance_order === isAdvanceOrderEnum.YES && timeSlot.label === tomorrowTimeSlot.label ? 'bg-primary/5 border-primary/40' : 'border-gray-100 bg-gray-100'">
                        {{ tomorrowTimeSlot.label }}
                    </li>
                </ul>
            </div>

            <div v-if="todayTimeSlots.length === 0 && tomorrowTimeSlots.length === 0" class="data-tab active">
                <div class="p-4 grid grid-cols-2 gap-y-4 gap-6 text-heading">
                    {{ $t('message.no_schedule_found') }}
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import appService from "../../../services/appService";
import alertService from "../../../services/alertService";
import MapComponent from "../components/MapComponent";
import dayTakeEnum from "../../../enums/modules/dayTakeEnum";
import isAdvanceOrderEnum from "../../../enums/modules/isAdvanceOrderEnum";
import paymentTypeEnum from "../../../enums/modules/paymentTypeEnum";
import sourceEnum from "../../../enums/modules/sourceEnum";
import AddressComponent from "./AddressComponent";
import LoadingComponent from "../components/LoadingComponent";
import labelEnum from "../../../enums/modules/labelEnum";
import activityEnum from "../../../enums/modules/activityEnum";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum";
import CouponComponent from "./CouponComponent";
import router from "../../../router";
import _ from "lodash";
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';
import statusEnum from "../../../enums/modules/statusEnum";
import env from "../../../config/env";
import { setOrderPlaced } from "../../../services/cartAbandonmentService";


export default {
    name: "CheckoutComponent",
    components: {
        LoadingComponent,
        AddressComponent,
        CouponComponent,
        MapComponent,
        Swiper,
        SwiperSlide,
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            mapShow: false,
            placeOrderShow: false,
            mapKey: "branch",
            location: {
                lat: null,
                lng: null
            },
            branchAddress: null,
            localDeliveryTimeLabel: null,
            localAddress: {},
            dayTakeEnum: dayTakeEnum,
            activityEnum: activityEnum,
            isAdvanceOrderEnum: isAdvanceOrderEnum,
            labelEnum: labelEnum,
            dayTake: dayTakeEnum.TODAY,
            schedule: dayTakeEnum.NOW,
            scheduleTab: dayTakeEnum.TODAY,
            orderTypeEnum: orderTypeEnum,
            enums: {
                orderTypeEnumArray: {
                    [orderTypeEnum.DELIVERY]: this.$t("label.delivery"),
                    [orderTypeEnum.TAKEAWAY]: this.$t("label.takeaway")
                },
                paymentTypeEnum: paymentTypeEnum
            },
            checkoutProps: {
                form: {
                    branch_id: null,
                    subtotal: 0,
                    discount: 0,
                    delivery_charge: 0,
                    delivery_time: null,
                    total: 0,
                    order_type: null,
                    is_advance_order: null,
                    source: sourceEnum.WEB,
                    payment_method: paymentTypeEnum.E_WALLET,
                    address_id: null,
                    coupon_id: null,
                    customer_name: null,
                    customer_phone: null,
                    customer_email: null,
                    items: []
                }
            },
            addressProps: {
                form: {
                    address: "",
                    apartment: "",
                    latitude: "",
                    longitude: "",
                    label: "",
                },
                search: {
                    paginate: 0,
                    order_column: 'id',
                    order_type: 'asc'
                },
                status: false,
                switchLabel: "",
                isMap: false,
            },
            branchSettings: {
                itemsToShow: 2.5,
                wrapAround: false,
                snapAlign: "start"
            },
            branchBreakpoints: {
                200: {
                    itemsToShow: 1.1,
                    wrapAround: false,
                    snapAlign: 'start',
                },
                250: {
                    itemsToShow: 1.3,
                    wrapAround: false,
                    snapAlign: 'start',
                },
                300: {
                    itemsToShow: 1.4,
                    wrapAround: false,
                    snapAlign: 'start',
                },
                375: {
                    itemsToShow: 1.7,
                    wrapAround: false,
                    snapAlign: 'start',
                },
                540: {
                    itemsToShow: 2.5,
                    wrapAround: false,
                    snapAlign: 'start',
                },
                700: {
                    itemsToShow: 2.5,
                    wrapAround: false,
                    snapAlign: 'start',
                }
            },
            dayTakeSettings: {
                itemsToShow: 2,
                wrapAround: false,
                snapAlign: "start"
            },
            dayTakeBreakpoints: {
                200: {
                    itemsToShow: 1.1,
                    wrapAround: false,
                    snapAlign: 'start',
                },
                250: {
                    itemsToShow: 1.3,
                    wrapAround: false,
                    snapAlign: 'start',
                },
                300: {
                    itemsToShow: 1.4,
                    wrapAround: false,
                    snapAlign: 'start',
                },
                375: {
                    itemsToShow: 1.7,
                    wrapAround: false,
                    snapAlign: 'start',
                },
                540: {
                    itemsToShow: 2.5,
                    wrapAround: false,
                    snapAlign: 'start',
                },
                700: {
                    itemsToShow: 3.2,
                    wrapAround: false,
                    snapAlign: 'start',
                }
            },
            timeSettings: {
                itemsToShow: 3.2,
                wrapAround: false,
                snapAlign: "start"
            },
            timeBreakpoints: {
                200: {
                    itemsToShow: 1.1,
                    wrapAround: false,
                    snapAlign: 'start',
                },
                250: {
                    itemsToShow: 1.3,
                    wrapAround: false,
                    snapAlign: 'start',
                },
                300: {
                    itemsToShow: 1.4,
                    wrapAround: false,
                    snapAlign: 'start',
                },
                375: {
                    itemsToShow: 1.7,
                    wrapAround: false,
                    snapAlign: 'start',
                },
                540: {
                    itemsToShow: 2.5,
                    wrapAround: false,
                    snapAlign: 'start',
                },
                700: {
                    itemsToShow: 3.2,
                    wrapAround: false,
                    snapAlign: 'start',
                }
            },
            whatsappSetup: {
                status: activityEnum.DISABLE,
                phone: null,
            },
            checkoutDraftRestored: false,
            submittingOrder: false,
            guestLoginInProgress: false,
            guestLoginAttemptedPhone: null,
            distanceExceeded: false,
            orderPlacedSuccessfully: false,
            abandonmentTimer: null
        }
    },
    computed: {
        authInfo: function () {
            return this.$store.getters.authInfo;
        },
        authStatus: function () {
            return this.$store.getters.authStatus;
        },
        globalState: function () {
            return this.$store.getters['globalState/lists'];
        },
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        branches: function () {
            return this.$store.getters['frontendBranch/lists'];
        },
        branch: function () {
            return this.$store.getters['frontendBranch/show'];
        },
        weather: function () {
            return this.$store.getters['frontendWeather/show'];
        },
        carts: function () {
            return this.$store.getters['frontendCart/lists'];
        },
        subtotal: function () {
            return this.$store.getters['frontendCart/subtotal'];
        },
        nowTimeSlot: function () {
            return this.$store.getters['frontendTimeSlot/now'];
        },
        todayTimeSlots: function () {
            return this.$store.getters['frontendTimeSlot/today'];
        },
        tomorrowTimeSlots: function () {
            return this.$store.getters['frontendTimeSlot/tomorrow'];
        },
        addresses: function () {
            return this.$store.getters['frontendAddress/lists'];
        },
        orderType: function () {
            return this.$store.getters['frontendCart/orderType'];
        },
        countryCode: function () {
            return this.$store.getters['frontendCountryCode/show'];
        },
        callingCode: function () {
            const cc = this.$store.getters['frontendCountryCode/show'];
            if (cc && cc.calling_code) {
                return cc.calling_code;
            }
            if (this.setting && this.setting.company_country_code) {
                return this.setting.company_country_code;
            }
            return '+254';
        },
        timeSlot: function () {
            return this.$store.getters['frontendCart/timeSlot'];
        },
        canPayOnDelivery: function () {
            if (!this.setting || this.setting.order_setup_trust_score_enable != this.activityEnum.ENABLE) {
                return true;
            }
            if (!this.authStatus || !this.authInfo) {
                return false;
            }
            if (typeof this.authInfo.can_pay_on_delivery !== 'undefined') {
                return !!this.authInfo.can_pay_on_delivery;
            }
            const successfulOrders = this.authInfo.trust_metrics?.successful_orders ?? this.authInfo.order ?? 0;
            const minRequired = parseInt(this.setting.order_setup_trust_score_min_orders || 1);
            return successfulOrders >= minRequired;
        },
        showCashOnDelivery: function () {
            return this.setting.payment_gateway_cash_on_delivery === this.activityEnum.ENABLE && this.canPayOnDelivery;
        },
    },
    beforeUnmount() {
        if (this.abandonmentTimer) {
            clearTimeout(this.abandonmentTimer);
        }
        window.removeEventListener('beforeunload', this.triggerCartAbandonmentAlert);
        if (!this.orderPlacedSuccessfully) {
            this.triggerCartAbandonmentAlert();
        }
    },
    mounted() {
        window.addEventListener('beforeunload', this.triggerCartAbandonmentAlert);
        this.scheduleAbandonmentCheck();

        if (!this.canPayOnDelivery && this.checkoutProps.form.payment_method === this.enums.paymentTypeEnum.CASH_ON_DELIVERY) {
            this.checkoutProps.form.payment_method = this.enums.paymentTypeEnum.E_WALLET;
        }
        if (this.authStatus && this.authInfo) {
            this.checkoutProps.form.customer_name = this.authInfo.name;
            this.checkoutProps.form.customer_phone = this.authInfo.phone;
            this.checkoutProps.form.customer_email = this.authInfo.email;
        }

        if (!document.getElementById('paystack-inline-script')) {
            let script = document.createElement('script');
            script.id = 'paystack-inline-script';
            script.src = 'https://js.paystack.co/v1/inline.js';
            document.head.appendChild(script);
        }

        this.loading.isActive = true;
        this.$store.dispatch("frontendBranch/lists", this.branchProps).then(res => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });
        this.$store.dispatch("frontendSetting/lists").then(res => {
            if (res.data && res.data.data && res.data.data.company_country_code) {
                this.$store.dispatch("frontendCountryCode/show", res.data.data.company_country_code).catch(() => {});
            }
            if ((res.data.data.order_setup_delivery === activityEnum.DISABLE && res.data.data.order_setup_takeaway === activityEnum.DISABLE) || this.$store.getters['frontendCart/lists'].length === 0) {
                this.$router.push({ name: 'frontend.home' });
            }
            if (!this.checkoutProps.form.branch_id) {
                const savedBranchId = localStorage.getItem('selected_branch_id');
                if (savedBranchId) {
                    this.checkoutProps.form.branch_id = parseInt(savedBranchId);
                } else if (this.checkoutProps.form.order_type !== orderTypeEnum.TAKEAWAY) {
                    this.checkoutProps.form.branch_id = this.$store.getters['globalState/lists'].branch_id;
                }
            }
            if (this.checkoutProps.form.branch_id > 0) {
                this.$store.dispatch("frontendBranch/show", this.checkoutProps.form.branch_id).then(branchRes => {
                    this.loading.isActive = false;
                    this.location = {
                        lat: branchRes.data.data.latitude,
                        lng: branchRes.data.data.longitude
                    };
                    this.branchAddress = branchRes.data.data.address;
                    this.mapShow = true;
                }).catch((err) => {
                    this.loading.isActive = false;
                });
            }
        }).catch();

        this.$store.dispatch("frontendTimeSlot/today", {}).then(res => {
            this.loading.isActive = false;
            if (!this.checkoutDraftRestored) {
                this.checkoutProps.form.is_advance_order = isAdvanceOrderEnum.NO;
            }
            
            // Set default delivery time to 'Now' if no time is selected yet
            if (!this.checkoutDraftRestored && Object.keys(this.timeSlot).length === 0 && Object.keys(this.nowTimeSlot).length > 0) {
                this.selectNowDeliveryTime(this.nowTimeSlot);
            }
        }).catch((err) => {
            this.loading.isActive = false;
        });

        this.loading.isActive = true;
        this.$store.dispatch("frontendTimeSlot/tomorrow", {}).then(res => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });

        if (this.authStatus) {
            this.loadCustomerAddresses();
        }

        this.checkoutProps.form.order_type = this.orderType;

        if (Object.keys(this.timeSlot).length > 0) {
            this.localDeliveryTimeLabel = this.timeSlot.label;
            this.checkoutProps.form.delivery_time = this.timeSlot.delivery_time;
            this.checkoutProps.form.is_advance_order = this.timeSlot.is_advance_order;
            this.schedule = this.timeSlot.schedule;
            this.scheduleTab = this.timeSlot.scheduleTab;
        }

        this.restoreCheckoutDraft();
        this.restoreSavedPreferences();
        this.saveCustomerPreferences();
    },
    methods: {
        saveCustomerPreferences: function () {
            let current = {};
            try {
                current = JSON.parse(localStorage.getItem('savedOrderPreferences') || '{}');
            } catch (error) {
                current = {};
            }
            localStorage.setItem('savedOrderPreferences', JSON.stringify({
                ...current,
                customer_name: this.checkoutProps.form.customer_name || '',
                customer_phone: this.checkoutProps.form.customer_phone || '',
                customer_email: this.checkoutProps.form.customer_email || ''
            }));
        },
        onPhoneInput: function () {
            this.saveCustomerPreferences();
            if (this.phoneDebounceTimer) {
                clearTimeout(this.phoneDebounceTimer);
            }
            this.phoneDebounceTimer = setTimeout(() => {
                const phone = (this.checkoutProps.form.customer_phone || '').replace(/\D/g, '');
                if (phone.length >= 9) {
                    this.ensureGuestSession();
                }
            }, 400);
        },
        loadCustomerAddresses: function () {
            if (!this.authStatus) return Promise.resolve();
            this.loading.isActive = true;
            return this.$store.dispatch("frontendAddress/lists", this.addressProps).then((res) => {
                let saved = {};
                try {
                    saved = JSON.parse(localStorage.getItem('savedOrderPreferences') || '{}');
                } catch (error) {
                    saved = {};
                }
                const available = res.data.data || [];
                if (available.length > 0) {
                    const selected = available.find(address => address.id === saved.address_id) || available[0];
                    if (selected) {
                        this.updateAddress(selected);
                    }
                }
                return res;
            }).finally(() => {
                this.loading.isActive = false;
            });
        },
        ensureGuestSession: function () {
            this.saveCustomerPreferences();
            if (this.guestLoginInProgress) return Promise.resolve();

            const phone = (this.checkoutProps.form.customer_phone || '').trim();
            if (phone.replace(/\D/g, '').length < 9) {
                return Promise.resolve();
            }

            if (this.guestLoginAttemptedPhone === phone && this.authStatus) {
                return this.loadCustomerAddresses();
            }

            const name = (this.checkoutProps.form.customer_name || '').trim() || 'Guest Customer';

            this.guestLoginInProgress = true;
            this.guestLoginAttemptedPhone = phone;
            return this.$store.dispatch('GuestLoginVerify', {
                name,
                phone,
                email: this.checkoutProps.form.customer_email || null,
                code: this.callingCode,
                token: 'guest'
            }).then(() => {
                return this.loadCustomerAddresses();
            }).catch(() => {
                this.guestLoginAttemptedPhone = null;
            }).finally(() => {
                this.guestLoginInProgress = false;
            });
        },
        saveSuccessfulOrderPreferences: function () {
            const preferences = {
                branch_id: this.checkoutProps.form.branch_id,
                address_id: this.checkoutProps.form.address_id,
                localAddress: this.localAddress,
                customer_name: this.checkoutProps.form.customer_name,
                customer_phone: this.checkoutProps.form.customer_phone,
                customer_email: this.checkoutProps.form.customer_email,
                order_type: this.checkoutProps.form.order_type
            };
            localStorage.setItem('savedOrderPreferences', JSON.stringify(preferences));
        },
        restoreSavedPreferences: function () {
            const saved = localStorage.getItem('savedOrderPreferences');
            if (!saved) return;
            try {
                const prefs = JSON.parse(saved);
                if (prefs.customer_name && !this.checkoutProps.form.customer_name) {
                    this.checkoutProps.form.customer_name = prefs.customer_name;
                }
                if (prefs.customer_phone && !this.checkoutProps.form.customer_phone) {
                    this.checkoutProps.form.customer_phone = prefs.customer_phone;
                }
                if (prefs.customer_email && !this.checkoutProps.form.customer_email) {
                    this.checkoutProps.form.customer_email = prefs.customer_email;
                }
                if (prefs.order_type) {
                    this.checkoutProps.form.order_type = prefs.order_type;
                }
                if (prefs.branch_id && !this.checkoutProps.form.branch_id) {
                    this.checkoutProps.form.branch_id = prefs.branch_id;
                    this.loadSelectedBranchDetails(prefs.branch_id);
                }
                if (prefs.address_id && !this.checkoutProps.form.address_id) {
                    this.checkoutProps.form.address_id = prefs.address_id;
                    if (prefs.localAddress && Object.keys(prefs.localAddress).length > 0) {
                        this.localAddress = prefs.localAddress;
                    }
                }
            } catch (e) {
                localStorage.removeItem('savedOrderPreferences');
            }
        },
        loadSelectedBranchDetails: function (branchId) {
            if (!branchId) return;
            this.$store.dispatch('frontendWeather/show', branchId);
            this.$store.dispatch("frontendBranch/show", branchId).then(branchRes => {
                const b = branchRes.data.data;
                this.location = { lat: b.latitude, lng: b.longitude };
                this.branchAddress = b.address;
                this.mapShow = true;
                this.deliveryChargeCalculation();
            }).catch();
        },
        saveCheckoutDraft: function () {
            const form = Object.assign({}, this.checkoutProps.form, { items: [] });
            sessionStorage.setItem('pendingCheckoutDraft', JSON.stringify({
                form: form,
                localAddress: this.localAddress,
                location: this.location,
                branchAddress: this.branchAddress,
                localDeliveryTimeLabel: this.localDeliveryTimeLabel,
                schedule: this.schedule,
                scheduleTab: this.scheduleTab,
                dayTake: this.dayTake
            }));
        },
        restoreCheckoutDraft: function () {
            const savedDraft = sessionStorage.getItem('pendingCheckoutDraft');
            if (!savedDraft) {
                return;
            }

            try {
                const draft = JSON.parse(savedDraft);
                this.checkoutProps.form = Object.assign(this.checkoutProps.form, draft.form || {}, { items: [] });
                this.localAddress = draft.localAddress || {};
                this.location = draft.location || this.location;
                this.branchAddress = draft.branchAddress || null;
                this.localDeliveryTimeLabel = draft.localDeliveryTimeLabel || null;
                this.schedule = draft.schedule || this.schedule;
                this.scheduleTab = draft.scheduleTab || this.scheduleTab;
                this.dayTake = draft.dayTake || this.dayTake;
                this.checkoutDraftRestored = true;
            } catch (error) {
                sessionStorage.removeItem('pendingCheckoutDraft');
            }
        },
        clearCompletedCheckout: function () {
            sessionStorage.removeItem('pendingCheckoutDraft');
            this.mapShow = false;
            this.location.lat = null;
            this.location.lng = null;
            this.branchAddress = null;
            this.localAddress = {};
            this.$store.dispatch('frontendCart/resetCart');
        },
        scheduleAbandonmentCheck: function () {
            if (this.abandonmentTimer) {
                clearTimeout(this.abandonmentTimer);
            }
            this.abandonmentTimer = setTimeout(() => {
                if (!this.orderPlacedSuccessfully) {
                    this.triggerCartAbandonmentAlert();
                }
            }, 35000);
        },
        triggerCartAbandonmentAlert: function () {
            if (this.orderPlacedSuccessfully) return;

            const name = (this.checkoutProps.form.customer_name || '').trim();
            const phone = (this.checkoutProps.form.customer_phone || '').trim();
            const email = (this.checkoutProps.form.customer_email || '').trim();
            const branchId = this.checkoutProps.form.branch_id || (this.$store.getters['globalState/lists'] ? this.$store.getters['globalState/lists'].branch_id : 0);
            const cartItems = this.carts || [];

            if (!name || !phone || cartItems.length === 0) return;

            const payload = {
                customer_name: name,
                customer_phone: phone,
                customer_email: email,
                branch_id: branchId,
                cart_items: cartItems,
                total: this.checkoutProps.form.total || this.subtotal
            };

            if (navigator.sendBeacon) {
                const blob = new Blob([JSON.stringify(payload)], { type: 'application/json' });
                navigator.sendBeacon('/api/frontend/cart-abandonment-alert', blob);
            } else {
                axios.post('/api/frontend/cart-abandonment-alert', payload, {
                    headers: { 'x-api-key': this.setting.apiKey || '' }
                }).catch(() => {});
            }
        },
        resetTimeSlotModal: function () {
            appService.modalHide('#time-schedule-modal');
        },
        openTimeSlotModal: function () {
            this.checkoutProps.form.delivery_time = null;
            appService.modalShow('#time-schedule-modal');
        },
        selectDeliveryTime: function (timeSlot, advance = isAdvanceOrderEnum.NO) {
            this.localDeliveryTimeLabel = timeSlot.label;
            this.checkoutProps.form.delivery_time = timeSlot.time;
            this.checkoutProps.form.is_advance_order = advance;

            this.$store.dispatch("frontendCart/timeSlot", {
                scheduleTab: this.scheduleTab,
                schedule: dayTakeEnum.TOMORROW,
                label: this.localDeliveryTimeLabel,
                delivery_time: this.checkoutProps.form.delivery_time,
                is_advance_order: this.checkoutProps.form.is_advance_order
            });
            this.resetTimeSlotModal();
        },
        changeTimeSlot: function (time) {
            this.scheduleTab = time;
        },
        selectNowDeliveryTime: function (timeSlot) {
            this.localDeliveryTimeLabel = null;
            this.checkoutProps.form.delivery_time = timeSlot.time;
            this.checkoutProps.form.is_advance_order = isAdvanceOrderEnum.NO;
            this.schedule = dayTakeEnum.NOW;
            this.$store.dispatch("frontendCart/timeSlot", {
                scheduleTab: dayTakeEnum.TODAY,
                schedule: dayTakeEnum.NOW,
                label: this.localDeliveryTimeLabel,
                delivery_time: this.checkoutProps.form.delivery_time,
                is_advance_order: this.checkoutProps.form.is_advance_order
            });
        },
        branchPosition: function (e) {
            window.setTimeout(() => {
                this.deliveryChargeCalculation();
            }, 300);
        },
        currencyFormat: function (amount, decimal, currency, position) {
            return appService.currencyFormat(amount, decimal, currency, position);
        },
        editAddress: function () {
            if (typeof this.localAddress === "object" && this.checkoutProps.form.address_id !== null) {
                this.loading.isActive = true;
                this.$store.dispatch("frontendAddress/edit", this.checkoutProps.form.address_id).then((res) => {
                    this.loading.isActive = false;

                    this.addressProps.form.address = this.localAddress.address;
                    this.addressProps.form.apartment = this.localAddress.apartment;
                    this.addressProps.form.latitude = this.localAddress.latitude;
                    this.addressProps.form.longitude = this.localAddress.longitude;
                    this.addressProps.form.label = this.localAddress.label;

                    if (this.addressProps.form.label !== labelEnum.HOME && this.addressProps.form.label !== labelEnum.WORK) {
                        this.addressProps.status = true;
                        this.addressProps.switchLabel = labelEnum.OTHER;
                    } else {
                        this.addressProps.switchLabel = this.localAddress.label;
                    }

                    this.addressProps.isMap = true;
                    appService.modalShow('.address-modal');
                }).catch((err) => {
                    alertService.error(err.response.data.message);
                });
            }
        },
        updateAddress: function (address) {
            this.localAddress = address;
            this.checkoutProps.form.address_id = address.id;
            this.deliveryChargeCalculation();
        },
        changeBranch: function (branch) {
            this.mapShow = false;
            this.location.lat = branch.latitude;
            this.location.lng = branch.longitude;
            this.branchAddress = branch.address;
            this.checkoutProps.form.branch_id = branch.id;
            this.$store.dispatch('globalState/set', { branch_id: branch.id });
            localStorage.setItem('selected_branch_id', branch.id);
            if (this.$store.getters.authStatus) {
                this.$store.dispatch('frontendEditProfile/changeBranch', { branch_id: branch.id }).then().catch();
            }
            this.saveCheckoutDraft();
            this.saveSuccessfulOrderPreferences();
            this.$store.dispatch('frontendWeather/show', branch.id);
            window.setTimeout(() => {
                this.mapShow = true;
            }, 3000);
            this.deliveryChargeCalculation();
        },
        changeDayTake: function (id) {
            if (id === dayTakeEnum.TODAY) {
                if (typeof this.todayTimeSlots[0] !== "undefined") {
                    this.checkoutProps.form.delivery_time = this.todayTimeSlots[0].time;
                    this.checkoutProps.form.is_advance_order = isAdvanceOrderEnum.NO;
                } else {
                    this.checkoutProps.form.delivery_time = null;
                    this.checkoutProps.form.is_advance_order = isAdvanceOrderEnum.NO;
                }
            } else if (id === dayTakeEnum.TOMORROW) {
                if (typeof this.tomorrowTimeSlots[0] !== "undefined") {
                    this.checkoutProps.form.delivery_time = this.tomorrowTimeSlots[0].time;
                    this.checkoutProps.form.is_advance_order = isAdvanceOrderEnum.YES;
                } else {
                    this.checkoutProps.form.delivery_time = null;
                    this.checkoutProps.form.is_advance_order = isAdvanceOrderEnum.YES;
                }
            }
        },
        changeAddress: function (e, address) {
            e.preventDefault();
            this.localAddress = address;
            this.checkoutProps.form.address_id = address.id;
            this.deliveryChargeCalculation();
        },
        deliveryChargeCalculation: function () {
            if (this.checkoutProps.form.order_type === orderTypeEnum.DELIVERY && (typeof this.localAddress.latitude !== 'undefined' && this.localAddress.latitude !== '')) {
                const checkDistanceAndSetCharge = (branchLat, branchLng) => {
                    const distance = appService.distance(parseFloat(this.localAddress.latitude), parseFloat(this.localAddress.longitude), parseFloat(branchLat), parseFloat(branchLng));
                    const maxRadius = parseFloat(this.setting.order_setup_max_delivery_kilometer || 0);

                    if (maxRadius > 0 && distance > maxRadius) {
                        this.checkoutProps.form.delivery_charge = 0;
                        this.distanceExceeded = true;
                        alertService.error(`Sorry, your location is ${distance.toFixed(1)} KM away. Maximum delivery radius is ${maxRadius} KM from this branch.`);
                        return;
                    }

                    this.distanceExceeded = false;
                    if (distance > this.setting.order_setup_free_delivery_kilometer) {
                        let extraDistance = distance - parseFloat(this.setting.order_setup_free_delivery_kilometer);
                        this.checkoutProps.form.delivery_charge = (extraDistance * parseFloat(this.setting.order_setup_charge_per_kilo) + parseFloat(this.setting.order_setup_basic_delivery_charge));
                    } else {
                        this.checkoutProps.form.delivery_charge = parseFloat(this.setting.order_setup_basic_delivery_charge);
                    }
                };

                if (this.checkoutProps.form.branch_id) {
                    this.$store.dispatch("frontendBranch/show", this.checkoutProps.form.branch_id).then(branchRes => {
                        checkDistanceAndSetCharge(branchRes.data.data.latitude, branchRes.data.data.longitude);
                        this.branchWhatsappSetup();
                    }).catch(() => {
                        this.loading.isActive = false;
                        this.branchWhatsappSetup();
                    });
                } else {
                    this.$store.dispatch("frontendBranch/showByLatLong", {
                        latitude: this.localAddress.latitude,
                        longitude: this.localAddress.longitude
                    }).then((branchRes) => {
                        this.checkoutProps.form.branch_id = branchRes.data.data.id;
                        checkDistanceAndSetCharge(branchRes.data.data.latitude, branchRes.data.data.longitude);
                        this.branchWhatsappSetup();
                    }).catch((err) => {
                        this.loading.isActive = false;
                        this.checkoutProps.form.delivery_charge = 0;
                        if (err && err.response && err.response.data && err.response.data.message) {
                            alertService.info(err.response.data.message);
                        }
                        this.branchWhatsappSetup();
                    });
                }
            } else {
                this.distanceExceeded = false;
                if (!this.checkoutProps.form.address_id) {
                    this.localAddress = {};
                }
                this.checkoutProps.form.delivery_charge = 0;
                this.branchWhatsappSetup();
            }
        },
        coupon: function (e) {
            if (Object.keys(e).length !== 0) {
                this.checkoutProps.form.discount = e.convert_discount;
                this.checkoutProps.form.coupon_id = e.id;
            } else {
                this.checkoutProps.form.discount = 0;
                this.checkoutProps.form.coupon_id = null;
            }
        },
        orderSubmit: function (e, is_whats_app = false) {
            if (this.submittingOrder) {
                return;
            }
            this.submittingOrder = true;
            if (!this.authStatus) {
                if (!this.checkoutProps.form.customer_name || !this.checkoutProps.form.customer_phone) {
                    alertService.error("Please enter your name and phone number in 'Your Details' section");
                    this.submittingOrder = false;
                    return;
                }
                this.loading.isActive = true;
                let payload = {
                    name: this.checkoutProps.form.customer_name,
                    phone: this.checkoutProps.form.customer_phone,
                    email: this.checkoutProps.form.customer_email || null,
                    code: this.callingCode,
                    token: "guest"
                };
                this.$store.dispatch('GuestLoginVerify', payload).then((res) => {
                    this.processOrderSubmit(e, is_whats_app);
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.submittingOrder = false;
                    alertService.error("Failed to authenticate as guest");
                });
            } else {
                this.processOrderSubmit(e, is_whats_app);
            }
        },
        processOrderSubmit: function (e, is_whats_app = false) {
            if (!this.checkoutProps.form.branch_id) {
                this.loading.isActive = false;
                this.submittingOrder = false;
                alertService.error("Please select a branch for your order.");
                return;
            }
            if (this.checkoutProps.form.order_type === orderTypeEnum.DELIVERY && this.distanceExceeded) {
                this.loading.isActive = false;
                this.submittingOrder = false;
                const maxRadius = parseFloat(this.setting.order_setup_max_delivery_kilometer || 0);
                alertService.error(`Your delivery location exceeds our maximum radius of ${maxRadius} KM. Please choose a closer address or select Takeaway.`);
                return;
            }
            this.loading.isActive = true;
            this.saveCheckoutDraft();
            this.checkoutProps.form.subtotal = this.subtotal;
            this.checkoutProps.form.total = parseFloat(this.subtotal + this.checkoutProps.form.delivery_charge - this.checkoutProps.form.discount).toFixed(this.setting.site_digit_after_decimal_point);
            const orderItems = [];
            _.forEach(this.carts, (item, index) => {
                let item_variations = [];
                if (Object.keys(item.item_variations.variations).length > 0) {
                    _.forEach(item.item_variations.variations, (value, index) => {
                        item_variations.push({
                            "id": value,
                            "item_id": item.item_id,
                            "item_attribute_id": index,
                        });
                    });
                }

                if (Object.keys(item.item_variations.names).length > 0) {
                    let i = 0;
                    _.forEach(item.item_variations.names, (value, index) => {
                        item_variations[i].variation_name = index;
                        item_variations[i].name = value;
                        i++;
                    });
                }

                let item_extras = [];
                if (item.item_extras.extras.length) {
                    _.forEach(item.item_extras.extras, (value) => {
                        item_extras.push({
                            id: value,
                            item_id: item.item_id,
                        });
                    });
                }

                if (item.item_extras.names.length) {
                    let i = 0;
                    _.forEach(item.item_extras.names, (value) => {
                        item_extras[i].name = value;
                        i++;
                    });
                }

                orderItems.push({
                    item_id: item.item_id,
                    item_name: item.name,
                    item_price: item.convert_price,
                    branch_id: this.checkoutProps.form.branch_id,
                    instruction: item.instruction,
                    quantity: item.quantity,
                    discount: item.discount,
                    total_price: item.total,
                    item_variation_total: item.item_variation_total,
                    item_extra_total: item.item_extra_total,
                    item_variations: item_variations,
                    item_extras: item_extras
                });
            });
            // Use an immutable request object. Mutating form.items after dispatch
            // allowed rapid/repeated taps to turn the JSON string back into [].
            const orderPayload = {
                ...this.checkoutProps.form,
                items: JSON.stringify(orderItems)
            };
            this.$store.dispatch('frontendOrder/save', orderPayload).then(orderResponse => {
                this.orderPlacedSuccessfully = true;
                setOrderPlaced(true);
                if (this.abandonmentTimer) {
                    clearTimeout(this.abandonmentTimer);
                }
                this.saveSuccessfulOrderPreferences();

                if (is_whats_app) {
                    this.whatsAppOrderSubmit(orderResponse.data.data);
                }

                this.loading.isActive = false;
                this.submittingOrder = false;

                if (!is_whats_app) {
                        if (this.checkoutProps.form.payment_method !== this.enums.paymentTypeEnum.CASH_ON_DELIVERY) {
                            if (this.setting.paystack_status == 5 && this.setting.paystack_public_key) {
                                let handler = PaystackPop.setup({
                                    key: this.setting.paystack_public_key,
                                    email: orderResponse.data.data.email || this.setting.company_email || '',
                                    amount: Math.ceil(parseFloat(orderResponse.data.data.total) * 100),
                                    currency: 'KES',
                                    callback: function (response) {
                                        window.location.href = '/payment/paystack/' + orderResponse.data.data.id + '/success?reference=' + response.reference;
                                    },
                                    onClose: function () {
                                        router.push({ name: "frontend.checkout", query: { payment: "cancelled" } });
                                    }
                                });
                                handler.openIframe();
                            } else if (orderResponse.data.data.payment_url) {
                                window.location.href = orderResponse.data.data.payment_url;
                            } else {
                                router.push({ name: "frontend.myOrder.details", params: { id: orderResponse.data.data.id } });
                            }
                        } else {
                            this.clearCompletedCheckout();
                            router.push({ name: "frontend.myOrder.details", params: { id: orderResponse.data.data.id } });
                        }
                    } else {
                        this.clearCompletedCheckout();
                        if (env.DEMO === "true" || env.DEMO === true || env.DEMO === "1" || env.DEMO === 1) {
                        router.push({ name: "frontend.myOrder" });
                        }
                    }

            }).catch((err) => {
                this.loading.isActive = false;
                this.submittingOrder = false;
                if (typeof err.response?.data?.errors === 'object') {
                    _.forEach(err.response.data.errors, (error) => {
                        alertService.error(error[0]);
                    });
                } else {
                    alertService.error(err.response?.data?.message || 'Unable to place the order. Please try again.');
                }
            });
        },
        whatsAppOrderSubmit: function (order) {


            let text = `
${this.$t('menu.order')} - ${this.setting.company_name}
****************************************************
${this.$t('label.order_id')}#  : ${order.order_serial_no}
${this.$t("label.order_type")}  : ${this.enums.orderTypeEnumArray[order.order_type]}
${this.$t('label.delivery_time')}   :   ${order.delivery_date} (${order.delivery_time})
--------------------------
`;

            text += `
${this.$t('label.order_details')}
--------------------------
`;

            for (let i = 0; i < order.order_items.length; i++) {
                const item = order.order_items[i];

                text += `
${i + 1})  ${item.item_name}
`;

                //item variations
                if (item.item_variations.length > 0) {
                    for (let j = 0; j < item.item_variations.length; j++) {
                        const variation = item.item_variations[j];
                        text += `
    ${variation.variation_name} :   ${variation.name}
    `;
                    }
                }
                text += `
    --------------------------
    ${this.$t('label.price')}       : ${item.price}
    ${this.$t('label.quantity')}    : ${item.quantity}
    ${this.$t('label.discount')}    : ${item.discount}
    --------------------------
    ${this.$t('label.total')}       : ${item.total_currency_price}
    --------------------------
                    `;

                // item extras
                if (item.item_extras.length > 0) {
                    text += `
    ${this.$t('label.item')} ${this.$t('label.extras')}
    --------------------------
                        `;
                    for (let j = 0; j < item.item_extras.length; j++) {
                        const extra = item.item_extras[j];
                        text += `
        ${j + 1}    :   ${extra.name}
                            `;
                    }
                    text += `
    --------------------------
                        `;
                }
                if (item.instruction) {
                    text += `
    ${this.$t('label.instruction')}    :   ${item.instruction}
    `;
                }

            }


            text += `
${this.$t('label.subtotal')}        : ${order.subtotal_currency_price}
${this.$t('label.discount')}        : ${order.discount_currency_price}
${this.$t('label.delivery_charge')} :    ${order.delivery_charge_currency_price}
${this.$t('label.total')}           :   ${order.total_currency_price}
--------------------------
`;


            text += `
${this.$t('label.customer')}
--------------------------
${this.$t('label.name')}    :   ${order.user?.name}
${this.$t('label.email')}   :   ${order.user?.email}
${this.$t('label.mobile_number')}  : ${this.internationalPhone(this.countryCode.calling_code, order.user?.phone)}
${this.$t('label.apartment')}  : ${order.order_address?.apartment}
${this.$t('label.address')}  : ${order.order_address?.address}
`;



            //send to whats app
            text = encodeURIComponent(text);

            if (env.DEMO === "true" || env.DEMO === true || env.DEMO === "1" || env.DEMO === 1) {
                window.open("https://api.whatsapp.com/send?phone=" + this.countryCode.calling_code + '' + this.whatsappSetup.phone + "&text=" + text, "_blank");
            } else {
                window.location = "https://api.whatsapp.com/send?phone=" + this.countryCode.calling_code + '' + this.whatsappSetup.phone + "&text=" + text;
            }
        },
        changeOrderType: function (e) {
            this.checkoutProps.form.order_type = e;
            this.$store.dispatch('frontendCart/updateOrderType', this.checkoutProps.form.order_type).then().catch();
            if (this.checkoutProps.form.order_type === orderTypeEnum.TAKEAWAY) {
                this.checkoutProps.form.delivery_charge = 0;
                if (!this.checkoutProps.form.branch_id) {
                    this.checkoutProps.form.branch_id = this.$store.getters['globalState/lists'].branch_id || null;
                }
                if (this.checkoutProps.form.branch_id) {
                    this.$store.dispatch("frontendBranch/show", this.checkoutProps.form.branch_id).then(branchRes => {
                        this.location = {
                            lat: branchRes.data.data.latitude,
                            lng: branchRes.data.data.longitude
                        };
                        this.branchAddress = branchRes.data.data.address;
                        this.mapShow = true;
                    }).catch();
                }
            } else {
                this.deliveryChargeCalculation();
            }
        },
        internationalPhone: function (countryCode, localPhone) {
            const code = String(countryCode || '').replace(/\D/g, '');
            let number = String(localPhone || '').replace(/\D/g, '');
            if (code && number.startsWith(code)) {
                number = number.slice(code.length);
            }
            return (code ? `+${code}` : '') + number.replace(/^0+/, '');
        },
        branchWhatsappSetup: function () {
            this.whatsappSetup = {
                status: activityEnum.DISABLE,
                phone: null
            }
            this.$store.dispatch('frontendBranch/whatsappSetup', this.checkoutProps.form.branch_id)
                .then((res) => {
                    const setup = res.data.data;
                    this.whatsappSetup = {
                        status: setup?.status,
                        phone: setup?.phone
                    }
                })
        }
    },
    watch: {
        showCashOnDelivery(isVisible) {
            if (!isVisible && this.checkoutProps.form.payment_method === this.enums.paymentTypeEnum.CASH_ON_DELIVERY) {
                this.checkoutProps.form.payment_method = this.enums.paymentTypeEnum.E_WALLET;
            }
        },
        authInfo: {
            deep: true,
            handler(newVal) {
                if (this.authStatus && newVal) {
                    this.checkoutProps.form.customer_name = newVal.name || newVal.first_name || '';
                    this.checkoutProps.form.customer_phone = newVal.phone || '';
                    this.checkoutProps.form.customer_email = newVal.email || '';
                }
            }
        },
        'checkoutProps.form.customer_name': function () {
            this.saveCustomerPreferences();
        },
        'checkoutProps.form.customer_phone': function () {
            this.saveCustomerPreferences();
        },
        'checkoutProps.form.customer_email': function () {
            this.saveCustomerPreferences();
        },
        globalState: {
            deep: true,
            handler(global) {
                if (!this.checkoutProps.form.branch_id && global.branch_id && global.branch_id !== "undefined") {
                    this.loading.isActive = true;
                    this.checkoutProps.form.branch_id = global.branch_id;
                    this.$store.dispatch("frontendBranch/show", this.checkoutProps.form.branch_id).then(res => {
                        this.loading.isActive = false;
                        this.location.lat = res.data.data.latitude;
                        this.location.lng = res.data.data.longitude;
                        this.branchAddress = res.data.data.address;
                    }).catch();

                    window.setTimeout(() => {
                        this.mapShow = true;
                    }, 3000);
                }
            }
        },
        orderType: {
            deep: true,
            handler(orderTypeObject) {
                this.checkoutProps.form.order_type = orderTypeObject;
                if (orderTypeObject === orderTypeEnum.TAKEAWAY) {
                    this.checkoutProps.form.delivery_charge = 0;
                } else {
                    this.deliveryChargeCalculation();
                }
            }
        },
        'checkoutProps.form.branch_id': {
            immediate: true,
            handler(newBranchId) {
                if (newBranchId && newBranchId > 0) {
                    localStorage.setItem('selected_branch_id', newBranchId);
                    this.$store.dispatch('globalState/set', { branch_id: newBranchId });
                    this.saveCheckoutDraft();
                }
            }
        }
    }
}
</script>
