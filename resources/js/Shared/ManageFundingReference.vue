<template>
    <div>
        <jet-dialog-modal
            :show="showDialog"
            :max-width="'6xl'"
            @close="onClose"
        >
            <template #title>
                <span class="text-base font-medium text-gray-900">
                    {{ project.name }} - Manage Funding References
                </span>
            </template>
            <template #content>
                <div>
                    <div
                        class="mb-4 rounded-md bg-gray-50 p-4 ring-1 ring-primary-200"
                    >
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg
                                    class="h-5 w-5 text-primary-500"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-primary-900">
                                    Funding references are included in DataCite
                                    DOI metadata when your project is published.
                                    You can add or update them at any time;
                                    changes sync automatically when a DOI
                                    exists.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="mx-auto flex max-w-3xl flex-col gap-10 px-4 sm:px-6"
                    >
                        <section aria-labelledby="funding-manual-heading">
                            <h3
                                id="funding-manual-heading"
                                class="text-sm font-bold leading-6 text-gray-900"
                            >
                                {{
                                    isEdit
                                        ? "Edit funding reference"
                                        : "Add funding reference"
                                }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Declare third-party funding such as DFG grants.
                                Funder name is required; other fields improve
                                discoverability.
                            </p>

                            <div
                                class="mt-4 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2"
                            >
                                <div class="sm:col-span-2">
                                    <label
                                        for="funder_name"
                                        class="block text-sm font-medium text-gray-700"
                                    >
                                        Funder name
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="funder_name"
                                        v-model="form.funder_name"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                        placeholder="e.g. Deutsche Forschungsgemeinschaft"
                                    />
                                    <jet-input-error
                                        :message="form.errors.funder_name"
                                        class="mt-2"
                                    />
                                </div>

                                <div class="sm:col-span-2">
                                    <label
                                        for="funder_ror"
                                        class="block text-sm font-medium text-gray-700"
                                    >
                                        Funder (ROR search)
                                    </label>
                                    <ror-affiliation-typeahead
                                        id="funder_ror"
                                        v-model="funderSearchLabel"
                                        placeholder="Search funder organization via ROR"
                                        @organization-selected="
                                            onFunderOrganizationSelected
                                        "
                                    />
                                </div>

                                <div>
                                    <label
                                        for="funder_identifier_type"
                                        class="block text-sm font-medium text-gray-700"
                                    >
                                        Funder identifier type
                                    </label>
                                    <select
                                        id="funder_identifier_type"
                                        v-model="form.funder_identifier_type"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                    >
                                        <option :value="null">
                                            Select type (optional)
                                        </option>
                                        <option value="ROR">ROR</option>
                                        <option value="Crossref Funder ID">
                                            Crossref Funder ID
                                        </option>
                                    </select>
                                    <jet-input-error
                                        :message="
                                            form.errors.funder_identifier_type
                                        "
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <label
                                        for="funder_identifier"
                                        class="block text-sm font-medium text-gray-700"
                                    >
                                        Funder identifier
                                    </label>
                                    <input
                                        id="funder_identifier"
                                        v-model="form.funder_identifier"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                        placeholder="https://ror.org/... or 10.13039/..."
                                    />
                                    <jet-input-error
                                        :message="form.errors.funder_identifier"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <label
                                        for="award_number"
                                        class="block text-sm font-medium text-gray-700"
                                    >
                                        Award number
                                    </label>
                                    <input
                                        id="award_number"
                                        v-model="form.award_number"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                        placeholder="e.g. 441958208"
                                    />
                                    <jet-input-error
                                        :message="form.errors.award_number"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <label
                                        for="award_title"
                                        class="block text-sm font-medium text-gray-700"
                                    >
                                        Award title
                                    </label>
                                    <input
                                        id="award_title"
                                        v-model="form.award_title"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                        placeholder="Grant or project title"
                                    />
                                    <jet-input-error
                                        :message="form.errors.award_title"
                                        class="mt-2"
                                    />
                                </div>

                                <div class="sm:col-span-2">
                                    <label
                                        for="award_uri"
                                        class="block text-sm font-medium text-gray-700"
                                    >
                                        Award URI
                                    </label>
                                    <input
                                        id="award_uri"
                                        v-model="form.award_uri"
                                        type="url"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                        placeholder="https://gepris.dfg.de/gepris/projekt/..."
                                    />
                                    <p class="mt-1 text-xs text-gray-500">
                                        Link to the grant record (e.g. DFG
                                        GEPRIS).
                                    </p>
                                    <jet-input-error
                                        :message="form.errors.award_uri"
                                        class="mt-2"
                                    />
                                </div>
                            </div>

                            <div class="mt-6 flex flex-wrap gap-2">
                                <jet-secondary-button @click="addManually">
                                    {{
                                        isEdit
                                            ? "Save changes"
                                            : "Add funding reference"
                                    }}
                                </jet-secondary-button>
                                <jet-secondary-button
                                    v-if="isEdit"
                                    @click="onCancelEdit"
                                >
                                    Cancel edit
                                </jet-secondary-button>
                            </div>
                            <jet-input-error :message="error" class="mt-2" />
                        </section>
                    </div>
                </div>
            </template>
            <template #footer>
                <jet-secondary-button @click="onClose">
                    Close
                </jet-secondary-button>
            </template>
        </jet-dialog-modal>

        <jet-dialog-modal
            :show="confirmDelete"
            :max-width="'md'"
            @close="closeDeleteConfirm"
        >
            <template #title> Delete funding reference </template>
            <template #content>
                Are you sure you want to remove this funding reference from the
                project?
            </template>
            <template #footer>
                <jet-secondary-button @click="closeDeleteConfirm">
                    Cancel
                </jet-secondary-button>
                <jet-danger-button
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="deleteFundingReference"
                >
                    Delete
                </jet-danger-button>
            </template>
        </jet-dialog-modal>
    </div>
</template>

<script>
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import RorAffiliationTypeahead from "@/Shared/RorAffiliationTypeahead.vue";
import { router } from "@inertiajs/vue3";

export default {
    components: {
        JetDialogModal,
        JetSecondaryButton,
        JetDangerButton,
        JetInputError,
        RorAffiliationTypeahead,
    },

    props: ["project"],

    data() {
        return {
            form: this.$inertia.form({
                id: null,
                funder_name: "",
                funder_identifier: "",
                funder_identifier_type: null,
                award_number: "",
                award_title: "",
                award_uri: "",
                errors: {},
            }),
            fundingReferencesForm: this.$inertia.form({
                funding_references: [],
            }),
            showDialog: false,
            fundingReferences: [],
            confirmDelete: false,
            selectedFundingReference: null,
            isEdit: false,
            error: "",
            funderSearchLabel: "",
        };
    },

    mounted() {
        this.loadInitial();
    },

    methods: {
        loadInitial() {
            if (this.project?.funding_references) {
                this.fundingReferences = this.project.funding_references;
            }
        },

        toggleDialog() {
            if (this.showDialog) {
                this.onClose();
            } else {
                this.loadInitial();
                this.resetForm();
                this.isEdit = false;
                this.selectedFundingReference = null;
                this.confirmDelete = false;
                this.showDialog = true;
            }
        },

        onClose() {
            this.showDialog = false;
            this.confirmDelete = false;
            this.isEdit = false;
            this.selectedFundingReference = null;
            this.resetForm();
            this.error = "";
        },

        closeDeleteConfirm() {
            this.confirmDelete = false;
            this.fundingReferencesForm.reset();
        },

        resetForm() {
            this.form.reset();
            this.funderSearchLabel = "";
            this.form.errors = {};
        },

        onFunderOrganizationSelected({ name, rorId }) {
            if (name && !this.form.funder_name) {
                this.form.funder_name = name;
            }

            if (rorId) {
                this.form.funder_identifier = rorId.startsWith("https://")
                    ? rorId
                    : `https://ror.org/${rorId.replace(
                          /^https?:\/\/ror\.org\//i,
                          ""
                      )}`;
                this.form.funder_identifier_type = "ROR";
            }
        },

        edit(fundingReference) {
            this.selectedFundingReference = fundingReference;
            this.isEdit = true;
            this.form.id = fundingReference.id ?? null;
            this.form.funder_name = fundingReference.funder_name ?? "";
            this.form.funder_identifier =
                fundingReference.funder_identifier ?? "";
            this.form.funder_identifier_type =
                fundingReference.funder_identifier_type ?? null;
            this.form.award_number = fundingReference.award_number ?? "";
            this.form.award_title = fundingReference.award_title ?? "";
            this.form.award_uri = fundingReference.award_uri ?? "";
            this.funderSearchLabel = fundingReference.funder_name ?? "";
        },

        onCancelEdit() {
            this.isEdit = false;
            this.selectedFundingReference = null;
            this.resetForm();
        },

        validateForm() {
            this.form.errors = {};

            if (!this.form.funder_name || this.form.funder_name.trim() === "") {
                this.form.errors.funder_name = "Funder name is required.";
                return false;
            }

            if (
                this.form.funder_identifier &&
                this.form.funder_identifier.trim() !== "" &&
                !this.form.funder_identifier_type
            ) {
                this.form.errors.funder_identifier_type =
                    "Select an identifier type when providing a funder identifier.";
                return false;
            }

            return true;
        },

        addManually() {
            if (!this.validateForm()) {
                return;
            }

            this.fundingReferencesForm.reset();
            const payload = this.buildFundingReferenceObject();
            const existing = (this.fundingReferences || [])
                .filter((reference) => reference.id !== payload.id)
                .map((reference) => this.serializeFundingReference(reference));

            this.fundingReferencesForm.funding_references = [
                ...existing,
                payload,
            ];
            this.submitToBackend();
        },

        serializeFundingReference(reference) {
            return {
                id: reference.id ?? null,
                funder_name: reference.funder_name ?? null,
                funder_identifier: reference.funder_identifier ?? null,
                funder_identifier_type:
                    reference.funder_identifier_type ?? null,
                award_number: reference.award_number ?? null,
                award_title: reference.award_title ?? null,
                award_uri: reference.award_uri ?? null,
            };
        },

        buildFundingReferenceObject() {
            return {
                id: this.form.id,
                funder_name: this.form.funder_name?.trim() || null,
                funder_identifier: this.form.funder_identifier?.trim() || null,
                funder_identifier_type:
                    this.form.funder_identifier_type || null,
                award_number: this.form.award_number?.trim() || null,
                award_title: this.form.award_title?.trim() || null,
                award_uri: this.form.award_uri?.trim() || null,
            };
        },

        submitToBackend() {
            axios
                .post(
                    route("fundingReference.save", this.project.id),
                    {
                        funding_references:
                            this.fundingReferencesForm.funding_references,
                    },
                    {
                        headers: {
                            Accept: "application/json",
                        },
                    }
                )
                .then(() => {
                    this.showDialog = false;
                    this.confirmDelete = false;
                    this.isEdit = false;
                    this.selectedFundingReference = null;
                    this.resetForm();
                    this.error = "";
                    router.reload({ only: ["project"] });
                })
                .catch((err) => {
                    this.form.errors = err?.response?.data?.errors ?? {};
                    this.error =
                        err?.response?.data?.message ||
                        "An error occurred while saving funding references.";
                });
        },

        confirmDeletion(fundingReference) {
            this.confirmDelete = true;
            this.fundingReferencesForm.reset();
            this.fundingReferencesForm.funding_references = [
                {
                    id: fundingReference.id ?? null,
                },
            ];
        },

        deleteFundingReference() {
            axios
                .delete(route("fundingReference.delete", this.project.id), {
                    data: {
                        funding_references:
                            this.fundingReferencesForm.funding_references,
                    },
                    headers: {
                        Accept: "application/json",
                    },
                })
                .then(() => {
                    router.reload({ only: ["project"] });
                    this.loadInitial();
                    this.closeDeleteConfirm();
                })
                .catch((err) => {
                    this.error =
                        err?.response?.data?.message ||
                        "An error occurred while deleting the funding reference.";
                });
        },
    },
};
</script>
