<template>
    <div class="db-card db-tab-div active">
        <div class="db-card-header">
            <h3 class="db-card-title">{{ $t("menu.progressive_web_app") }}</h3>
        </div>
        <div class="db-card-body">
            <div v-if="loading.isActive" class="mb-5 rounded-lg border border-primary/30 bg-primary/10 p-4">
                <div class="flex items-center justify-between gap-3 mb-2">
                    <p class="font-medium text-heading">
                        {{ processing ? 'Upload complete. Creating PWA images…' : 'Uploading images…' }}
                    </p>
                    <span class="font-semibold text-primary">{{ uploadProgress }}%</span>
                </div>
                <div class="h-2 overflow-hidden rounded-full bg-white">
                    <div class="h-full rounded-full bg-primary transition-all duration-300"
                        :style="{ width: uploadProgress + '%' }"></div>
                </div>
                <p v-if="processing" class="mt-2 text-sm text-paragraph">Please keep this page open while all app icon and splash sizes are generated.</p>
            </div>
            <form @submit.prevent="save">
                <div class="form-row">
                    <div class="form-col-12  sm:form-col-5">
                        <label for="splash" class="db-field-title required">
                            {{ $t("label.splash") }} (2048px,2732px)
                        </label>
                        <input @change="changeSplash" v-bind:class="errors.pwa_splash ? 'invalid' : ''" id="splash"
                            type="file" class="db-field-control" ref="splashProperty"
                            accept="image/png, image/jpeg, image/jpg" />
                        <small class="db-field-alert" v-if="errors.pwa_splash">{{
                            errors.pwa_splash[0]
                        }}</small>
                    </div>
                    <div class="form-col-12  sm:form-col-5">
                        <label for="icon" class="db-field-title required">
                            {{ $t("label.icon") }} (512px,512px)
                        </label>
                        <input @change="changeIcon" v-bind:class="errors.pwa_icon ? 'invalid' : ''" id="icon" type="file"
                            class="db-field-control" ref="iconProperty" accept="image/png, image/jpeg, image/jpg" />
                        <small class="db-field-alert" v-if="errors.pwa_icon">{{
                            errors.pwa_icon[0]
                        }}</small>
                    </div>

                    <div class="form-col-2 sm:form-col-2 mt-6">
                        <button type="submit" :disabled="loading.isActive"
                            class="db-btn text-white bg-primary disabled:cursor-not-allowed disabled:opacity-60">
                            <i class="lab lab-fill-save"></i>
                            <span>{{ loading.isActive ? 'Saving…' : $t("button.save") }}</span>
                        </button>
                    </div>
                </div>
            </form>

            <div class="row mt-4">
                <div class="col-6 sm:col-3">
                    <h3 class="text-lg font-medium capitalize mb-2 text-paragraph">
                        {{ $t("label.splash") }}
                    </h3>
                    <img class="db-image" alt="splash" :src="pwa.splash" />
                </div>
                <div class="col-6 sm:col-3">
                    <h3 class="text-lg font-medium capitalize mb-2 text-paragraph">
                        {{ $t("label.icon") }}
                    </h3>
                    <img class="db-image" alt="icon" :src="pwa.icon" />
                </div>
            </div>

        </div>
    </div>
</template>
<script lang="js">
import alertService from "../../../../services/alertService";
export default {
    name: 'PWAComponent',
    data() {
        return {
            loading: {
                isActive: false,
            },
            splash: "",
            icon: "",
            uploadProgress: 0,
            processing: false,
            errors: {},
        }
    },
    mounted() {
        this.$store.dispatch("pwa/lists");
    },
    computed: {
        pwa: function () {
            return this.$store.getters["pwa/lists"];
        }
    },
    methods: {
        changeSplash: function (e) {
            this.splash = e.target.files[0];
            delete this.errors.pwa_splash;
        },
        changeIcon: function (e) {
            this.icon = e.target.files[0];
            delete this.errors.pwa_icon;
        },
        save: function () {
            try {
                if (!this.splash && !this.icon) {
                    alertService.error('Choose a splash image or app icon to update.');
                    return;
                }

                const form = new FormData();
                if (this.splash) form.append('pwa_splash', this.splash);
                if (this.icon) form.append('pwa_icon', this.icon);

                this.loading.isActive = true;
                this.uploadProgress = 0;
                this.processing = false;
                this.$store
                    .dispatch("pwa/save", {
                        form: form,
                        onUploadProgress: (event) => {
                            if (event.total) {
                                this.uploadProgress = Math.min(100, Math.round((event.loaded * 100) / event.total));
                                this.processing = this.uploadProgress >= 100;
                            }
                        },
                    })
                    .then((res) => {
                        alertService.successFlip(1, this.$t("menu.progressive_web_app"));
                        this.errors = {};
                        this.splash = "";
                        this.icon = "";
                        this.$refs.splashProperty.value = null;
                        this.$refs.iconProperty.value = null;
                    })
                    .catch((err) => {
                        const response = err?.response?.data;
                        this.errors = response?.errors || {};
                        alertService.error(response?.message || 'The PWA images could not be saved. Please try again.');
                    })
                    .finally(() => {
                        this.loading.isActive = false;
                        this.processing = false;
                    });
            } catch (err) {
                this.loading.isActive = false;
                this.processing = false;
                alertService.error(err?.message || 'The PWA images could not be saved.');
            }
        }
    }
}
</script>
