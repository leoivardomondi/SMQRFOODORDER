<template>
    <LoadingComponent :props="loading" />
    <SmSidebarModalCreateComponent :props="addButton" @click="addReset" />

    <div id="sidebar" class="drawer">
        <div class="drawer-header">
            <h3 class="drawer-title">{{ $t('menu.offers') }}</h3>
            <button class="fa-solid fa-xmark close-btn" @click="reset"></button>
        </div>
        <div class="drawer-body">
            <form @submit.prevent="save">
                <div class="form-row">
                    <div class="form-col-12 sm:form-col-6">
                        <label for="name" class="db-field-title required">{{ $t("label.name") }}</label>
                        <input v-model="props.form.name" v-bind:class="errors.name ? 'invalid' : ''" type="text"
                            id="name" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.name">{{
                            errors.name[0]
                        }}</small>
                    </div>

                    <div class="form-col-12">
                        <label for="description" class="db-field-title">Offer description</label>
                        <textarea v-model="props.form.description" id="description" rows="3"
                            class="db-field-control" placeholder="Tell customers why they should order this offer today..."></textarea>
                        <small class="db-field-alert" v-if="errors.description">{{ errors.description[0] }}</small>
                    </div>

                    <div class="form-col-12">
                        <label class="db-field-title">Show this offer on</label>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <label v-for="day in weekdays" :key="day.value"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-[#D9DBE9] cursor-pointer">
                                <input type="checkbox" :value="day.value" v-model="props.form.visible_days">
                                <span class="text-sm">{{ day.label }}</span>
                            </label>
                        </div>
                        <p class="text-xs text-[#6E7191] mt-2">Leave all days unchecked to show the offer throughout its date range.</p>
                        <small class="db-field-alert" v-if="errors.visible_days">{{ errors.visible_days[0] }}</small>
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label for="discount_type" class="db-field-title required">Discount type</label>
                        <select v-model="props.form.discount_type" id="discount_type" class="db-field-control">
                            <option :value="discountTypeEnum.PERCENTAGE">Percentage (%)</option>
                            <option :value="discountTypeEnum.FIXED">Fixed amount</option>
                        </select>
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label for="amount" class="db-field-title required">
                            {{ props.form.discount_type === discountTypeEnum.FIXED ? 'Discount amount' : $t("label.discount_percentage") }}
                        </label>
                        <input v-model="props.form.amount" v-on:keypress="floatNumber($event)"
                            v-bind:class="errors.amount ? 'invalid' : ''" type="text" id="amount"
                            class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.amount">{{ errors.amount[0] }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="start_date" class="db-field-title required">{{ $t("label.start_date") }}</label>
                        <Datepicker hideInputIcon autoApply v-model="props.form.start_date" :enableTimePicker="true"
                            :is24="false" :monthChangeOnScroll="false" utc="false"
                            :input-class-name="errors.start_date ? 'invalid' : ''">
                            <template #am-pm-button="{ toggle, value }">
                                <button @click="toggle">{{ value }}</button>
                            </template>
                        </Datepicker>
                        <small class="db-field-alert" v-if="errors.start_date">{{ errors.start_date[0] }}</small>
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label for="end_date" class="db-field-title required">{{ $t("label.end_date") }}</label>
                        <Datepicker hideInputIcon autoApply v-model="props.form.end_date" :enableTimePicker="true"
                            :is24="false" :monthChangeOnScroll="false" utc="false"
                            :input-class-name="errors.end_date ? 'invalid' : ''">
                            <template #am-pm-button="{ toggle, value }">
                                <button @click="toggle">{{ value }}</button>
                            </template>
                        </Datepicker>
                        <small class="db-field-alert" v-if="errors.end_date">{{
                            errors.end_date[0]
                        }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required">{{ $t("label.image") }} (full poster artwork)</label>
                        <input @change="changeImage" v-bind:class="errors.image ? 'invalid' : ''" id="image" type="file"
                            class="db-field-control" ref="imageProperty" accept="image/png, image/jpeg, image/jpg" />
                        <small class="db-field-alert" v-if="errors.image">{{ errors.image[0] }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title">{{ $t("label.status") }}</label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input type="radio" v-model="props.form.status" id="active"
                                        :value="enums.statusEnum.ACTIVE" class="custom-radio-field" checked />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="active" class="db-field-label">{{
                                    $t("label.active")
                                }}</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input type="radio" class="custom-radio-field" v-model="props.form.status"
                                        id="inactive" :value="enums.statusEnum.INACTIVE" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="inactive" class="db-field-label">{{
                                    $t("label.inactive")
                                }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-col-12">
                        <div class="flex flex-wrap gap-3 mt-4">
                            <button type="submit" class="db-btn py-2 text-white bg-primary">
                                <i class="lab lab-save"></i>
                                <span>{{ $t("label.save") }}</span>
                            </button>

                            <button type="button" class="modal-btn-outline modal-close" @click="reset">
                                <i class="lab lab-close"></i>
                                <span>{{ $t("button.close") }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
import SmSidebarModalCreateComponent from "../components/buttons/SmSidebarModalCreateComponent";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import LoadingComponent from "../components/LoadingComponent";
import statusEnum from "../../../enums/modules/statusEnum";
import alertService from "../../../services/alertService";
import appService from "../../../services/appService";
import discountTypeEnum from "../../../enums/modules/discountTypeEnum";

export default {
    name: "OfferCreateComponent",
    components: { SmSidebarModalCreateComponent, LoadingComponent, Datepicker },
    props: ["props"],
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                statusEnum: statusEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive"),
                },
            },
            discountTypeEnum: discountTypeEnum,
            weekdays: [
                { value: 'monday', label: 'Monday' },
                { value: 'tuesday', label: 'Tuesday' },
                { value: 'wednesday', label: 'Wednesday' },
                { value: 'thursday', label: 'Thursday' },
                { value: 'friday', label: 'Friday' },
                { value: 'saturday', label: 'Saturday' },
                { value: 'sunday', label: 'Sunday' },
            ],
            image: "",
            errors: {},
        };
    },
    computed: {
        addButton: function () {
            return { title: this.$t("button.add_offer") }
        },
    },
    methods: {
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        changeImage: function (e) {
            this.image = e.target.files[0];
        },
        reset: function () {
            appService.sideDrawerHide();
            this.$store.dispatch("offer/reset").then().catch();
            this.errors = {};
            this.$props.props.form = {
                name: "",
                description: "",
                amount: "",
                discount_type: discountTypeEnum.PERCENTAGE,
                start_date: "",
                end_date: "",
                visible_days: [],
                status: statusEnum.ACTIVE,
            };
            if (this.image) {
                this.image = "";
                this.$refs.imageProperty.value = null;
            }
        },
        addReset: function () {
            this.$store.dispatch("offer/reset").then().catch();
            this.errors = {};
            this.$props.props.form = {
                name: "",
                description: "",
                amount: "",
                discount_type: discountTypeEnum.PERCENTAGE,
                start_date: "",
                end_date: "",
                visible_days: [],
                status: statusEnum.ACTIVE,
            };
            if (this.image) {
                this.image = "";
                this.$refs.imageProperty.value = null;
            }
        },

        save: function () {
            try {
                const fd = new FormData();
                fd.append("name", this.props.form.name);
                fd.append("description", this.props.form.description || "");
                fd.append("amount", this.props.form.amount);
                fd.append("discount_type", this.props.form.discount_type);
                fd.append("start_date", this.props.form.start_date);
                fd.append("end_date", this.props.form.end_date);
                fd.append("status", this.props.form.status);
                (this.props.form.visible_days || []).forEach((day) => fd.append("visible_days[]", day));
                if (this.image) {
                    fd.append("image", this.image);
                }
                const tempId = this.$store.getters["offer/temp"].temp_id;
                this.loading.isActive = true;
                this.$store
                    .dispatch("offer/save", {
                        form: fd,
                        search: this.props.search,
                    })
                    .then((res) => {
                        appService.sideDrawerHide();
                        this.loading.isActive = false;
                        alertService.successFlip(
                            tempId === null ? 0 : 1,
                            this.$t("menu.offers")
                        );
                        this.props.form = {
                            name: "",
                            description: "",
                            amount: "",
                            discount_type: discountTypeEnum.PERCENTAGE,
                            start_date: "",
                            end_date: "",
                            visible_days: [],
                            status: statusEnum.ACTIVE,
                        };
                        this.image = "";
                        this.errors = {};
                        this.$refs.imageProperty.value = null;
                    })
                    .catch((err) => {
                        this.loading.isActive = false;
                        this.errors = err.response.data.errors;
                    });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
    },
};
</script>
