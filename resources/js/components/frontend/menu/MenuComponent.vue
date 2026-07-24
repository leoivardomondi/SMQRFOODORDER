<template>
    <!-- Removed preloader -->
    <section class="pb-24 sm:pb-16 pt-4 sm:pt-8">
        <div class="container">
            <div v-if="categories.length > 0" class="swiper mb-6 sm:mb-12 menu-swiper">
                <CategoryComponent :categories="categories" :design="categoryProps.design" />
            </div>

            <ItemComponent :items="items.items" :type="itemProps.type" :design="itemProps.design" />
        </div>
    </section>
</template>

<script>

import statusEnum from "../../../enums/modules/statusEnum";
import categoryDesignEnum from "../../../enums/modules/categoryDesignEnum";
import CategoryComponent from "../components/CategoryComponent";
import ItemComponent from "../components/ItemComponent";
import itemDesignEnum from "../../../enums/modules/itemDesignEnum";
import itemTypeEnum from "../../../enums/modules/itemTypeEnum";
import LoadingComponent from "../components/LoadingComponent";

export default {
    name: "MenuComponent",
    components: { CategoryComponent, ItemComponent, LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false
            },
            itemTypeEnum: itemTypeEnum,
            itemDesignEnum: itemDesignEnum,
            category: {},
            items: {},
            categoryProps: {
                search: {
                    paginate: 0,
                    order_column: 'sort',
                    order_type: 'asc',
                    status: statusEnum.ACTIVE
                },
                design: categoryDesignEnum.SECOND
            },
            itemProps: {
                design: itemDesignEnum.LIST,
                type: null
            }
        }
    },
    computed: {
        categories: function () {
            return this.$store.getters['frontendItemCategory/lists'];
        },
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        }
    },
    mounted() {
        this.$store.dispatch("frontendItemCategory/lists", this.categoryProps.search);
        this.categoryShow();
    },
    methods: {
        itemTypeSet: function (e) {
            this.itemProps.type = e;
        },
        itemTypeReset: function () {
            this.itemProps.type = null;
        },
        categoryShow: function () {
            if (typeof this.$route.query.s !== "undefined" && this.$route.query.s !== "") {
                this.loading.isActive = true;
                this.$store.dispatch("frontendItemCategory/show", {
                    slug: this.$route.query.s,
                    vuex: false
                }).then((res) => {
                    this.category = res.data.data;
                    this.items = res.data.data;
                    this.loading.isActive = false;
                }).catch((err) => {
                    this.loading.isActive = false;
                });
            }
        }
    },
    watch: {
        $route() {
            this.categoryShow();
        }
    }
}
</script>
