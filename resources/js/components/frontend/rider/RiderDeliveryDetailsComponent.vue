<template>
    <section class="py-6 pb-24 sm:py-10">
        <div class="container max-w-3xl">
            <router-link :to="{ name: 'frontend.rider.deliveries' }" class="inline-flex items-center gap-2 text-primary font-semibold mb-5">← My Deliveries</router-link>
            <div v-if="loading" class="py-16 text-center text-paragraph">Loading delivery…</div>
            <div v-else-if="order.id" class="space-y-4">
                <div class="rounded-2xl border border-primary/25 bg-white p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div><p class="text-xs uppercase tracking-wider text-primary">Assigned delivery</p><h1 class="text-2xl font-bold text-heading">Order #{{ order.order_serial_no }}</h1></div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary">{{ order.status_name }}</span>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3 mt-5 text-sm">
                        <p><span class="block text-paragraph">Delivery time</span><strong>{{ order.delivery_date }} · {{ order.delivery_time }}</strong></p>
                        <p><span class="block text-paragraph">Payment</span><strong>{{ order.payment_status === 5 ? 'Paid' : 'Collect ' + order.total_currency_price }}</strong></p>
                    </div>
                </div>

                <div class="rounded-2xl border border-primary/25 bg-white p-5 sm:p-6">
                    <h2 class="font-bold text-heading mb-4">Customer & destination</h2>
                    <p class="font-semibold text-heading">{{ order.user?.name || 'Customer' }}</p>
                    <p class="text-sm text-paragraph mt-1">{{ order.order_address?.address || 'Address unavailable' }}</p>
                    <p v-if="order.order_address?.apartment" class="text-sm text-paragraph">Apartment: {{ order.order_address.apartment }}</p>
                    <div class="grid grid-cols-3 gap-2 mt-5">
                        <a v-if="isAssigned" :href="customerPhone ? ('tel:' + customerPhone) : null" 
                            class="py-3 rounded-xl text-center bg-primary text-white text-sm font-semibold shadow-xs">
                            <i class="fa-solid fa-phone mr-1"></i> Call
                        </a>
                        <button v-else disabled 
                            class="py-3 rounded-xl text-center bg-gray-200 text-gray-400 text-sm font-semibold cursor-not-allowed opacity-50">
                            <i class="fa-solid fa-phone mr-1"></i> Call
                        </button>

                        <a v-if="isAssigned" :href="whatsappPhone ? ('https://wa.me/' + whatsappPhone) : null" target="_blank" 
                            class="py-3 rounded-xl text-center bg-[#25D366] text-white text-sm font-semibold shadow-xs">
                            <i class="fa-brands fa-whatsapp mr-1"></i> WhatsApp
                        </a>
                        <button v-else disabled 
                            class="py-3 rounded-xl text-center bg-gray-200 text-gray-400 text-sm font-semibold cursor-not-allowed opacity-50">
                            <i class="fa-brands fa-whatsapp mr-1"></i> WhatsApp
                        </button>

                        <a v-if="isAssigned && mapUrl" :href="mapUrl" target="_blank" 
                            class="py-3 rounded-xl text-center border border-primary text-primary text-sm font-semibold shadow-xs">
                            <i class="fa-solid fa-location-arrow mr-1"></i> Map
                        </a>
                        <button v-else disabled 
                            class="py-3 rounded-xl text-center border border-gray-200 bg-gray-100 text-gray-400 text-sm font-semibold cursor-not-allowed opacity-50">
                            <i class="fa-solid fa-location-arrow mr-1"></i> Map
                        </button>
                    </div>
                    <p v-if="!isAssigned" class="text-xs text-amber-600 font-medium mt-2">
                        <i class="fa-solid fa-lock mr-1"></i> Customer contact & map navigation unlock once you claim this delivery.
                    </p>
                </div>

                <div class="rounded-2xl border border-primary/25 bg-white p-5 sm:p-6">
                    <h2 class="font-bold text-heading mb-4">Order items</h2>
                    <div v-for="item in order.order_items" :key="item.id" class="flex justify-between gap-4 py-3 border-b last:border-0 border-primary/15">
                        <span class="text-sm text-heading"><strong>{{ item.quantity }}×</strong> {{ item.item_name }}</span><strong class="text-sm">{{ item.total_currency_price }}</strong>
                    </div>
                    <div class="flex justify-between pt-4 text-lg font-bold text-heading"><span>Total</span><span>{{ order.total_currency_price }}</span></div>
                </div>

                <button v-if="!order.delivery_boy_id" @click="claimOrder" :disabled="saving"
                    class="w-full py-4 rounded-2xl bg-[#00B3A5] text-white font-bold disabled:opacity-50">{{ saving ? 'Claiming…' : 'Claim Delivery (Assign to Me)' }}</button>
                <button v-else-if="canStart" @click="changeStatus(status.OUT_FOR_DELIVERY)" :disabled="saving"
                    class="w-full py-4 rounded-2xl bg-primary text-white font-bold disabled:opacity-50">{{ saving ? 'Updating…' : 'Accept & Start Delivery' }}</button>
                <button v-else-if="canDeliver" @click="changeStatus(status.DELIVERED)" :disabled="saving"
                    class="w-full py-4 rounded-2xl bg-[#1AB759] text-white font-bold disabled:opacity-50">{{ saving ? 'Updating…' : 'Mark as Delivered' }}</button>
                <div v-else-if="order.status === status.DELIVERED" class="rounded-2xl bg-[#1AB759]/10 text-[#138a43] p-4 text-center font-bold">Delivery completed</div>
                <div v-else class="rounded-2xl bg-primary/10 text-heading p-4 text-center text-sm font-semibold">Waiting for the restaurant to prepare this order.</div>
            </div>
        </div>
    </section>
