<template>
    <div class="float-left">
        <button
            type="button"
            class="p-0 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-200 focus:ring-offset-1 rounded dark:text-gray-400 dark:hover:text-gray-200"
            :aria-label="settingsAriaLabel"
            :title="settingsAriaLabel"
            @click="openModal"
        >
            <Cog6ToothIcon class="h-5 w-5" aria-hidden="true" />
        </button>

        <jet-dialog-modal :show="showModal" max-width="md" @close="closeModal">
            <template #title> Default spectrum </template>

            <template #content>
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    When a sample has more than one spectrum, choose whether
                    NMRium should open a
                    <span class="font-medium text-gray-800 dark:text-gray-100"
                        >1D</span
                    >
                    nucleus or a
                    <span class="font-medium text-gray-800 dark:text-gray-100"
                        >2D</span
                    >
                    experiment first.
                </p>

                <p
                    v-if="isAuthenticated"
                    class="mt-3 text-sm text-gray-600 dark:text-gray-300"
                >
                    Your choice is saved to your account and used whenever you
                    view or edit spectra in nmrXiv, including on other devices
                    while you are signed in.
                </p>
                <p v-else class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                    Your choice is saved in this browser only. Sign in and save
                    again under Account → Spectra preferences to keep it across
                    devices.
                </p>

                <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                    If the selected spectrum is not available for a sample,
                    NMRium opens the first tab it can.
                </p>

                <SpectrumTabPreferenceFields
                    class="mt-4"
                    :dimension="draftDimension"
                    :tab="draftTab"
                    :tabs1-d="tabs1D"
                    :tabs2-d="tabs2D"
                    :disabled="saving"
                    @update:dimension="draftDimension = $event"
                    @update:tab="draftTab = $event"
                />
            </template>

            <template #footer>
                <div class="flex items-center justify-end gap-4">
                    <button
                        type="button"
                        class="text-sm font-medium text-gray-600 underline-offset-2 hover:text-gray-900 hover:underline dark:text-gray-400 dark:hover:text-gray-200 disabled:cursor-not-allowed disabled:opacity-50 disabled:no-underline"
                        :disabled="saving"
                        @click="closeModal"
                    >
                        Cancel
                    </button>

                    <jet-button
                        :class="{ 'opacity-25': saving }"
                        :disabled="saving || !isDraftValid"
                        @click="save"
                    >
                        {{ saving ? "Saving…" : "Save" }}
                    </jet-button>
                </div>
            </template>
        </jet-dialog-modal>
    </div>
</template>

<script>
import { router } from "@inertiajs/vue3";
import { Cog6ToothIcon } from "@heroicons/vue/24/solid";
import JetButton from "@/Jetstream/Button.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import SpectrumTabPreferenceFields from "@/Shared/SpectrumTabPreferenceFields.vue";
import {
    allAllowedSpectrumTabs,
    initialSpectrumPreference,
    preferenceTabFromSelection,
    readStoredSpectrumTab,
    writeStoredSpectrumTab,
} from "@/Utils/nmriumTabPreference.js";

export default {
    components: {
        Cog6ToothIcon,
        JetButton,
        JetDialogModal,
        SpectrumTabPreferenceFields,
    },
    emits: ["changed"],
    data() {
        const initial = initialSpectrumPreference(this.$page);

        return {
            showModal: false,
            selectedDimension: initial.dimension,
            selectedTab: initial.tab,
            draftDimension: initial.dimension,
            draftTab: initial.tab,
            saving: false,
        };
    },
    computed: {
        tabs1D() {
            return this.$page.props.defaultSpectrumTabs1D ?? [];
        },
        tabs2D() {
            return this.$page.props.defaultSpectrumTabs2D ?? [];
        },
        isAuthenticated() {
            return Boolean(this.$page.props.auth?.user);
        },
        settingsAriaLabel() {
            if (!this.selectedTab) {
                return "Default spectrum settings (currently: automatic)";
            }

            const prefix = this.selectedDimension === "2d" ? "2D" : "1D";

            return `Default spectrum settings (currently: ${prefix} ${this.selectedTab})`;
        },
        isDraftValid() {
            if (!this.draftDimension) {
                return true;
            }

            const options =
                this.draftDimension === "1d" ? this.tabs1D : this.tabs2D;

            return options.includes(this.draftTab);
        },
        resolvedDraftTab() {
            return preferenceTabFromSelection(
                this.draftDimension,
                this.draftTab
            );
        },
        resolvedSelectedTab() {
            return preferenceTabFromSelection(
                this.selectedDimension,
                this.selectedTab
            );
        },
    },
    watch: {
        "$page.props.auth.user.preferences.default_spectrum_tab"() {
            if (this.isAuthenticated) {
                this.syncFromPage();
            }
        },
    },
    mounted() {
        if (
            this.isAuthenticated &&
            !this.$page.props.auth.user?.preferences?.default_spectrum_tab
        ) {
            const stored = readStoredSpectrumTab(
                allAllowedSpectrumTabs(this.$page)
            );
            if (stored) {
                const initial = initialSpectrumPreference({
                    props: {
                        ...this.$page.props,
                        auth: {
                            user: {
                                preferences: { default_spectrum_tab: stored },
                            },
                        },
                    },
                });
                this.selectedDimension = initial.dimension;
                this.selectedTab = initial.tab;
                this.draftDimension = initial.dimension;
                this.draftTab = initial.tab;
            }
        }
    },
    methods: {
        syncFromPage() {
            const initial = initialSpectrumPreference(this.$page);
            this.selectedDimension = initial.dimension;
            this.selectedTab = initial.tab;
            this.draftDimension = initial.dimension;
            this.draftTab = initial.tab;
        },
        openModal() {
            this.draftDimension = this.selectedDimension;
            this.draftTab = this.selectedTab;
            this.showModal = true;
        },
        closeModal() {
            if (this.saving) {
                return;
            }

            this.draftDimension = this.selectedDimension;
            this.draftTab = this.selectedTab;
            this.showModal = false;
        },
        save() {
            const tab = this.resolvedDraftTab;

            if (tab === this.resolvedSelectedTab) {
                this.showModal = false;

                return;
            }

            this.selectedDimension = this.draftDimension;
            this.selectedTab = this.draftTab;

            if (this.isAuthenticated) {
                this.persistToServer(tab);

                return;
            }

            writeStoredSpectrumTab(tab);
            this.showModal = false;
            this.$emit("changed", tab);
        },
        persistToServer(tab) {
            this.saving = true;

            router.put(
                route("user.preferences.update"),
                { default_spectrum_tab: tab },
                {
                    preserveScroll: true,
                    onFinish: () => {
                        this.saving = false;
                    },
                    onSuccess: () => {
                        this.showModal = false;
                        this.$emit("changed", tab);
                    },
                    onError: () => {
                        this.syncFromPage();
                    },
                }
            );
        },
    },
};
</script>
