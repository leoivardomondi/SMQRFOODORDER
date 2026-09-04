<template>
    <LoadingComponent :props="loading" />
    <div v-if="demo === 'true' || demo === 'TRUE' || demo === 'True' || demo === '1' || demo === 1"
        class="mb-4 mt-1 bg-red-100 p-2 pl-4  rounded">
        <h2 class="mb-1">{{ $t('label.reminder') }}</h2>
        <p>{{ $t('label.data_reset') }}</p>
    </div>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="font-semibold text-[26px] leading-10 capitalize text-primary">{{ visitorMessage() }}</h3>
            <h4 class="font-medium text-[22px] leading-[34px] capitalize">{{ authInfo.name }}</h4>
        </div>
        <div v-if="branches && branches.length > 0 && showBranchDropdown" class="flex items-center gap-3 bg-white px-4 py-2.5 rounded-xl border border-gray-200 shadow-xs w-fit">
            <i class="lab lab-shop lab-font-size-22 text-primary"></i>
            <div class="flex flex-col">
                <span class="text-[11px] font-medium text-gray-500 uppercase tracking-wider leading-tight">{{ $t('label.branch') }}</span>
                <select :value="defaultBranch" @change="changeBranch($event.target.value)" class="text-sm font-semibold text-heading bg-transparent border-none p-0 focus:outline-none cursor-pointer">
                    <option v-for="b in branches" :key="b.id" :value="b.id">
                        {{ b.name }}
                    </option>
                </select>
            </div>
        </div>
    </div>
    <!--========OVERVIEW START=============-->
    <OverviewComponent />
    <!--========OVERVIEW END=============-->

    <!--========ORDER STATISTIC START=============-->
    <OrderStatisticsComponent />
    <!--========ORDER STATISTIC END=============-->
    <div class="row">
        <!--========SALES SUMMARY START=============-->
        <SalesSummaryComponent />
        <!--========SALES SUMMARY END=============-->

        <!--========ORDERS SUMMARY START=============-->
        <OrderSummaryComponent />
        <!--========ORDERS SUMMARY END=============-->

        <!--========CUSTOMER STATS START=============-->
        <CustomerStatsComponent />
        <!--========CUSTOMER STATS END=============-->

        <!--========TOP CUSTOMERS START=============-->
        <TopCustomersComponent />
        <!--========TOP CUSTOMERS END=============-->

        <!--========FEATURED ITEMS START=============-->
        <FeaturedItemsComponent />
        <!--========FEATURED ITEMS END=============-->

        <!--========MOST POPULAR ITEMS START=============-->
        <MostPopularItemsComponent />
        <!--========MOST POPULAR ITEMS END=============-->
    </div>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";
import OverviewComponent from "./OverviewComponent";
import OrderStatisticsComponent from "./OrderStatisticsComponent";
import TopCustomersComponent from "./TopCustomersComponent";
import FeaturedItemsComponent from "./FeaturedItemsComponent";
import MostPopularItemsComponent from "./MostPopularItemsComponent";
import SalesSummaryComponent from "./SalesSummaryComponent";
import OrderSummaryComponent from "./OrderSummaryComponent";
import CustomerStatsComponent from "./CustomerStatsComponent";
import ENV from "../../../config/env";

export default {
    name: "DashboardComponent",
    components: {
        LoadingComponent,
        OverviewComponent,
        OrderStatisticsComponent,
        TopCustomersComponent,
        FeaturedItemsComponent,
        MostPopularItemsComponent,
        SalesSummaryComponent,
        OrderSummaryComponent,
        CustomerStatsComponent
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            defaultBranch: null,
            demo: ENV.DEMO
        };
    },
    computed: {
        authInfo: function () {
            return this.$store.getters.authInfo;
        },
        authBranch: function () {
            return this.$store.getters.authBranchId;
        },
        branches: function () {
            return this.$store.getters['backendGlobalState/branches'];
        },
        branch: function () {
            return this.$store.getters['backendGlobalState/branchShow'];
        },
        showBranchDropdown: function () {
            const b = this.authBranch;
            return b == 0 || b === "0" || b === null || b === undefined || b === "" || parseInt(b) === 0;
        }
    },
    mounted() {
        this.$store.dispatch("defaultAccess/show").then(res => {
            this.defaultBranch = res.data.data.branch_id;
        }).catch();
        this.$store.dispatch('backendGlobalState/branches', { paginate: 0, order_column: "id", order_type: "asc" }).then().catch();
    },
    methods: {
        changeBranch: function (id) {
            this.loading.isActive = true;
            this.$store.dispatch("defaultAccess/saveOrUpdate", { branch_id: id }).then(res => {
                this.$store.dispatch('backendGlobalState/branchShow', id).then(res => {
                    location.reload();
                }).catch();
            });
        },
        visitorMessage: function () {
            let greet;
            let myDate = new Date();
            let hrs = myDate.getHours();
            if (hrs < 12) {
                greet = this.$t('message.good_morning');
            } else if (hrs >= 12 && hrs <= 17) {
                greet = this.$t('message.good_afternoon');
            } else if (hrs >= 17 && hrs <= 24) {
                greet = this.$t('message.good_evening');
            }
            return greet;
        }
    }
}
</script>