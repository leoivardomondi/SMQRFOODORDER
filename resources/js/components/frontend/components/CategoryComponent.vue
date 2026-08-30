<template>
    <div class="flex overflow-x-auto gap-6 border-b border-gray-200 dark:border-gray-800 hide-scrollbar pb-2 pt-3 px-2 bg-white dark:bg-gray-900 transition-all">
        <button
            v-for="category in categories" :key="category.slug"
            @click.prevent="handleCategoryClick(category.slug)"
            class="whitespace-nowrap pb-2.5 text-sm font-medium transition-all relative cursor-pointer outline-none bg-transparent border-0"
            :class="isCategoryActive(category.slug) ? 'text-black dark:text-white font-bold' : 'text-gray-500 hover:text-black dark:hover:text-white'"
        >
            {{ category.name }}
            <div v-if="isCategoryActive(category.slug)" class="absolute bottom-0 left-0 w-full h-[3px] bg-black dark:bg-white rounded-full transition-all"></div>
        </button>
    </div>
</template>

<script>
export default {
    name: "CategoryComponent",
    props: {
        categories: {
            type: [Array, Object],
            default: () => []
        },
        activeCategory: {
            type: String,
            default: ""
        },
        design: Number
    },
    methods: {
        isCategoryActive(slug) {
            if (this.activeCategory) {
                return this.activeCategory === slug;
            }
            if (this.$route.query.s) {
                return this.$route.query.s === slug;
            }
            if (Array.isArray(this.categories) && this.categories.length > 0) {
                return this.categories[0].slug === slug;
            }
            return false;
        },
        handleCategoryClick(slug) {
            this.$emit("select-category", slug);
            if (this.$route.name !== 'frontend.menu') {
                this.$router.push({ name: 'frontend.menu', query: { s: slug } });
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
