<template>
    <div class="relative">
        <button type="button" class="db-btn py-2 text-black bg-[#cdaa5d]" @click.prevent="showRecipients = !showRecipients">
            <i class="fa-solid fa-envelope"></i>
            <span>Send Account Setups</span>
        </button>
        <div v-if="showRecipients" class="absolute right-0 z-30 mt-2 w-80 rounded-lg border border-gray-200 bg-white p-4 shadow-lg">
            <div class="mb-3 flex items-center justify-between">
                <strong class="text-sm text-heading">Choose recipients</strong>
                <button type="button" class="text-xs text-primary" @click="toggleAll">
                    {{ allSelected ? 'Clear all' : 'Select all' }}
                </button>
            </div>
            <div class="max-h-56 space-y-2 overflow-y-auto">
                <label v-for="user in usersWithEmail" :key="user.id" class="flex cursor-pointer items-start gap-2 text-sm text-heading">
                    <input v-model="selectedIds" type="checkbox" :value="user.id" class="mt-1">
                    <span>{{ user.name }}<small class="block text-paragraph">{{ user.email }}</small></span>
                </label>
                <p v-if="usersWithEmail.length === 0" class="text-sm text-paragraph">No users with email addresses found.</p>
            </div>
            <button type="button" class="mt-4 w-full db-btn py-2 text-white bg-primary disabled:opacity-50" :disabled="selectedIds.length === 0 || loading" @click="send">
                {{ loading ? 'Sending...' : `Send selected (${selectedIds.length})` }}
            </button>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import alertService from "../../../services/alertService";
import appService from "../../../services/appService";

export default {
    name: "AccountSetupEmailsComponent",
    props: {
        users: { type: Array, default: () => [] },
    },
    data() {
        return { showRecipients: false, selectedIds: [], loading: false };
    },
    computed: {
        usersWithEmail() {
            return this.users.filter((user) => user.email);
        },
        allSelected() {
            return this.usersWithEmail.length > 0 && this.selectedIds.length === this.usersWithEmail.length;
        },
    },
    methods: {
        toggleAll() {
            this.selectedIds = this.allSelected ? [] : this.usersWithEmail.map((user) => user.id);
        },
        send() {
            if (!this.selectedIds.length) return;
            appService.adminInvitationConfirmation().then(() => {
                this.loading = true;
                axios.post('/admin/administrator/send-setup-invitations', { user_ids: this.selectedIds })
                    .then((res) => {
                        alertService.success(res.data.message);
                        if (res.data.failed?.length) alertService.warning('Some emails could not be sent: ' + res.data.failed.join(', '));
                        this.showRecipients = false;
                        this.selectedIds = [];
                    })
                    .catch((err) => alertService.error(err.response?.data?.message || 'Unable to send account setup emails.'))
                    .finally(() => { this.loading = false; });
            }).catch(() => {});
        },
    },
};
</script>
