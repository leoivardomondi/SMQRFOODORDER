<template>
    <div class="flex overflow-x-auto gap-6 border-b border-gray-200 dark:border-gray-800 hide-scrollbar pb-2 pt-3 px-2 bg-white dark:bg-gray-900 transition-all">
        <button
            v-for="category in categories" 
            :key="category.slug"
            :ref="'cat-' + category.slug"
            @click.prevent="handleCategoryClick(category.slug)"
            class="whitespace-nowrap pb-2.5 text-sm transition-all relative cursor-pointer outline-none bg-transparent border-0 uppercase tracking-wide"
            :class="isCategoryActive(category.slug) ? 'text-black dark:text-white font-bold' : 'text-gray-500 font-medium hover:text-black dark:hover:text-white'"
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
    watch: {
        activeCategory: {
            immediate: true,
            handler(newSlug) {
                if (newSlug) {
                    this.$nextTick(() => {
                        this.scrollTabIntoView(newSlug);
                    });
                }
            }
        }
    },
    methods: {
        isCategoryActive(slug) {
            if (this.activeCategory !== undefined && this.activeCategory !== null && this.activeCategory !== "") {
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
        },
        scrollTabIntoView(slug) {
            const el = this.$refs['cat-' + slug];
            const targetEl = Array.isArray(el) ? el[0] : el;
            if (targetEl && targetEl.scrollIntoView) {
                targetEl.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
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

