<template>
    <LoadingComponent :props="loading" />

    <div id="whatsapp-api" class="db-card db-tab-div active">
        <div class="db-card-header">
            <h3 class="db-card-title">Meta WhatsApp API Settings</h3>
        </div>
        <div class="db-card-body">
            <form @submit.prevent="save">
                <div class="form-row">
                    <!-- Status Activation Switch -->
                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required">Activate WhatsApp Notifications to Restaurant</label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.ENABLE" v-model="form.whatsapp_status" id="active_enable"
                                        type="radio" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="active_enable" class="db-field-label">Enable</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.DISABLE" v-model="form.whatsapp_status" type="radio"
                                        id="active_disable" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="active_disable" class="db-field-label">Disable</label>
                            </div>
                        </div>
                        <small class="db-field-alert" v-if="errors.whatsapp_status">{{ errors.whatsapp_status[0] }}</small>
                    </div>

                    <!-- Recipient Phone Number -->
                    <div class="form-col-12 sm:form-col-6">
                        <label for="whatsapp_recipient_phone" class="db-field-title">Recipient Phone Number (Restaurant's WhatsApp)</label>
                        <input v-model="form.whatsapp_recipient_phone" v-bind:class="errors.whatsapp_recipient_phone ? 'invalid' : ''" type="text"
                            id="whatsapp_recipient_phone" class="db-field-control" placeholder="e.g. +254700000000" />
                        <small class="db-field-alert" v-if="errors.whatsapp_recipient_phone">{{ errors.whatsapp_recipient_phone[0] }}</small>
                    </div>

                    <!-- Phone Number ID -->
                    <div class="form-col-12 sm:form-col-6">
                        <label for="whatsapp_phone_number_id" class="db-field-title">Phone Number ID</label>
                        <input v-model="form.whatsapp_phone_number_id" v-bind:class="errors.whatsapp_phone_number_id ? 'invalid' : ''" type="text"
                            id="whatsapp_phone_number_id" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.whatsapp_phone_number_id">{{ errors.whatsapp_phone_number_id[0] }}</small>
                    </div>

                    <!-- Template Name -->
                    <div class="form-col-12 sm:form-col-6">
                        <label for="whatsapp_template_name" class="db-field-title">Template Name</label>
                        <input v-model="form.whatsapp_template_name" v-bind:class="errors.whatsapp_template_name ? 'invalid' : ''" type="text"
                            id="whatsapp_template_name" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.whatsapp_template_name">{{ errors.whatsapp_template_name[0] }}</small>
                    </div>

                    <!-- Access Token -->
                    <div class="form-col-12">
                        <label for="whatsapp_access_token" class="db-field-title">Access Token (Permanent/System User Token)</label>
                        <textarea v-model="form.whatsapp_access_token" v-bind:class="errors.whatsapp_access_token ? 'invalid' : ''"
                            id="whatsapp_access_token" class="db-field-control" rows="3"></textarea>
                        <small class="db-field-alert" v-if="errors.whatsapp_access_token">{{ errors.whatsapp_access_token[0] }}</small>
                    </div>

                    <!-- Buttons -->
                    <div class="form-col-12 flex items-center gap-3 mt-4">
                        <button type="submit" class="db-btn text-white bg-primary">
                            <i class="lab lab-save"></i>
                            <span>{{ $t("button.save") }}</span>
                        </button>
                        <button type="button" @click="testConnection" class="db-btn border border-primary text-primary bg-transparent hover:bg-primary/5 transition">
                            <i class="lab lab-whatsapp"></i>
                            <span>Test Notification</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../components/LoadingComponent";
import alertService from "../../../../services/alertService";
import activityEnum from "../../../../enums/modules/activityEnum";
import axios from "axios";

export default {
    name: "WhatsappApiComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                activityEnum: activityEnum,
            },
            form: {
                whatsapp_phone_number_id: "",
                whatsapp_access_token: "",
                whatsapp_template_name: "",
                whatsapp_recipient_phone: "",
                whatsapp_status: activityEnum.DISABLE,
            },
            errors: {},
        };
    },
    mounted() {
        this.fetchSettings();
    },
    methods: {
        fetchSettings: function() {
            try {
                this.loading.isActive = true;
                axios.get("/api/admin/whatsapp-api")
                    .then((res) => {
                        const data = res.data.data;
                        this.form = {
                            whatsapp_phone_number_id: data.whatsapp_phone_number_id || "",
                            whatsapp_access_token: data.whatsapp_access_token || "",
                            whatsapp_template_name: data.whatsapp_template_name || "",
                            whatsapp_recipient_phone: data.whatsapp_recipient_phone || "",
                            whatsapp_status: data.whatsapp_status ? parseInt(data.whatsapp_status) : activityEnum.DISABLE,
                        };
                        this.loading.isActive = false;
                    })
                    .catch((err) => {
                        this.loading.isActive = false;
                        alertService.error("Failed to load WhatsApp API settings.");
                    });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        save: function() {
            try {
                this.loading.isActive = true;
                axios.post("/api/admin/whatsapp-api", this.form)
                    .then((res) => {
                        this.loading.isActive = false;
                        alertService.success("WhatsApp API configuration saved successfully!");
                        this.errors = {};
                        this.fetchSettings();
                    })
                    .catch((err) => {
                        this.loading.isActive = false;
                        if (err.response && err.response.data && err.response.data.errors) {
                            this.errors = err.response.data.errors;
                        } else if (err.response && err.response.data && err.response.data.message) {
                            alertService.error(err.response.data.message);
                        } else {
                            alertService.error("An error occurred while saving configuration.");
                        }
                    });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        testConnection: function() {
            try {
                if (!this.form.whatsapp_phone_number_id || !this.form.whatsapp_access_token || !this.form.whatsapp_template_name || !this.form.whatsapp_recipient_phone) {
                    alertService.error("Please fill in all WhatsApp API credentials before testing.");
                    return;
                }
                this.loading.isActive = true;
                axios.post("/api/admin/whatsapp-api/test", this.form)
                    .then((res) => {
                        this.loading.isActive = false;
                        alertService.success(res.data.message || "Test WhatsApp message sent successfully!");
                    })
                    .catch((err) => {
                        this.loading.isActive = false;
                        if (err.response && err.response.data && err.response.data.message) {
                            alertService.error(err.response.data.message);
                        } else {
                            alertService.error("WhatsApp API test failed. Check your credentials.");
                        }
                    });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        }
    }
};
</script>

<style scoped></style>
