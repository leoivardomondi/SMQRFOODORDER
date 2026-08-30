<template>
    <section class="pb-24 sm:pb-16 pt-2 sm:pt-4">
        <div class="container">
            <!-- Sticky Category Navigation Bar with Black Underline -->
            <div v-if="categories && categories.length > 0" class="mb-6 sm:mb-8 sticky top-[58px] lg:top-[74px] z-40">
                <CategoryComponent 
                    :categories="categories" 
                    :active-category="activeCategorySlug"
                    :design="categoryProps.design" 
                    @select-category="scrollToCategory"
                />
            </div>

            <!-- Loading Spinner -->
            <div v-if="loading.isActive" class="py-12 text-center text-gray-500">
                <i class="fa-solid fa-spinner fa-spin text-2xl mb-2 block"></i>
                <span>Loading Menu...</span>
            </div>

            <!-- Category Sections with ScrollSpy -->
            <div v-else-if="categorySections && categorySections.length > 0" class="flex flex-col gap-10">
                <div 
                    v-for="categorySection in categorySections" 
                    :key="categorySection.slug"
                    :id="'category-section-' + categorySection.slug"
                    class="category-menu-section scroll-mt-32"
                >
                    <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-800 pb-3 mb-6">
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white capitalize flex items-center gap-3">
                            {{ categorySection.name }}
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                                {{ categorySection.items ? categorySection.items.length : 0 }}
                            </span>
                        </h2>
                    </div>

                    <div v-if="categorySection.items && categorySection.items.length > 0">
                        <ItemComponent :items="categorySection.items" :type="itemProps.type" :design="itemProps.design" />
                    </div>
                    <div v-else class="py-6 text-center text-sm text-gray-400">
                        No items in this category yet.
                    </div>
                </div>
            </div>

            <div v-else-if="!loading.isActive" class="py-16 text-center text-gray-500">
                <p>No menu items available at this time.</p>
            </div>
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

export default {
    name: "MenuComponent",
    components: { CategoryComponent, ItemComponent },
    data() {
        return {
            loading: {
                isActive: true
            },
            itemTypeEnum: itemTypeEnum,
            itemDesignEnum: itemDesignEnum,
            activeCategorySlug: "",
            categorySections: [],
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
            },
            observer: null
        }
    },
    computed: {
        categories: function () {
            return this.$store.getters['frontendItemCategory/lists'] || [];
        }
    },
    mounted() {
        this.loadMenuData();
    },
    beforeUnmount() {
        if (this.observer) {
            this.observer.disconnect();
        }
    },
    methods: {
        async loadMenuData() {
            this.loading.isActive = true;
            try {
                const res = await this.$store.dispatch("frontendItemCategory/lists", this.categoryProps.search);
                const categoriesList = res.data.data || [];
                
                if (categoriesList.length === 0) {
                    this.loading.isActive = false;
                    return;
                }

                const querySlug = (this.$route.query.s || "").trim();
                const matchedCategory = categoriesList.find(c => c.slug === querySlug);
                this.activeCategorySlug = matchedCategory ? matchedCategory.slug : categoriesList[0].slug;

                const sectionPromises = categoriesList.map(cat => {
                    return this.$store.dispatch("frontendItemCategory/show", {
                        slug: cat.slug,
                        vuex: false
                    }).then(response => response.data.data).catch(() => cat);
                });

                const loadedSections = await Promise.all(sectionPromises);
                this.categorySections = loadedSections.filter(sec => sec && sec.items);
                this.loading.isActive = false;

                this.$nextTick(() => {
                    this.setupScrollSpy();
                    if (querySlug) {
                        this.scrollToCategory(this.activeCategorySlug, false);
                    }
                });
            } catch (err) {
                this.loading.isActive = false;
            }
        },
        scrollToCategory(slug, smooth = true) {
            this.activeCategorySlug = slug;
            const element = document.getElementById('category-section-' + slug);
            if (element) {
                const yOffset = -130;
                const y = element.getBoundingClientRect().top + window.pageYOffset + yOffset;
                window.scrollTo({ top: y, behavior: smooth ? 'smooth' : 'auto' });
            }
            if (this.$route.query.s !== slug) {
                this.$router.replace({ query: { ...this.$route.query, s: slug } }).catch(() => {});
            }
        },
        setupScrollSpy() {
            if (this.observer) {
                this.observer.disconnect();
            }
            const options = {
                root: null,
                rootMargin: '-120px 0px -50% 0px',
                threshold: 0
            };
            this.observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        const slug = entry.target.id.replace('category-section-', '');
                        if (slug && slug !== this.activeCategorySlug) {
                            this.activeCategorySlug = slug;
                            if (this.$route.query.s !== slug) {
                                this.$router.replace({ query: { ...this.$route.query, s: slug } }).catch(() => {});
                            }
                        }
                    }
                });
            }, options);

            const sections = document.querySelectorAll('.category-menu-section');
            sections.forEach((section) => this.observer.observe(section));
        }
    },
    watch: {
        '$route.query.s'(newSlug) {
            if (newSlug && newSlug !== this.activeCategorySlug) {
                this.scrollToCategory(newSlug, true);
            }
        }
    }
}
</script>
