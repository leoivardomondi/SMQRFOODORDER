<template>
  <transition name="slide-up">
    <div v-if="showOfferPopup && featuredOffer"
         class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-[120] w-[calc(100%-2rem)] max-w-sm overflow-hidden rounded-2xl bg-white shadow-2xl border border-gray-100 p-3 sm:p-4">
      <button type="button" aria-label="Close offer"
              class="absolute right-2 top-2 z-10 flex h-6 w-6 items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 text-xs transition"
              @click="closeOfferPopup">
        <i class="fa-solid fa-xmark"></i>
      </button>
      <div class="flex gap-3 items-center">
        <img class="h-20 w-20 shrink-0 rounded-xl object-cover bg-black" :src="featuredOffer.image" :alt="featuredOffer.name" />
        <div class="flex-1 min-w-0 pr-3">
          <span class="inline-block text-[10px] font-bold uppercase tracking-wider text-primary">Today’s offer</span>
          <h4 class="text-sm font-bold text-gray-900 truncate">{{ featuredOffer.name }}</h4>
          <p v-if="featuredOffer.description" class="mt-0.5 text-xs text-gray-500 line-clamp-2 leading-tight">{{ featuredOffer.description }}</p>
          <router-link :to="{ name: 'frontend.offers.item', params: { slug: featuredOffer.slug } }"
                       class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-primary px-3 py-1 text-xs font-bold text-black hover:opacity-90 transition"
                       @click="closeOfferPopup">
            View offer <i class="fa-solid fa-arrow-right text-[10px]"></i>
          </router-link>
        </div>
      </div>
    </div>
  </transition>

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
      scrollTimer: null,
      isScrollListenerAttached: false,
      hasDismissedPopup: false,
    };
  },
  computed: {
    offers: function () {
      return this.$store.getters["frontendOffer/lists"];
    },
    featuredOffer: function () {
      return this.offers[0] || null;
    },
    selectedBranchId: function () {
      const globalState = this.$store.getters['globalState/lists'];
      return (globalState && globalState.branch_id) ? globalState.branch_id : (parseInt(localStorage.getItem('selected_branch_id')) || 0);
    }
  },
  watch: {
    selectedBranchId(newBranchId) {
      if (newBranchId > 0) {
        this.checkAndSetupTrigger();
      } else {
        this.resetTimerAndListener();
      }
    }
  },
  mounted() {
    this.initOfferFetch();
  },
  beforeUnmount() {
    this.resetTimerAndListener();
  },
  methods: {
    closeOfferPopup: function () {
      this.showOfferPopup = false;
      this.hasDismissedPopup = true;
      if (this.scrollTimer) {
        clearTimeout(this.scrollTimer);
        this.scrollTimer = null;
      }
    },
    initOfferFetch() {
      try {
        this.loading.isActive = true;
        this.$store.dispatch("frontendOffer/lists", {
          order_column: "id",
          order_type: "desc",
          limit: this.limit,
          status: statusEnum.ACTIVE,
        }).then(() => {
          this.loading.isActive = false;
          this.checkAndSetupTrigger();
        });
      } catch (err) {
        this.loading.isActive = false;
      }
    },
    checkAndSetupTrigger() {
      if (this.offers.length === 0 || this.hasDismissedPopup) return;

      this.resetTimerAndListener();

      if (this.selectedBranchId > 0) {
        this.attachScrollListener();
      }
    },
    attachScrollListener() {
      if (this.isScrollListenerAttached) return;
      this.isScrollListenerAttached = true;
      window.addEventListener("scroll", this.handleUserScroll, { passive: true });
    },
    handleUserScroll() {
      if (this.selectedBranchId > 0 && !this.showOfferPopup && !this.hasDismissedPopup && !this.scrollTimer) {
        this.scrollTimer = setTimeout(() => {
          if (this.selectedBranchId > 0 && !this.hasDismissedPopup && this.offers.length > 0) {
            this.showOfferPopup = true;
          }
        }, 8000);

        this.removeScrollListener();
      }
    },
    removeScrollListener() {
      if (this.isScrollListenerAttached) {
        window.removeEventListener("scroll", this.handleUserScroll);
        this.isScrollListenerAttached = false;
      }
    },
    resetTimerAndListener() {
      if (this.scrollTimer) {
        clearTimeout(this.scrollTimer);
        this.scrollTimer = null;
      }
      this.removeScrollListener();
      this.showOfferPopup = false;
    }
  },
};
</script>

<style scoped>
.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.4s ease-out;
}
.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateY(20px);
  opacity: 0;
}
</style>