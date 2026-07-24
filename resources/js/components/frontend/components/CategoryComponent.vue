<template>
    <div class="flex overflow-x-auto gap-6 border-b border-gray-200 hide-scrollbar pb-2 pt-4 px-4">
        <router-link
            v-for="category in categories" :key="category.slug"
            :to="{ name: 'frontend.menu', query: { s: category.slug } }"
            class="whitespace-nowrap pb-2 text-sm font-medium transition-colors relative"
            :class="checkIsQueryAndRouteQuerySame(category.slug) ? 'text-primary font-bold' : 'text-gray-500'"
        >
            {{ category.name }}
            <div v-if="checkIsQueryAndRouteQuerySame(category.slug)" class="absolute bottom-[-8px] left-0 w-full h-1 bg-primary rounded-t-md"></div>
        </router-link>
    </div>
</template>

<script>
export default {
    name: "CategoryComponent",
    props: {
        categories: Object,
        design: Number
    },
    data() {
        return {
            currentCategory: "",
        }
    },
    mounted() {
        if (this.$route.query.s !== "undefined") {
            this.currentCategory = this.$route.query.s;
        }
    },
    methods: {
        checkIsQueryAndRouteQuerySame(query) {
            if (this.currentCategory === query) {
                return true;
            }
        },
    },
    watch: {
        $route(to, from) {
            if (to.query.s !== "undefined") {
                this.currentCategory = to.query.s;
            }
        }
    }
}
</script>

<style scoped>
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