</template>

<script>
import axios from 'axios';
import orderStatusEnum from '../../../enums/modules/orderStatusEnum';
import alertService from '../../../services/alertService';
import VueSimpleAlert from 'vue3-simple-alert';

export default {
    name: 'RiderDeliveryDetailsComponent',
    data() { return { loading: false, saving: false, order: {}, status: orderStatusEnum }; },
    computed: {
        authInfo() { return this.$store.getters.authInfo; },
        isAssigned() {
            return !!(this.order && this.order.delivery_boy_id && this.authInfo && (this.order.delivery_boy_id === this.authInfo.id));
        },
        canStart() { return [this.status.ACCEPT, this.status.PREPARING, this.status.PREPARED].includes(this.order.status); },
        canDeliver() { return this.order.status === this.status.OUT_FOR_DELIVERY; },
        customerPhone() { return `${this.order.user?.country_code || ''}${this.order.user?.phone || ''}`.replace(/\s+/g, ''); },
        whatsappPhone() { return this.customerPhone.replace(/^\+/, '').replace(/^(254)0/, '$1'); },
        mapUrl() {
            const address = this.order.order_address;
            if (!address) return null;
            if (address.latitude && address.longitude) return `https://www.google.com/maps/search/?api=1&query=${address.latitude},${address.longitude}`;
            const query = encodeURIComponent(`${address.address}, ${address.city || ''}, ${address.state || ''}`);
            return `https://www.google.com/maps/search/?api=1&query=${query}`;
        },
    },
    mounted() { this.loadOrder(); },
    methods: {
        loadOrder() {
            this.loading = true;
            axios.get(`frontend/delivery-boy-order/show/${this.$route.params.id}`)
                .then(res => { this.order = res.data.data || {}; })
                .catch(err => alertService.error(err?.response?.data?.message || 'Unable to load this delivery.'))
                .finally(() => { this.loading = false; });
        },
        changeStatus(status) {
            const action = status === this.status.DELIVERED ? 'mark this order as delivered' : 'start this delivery';
            VueSimpleAlert.confirm(`Are you sure you want to ${action}?`, 'Confirm delivery update', 'question', { confirmButtonColor: '#cdaa5d' })
                .then(() => {
                    this.saving = true;
                    axios.post(`frontend/delivery-boy-order/change-status/${this.order.id}`, { status })
                        .then(res => { this.order = res.data.data; alertService.success('Delivery status updated.'); })
                        .catch(err => alertService.error(err?.response?.data?.message || 'Unable to update delivery.'))
                        .finally(() => { this.saving = false; });
                }).catch(() => {});
        },
        claimOrder() {
            VueSimpleAlert.confirm(`Are you sure you want to claim this delivery?`, 'Claim Delivery', 'question', { confirmButtonColor: '#00B3A5' })
                .then(() => {
                    this.saving = true;
                    axios.post(`frontend/delivery-boy-order/claim/${this.order.id}`)
                        .then(res => { 
                            this.order = res.data.data; 
                            alertService.success('You have successfully claimed this delivery.'); 
                            this.$router.push({ name: 'frontend.rider.deliveries' });
                        })
                        .catch(err => alertService.error(err?.response?.data?.message || 'Unable to claim delivery.'))
                        .finally(() => { this.saving = false; });
                }).catch(() => {});
        },
    },
};
</script>
