<template>
    <LoadingComponent :props="loading" />

    <div id="company" class="db-card db-tab-div active">
        <div class="db-card-header">
            <h3 class="db-card-title">{{ $t("menu.mail") }}</h3>
        </div>
        <div class="db-card-body">
            <form @submit.prevent="save">
                <div class="form-row">
                    <div class="form-col-12 sm:form-col-6">
                        <label for="mail_host" class="db-field-title required">{{ $t("label.mail_host") }}</label>
                        <input v-model="form.mail_host" v-bind:class="errors.mail_host ? 'invalid' : ''" type="text"
                            id="mail_host" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.mail_host">{{
                            errors.mail_host[0]
                        }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="mail_port" class="db-field-title required">{{ $t("label.mail_port") }}</label>
                        <input v-model="form.mail_port" v-bind:class="errors.mail_port ? 'invalid' : ''" type="text"
                            id="mail_port" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.mail_port">{{
                            errors.mail_port[0]
                        }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="mail_username" class="db-field-title required">{{
                            $t("label.mail_username")
                        }}</label>
                        <input v-model="form.mail_username" v-bind:class="errors.mail_username ? 'invalid' : ''"
                            type="text" id="mail_username" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.mail_username">{{ errors.mail_username[0] }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="mail_password" class="db-field-title">{{
                            $t("label.mail_password")
                        }}</label>
                        <input v-model="form.mail_password" v-bind:class="errors.mail_password ? 'invalid' : ''"
                            type="text" id="mail_password" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.mail_password">{{ errors.mail_password[0] }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="mail_from_name" class="db-field-title required">{{
                            $t("label.mail_from_name")
                        }}</label>
                        <input v-model="form.mail_from_name" v-bind:class="
                            errors.mail_from_name ? 'invalid' : ''
                        " type="text" id="mail_from_name" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.mail_from_name">{{
                            errors.mail_from_name[0]
                        }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="mail_from_email" class="db-field-title required">{{
                            $t("label.mail_from_email")
                        }}</label>
                        <input v-model="form.mail_from_email" v-bind:class="
                            errors.mail_from_email ? 'invalid' : ''
                        " type="text" id="mail_from_email" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.mail_from_email">{{
                            errors.mail_from_email[0]
                        }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required" for="active">{{
                            $t("label.mail_encryption")
                        }}</label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.encryptionEnum.SSL" v-model="form.mail_encryption" id="ssl"
                                        type="radio" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="ssl" class="db-field-label">{{
                                    $t("label.ssl")
                                }}</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.encryptionEnum.TLS" v-model="form.mail_encryption" type="radio"
                                        id="tls" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="tls" class="db-field-label">{{
                                    $t("label.tls")
                                }}</label>
                            </div>
                        </div>
                        <small class="db-field-alert" v-if="errors.mail_encryption">{{
                            errors.mail_encryption[0]
                        }}</small>
                    </div>
                    <div class="form-col-12">
                        <button type="submit" class="db-btn text-white bg-primary">
                            <i class="lab lab-save"></i>
                            <span>{{ $t("button.save") }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- SMTP EMAIL SETUP TEST & SAMPLE SIMULATOR CARD -->
    <div class="db-card mt-6 border border-emerald-100 shadow-sm">
        <div class="db-card-header bg-emerald-50/60 border-b border-emerald-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <i class="lab lab-send text-emerald-600 text-xl font-bold"></i>
                <div>
                    <h3 class="db-card-title text-emerald-950 font-bold">SMTP Test & Notification Simulator</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Send sample emails to test your SMTP server and verify whether types like Customer Order Details are received by the Branch Manager.</p>
                </div>
            </div>
            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                Email Test
            </span>
        </div>

        <div class="db-card-body space-y-6">
            <!-- STEP 1: SELECT EMAIL SAMPLE TYPE -->
            <div>
                <label class="db-field-title required font-semibold text-gray-800 mb-2">
                    1. Select Sample Email Type
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                    <!-- Branch Manager Order Details Alert -->
                    <label :class="[
                        'border rounded-xl p-4 cursor-pointer transition-all duration-150 flex flex-col justify-between select-none relative',
                        testForm.sample_type === 'branch_manager_order'
                            ? 'border-emerald-500 bg-emerald-50/40 ring-2 ring-emerald-500/20 shadow-sm'
                            : 'border-gray-200 hover:border-gray-300 bg-white hover:bg-gray-50/50'
                    ]">
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-2">
                                    <input type="radio" value="branch_manager_order" v-model="testForm.sample_type" class="text-emerald-600 focus:ring-emerald-500 h-4 w-4" />
                                    <span class="font-bold text-sm text-gray-900">Branch Manager: Order Details</span>
                                </div>
                                <span class="px-2 py-0.5 text-[11px] font-semibold bg-emerald-100 text-emerald-800 rounded-md">
                                    Order Details
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 pl-6 leading-relaxed">
                                Complete order notification email sent to the restaurant branch manager. Includes items, quantities, pricing, customer contact, delivery/pickup details, and admin order view link.
                            </p>
                        </div>
                    </label>

                    <!-- Customer Order Confirmation -->
                    <label :class="[
                        'border rounded-xl p-4 cursor-pointer transition-all duration-150 flex flex-col justify-between select-none relative',
                        testForm.sample_type === 'customer_order_confirmation'
                            ? 'border-emerald-500 bg-emerald-50/40 ring-2 ring-emerald-500/20 shadow-sm'
                            : 'border-gray-200 hover:border-gray-300 bg-white hover:bg-gray-50/50'
                    ]">
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-2">
                                    <input type="radio" value="customer_order_confirmation" v-model="testForm.sample_type" class="text-emerald-600 focus:ring-emerald-500 h-4 w-4" />
                                    <span class="font-bold text-sm text-gray-900">Customer: Order Confirmation</span>
                                </div>
                                <span class="px-2 py-0.5 text-[11px] font-semibold bg-blue-100 text-blue-800 rounded-md">
                                    Customer Alert
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 pl-6 leading-relaxed">
                                Standard notification sent directly to customer inboxes confirming that their online order was successfully placed and received by the kitchen.
                            </p>
                        </div>
                    </label>

                    <!-- Customer Out For Delivery -->
                    <label :class="[
                        'border rounded-xl p-4 cursor-pointer transition-all duration-150 flex flex-col justify-between select-none relative',
                        testForm.sample_type === 'customer_order_out_for_delivery'
                            ? 'border-emerald-500 bg-emerald-50/40 ring-2 ring-emerald-500/20 shadow-sm'
                            : 'border-gray-200 hover:border-gray-300 bg-white hover:bg-gray-50/50'
                    ]">
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-2">
                                    <input type="radio" value="customer_order_out_for_delivery" v-model="testForm.sample_type" class="text-emerald-600 focus:ring-emerald-500 h-4 w-4" />
                                    <span class="font-bold text-sm text-gray-900">Customer: Out for Delivery</span>
                                </div>
                                <span class="px-2 py-0.5 text-[11px] font-semibold bg-amber-100 text-amber-800 rounded-md">
                                    Rider Alert
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 pl-6 leading-relaxed">
                                Notification informing the customer that their order has been prepared and is currently on the way with a delivery boy.
                            </p>
                        </div>
                    </label>

                    <!-- Basic SMTP Connection Test -->
                    <label :class="[
                        'border rounded-xl p-4 cursor-pointer transition-all duration-150 flex flex-col justify-between select-none relative',
                        testForm.sample_type === 'smtp_connection_test'
                            ? 'border-emerald-500 bg-emerald-50/40 ring-2 ring-emerald-500/20 shadow-sm'
                            : 'border-gray-200 hover:border-gray-300 bg-white hover:bg-gray-50/50'
                    ]">
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-2">
                                    <input type="radio" value="smtp_connection_test" v-model="testForm.sample_type" class="text-emerald-600 focus:ring-emerald-500 h-4 w-4" />
                                    <span class="font-bold text-sm text-gray-900">Basic SMTP Connection Test</span>
                                </div>
                                <span class="px-2 py-0.5 text-[11px] font-semibold bg-gray-100 text-gray-700 rounded-md">
                                    Diagnostics
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 pl-6 leading-relaxed">
                                Direct connection test that dispatches a clean verification email displaying the SMTP host, port, security encryption, and sender details.
                            </p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- STEP 2: SAMPLE DATA CONTEXT (BRANCH & ORDER SELECTION) -->
            <div v-if="testForm.sample_type !== 'smtp_connection_test'" class="p-4 bg-gray-50/80 rounded-xl border border-gray-200 space-y-4">
                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-700 flex items-center gap-1.5">
                    <i class="lab lab-shop text-sm text-emerald-600"></i>
                    <span>2. Sample Data Context</span>
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Branch Selector & Branch Manager info -->
                    <div>
                        <label class="db-field-title text-xs font-medium text-gray-700">Target Branch</label>
                        <select v-model="testForm.branch_id" @change="onBranchChange" class="db-field-control text-sm">
                            <option v-for="branch in sampleData.branches" :key="branch.id" :value="branch.id">
                                {{ branch.name }}
                            </option>
                        </select>

                        <!-- Branch Manager Quick Fill Option -->
                        <div v-if="currentBranchManagers.length > 0" class="mt-2.5 space-y-1.5">
                            <span class="text-xs text-gray-500 font-medium block">Branch Manager(s) for this branch:</span>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    type="button"
                                    v-for="bm in currentBranchManagers"
                                    :key="bm.id"
                                    @click="fillRecipientEmail(bm.email)"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs rounded-lg bg-emerald-100/70 hover:bg-emerald-200 text-emerald-900 font-medium border border-emerald-200 transition-colors"
                                    :title="'Click to test sending to ' + bm.email"
                                >
                                    <i class="lab lab-profile-circle text-sm"></i>
                                    <span>{{ bm.name }}: <strong>{{ bm.email }}</strong></span>
                                    <span class="text-[10px] bg-emerald-600 text-white rounded px-1.5 py-0.2">Use</span>
                                </button>
                            </div>
                        </div>
                        <div v-else class="mt-2 text-xs text-amber-700 bg-amber-50 p-2 rounded-lg border border-amber-200">
                            No Branch Manager assigned to this branch. You can enter any recipient email below.
                        </div>
                    </div>

                    <!-- Order Selector -->
                    <div>
                        <label class="db-field-title text-xs font-medium text-gray-700">Sample Order</label>
                        <select v-model="testForm.order_id" class="db-field-control text-sm">
                            <option :value="''">Auto-Generated Sample Order (Comprehensive Demo)</option>
                            <option v-for="order in sampleData.recent_orders" :key="order.id" :value="order.id">
                                #{{ order.order_serial_no }} — {{ order.customer_name }} ({{ order.total_formatted }})
                            </option>
                        </select>
                        <span class="text-xs text-gray-400 mt-1 block">
                            Choose an existing order or use the rich auto-generated sample order with full items & address.
                        </span>
                    </div>
                </div>
            </div>

            <!-- STEP 3: RECIPIENT EMAIL & SEND CONTROLS -->
            <form @submit.prevent="sendTestMail">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    <div class="md:col-span-8">
                        <label for="test_email" class="db-field-title required font-semibold text-gray-800">
                            {{ testForm.sample_type !== 'smtp_connection_test' ? '3.' : '2.' }} Recipient Email Address
                        </label>
                        <div class="relative">
                            <input
                                v-model="testForm.email"
                                type="email"
                                id="test_email"
                                class="db-field-control pr-10"
                                placeholder="e.g. branchmanager@bwibo.com or your personal email"
                                required
                            />
                            <i class="lab lab-mail absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg pointer-events-none"></i>
                        </div>
                        <small class="db-field-alert" v-if="testErrors.email">{{ testErrors.email[0] }}</small>
                        <span class="text-xs text-gray-500 mt-1 block">
                            The test message will be sent to this email address to verify receipt.
                        </span>
                    </div>

                    <div class="md:col-span-4 flex flex-col justify-end">
                        <button
                            type="submit"
                            :disabled="testLoading"
                            class="db-btn bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-5 rounded-lg flex items-center justify-center gap-2 shadow-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="testLoading" class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full"></span>
                            <i v-else class="lab lab-send text-base"></i>
                            <span>{{ testLoading ? 'Sending Test Mail...' : 'Send Test Email' }}</span>
                        </button>
                    </div>
                </div>

                <!-- TEST OPTIONS -->
                <div class="mt-3 flex items-center gap-2">
                    <input
                        type="checkbox"
                        id="use_form_credentials"
                        v-model="testForm.use_form_credentials"
                        class="rounded text-emerald-600 focus:ring-emerald-500 h-4 w-4"
                    />
                    <label for="use_form_credentials" class="text-xs text-gray-600 select-none cursor-pointer">
                        Use current credentials entered in the form above (allows testing before clicking Save)
                    </label>
                </div>
            </form>

            <!-- LIVE TEST RESULT BANNER -->
            <div v-if="testResult" class="mt-4 transition-all duration-200">
                <!-- SUCCESS BANNER -->
                <div v-if="testResult.success" class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start gap-3">
                    <i class="lab lab-check-circle-line text-emerald-600 text-xl flex-shrink-0 mt-0.5"></i>
                    <div class="text-sm">
                        <h5 class="font-bold text-emerald-900">Email Sent Successfully!</h5>
                        <p class="mt-0.5 text-emerald-800">{{ testResult.message }}</p>
                        <p class="mt-1 text-xs text-emerald-700">Please check the inbox and spam folder of <strong>{{ testForm.email }}</strong> to confirm receipt and formatting.</p>
                    </div>
                </div>

                <!-- ERROR BANNER -->
                <div v-else class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-900 flex items-start gap-3">
                    <i class="lab lab-close-circle-line text-red-600 text-xl flex-shrink-0 mt-0.5"></i>
                    <div class="text-sm">
                        <h5 class="font-bold text-red-900">Delivery Failed</h5>
                        <p class="mt-0.5 text-red-800 font-mono text-xs bg-red-100/60 p-2 rounded border border-red-200 break-words">{{ testResult.message }}</p>
                        <p class="mt-1.5 text-xs text-red-700">
                            Tip: Double-check that your SMTP Host, Port, Encryption (SSL/TLS), Username, and Password are correct. If using Gmail or Google Workspace, ensure an <strong>App Password</strong> is used.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../components/LoadingComponent";
