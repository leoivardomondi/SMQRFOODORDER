<template>
    <!--========ITEM PART START=========-->
    <div v-if="design === itemDesignEnum.LIST" class="flex flex-col gap-6">
        <div v-for="item in items" :key="item.id" v-show="type === null || type === item.item_type" class="flex gap-4 relative">
            <img class="w-24 h-24 sm:w-28 sm:h-28 rounded-xl object-cover" :src="item.thumb" alt="thumbnail">
            <div class="flex-1 flex flex-col justify-start">
                <div class="flex justify-between items-start mb-1">
                    <h3 class="text-base sm:text-lg font-medium text-gray-800">{{ item.name }}</h3>
                    <div class="text-right">
                        <h4 class="text-base font-medium text-gray-800">
                            {{ item.offer.length > 0 ? item.offer[0].currency_price : item.currency_price }}
                        </h4>
                        <del v-if="item.offer.length > 0" class="text-xs text-gray-400 block">
                            {{ item.currency_price }}
                        </del>
                        <div v-if="item.offer.length > 0" class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded inline-block mt-1">
                            -20%
                        </div>
                    </div>
                </div>
                <p class="text-sm text-gray-500 leading-snug line-clamp-3 w-4/5">{{ item.description }}</p>
                <div class="absolute bottom-0 right-0">
                    <button v-if="cartQuantity(item) === 0" @click.prevent="handleAddItem(item)" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-gray-200 transition">
                        <i class="fa-solid fa-plus text-sm"></i>
                    </button>
                    <div v-else class="flex items-center gap-2 rounded-full bg-gray-100 p-1">
                        <button @click.prevent="decrementOrDeleteCartItem(item)" class="w-7 h-7 rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-200 transition" :aria-label="cartQuantity(item) > 1 ? 'Decrease quantity' : 'Remove item from cart'">
                            <i :class="cartQuantity(item) > 1 ? 'fa-solid fa-minus' : 'fa-solid fa-trash-can'" class="text-xs"></i>
                        </button>
                        <span class="min-w-[1.25rem] text-center text-sm font-semibold">{{ cartQuantity(item) }}</span>
                        <button @click.prevent="incrementCartItem(item)" class="w-7 h-7 rounded-full flex items-center justify-center text-primary hover:bg-gray-200 transition" aria-label="Add one item">
                            <i class="fa-solid fa-plus text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-else-if="design === itemDesignEnum.GRID"
        class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 lg:gap-6">
        <div v-for="item in items" :key="item" v-show="type === null || type === item.item_type"
            class="product-card-grid">
            <img class="product-card-grid-image" :src="item.cover" alt="product">
            <div class="product-card-grid-content-group">
                <div class="product-card-grid-header-group">
                    <h3 class="product-card-grid-title">{{ textShortener(item.name, 26) }}</h3>
                    <button type="button" class="product-card-grid-info-btn info-btn leading-none"
                        data-modal="#item-info-modal" @click.prevent="infoModalShow(item.name, item.caution)">
                        <i class="lab lab-information font-fill-paragraph transition lab-font-size-16"></i>
                    </button>
                </div>
                <p class="product-card-grid-describe char-limit">{{ textShortener(item.description, 50) }}</p>
                <div class="product-card-grid-footer-group">
                    <div class="product-card-grid-price-group">
                        <del v-if="item.offer.length > 0" class="product-card-grid-price-previous">
                            {{ item.currency_price }}
                        </del>
                        <h4 class="product-card-grid-price-current">
                            {{ item.offer.length > 0 ? item.offer[0].currency_price : item.currency_price }}
                        </h4>
                    </div>
                    <button v-if="cartQuantity(item) === 0" @click.prevent="handleAddItem(item)"
                        class="product-card-grid-cart-btn add-btn">
                        <i class="lab lab-bag-2 font-fill-primary transition !text-xs sm:!text-sm"></i>
                        <span class="text-[10px] sm:text-xs text-primary transition">{{ $t('button.add') }}</span>
                    </button>
                    <div v-else class="flex items-center gap-1 rounded-full bg-gray-100 p-1">
                        <button @click.prevent="decrementOrDeleteCartItem(item)" class="w-7 h-7 rounded-full text-gray-600 hover:bg-gray-200 transition" :aria-label="cartQuantity(item) > 1 ? 'Decrease quantity' : 'Remove item from cart'">
                            <i :class="cartQuantity(item) > 1 ? 'fa-solid fa-minus' : 'fa-solid fa-trash-can'" class="text-xs"></i>
                        </button>
                        <span class="min-w-[1.25rem] text-center text-xs font-semibold">{{ cartQuantity(item) }}</span>
                        <button @click.prevent="incrementCartItem(item)" class="w-7 h-7 rounded-full text-primary hover:bg-gray-200 transition" aria-label="Add one item">
                            <i class="fa-solid fa-plus text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--========ITEM PART END===========-->

    <!--========INFO PART START=========-->
    <div id="item-info-modal" ref="itemInfoModal" class="modal ff-modal info-modal">
        <div class="modal-dialog" v-if="itemInfo">
            <div class="modal-header">
                <h3 class="modal-title text-base font-medium">{{ itemInfo.name }}</h3>
                <button class="modal-close fa-regular fa-circle-xmark" @click.prevent="infoModalHide"></button>
            </div>
            <div class="modal-body">
                {{ itemInfo.caution }}
            </div>
        </div>
    </div>
    <!--========INFO PART END===========-->

    <!--========VARIATION PART START=========-->
    <div id="item-variation-modal" ref="itemVariationModal" class="fixed inset-0 z-[100] hidden bg-black text-white flex flex-col h-full w-full overflow-hidden">
        <div class="w-full h-full flex flex-col relative overflow-hidden" v-if="item">
            
            <!-- Fixed Top Header Bar -->
            <div class="sticky top-0 left-0 right-0 z-30 bg-black/95 backdrop-blur-md border-b border-gray-800 px-4 py-3 flex items-center justify-between shadow-xs flex-shrink-0">
                <div class="flex items-center gap-3 min-w-0 pr-2">
                    <!-- Close Button -->
                    <button class="w-9 h-9 flex items-center justify-center rounded-full bg-gray-800 text-white hover:bg-gray-700 transition flex-shrink-0"
                        @click.prevent="variationModalHide" aria-label="Close product view">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                    <!-- Fixed Product Name -->
                    <h2 class="text-base sm:text-lg font-bold text-white truncate capitalize">{{ item.name }}</h2>
                </div>
                <!-- Price Display -->
                <span class="text-sm sm:text-base font-bold text-primary flex-shrink-0">
                    {{ item.offer.length > 0 ? item.offer[0].currency_price : item.currency_price }}
                </span>
            </div>

            <!-- Scrollable Body Content Area -->
            <div class="flex-1 overflow-y-auto bg-black text-white">
                <!-- Header Cover Image -->
                <div class="relative w-full min-h-[220px] sm:min-h-[300px] max-h-[400px] bg-slate-950 flex items-center justify-center p-2">
                    <img class="max-w-full max-h-[380px] w-auto h-auto object-contain mx-auto rounded-lg shadow-md" :src="item.cover || item.thumb" alt="product image">
                </div>

                <!-- Product Details & Addons Area -->
                <div class="px-4 py-5 mb-28">
                    <h2 class="text-2xl font-bold text-white mb-1 capitalize">{{ item.name }}</h2>
                    <h3 class="text-xl font-bold text-white mb-4">
                        {{ item.offer.length > 0 ? item.offer[0].currency_price : item.currency_price }}
                    </h3>
                    <p v-if="item.description" class="text-base text-gray-400 mb-6">{{ item.description }}</p>

                    <!-- Quantity Selector -->
                    <div class="flex justify-center mb-6">
                        <div class="flex items-center gap-6 px-6 py-3 rounded-full bg-gray-800 text-white">
                            <button @click.prevent="quantityDecrement" class="text-gray-400 hover:text-white" aria-label="Decrease quantity">
                                <i class="fa-solid fa-minus text-lg"></i>
                            </button>
                            <span class="text-lg font-bold text-white w-6 text-center">{{ temp.quantity }}</span>
                            <button @click.prevent="quantityIncrement" class="text-gray-400 hover:text-white" aria-label="Increase quantity">
                                <i class="fa-solid fa-plus text-lg"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Variations -->
                    <div v-if="item.itemAttributes && item.itemAttributes.length > 0" class="mb-6">
                        <div v-for="attribute in item.itemAttributes" :key="attribute.id" class="mb-4">
                            <h3 class="text-sm font-semibold text-white mb-2 capitalize">{{ attribute.name }}</h3>
                            <div class="flex flex-wrap gap-2">
                                <button type="button"
                                    v-for="variation in item.variations[attribute.id]"
                                    :key="variation.id"
                                    @click.prevent="changeVariationAdjust(attribute.id, variation.id)"
                                    class="px-4 py-2 text-sm rounded-lg border transition font-medium"
                                    :class="temp.item_variations.variations[attribute.id] === variation.id ? 'border-primary bg-primary/20 text-primary font-bold' : 'border-gray-800 text-gray-300 bg-gray-900'">
                                    {{ variation.name }} <span v-if="variation.convert_price > 0" class="text-xs opacity-75">(+{{ variation.currency_price }})</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Extras -->
                    <div v-if="item.extras && item.extras.length > 0" class="mb-6">
                        <h3 class="text-sm font-semibold text-white mb-2 capitalize">Extras</h3>
                        <div class="flex flex-col gap-2">
                            <label v-for="extra in item.extras" :key="extra.id" class="flex items-center justify-between p-3 rounded-xl border border-gray-800 bg-gray-900 cursor-pointer hover:border-primary transition">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" :value="extra.id" @change="changeExtra($event, extra.id, extra.name)" class="w-4 h-4 rounded text-primary focus:ring-primary bg-gray-800 border-gray-700">
                                    <span class="text-sm font-medium text-gray-200">{{ extra.name }}</span>
                                </div>
                                <span class="text-xs font-semibold text-gray-400">+{{ extra.currency_price }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Addons Grouped by Category -->
                    <div v-if="Object.keys(groupedAddons).length > 0" class="mb-6">
                        <div v-for="(addonGroup, groupTitle) in groupedAddons" :key="groupTitle" class="mb-5">
                            <h3 class="text-sm font-bold text-white mb-3 uppercase tracking-wide">
                                <span>{{ groupTitle }}</span>
                            </h3>
                            <div class="flex flex-col gap-3">
                                <div v-for="addon in addonGroup" :key="addon.id" class="flex items-center justify-between p-3 rounded-xl border border-gray-800 bg-gray-900">
                                    <div class="flex items-center gap-3">
                                        <img :src="addon.thumb" alt="addon image" class="w-12 h-12 rounded-lg object-cover bg-gray-800">
                                        <div>
                                            <h4 class="text-sm font-medium text-white">{{ addon.addon_item_name }}</h4>
                                            <p class="text-xs text-gray-400 font-semibold">{{ addon.offer.length > 0 ? addon.offer[0].currency_price : addon.addon_item_currency_price }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <button v-if="!addons[addon.id]" @click.prevent="changeAddon(addon)" class="px-3 py-1.5 rounded-full text-xs font-bold text-primary border border-primary hover:bg-primary hover:text-white transition">
                                            + Add
                                        </button>
                                        <div v-else class="flex items-center gap-2 rounded-full bg-gray-800 px-2 py-1">
                                            <button @click.prevent="addonQuantityDecrement(addon.id)" class="w-6 h-6 rounded-full flex items-center justify-center text-gray-400 hover:bg-gray-700">
                                                <i class="fa-solid fa-minus text-xs"></i>
                                            </button>
                                            <span class="text-xs font-bold text-white min-w-[1rem] text-center">{{ addonQuantity[addon.id] }}</span>
                                            <button @click.prevent="addonQuantityIncrement(addon.id)" class="w-6 h-6 rounded-full flex items-center justify-center text-primary hover:bg-gray-700">
                                                <i class="fa-solid fa-plus text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-12">
                        <h3 class="text-sm font-semibold text-white mb-2">Special Instructions</h3>
                        <textarea v-model="temp.instruction" placeholder="Add note" class="w-full border border-gray-800 rounded-lg p-2 text-sm bg-gray-900 text-white placeholder-gray-500"></textarea>
                    </div>
                </div>
            </div>

            <!-- Fixed Bottom Action -->
            <div class="fixed bottom-0 left-0 w-full p-4 bg-black/95 backdrop-blur-md border-t border-gray-800 z-30">
                <button type="button" :disabled="temp.total_price <= 0" @click.prevent="addToCart"
                    class="w-full bg-primary text-white font-bold text-lg py-4 rounded-full shadow-lg hover:bg-primary-dark transition flex justify-center items-center">
                    Add {{ temp.quantity }} for {{
                            currencyFormat(temp.total_price, setting.site_digit_after_decimal_point,
                                setting.site_default_currency_symbol, setting.site_currency_position)
                        }}
                </button>
            </div>
        </div>
    </div>
    <!--========VARIATION PART END===========-->
</template>
<script>
import itemDesignEnum from "../../../enums/modules/itemDesignEnum";
import appService from "../../../services/appService";
import _ from 'lodash';
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';


export default {
    name: "ItemComponent",
    components: {
        Swiper,
        SwiperSlide,
    },
    props: {
        items: Object,
        design: Number,
        type: Number
    },
    data() {
        return {
            item: null,
            itemInfo: null,
            addons: {},
            addonQuantity: {},
            itemArrays: [],
            itemDesignEnum: itemDesignEnum,
            settings: {
                itemsToShow: 4.3,
                wrapAround: false,
                snapAlign: "start"
            },
            addonSettings: {
                itemsToShow: 3,
                wrapAround: false,
                snapAlign: "start"
            },
            temp: {
                name: "",
                image: "",
                item_id: 0,
                quantity: 0,
                discount: 0,
                currency_price: 0,
                convert_price: 0,
                item_variations: {
                    variations: {},
                    names: {}
                },
                item_extras: {
                    extras: [],
                    names: []
                },
                item_variation_total: 0,
                item_extra_total: 0,
                total_price: 0,
                instruction: "",
            },
        }
    },
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        carts: function () {
            return this.$store.getters['frontendCart/lists'];
        },
        groupedAddons: function () {
            if (!this.item || !this.item.addons || this.item.addons.length === 0) return {};

            const groups = {};
            _.forEach(this.item.addons, (addon) => {
                let catName = addon.addon_item_category_name || (addon.addonItem && addon.addonItem.category ? addon.addonItem.category.name : '');
                if (!catName) {
                    const name = (addon.addon_item_name || '').toLowerCase();
                    if (name.includes('drink') || name.includes('soda') || name.includes('juice') || name.includes('water') || name.includes('coke') || name.includes('sprite') || name.includes('fanta') || name.includes('pepsi') || name.includes('beverage')) {
                        catName = 'Drinks';
                    } else if (name.includes('fries') || name.includes('chips')) {
                        catName = 'Fries';
                    } else {
                        catName = 'Addons';
                    }
                }

                let sectionTitle = '';
                const catLower = catName.trim().toLowerCase();
                if (catLower.includes('drink') || catLower.includes('beverage') || catLower.includes('soda')) {
                    sectionTitle = 'ADD A DRINK ?';
                } else if (catLower.includes('fries') || catLower.includes('chips')) {
                    sectionTitle = 'ADD FRIES ?';
                } else if (catLower === 'addons' || catLower === 'addon') {
                    sectionTitle = 'ADDONS ?';
                } else {
                    sectionTitle = 'ADD ' + catName.toUpperCase() + ' ?';
                }

                if (!groups[sectionTitle]) {
                    groups[sectionTitle] = [];
                }
                groups[sectionTitle].push(addon);
            });
            return groups;
        }
    },
    methods: {
        setup() {
            const spaceBetween = 10;
            const onProgress = (e) => {
                const [swiper, progress] = e.detail;
            };

            const onSlideChange = (e) => {

            }

            return {
                spaceBetween,
                onProgress,
                onSlideChange,
            };
        },
        onlyNumber: function (e) {
            return appService.onlyNumber(e);
        },
        textShortener: function (text, number) {
            return appService.textShortener(text, number);
        },
        currencyFormat: function (amount, decimal, currency, position) {
            return appService.currencyFormat(amount, decimal, currency, position);
        },
        infoModalShow: function (name, caution) {
            this.itemInfo = {
                name: name,
                caution: caution
            };
            const modalTarget = this.$refs.itemInfoModal;
            modalTarget?.classList?.add("active");
            document.body.style.overflowY = "hidden";
        },
        infoModalHide: function () {
            this.itemInfo = null;
            const modalDiv = this.$refs.itemInfoModal;
            modalDiv?.classList?.remove("active");
            document.body.style.overflowY = "auto";
        },
        requiresConfiguration: function (item) {
            return (item.itemAttributes && item.itemAttributes.length > 0) ||
                (item.extras && item.extras.length > 0) ||
                (item.addons && item.addons.length > 0);
        },
        handleAddItem: function (selectedItem) {
            if (selectedItem.itemAttributes && selectedItem.extras && selectedItem.addons) {
                if (this.requiresConfiguration(selectedItem)) {
                    this.variationModalShow(selectedItem);
                } else {
                    this.addSimpleItemToCart(selectedItem);
                }
                return;
            }

            this.$store.dispatch('frontendItem/details', selectedItem.id).then((res) => {
                const item = res.data.data;
                if (this.requiresConfiguration(item)) {
                    this.variationModalShow(item, false);
                } else {
                    this.addSimpleItemToCart(item);
                }
            }).catch(() => {});
        },
        addSimpleItemToCart: function (item) {
            const price = item.offer.length > 0 ? item.offer[0] : item;
            this.$store.dispatch("frontendCart/lists", [{
                name: item.name,
                image: item.thumb,
                item_id: item.id,
                quantity: 1,
                discount: 0,
                currency_price: price.currency_price,
                convert_price: price.convert_price,
                item_variations: { variations: {}, names: {} },
                item_extras: { extras: [], names: [] },
                item_variation_total: 0,
                item_extra_total: 0,
                instruction: ""
            }]).catch(() => {});
        },
        simpleCartIndex: function (item) {
            return this.carts.findIndex((cartItem) =>
                cartItem.item_id === item.id &&
                Object.keys(cartItem.item_variations.variations || {}).length === 0 &&
                (cartItem.item_extras.extras || []).length === 0 &&
                !cartItem.instruction
            );
        },
        cartQuantity: function (item) {
            const index = this.simpleCartIndex(item);
            return index === -1 ? 0 : this.carts[index].quantity;
        },
        incrementCartItem: function (item) {
            const index = this.simpleCartIndex(item);
            if (index !== -1) {
                this.$store.dispatch('frontendCart/quantity', { id: index, status: 'increment' });
            }
        },
        decrementOrDeleteCartItem: function (item) {
            const index = this.simpleCartIndex(item);
            if (index !== -1) {
                if (this.carts[index].quantity > 1) {
                    this.$store.dispatch('frontendCart/quantity', { id: index, status: 'decrement' });
                } else {
                    this.$store.dispatch('frontendCart/deleteCartItem', index);
                }
            }
        },
        variationModalShow: function (selectedItem, fetchDetails = true) {

            if (!fetchDetails) {
                this.prepareVariationModal(selectedItem);
                return;
            }

            this.$store.dispatch('frontendItem/details', selectedItem.id)
                .then((res) => {
                    this.prepareVariationModal(res.data.data);
                }).catch(() => {});
        },
        prepareVariationModal: function (item) {
                    this.item = item;

                    if (this.item.itemAttributes.length > 0) {
                        _.forEach(this.item.itemAttributes, (element) => {
                            if (typeof this.item.variations[element.id][0] !== "undefined") {
                                this.temp.item_variations.variations[this.item.variations[element.id][0].item_attribute_id] = this.item.variations[element.id][0].id;
                                this.temp.item_variations.names[element.name] = this.item.variations[element.id][0].name;
                                this.temp.item_variation_total += this.item.variations[element.id][0].convert_price;
                            }
                        });
                    }

                    if (this.item.addons.length > 0) {
                        _.forEach(this.item.addons, (addon) => {
                            this.addonQuantity[addon.id] = 1;
                        });
                    }

                    this.temp.name = this.item.name;
                    this.temp.image = this.item.thumb;
                    this.temp.item_id = this.item.id;
                    this.temp.quantity = 1;
                    this.temp.discount = 0;
                    this.temp.convert_price = item.offer.length > 0 ? item.offer[0].convert_price : item.convert_price;
                    this.temp.currency_price = item.offer.length > 0 ? item.offer[0].currency_price : item.currency_price;
                    this.temp.total_price = (item.offer.length > 0 ? item.offer[0].convert_price : item.convert_price) + this.temp.item_variation_total;

                    const modalTarget = this.$refs.itemVariationModal;
                    modalTarget?.classList?.remove("hidden");
                    document.body.style.overflowY = "hidden";
                    document.body.classList.add("product-page-open");
        },
        variationModalHide: function () {
            this.item = null;

            this.temp.name = "";
            this.temp.image = "";
            this.temp.item_id = 0;
            this.temp.quantity = 0;
            this.temp.discount = 0;
            this.temp.currency_price = 0;
            this.temp.convert_price = 0;
            this.temp.item_variations = {
                variations: {},
                names: {}
            };
            this.temp.item_extras = {
                extras: [],
                names: []
            };
            this.temp.item_variation_total = 0;
            this.temp.item_extra_total = 0;
            this.temp.total_price = 0;
            this.temp.instruction = "";
            this.addons = {};

            const modalDiv = this.$refs.itemVariationModal;
            modalDiv?.classList?.add("hidden");
            document.body.style.overflowY = "auto";
            document.body.classList.remove("product-page-open");
        },
        changeVariation: function (attributeId, variationId, variationName, variationPrice) {
            this.temp.item_variations.variations[attributeId] = variationId;
            _.forEach(this.item.itemAttributes, (element) => {
                if (element.id === attributeId) {
                    this.temp.item_variations.names[element.name] = variationName;
                }
            });
            this.totalPriceSetup();
        },
        changeVariationAdjust: function (attributeId, variationId) {
            _.forEach(this.item.variations[attributeId], (variation) => {
                if (variation.id === variationId) {
                    this.changeVariation(attributeId, variationId, variation.name, variation.convert_price);
                }
            });
        },
        changeExtra: function (e, id, name) {
            if (e.target.checked) {
                this.temp.item_extras.extras.push(id);
                this.temp.item_extras.names.push(name);
            } else {
                for (let i = 0; i < this.temp.item_extras.extras.length; i++) {
                    if (this.temp.item_extras.extras[i] === id) {
                        this.temp.item_extras.extras.splice(i, 1);
                    }
                }
                for (let i = 0; i < this.temp.item_extras.names.length; i++) {
                    if (this.temp.item_extras.names[i] === name) {
                        this.temp.item_extras.names.splice(i, 1);
                    }
                }
            }
            this.totalPriceSetup();
        },
        totalPriceSetup: function () {
            let item_variation_total = 0;
            let item_extra_total = 0;
            let item_addon_total = 0;
            _.forEach(this.temp.item_variations.variations, (variationId, attributeId) => {
                _.forEach(this.item.variations[attributeId], (itemVariation) => {
                    if (variationId === itemVariation.id) {
                        item_variation_total += itemVariation.convert_price;
                    }
                });
            });

            _.forEach(this.temp.item_extras.extras, (extraId) => {
                _.forEach(this.item.extras, (itemExtra) => {
                    if (extraId === itemExtra.id) {
                        item_extra_total += itemExtra.convert_price;
                    }
                });
            });

            _.forEach(this.addons, (addon) => {
                item_addon_total += (addon.total_price * addon.quantity);
            });

            this.temp.item_variation_total = item_variation_total;
            this.temp.item_extra_total = item_extra_total;
            this.temp.total_price = parseFloat((((this.item.offer.length > 0 ? this.item.offer[0].convert_price : this.item.convert_price) + this.temp.item_variation_total + this.temp.item_extra_total) * this.temp.quantity) + item_addon_total);
        },
        quantityUp: function () {
            if (this.temp.quantity === 0) {
                this.temp.quantity = 1;
            }
            this.totalPriceSetup();
        },
        quantityIncrement: function () {
            this.temp.quantity++;
            if (this.temp.quantity <= 0) {
                this.temp.quantity = 1;
            }
            this.totalPriceSetup();
        },
        quantityDecrement: function () {
            this.temp.quantity--;
            if (this.temp.quantity <= 0) {
                this.temp.quantity = 1;
            }
            this.totalPriceSetup();
        },
        addonQuantityUp: function (id) {
            if (typeof this.addonQuantity[id] !== "undefined") {
                if (this.addonQuantity[id] === 0) {
                    this.addonQuantity[id] = 1;
                }
            }
            if (typeof this.addons[id] !== "undefined") {
                this.addons[id].quantity = this.addonQuantity[id];
            }

            this.totalPriceSetup();
        },
        addonQuantityIncrement: function (id) {
            if (typeof this.addonQuantity[id] !== "undefined") {
                this.addonQuantity[id]++;
                if (this.addonQuantity[id] <= 0) {
                    this.addonQuantity[id] = 1;
                }
                if (typeof this.addons[id] !== "undefined") {
                    this.addons[id].quantity = this.addonQuantity[id];
                }
                this.totalPriceSetup();
            }
        },
        addonQuantityDecrement: function (id) {
            if (typeof this.addonQuantity[id] !== "undefined") {
                this.addonQuantity[id]--;
                if (this.addonQuantity[id] <= 0) {
                    this.addonQuantity[id] = 1;
                }
                if (typeof this.addons[id] !== "undefined") {
                    this.addons[id].quantity = this.addonQuantity[id];
                }
                this.totalPriceSetup();
            }
        },
        changeAddon: function (addon) {
            if (typeof this.addons[addon.id] === "undefined") {
                this.addons[addon.id] = {
                    name: addon.addon_item_name,
                    image: addon.thumb,
                    item_id: addon.item_addon_id,
                    quantity: this.addonQuantity[addon.id],
                    discount: 0,
                    currency_price: addon.offer.length > 0 ? addon.offer[0].currency_price : addon.addon_item_currency_price,
                    convert_price: addon.offer.length > 0 ? addon.offer[0].convert_price : addon.addon_item_convert_price,
                    item_variations: {
                        variations: {},
                        names: {}
                    },
                    item_extras: {
                        extras: [],
                        names: []
                    },
                    item_variation_total: addon.variation_total_convert_price,
                    item_extra_total: 0,
                    total_price: addon.total_convert_price,
                    instruction: "",
                };
                if (addon.variations !== "undefined" && Object.keys(addon.variations).length !== 0) {
                    _.forEach(addon.variations, (variationId, attributeId) => {
                        this.addons[addon.id].item_variations.variations[attributeId] = variationId;
                    });

                }
                if (addon.variation_names.length > 0) {
                    _.forEach(addon.variation_names, (variation) => {
                        this.addons[addon.id].item_variations.names[variation.attribute_name] = variation.name;
                    });
                }
            } else {
                delete this.addons[addon.id];
            }
            this.totalPriceSetup();
        },
        addToCart: function () {
            this.itemArrays = [
                {
                    name: this.temp.name,
                    image: this.temp.image,
                    item_id: this.temp.item_id,
                    quantity: this.temp.quantity,
                    discount: this.temp.discount,
                    currency_price: this.temp.currency_price,
                    convert_price: this.temp.convert_price,
                    item_variations: this.temp.item_variations,
                    item_extras: this.temp.item_extras,
                    item_variation_total: this.temp.item_variation_total,
                    item_extra_total: this.temp.item_extra_total,
                    instruction: this.temp.instruction
                }
            ];

            if (this.addons !== "undefined" && Object.keys(this.addons).length !== 0) {
                _.forEach(this.addons, (addon) => {
                    this.itemArrays.push({
                        name: addon.name,
                        image: addon.image,
                        item_id: addon.item_id,
                        quantity: addon.quantity,
                        discount: addon.discount,
                        price: addon.price,
                        currency_price: addon.currency_price,
                        convert_price: addon.convert_price,
                        item_variations: addon.item_variations,
                        item_extras: addon.item_extras,
                        item_variation_total: addon.item_variation_total,
                        item_extra_total: addon.item_extra_total,
                        instruction: addon.instruction
                    });
                });
            }

            if (this.itemArrays.length > 0) {
                this.$store.dispatch("frontendCart/lists", this.itemArrays).then((res) => {
                    this.item = null;
                    this.temp.name = "";
                    this.temp.image = "";
                    this.temp.item_id = 0;
                    this.temp.quantity = 0;
                    this.temp.discount = 0;
                    this.temp.currency_price = 0;
                    this.temp.convert_price = 0;
                    this.temp.item_variations = {
                        variations: {},
                        names: {}
                    };
                    this.temp.item_extras = {
                        extras: [],
                        names: []
                    };
                    this.temp.item_variation_total = 0;
                    this.temp.item_extra_total = 0;
                    this.temp.total_price = 0;
                    this.temp.instruction = "";
                    this.addons = {};
                    this.itemArrays = [];

                    this.variationModalHide();
                    appService.openCanvas('cart');
                }).catch();
            }
        },
    }
}
</script>
<style>
.swiper-variation .swiper-wrapper {
    gap: 16px;
}
</style>
