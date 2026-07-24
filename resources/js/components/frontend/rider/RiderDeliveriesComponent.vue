<template>
    <section class="py-6 pb-24 sm:py-10">
        <div class="container max-w-5xl">
            <div class="flex items-center justify-between gap-4 mb-6">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-primary font-semibold">Rider workspace</p>
                    <h1 class="text-2xl font-bold text-heading">My Deliveries</h1>
                </div>
                <button @click="loadOrders" class="h-10 px-4 rounded-full bg-primary text-white text-sm font-semibold">
                    <i class="fa-solid fa-rotate-right mr-2"></i>Refresh
                </button>
            </div>

            <div class="grid grid-cols-3 gap-3 mb-6">
                <div class="rounded-2xl border border-primary/30 bg-white p-4">
                    <p class="text-xs text-paragraph">Active</p><strong class="text-2xl text-heading">{{ activeOrders.length }}</strong>
                </div>
                <div class="rounded-2xl border border-primary/30 bg-white p-4">
                    <p class="text-xs text-paragraph">Delivered</p><strong class="text-2xl text-heading">{{ deliveredOrders.length }}</strong>
                </div>
                <div class="rounded-2xl border border-primary/30 bg-white p-4">
                    <p class="text-xs text-paragraph">Returned</p><strong class="text-2xl text-heading">{{ returnedOrders.length }}</strong>
                </div>
            </div>

            <div class="flex gap-2 mb-5 p-1 rounded-xl bg-white border border-primary/20 w-fit">
                <button v-for="tab in tabs" :key="tab.value" @click="selectedTab = tab.value"
                    :class="selectedTab === tab.value ? 'bg-primary text-white' : 'text-heading'"
                    class="px-4 py-2 rounded-lg text-sm font-semibold transition">{{ tab.label }}</button>
            </div>

            <div v-if="loading" class="py-16 text-center text-paragraph">Loading deliveries…</div>
            <div v-else-if="visibleOrders.length === 0" class="rounded-2xl border border-dashed border-primary/40 bg-white py-16 px-6 text-center">
                <i class="fa-solid fa-motorcycle text-3xl text-primary mb-3"></i>
                <h2 class="font-bold text-heading mb-1">No {{ selectedTab.toLowerCase() }} deliveries</h2>
                <p class="text-sm text-paragraph">Assigned orders will appear here automatically.</p>
            </div>

            <div v-else class="grid sm:grid-cols-2 gap-4">
                <router-link v-for="order in visibleOrders" :key="order.id"
                    :to="{ name: 'frontend.rider.delivery.details', params: { id: order.id } }"
                    class="block rounded-2xl border border-primary/25 bg-white p-5 hover:border-primary transition">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div><p class="text-xs text-paragraph">Order</p><h2 class="font-bold text-lg text-heading">#{{ order.order_serial_no }}</h2></div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary">{{ order.status_name }}</span>
                    </div>
                    <p class="text-sm text-heading mb-2"><i class="fa-solid fa-location-dot text-primary mr-2"></i>{{ order.order_address?.address || 'Pickup address unavailable' }}</p>
                    <p class="text-sm text-paragraph"><i class="fa-regular fa-clock mr-2"></i>{{ order.delivery_date }} · {{ order.delivery_time }}</p>
                    <div class="mt-4 pt-4 border-t border-primary/15 flex justify-between text-sm font-semibold text-heading">
                        <span>{{ order.payment_status === 5 ? 'Paid' : 'Collect payment' }}</span><span>View details →</span>
                    </div>
                </router-link>
            </div>
        </div>
    </section>
</template>

<script>
import axios from 'axios';
import orderStatusEnum from '../../../enums/modules/orderStatusEnum';
import alertService from '../../../services/alertService';

export default {
    name: 'RiderDeliveriesComponent',
    data() {
        return { 
            loading: false, 
            orders: [], 
            availableOrders: [],
            selectedTab: 'Active', 
            tabs: [
                { label: 'Active', value: 'Active' }, 
                { label: 'Available Orders', value: 'Available' }, 
                { label: 'History', value: 'History' }
            ] 
        };
    },
    computed: {
        activeOrders() { return this.orders.filter(order => ![orderStatusEnum.DELIVERED, orderStatusEnum.RETURNED, orderStatusEnum.CANCELED, orderStatusEnum.REJECTED].includes(order.status)); },
        deliveredOrders() { return this.orders.filter(order => order.status === orderStatusEnum.DELIVERED); },
        returnedOrders() { return this.orders.filter(order => order.status === orderStatusEnum.RETURNED); },
        visibleOrders() { 
            if (this.selectedTab === 'Available') {
                return this.availableOrders;
            }
            return this.selectedTab === 'Active' ? this.activeOrders : this.orders.filter(order => [orderStatusEnum.DELIVERED, orderStatusEnum.RETURNED].includes(order.status)); 
        },
    },
    watch: {
        selectedTab() {
            this.loadOrders();
        }
    },
    mounted() { this.loadOrders(); },
    methods: {
        loadOrders() {
            this.loading = true;
            const params = { paginate: 0, order_column: 'id', order_by: 'desc' };
            if (this.selectedTab === 'Available') {
                params.available = 1;
            }
            axios.get('frontend/delivery-boy-order', { params })
                .then(res => { 
                    if (this.selectedTab === 'Available') {
                        this.availableOrders = res.data.data || [];
                    } else {
                        this.orders = res.data.data || []; 
                    }
                })
                .catch(err => alertService.error(err?.response?.data?.message || 'Unable to load deliveries.'))
                .finally(() => { this.loading = false; });
        },
    },
};
</script>