import alertService from "../../../../services/alertService";
import encryptionEnum from "../../../../enums/modules/encryptionEnum";

export default {
    name: "MailComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                encryptionEnum: encryptionEnum,
                encryptionEnumArray: {
                    [encryptionEnum.SSL]: this.$t("label.ssl"),
                    [encryptionEnum.TLS]: this.$t("label.tls"),
                },
            },
            form: {
                mail_host: "",
                mail_port: "",
                mail_username: "",
                mail_password: "",
                mail_encryption: "",
                mail_from_name: "",
                mail_from_email: "",
            },
            errors: {},

            // Test Mail State
            testLoading: false,
            testResult: null,
            testErrors: {},
            sampleData: {
                branches: [],
                recent_orders: [],
                sample_types: [],
            },
            testForm: {
                sample_type: "branch_manager_order",
                branch_id: "",
                order_id: "",
                email: "",
                use_form_credentials: true,
            },
        };
    },
    computed: {
        currentBranchManagers() {
            if (!this.testForm.branch_id || !this.sampleData.branches) {
                return [];
            }
            const branch = this.sampleData.branches.find(b => b.id === Number(this.testForm.branch_id));
            return branch && branch.branch_managers ? branch.branch_managers : [];
        },
    },
    mounted() {
        try {
            this.loading.isActive = true;
            this.$store
                .dispatch("mail/lists")
                .then((res) => {
                    this.form = {
                        mail_host: res.data.data.mail_host,
                        mail_port: res.data.data.mail_port,
                        mail_username: res.data.data.mail_username,
                        mail_password: res.data.data.mail_password,
                        mail_encryption: res.data.data.mail_encryption,
                        mail_from_name: res.data.data.mail_from_name,
                        mail_from_email: res.data.data.mail_from_email,
                    };
                    this.loading.isActive = false;
                })
                .catch((err) => {
                    this.loading.isActive = false;
                });

            // Fetch sample data for testing simulator
            this.$store
                .dispatch("mail/sampleData")
                .then((res) => {
                    this.sampleData = res.data.data;
                    if (this.sampleData.branches && this.sampleData.branches.length > 0) {
                        this.testForm.branch_id = this.sampleData.branches[0].id;
                        this.autoFillFirstBranchManager();
                    }
                })
                .catch(() => {});
        } catch (err) {
            this.loading.isActive = false;
            alertService.error(err);
        }
    },
    methods: {
        save: function () {
            try {
                this.loading.isActive = true;
                this.$store
                    .dispatch("mail/save", this.form)
                    .then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(
                            res.config.method === "put" ?? 0,
                            this.$t("menu.mail")
                        );
                        this.errors = {};
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

        onBranchChange: function () {
            this.autoFillFirstBranchManager();
        },

        autoFillFirstBranchManager: function () {
            if (this.currentBranchManagers.length > 0 && !this.testForm.email) {
                this.testForm.email = this.currentBranchManagers[0].email;
            }
        },

        fillRecipientEmail: function (email) {
            this.testForm.email = email;
            this.testResult = null;
        },

        sendTestMail: function () {
            this.testLoading = true;
            this.testResult = null;
            this.testErrors = {};

            const payload = {
                email: this.testForm.email,
                sample_type: this.testForm.sample_type,
                branch_id: this.testForm.branch_id || null,
                order_id: this.testForm.order_id || null,
            };

            if (this.testForm.use_form_credentials) {
                payload.mail_host = this.form.mail_host;
                payload.mail_port = this.form.mail_port;
                payload.mail_username = this.form.mail_username;
                payload.mail_password = this.form.mail_password;
                payload.mail_encryption = this.form.mail_encryption;
                payload.mail_from_name = this.form.mail_from_name;
                payload.mail_from_email = this.form.mail_from_email;
            }

            this.$store
                .dispatch("mail/testMail", payload)
                .then((res) => {
                    this.testLoading = false;
                    this.testResult = {
                        success: true,
                        message: res.data.message || "Test email sent successfully!",
                    };
                    alertService.success("Test email dispatched successfully!");
                })
                .catch((err) => {
                    this.testLoading = false;
                    const errorMsg = err.response?.data?.message || err.message || "Failed to send test email.";
                    this.testResult = {
                        success: false,
                        message: errorMsg,
                    };
                    if (err.response?.data?.errors) {
                        this.testErrors = err.response.data.errors;
                    }
                    alertService.error(errorMsg);
                });
        },
    },
};
</script>
