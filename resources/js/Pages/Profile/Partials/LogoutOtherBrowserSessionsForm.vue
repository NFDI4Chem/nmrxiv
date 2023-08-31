<template>
    <jet-action-section>
        <template #title> Browser Sessions </template>

        <template #description>
            Manage and log out your active sessions on other browsers and
            devices.
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600">
                If necessary, you may log out of all of your other browser
                sessions across all of your devices. Some of your recent
                sessions are listed below; however, this list may not be
                exhaustive. If you feel your account has been compromised, you
                should also update your password.
            </div>

            <!-- Other Browser Sessions -->
            <div v-if="sessions.length > 0" class="mt-5 space-y-6">
                <div
                    v-for="(session, i) in sessions"
                    :key="i"
                    class="flex items-center"
                >
                    <ComputerDesktopIcon
                        v-if="session.agent.is_desktop"
                        class="w-8 h-8 text-gray-500"
                    />
                    <DevicePhoneMobileIcon
                        v-else
                        class="w-8 h-8 text-gray-500"
                    />

                    <div class="ml-3">
                        <div class="text-sm text-gray-600">
                            {{ session.agent.platform }} -
                            {{ session.agent.browser }}
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">
                                {{ session.ip_address }},

                                <span
                                    v-if="session.is_current_device"
                                    class="text-green-500 font-semibold"
                                    >This device</span
                                >
                                <span v-else
                                    >Last active {{ session.last_active }}</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center mt-5">
                <jet-button @click="confirmLogout">
                    Log Out Other Browser Sessions
                </jet-button>

                <jet-action-message :on="form.recentlySuccessful" class="ml-3">
                    Done.
                </jet-action-message>
            </div>

            <!-- Log Out Other Devices Confirmation Modal -->
            <jet-dialog-modal :show="confirmingLogout" @close="closeModal">
                <template #title> Log Out Other Browser Sessions </template>

                <template #content>
                    Please enter your password to confirm you would like to log
                    out of your other browser sessions across all of your
                    devices.

                    <div class="mt-4">
                        <jet-input
                            ref="password"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-3/4"
                            placeholder="Password"
                            @keyup.enter="logoutOtherBrowserSessions"
                        />

                        <jet-input-error
                            :message="form.errors.password"
                            class="mt-2"
                        />
                    </div>
                </template>

                <template #footer>
                    <jet-secondary-button @click="closeModal">
                        Cancel
                    </jet-secondary-button>

                    <jet-button
                        class="ml-2"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="logoutOtherBrowserSessions"
                    >
                        Log Out Other Browser Sessions
                    </jet-button>
                </template>
            </jet-dialog-modal>
        </template>
    </jet-action-section>
</template>

<script>
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetActionSection from "@/Jetstream/ActionSection.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import {
    ComputerDesktopIcon,
    DevicePhoneMobileIcon,
} from "@heroicons/vue/24/outline";
export default {
    components: {
        JetActionMessage,
        JetActionSection,
        JetButton,
        JetDialogModal,
        JetInput,
        JetInputError,
        JetSecondaryButton,
        ComputerDesktopIcon,
        DevicePhoneMobileIcon,
    },
    props: ["sessions"],

    data() {
        return {
            confirmingLogout: false,

            form: this.$inertia.form({
                password: "",
            }),
        };
    },

    methods: {
        confirmLogout() {
            this.confirmingLogout = true;

            setTimeout(() => this.$refs.password.focus(), 250);
        },

        logoutOtherBrowserSessions() {
            this.form.delete(route("other-browser-sessions.destroy"), {
                preserveScroll: true,
                onSuccess: () => this.closeModal(),
                onError: () => this.$refs.password.focus(),
                onFinish: () => this.form.reset(),
            });
        },

        closeModal() {
            this.confirmingLogout = false;

            this.form.reset();
        },
    },
};
</script>
