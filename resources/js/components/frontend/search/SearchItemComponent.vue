<template>
    <LoadingComponent :props="loading" />
    <section class="mb-24 sm:mb-16 mt-4 sm:mt-8">
        <div class="container">
            <!-- Dedicated Search Bar on Search Page -->
            <div class="mb-6">
                <div class="frontend-search-bar flex items-center gap-3 border px-4 py-3 rounded-2xl shadow-lg focus-within:border-primary transition-all">
                    <i class="lab lab-search-normal text-primary text-xl"></i>
                    <input type="search" v-model="props.search.name" @input="onSearchInput"
                        placeholder="Search food, drinks, loaded fries..."
                        class="w-full bg-transparent text-base focus:outline-none" />
                    <button v-if="props.search.name" @click.prevent="clearSearch" type="button" class="text-gray-400 hover:text-white">
                        <i class="lab lab-close-circle-line text-xl text-red-400"></i>
                    </button>
                </div>
            </div>

            <div class="flex gap-2 sm:gap-4 items-center justify-between mb-4 sm:mb-6">
                <h2 class="capitalize text-lg sm:text-2xl font-semibold text-heading">
                    {{ props.search.name ? 'Search Results' : 'All Items' }}
                </h2>
                <div class="flex items-center gap-3">
                    <button type="button" class="lab lab-row-vertical lab-font-size-20 text-xl"
                        v-on:click="itemProps.design = itemDesignEnum.LIST"
                        :class="itemProps.design === itemDesignEnum.LIST ? 'text-primary' : 'text-gray-400'"></button>
                    <button type="button" class="lab lab-element-3 lab-font-size-20 text-xl"
                        v-on:click="itemProps.design = itemDesignEnum.GRID"
                        :class="itemProps.design === itemDesignEnum.GRID ? 'text-primary' : 'text-gray-400'"></button>
                </div>
            </div>
            <ItemComponent :items="items" :type="itemProps.type" :design="itemProps.design" />
        </div>
    </section>
</template>

<script>
import ItemComponent from "../components/ItemComponent";
import itemDesignEnum from "../../../enums/modules/itemDesignEnum";
import statusEnum from "../../../enums/modules/statusEnum";
import LoadingComponent from "../components/LoadingComponent";
import _ from "lodash";

export default {
    name: "SearchItemComponent",
    components: {
        ItemComponent, LoadingComponent
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            itemDesignEnum: itemDesignEnum,
            items: [],
            itemProps: {
                design: itemDesignEnum.GRID,
                type: null,
            },
            props: {
                search: {
                    paginate: 0,
                    order_column: 'id',
                    order_type: 'asc',
                    name: "",
                    status: statusEnum.ACTIVE,
                }
            },
        };
    },
    mounted() {
        this.props.search.name = this.$route.query.s || "";
        this.searItems();
    },
    methods: {
        itemTypeSet: function (e) {
            this.itemProps.type = e;
        },
        itemTypeReset: function () {
            this.itemProps.type = null;
        },
        clearSearch: function () {
            this.props.search.name = "";
            this.searItems();
        },
        onSearchInput: _.debounce(function () {
            this.searItems();
        }, 300),
        searItems: function () {
            this.loading.isActive = true;
            this.$store.dispatch("frontendItem/lists", this.props.search).then((res) => {
                this.items = res.data.data;
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        }
    },
    watch: {
        $route() {
            this.props.search.name = this.$route.query.s || "";
            this.searItems();
        }
    }
};
</script>
