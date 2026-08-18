<template>
  <div v-if="showOfferPopup && featuredOffer" class="fixed inset-0 z-[120] flex items-center justify-center bg-black/80 p-4" @click.self="closeOfferPopup">
    <div class="relative max-h-[92vh] w-full max-w-xl overflow-y-auto rounded-2xl bg-white shadow-2xl">
      <button type="button" aria-label="Close offer" class="absolute right-3 top-3 z-10 flex h-9 w-9 items-center justify-center rounded-full bg-black/70 text-xl text-white" @click="closeOfferPopup">
        <i class="fa-solid fa-xmark"></i>
      </button>
      <img class="block max-h-[68vh] w-full object-contain bg-black" :src="featuredOffer.image" :alt="featuredOffer.name">
      <div class="offer-popup-content p-5 sm:p-6">
        <p class="mb-1 text-xs font-semibold uppercase tracking-[0.2em] text-primary">Today’s offer</p>
        <h2 class="offer-title text-2xl font-bold">{{ featuredOffer.name }}</h2>
        <p v-if="featuredOffer.description" class="offer-description mt-2 leading-6">{{ featuredOffer.description }}</p>
        <router-link :to="{ name: 'frontend.offers.item', params: { slug: featuredOffer.slug } }" class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-primary px-5 py-3 font-semibold text-black" @click="closeOfferPopup">
          View offer
        </router-link>
      </div>
    </div>
  </div>
  <section class="mb-6 sm:mb-12" v-if="offers.length > 0">
    <div class="container">
    <div class="offers-carousel flex gap-3 sm:gap-4 overflow-x-auto">
      <article v-for="offer in offers" :key="offer.id"
        class="offer-carousel-card flex-shrink-0 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg">
        <router-link :to="{ name: 'frontend.offers.item', params: { slug: offer.slug } }" class="block bg-black">
          <img class="block w-full h-auto" :src="offer.image" :alt="offer.name" />
        </router-link>
        <div class="p-5 sm:p-6">
          <h3 class="offer-title text-xl sm:text-2xl font-semibold text-heading">{{ offer.name }}</h3>
          <p v-if="offer.description" class="offer-description mt-2 text-sm sm:text-base leading-6 text-paragraph">{{ offer.description }}</p>
          <router-link :to="{ name: 'frontend.offers.item', params: { slug: offer.slug } }"
            class="mt-5 inline-flex items-center justify-center rounded-full bg-primary px-5 py-3 text-sm font-semibold text-black">
            View offer
          </router-link>
        </div>
      </article>
      </div>
    </div>
  </section>
</template>
<script>
import statusEnum from "../../../enums/modules/statusEnum";

export default {
  name: "OfferComponent",
  components: {},
  props: {
    limit: Number,
  },
  data() {
    return {
      loading: {
        isActive: false,
      },
      showOfferPopup: false,
    };
  },
  mounted() {
    try {
      this.loading.isActive = true;
      this.$store.dispatch("frontendOffer/lists", {
        order_column: "id",
        order_type: "desc",
        limit: this.limit,
        status: statusEnum.ACTIVE,
      }).then(() => {
        this.loading.isActive = false;
        this.showOfferPopup = this.offers.length > 0;
      });
    } catch (err) {
      this.loading.isActive = false;
    }
  },
  computed: {
    offers: function () {
      return this.$store.getters["frontendOffer/lists"];
    },
    featuredOffer: function () {
      return this.offers[0] || null;
    },
  },
  methods: {
    closeOfferPopup: function () {
      this.showOfferPopup = false;
    },
  },
};
</script>
