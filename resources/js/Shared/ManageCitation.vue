<template>
    <div>
        <jet-dialog-modal
            :show="showDialog"
            :max-width="'6xl'"
            @close="onClose"
        >
            <template #title>
                <span class="text-base font-medium text-gray-900">
                    {{ project.name }} - Manage Citations
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
                                    Note: Citation details can be updated during
                                    the embargo period. A DOI is not required
                                    for embargoed publication, but we recommend
                                    reviewing this section and adding any
                                    missing DOIs before making the project
                                    public.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div
                            class="mx-auto flex max-w-3xl flex-col gap-10 px-4 sm:px-6"
                        >
                            <!-- Import from DOI (add flow only; hidden while editing) -->
                            <section
                                v-if="!isEdit"
                                aria-labelledby="citation-import-heading"
                            >
                                <h3
                                    id="citation-import-heading"
                                    class="text-sm font-bold leading-6 text-gray-900"
                                >
                                    Import from DOI
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Fetch publication metadata automatically,
                                    then add it to your project.
                                </p>
                                <div
                                    class="mt-4 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2"
                                >
                                    <div class="sm:col-span-2">
                                        <label
                                            for="query"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            DOI
                                        </label>
                                        <div
                                            class="mt-1 flex rounded-md shadow-sm"
                                        >
                                            <input
                                                id="query"
                                                v-model="query"
                                                type="text"
                                                name="query"
                                                placeholder="Enter DOI to import citation automatically"
                                                autocomplete="off"
                                                class="min-w-0 flex-1 rounded border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 flex flex-wrap gap-2">
                                    <jet-secondary-button
                                        :disabled="!isQueryValid || loading"
                                        @click="fetchCitations"
                                    >
                                        Import
                                    </jet-secondary-button>
                                    <jet-secondary-button
                                        :disabled="
                                            isEmpty(fetchedCitations) && !query
                                        "
                                        @click="clearImportData"
                                    >
                                        Reset
                                    </jet-secondary-button>
                                </div>
                                <jet-input-error
                                    :message="error"
                                    class="mt-2"
                                />
                                <div
                                    v-if="loading"
                                    class="mt-4 sm:col-span-9 align-centre"
                                >
                                    <loading-button :loading="loading" />
                                </div>
                                <div
                                    v-if="
                                        !(
                                            isEmpty(fetchedCitations) ||
                                            fetchCitations == null
                                        ) && !loading
                                    "
                                    class="mt-4"
                                >
                                    <CitationCard
                                        :citations="[fetchedCitations]"
                                    />
                                    <div class="mt-3 flex justify-end">
                                        <jet-secondary-button
                                            class="text-md font-bold text-primary-900"
                                            @click="save('addFetched')"
                                        >
                                            Add
                                        </jet-secondary-button>
                                    </div>
                                </div>
                            </section>

                            <!-- Manual citation (below import when adding) -->
                            <section
                                :class="
                                    isEdit
                                        ? ''
                                        : 'border-t border-gray-200 pt-10'
                                "
                                aria-labelledby="citation-manual-heading"
                            >
                                <h3
                                    id="citation-manual-heading"
                                    class="text-sm font-bold leading-6 text-gray-900"
                                >
                                    <template v-if="!isEdit"
                                        >Add citation manually</template
                                    >
                                    <template v-else>Update citation</template>
                                </h3>
                                <p
                                    v-if="!isEdit"
                                    class="mt-1 text-sm text-gray-500"
                                >
                                    Or enter title, authors, and optional DOI
                                    and citation text yourself.
                                </p>
                                <p v-else class="mt-1 text-sm text-gray-500">
                                    Update title, authors, DOI, or citation text
                                    as needed.
                                </p>
                                <div
                                    class="mt-4 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2"
                                >
                                    <div class="sm:col-span-1">
                                        <label
                                            for="doi"
                                            class="block text-sm font-medium text-gray-700 after:ml-0.5 after:text-red-500"
                                        >
                                            DOI
                                        </label>
                                        <div class="mt-1">
                                            <input
                                                id="doi"
                                                v-model="form.doi"
                                                type="text"
                                                name="doi"
                                                placeholder="Enter DOI (e.g., 10.1000/journal.2023.0001)"
                                                :class="[
                                                    isEdit
                                                        ? 'shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100'
                                                        : '',
                                                    'shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md',
                                                ]"
                                            />
                                        </div>
                                        <jet-input-error
                                            :message="form.errors.doi"
                                            class="mt-2"
                                        />
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label
                                            for="title"
                                            class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                                        >
                                            Title
                                        </label>
                                        <div class="mt-1">
                                            <input
                                                id="title"
                                                v-model="form.title"
                                                type="text"
                                                name="title"
                                                placeholder="Enter publication title"
                                                autocomplete="title"
                                                :class="[
                                                    isEdit
                                                        ? 'shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100'
                                                        : '',
                                                    'shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md',
                                                ]"
                                            />
                                        </div>
                                        <jet-input-error
                                            :message="form.errors.title"
                                            class="mt-2"
                                        />
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label
                                            for="authors"
                                            class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                                        >
                                            Authors
                                        </label>
                                        <div class="mt-1">
                                            <textarea
                                                id="authors"
                                                v-model="form.authors"
                                                type="text"
                                                name="authors"
                                                placeholder="Enter author names (e.g., Smith J, Johnson A, Brown M)"
                                                :class="[
                                                    isEdit
                                                        ? 'shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100'
                                                        : '',
                                                    'shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md',
                                                ]"
                                            />
                                        </div>
                                        <jet-input-error
                                            :message="form.errors.authors"
                                            class="mt-2"
                                        />
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label
                                            for="citation_text"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Citation Text
                                        </label>
                                        <div class="mt-1">
                                            <textarea
                                                id="citation_text"
                                                v-model="form.citation_text"
                                                name="citation_text"
                                                autocomplete="citation_text"
                                                placeholder="Enter complete citation (journal, year, volume, issue, pages) or provide any relevant publication information"
                                                type="text"
                                                :class="[
                                                    isEdit
                                                        ? 'shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100'
                                                        : '',
                                                    'shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md',
                                                ]"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        v-if="!isEdit"
                                        class="sm:col-span-2 flex flex-wrap justify-end gap-2"
                                    >
                                        <jet-secondary-button
                                            :disabled="form.processing"
                                            @click="
                                                form.reset(),
                                                    citationsForm.reset(),
                                                    clearFormErrors()
                                            "
                                        >
                                            Clear
                                        </jet-secondary-button>
                                        <jet-secondary-button
                                            @click="save('addManually')"
                                        >
                                            Add
                                        </jet-secondary-button>
                                    </div>
                                    <div
                                        v-else
                                        class="sm:col-span-2 flex flex-wrap justify-end gap-2"
                                    >
                                        <jet-secondary-button
                                            :disabled="form.processing"
                                            @click="onCancelEdit()"
                                        >
                                            Cancel
                                        </jet-secondary-button>
                                        <jet-secondary-button
                                            :disabled="form.processing"
                                            @click="save('addManually')"
                                        >
                                            Update
                                        </jet-secondary-button>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </template>
            <template #footer>
                <div class="flex">
                    <jet-secondary-button class="float-left" @click="onClose">
                        Close
                    </jet-secondary-button>
                </div>
            </template>
        </jet-dialog-modal>

        <jet-dialog-modal
            :show="confirmDelete"
            max-width="md"
            @close="closeDeleteConfirm"
        >
            <template #title> Delete citation </template>

            <template #content>
                <p class="text-sm text-gray-600">
                    Are you sure you want to delete this citation? This cannot
                    be undone.
                </p>
            </template>

            <template #footer>
                <jet-secondary-button @click="closeDeleteConfirm">
                    Cancel
                </jet-secondary-button>

                <jet-danger-button
                    class="ml-2"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="deleteCitation()"
                >
                    Delete citation
                </jet-danger-button>
            </template>
        </jet-dialog-modal>
    </div>
</template>
<script>
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import LoadingButton from "@/Shared/LoadingButton.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import { router } from "@inertiajs/vue3";
import Global from "@/Mixins/Global.js";
import CitationCard from "@/Shared/CitationCard.vue";
export default {
    components: {
        JetDialogModal,
        JetSecondaryButton,
        JetDangerButton,
        JetInputError,
        LoadingButton,
        CitationCard,
    },
    mixins: [Global],

    props: ["project"],
    data() {
        return {
            form: this.$inertia.form({
                doi: "",
                title: "",
                authors: "",
                citation_text: "",
                errors: {},
            }),
            citationsForm: this.$inertia.form({
                citations: [],
            }),
            fetchedCitations: {},
            showDialog: false,
            citations: [],
            confirmDelete: false,
            selectedCitation: null,
            query: "",
            loading: false,
            isEdit: false,
            error: "",
        };
    },

    computed: {
        /*Check if query is valid for import*/
        isQueryValid() {
            if (!this.query || this.query.trim() === "") {
                return false;
            }
            const doiRegex = /^10\.[\d]{4,}(?:\.[\d]+)*\/[^\s]+$/;
            return doiRegex.test(this.extractQueryParam(this.query));
        },

        /*Get form error count for display*/
        formErrorCount() {
            return Object.keys(this.form.errors || {}).length;
        },

        /*Check if form has any errors*/
        hasFormErrors() {
            return this.formErrorCount > 0 || this.form.hasErrors;
        },
    },

    mounted() {
        this.loadInitial();
    },

    methods: {
        // =============================================================================
        // INITIALIZATION & STATE MANAGEMENT
        // =============================================================================

        /**
         * Load initial citations from project data
         */
        loadInitial() {
            if (this.project && this.project.citations) {
                this.citations = this.project.citations;
            }
        },

        /**
         * Toggle dialog visibility. Import from DOI and manual entry only;
         * existing citations stay on the publish/project page.
         */
        toggleDialog() {
            if (this.showDialog) {
                this.onClose();
            } else {
                this.loadInitial();
                this.form.reset();
                this.clearFormErrors();
                this.clearImportData();
                this.isEdit = false;
                this.selectedCitation = null;
                this.confirmDelete = false;
                this.showDialog = true;
            }
        },

        /**
         * Dismiss delete confirmation without deleting.
         */
        closeDeleteConfirm() {
            this.confirmDelete = false;
            this.citationsForm.reset();
        },

        /**
         * Close dialog and reset transient UI state
         */
        onClose() {
            this.closeDeleteConfirm();
            this.showDialog = false;
            this.isEdit = false;
            this.selectedCitation = null;
            this.confirmDelete = false;
            this.form.reset();
            this.form.errors = {};
            this.clearImportData();
        },

        // =============================================================================
        // API CALLS & DATA FETCHING
        // =============================================================================

        /**
         * Fetch citation from DOI using multiple API sources
         * Implements promise chain to try multiple APIs in sequence
         */
        fetchCitations() {
            this.loading = true;
            this.error = "";
            this.fetchedCitations = {};

            if (!this.validateQuery()) {
                this.loading = false;
                return;
            }

            this.query = this.extractQueryParam(this.query);

            this.fetchFromEuropePMC()
                .catch(() => this.fetchFromCrossref())
                .catch(() => this.fetchFromDatacite())
                .catch(() =>
                    this.handleFetchError(
                        "No citation data found for the provided DOI. Please enter details manually."
                    )
                )
                .finally(() => {
                    this.loading = false;
                });
        },

        /**
         * Fetches citation data from EuropePMC API
         * @returns {Promise} Promise that resolves on successful data fetch
         */
        fetchFromEuropePMC() {
            return axios
                .get(this.$page.props.europemcWSApi, {
                    params: {
                        query: `DOI:${this.query}`,
                        format: "json",
                        pageSize: "1",
                        resulttype: "core",
                        synonym: "true",
                    },
                })
                .then((res) => {
                    if (
                        res &&
                        res.data &&
                        res.data.resultList.result.length > 0
                    ) {
                        this.fetchedCitations = this.formatCitationResponse(
                            res.data.resultList.result[0],
                            "europemc"
                        );
                        return Promise.resolve();
                    }
                    return Promise.reject();
                });
        },

        /**
         * Fetches citation data from Crossref API
         * @returns {Promise} Promise that resolves on successful data fetch or rejects on failure
         */
        fetchFromCrossref() {
            const encodedQuery = encodeURIComponent(this.query);
            const safeUrl = `${this.$page.props.CROSSREF_API}${encodedQuery}`;

            return axios.get(safeUrl).then((res) => {
                if (res.data && res.data.message) {
                    this.fetchedCitations = this.formatCitationResponse(
                        res.data.message,
                        "crossref"
                    );
                    return Promise.resolve();
                }
                return Promise.reject();
            });
        },

        /**
         * Fetches citation data from DataCite API
         * @returns {Promise} Promise that resolves on successful data fetch or rejects on failure
         */
        fetchFromDatacite() {
            const encodedQuery = encodeURIComponent(this.query);
            const safeUrl = `${this.$page.props.DATACITE_API}${encodedQuery}`;

            return axios.get(safeUrl).then((res) => {
                if (res && res.data && res.data.data) {
                    this.fetchedCitations = this.formatCitationResponse(
                        res.data.data,
                        "datacite"
                    );
                    return Promise.resolve();
                }
                return Promise.reject();
            });
        },

        /**
         * Handles fetch errors with user-friendly messages
         * @param {string} message - Error message to display to user
         */
        handleFetchError(message) {
            this.error = message;
            this.fetchedCitations = {};
            this.loading = false;
        },

        // =============================================================================
        // CRUD OPERATIONS
        // =============================================================================

        /**
         * Edit existing citation - populate form and switch to edit mode
         * @param {Object} citation - Citation object to edit
         */
        edit(citation) {
            this.selectedCitation = citation;
            this.citations = this.citations.filter((existingCitation) => {
                if (this.selectedCitation?.id && existingCitation?.id) {
                    return existingCitation.id !== this.selectedCitation.id;
                }

                return existingCitation !== this.selectedCitation;
            });
            for (var key in citation) {
                this.form[key] = Object.prototype.hasOwnProperty.call(
                    citation,
                    key
                )
                    ? citation[key]
                    : null;
            }
            this.isEdit = true;
            this.fetchedCitations = {};
            this.query = "";
        },

        /**
         * Route save request to appropriate method based on input type
         * @param {string} input - Type of save operation ('addFetched' or 'addManually')
         */
        save(input) {
            switch (input) {
                case "addFetched":
                    this.addFetchedCitation();
                    break;
                case "addManually":
                    this.addManually();
                    break;
            }
        },

        /**
         * Add citation manually from form data
         */
        addManually() {
            if (!this.validateForm()) {
                return;
            }

            this.citationsForm.reset();
            let _citation = {};
            for (var key in this.form) {
                _citation[key] = Object.prototype.hasOwnProperty.call(
                    this.form,
                    key
                )
                    ? this.form[key]
                    : null;
            }
            this.citationsForm.citations.push(_citation);
            this.executeQuery();
        },

        /**
         * Add citation from fetched API data
         */
        addFetchedCitation() {
            this.citationsForm.reset();
            if (this.citations.length > 0) {
                this.citations = this.citations.concat(this.fetchedCitations);
            } else {
                this.citations.push(this.fetchedCitations);
            }
            this.executeQuery();
        },

        /**
         * Execute save request to backend API
         */
        executeQuery() {
            try {
                this.prepareCitationsForSave();
                this.deduplicateCitations();
                this.submitCitationsToBackend();
            } catch (error) {
                console.error("Execute query error:", error);
                this.error = "An unexpected error occurred. Please try again.";
            }
        },

        /**
         * Prepare citations data for saving to backend
         */
        prepareCitationsForSave() {
            if (this.citations.length > 0) {
                this.citations.forEach((citation) => {
                    const citationObj = this.buildCitationObject(citation);
                    this.citationsForm.citations.push(citationObj);
                });
            }
        },

        /**
         * Build citation object from citation data
         * @param {Object} citation - Citation data
         * @returns {Object} Formatted citation object for backend
         */
        buildCitationObject(citation) {
            let citationObj = {};
            for (var key in citation) {
                citationObj[key] = Object.prototype.hasOwnProperty.call(
                    citation,
                    key
                )
                    ? citation[key]
                    : null;
            }
            return citationObj;
        },

        /**
         * Remove duplicate citations based on DOI
         */
        deduplicateCitations() {
            const seen = new Set();

            this.citationsForm.citations = this.citationsForm.citations.filter(
                (citation) => {
                    const key = this.getCitationIdentityKey(citation);

                    if (seen.has(key)) {
                        return false;
                    }

                    seen.add(key);

                    return true;
                }
            );
        },

        getCitationIdentityKey(citation) {
            const normalizedId = citation?.id ? String(citation.id) : "";
            const normalizedDoi = citation?.doi ? citation.doi.trim() : "";
            const normalizedTitle = citation?.title
                ? citation.title.trim().toLowerCase()
                : "";
            const normalizedAuthors = citation?.authors
                ? citation.authors.trim().toLowerCase()
                : "";

            if (normalizedId !== "") {
                return `id:${normalizedId}`;
            }

            if (normalizedDoi !== "") {
                return `doi:${normalizedDoi}`;
            }

            return `meta:${normalizedTitle}|${normalizedAuthors}`;
        },

        /**
         * Submit citations to backend API
         */
        submitCitationsToBackend() {
            this.citationsForm.post(route("citation.save", this.project.id), {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => this.handleSaveSuccess(),
                onError: (err) => this.handleSaveError(err),
            });
        },

        /**
         * Handle successful citation save operation
         */
        handleSaveSuccess() {
            this.showDialog = false;
            this.confirmDelete = false;
            this.error = "";
            this.isEdit = false;
            this.selectedCitation = null;
            this.clearImportData();
            this.form.reset();
            this.form.errors = {};
            this.citationsForm.reset();

            router.reload({
                only: ["project"],
                preserveScroll: true,
                preserveState: true,
                onFinish: () => {
                    this.loadInitial();
                },
            });
        },

        /**
         * Handle citation save error
         * @param {Object} err - Error object from API
         */
        handleSaveError(err) {
            console.error("Citation save error:", err);
            this.error = "Failed to save citation. Please try again.";
        },
        /**
         * Comprehensive form validation for manual entry
         * @returns {boolean} - True if form is valid
         */
        validateForm() {
            this.form.errors = {};
            this.form.hasErrors = false;
            let hasErrors = false;

            hasErrors = this.validateTitle() || hasErrors;
            hasErrors = this.validateAuthors() || hasErrors;
            hasErrors = this.validateDoi() || hasErrors;

            if (!this.isEdit) {
                hasErrors = this.validateCitationText() || hasErrors;
            }

            if (hasErrors) {
                this.form.hasErrors = true;
            }

            return !hasErrors;
        },

        /**
         * Validate title field
         * @returns {boolean} True if validation failed
         */
        validateTitle() {
            if (!this.form.title || this.form.title.trim() === "") {
                this.form.errors.title = "The title field is required.";
                return true;
            } else if (this.form.title.length < 5) {
                this.form.errors.title =
                    "Title must be at least 5 characters long.";
                return true;
            } else if (this.form.title.length > 500) {
                this.form.errors.title =
                    "Title must not exceed 500 characters.";
                return true;
            }
            return false;
        },

        /**
         * Validate authors field
         * @returns {boolean} True if validation failed
         */
        validateAuthors() {
            if (!this.form.authors || this.form.authors.trim() === "") {
                this.form.errors.authors = "The authors field is required.";
                return true;
            } else if (this.form.authors.length < 3) {
                this.form.errors.authors =
                    "Authors must be at least 3 characters long.";
                return true;
            } else if (this.form.authors.length > 1000) {
                this.form.errors.authors =
                    "Authors must not exceed 1000 characters.";
                return true;
            }
            return false;
        },

        /**
         * Validate DOI field
         * @returns {boolean} True if validation failed
         */
        validateDoi() {
            const doiRegex = /^10\.[\d]{4,}(?:\.[\d]+)*\/[^\s]+$/;
            if (this.form.doi && !doiRegex.test(this.form.doi.trim())) {
                this.form.errors.doi =
                    "Please enter a valid DOI format (e.g., 10.1000/journal.2023.0001).";
                return true;
            }
            return false;
        },

        /**
         * Validate citation text field
         * @returns {boolean} True if validation failed
         */
        validateCitationText() {
            if (
                this.form.citation_text &&
                this.form.citation_text.trim() !== ""
            ) {
                if (this.form.citation_text.length > 2000) {
                    this.form.errors.citation_text =
                        "Citation text must not exceed 2000 characters.";
                    return true;
                }
            }
            return false;
        },

        /**
         * Enhanced query validation for DOI import
         * @returns {boolean} - True if query is valid
         */
        validateQuery() {
            if (!this.query || this.query.trim() === "") {
                this.error = "Please enter a DOI to search.";
                return false;
            }

            this.query = this.extractQueryParam(this.query);
            const doiRegex = /^10\.[\d]{4,}(?:\.[\d]+)*\/[^\s]+$/;

            if (!doiRegex.test(this.query)) {
                this.error = "Please enter a valid DOI format.";
                return false;
            }

            this.error = "";
            return true;
        },

        /*Helper methods for clean state management*/
        clearFormErrors() {
            this.form.errors = {};
            this.form.hasErrors = false;
        },

        clearImportData() {
            this.fetchedCitations = {};
            this.query = null;
            this.error = "";
        },

        /**
         * Enhanced form reset with error clearing
         */
        resetForm() {
            this.form.reset();
            this.clearFormErrors();
        },

        /**
         * Prepare citation for deletion
         * @param {Object} citation - Citation to delete
         */
        confirmDeletion(citation) {
            this.confirmDelete = true;
            this.citationsForm.reset();
            this.citationsForm.citations = [
                {
                    id: citation.id ? citation.id : null,
                    title: citation.title ? citation.title : null,
                    doi: citation.doi ? citation.doi : null,
                    authors: citation.authors ? citation.authors : null,
                    citation_text: citation.citation_text
                        ? citation.citation_text
                        : null,
                },
            ];
        },

        /**
         * Execute delete request to backend API
         */
        deleteCitation() {
            axios
                .delete(route("citation.delete", this.project.id), {
                    data: {
                        citations: this.citationsForm.citations,
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
                .catch((err) => console.error(err));
        },

        // =============================================================================
        // FORM & STATE MANAGEMENT
        // =============================================================================

        /**
         * Cancel edit operation and restore previous state
         */
        onCancelEdit() {
            if (this.selectedCitation) {
                this.citations.push(this.selectedCitation);
            }
            this.isEdit = false;
            this.selectedCitation = null;
            this.resetForm();
        },

        // =============================================================================
        // UTILITY & HELPER METHODS
        // =============================================================================

        /**
         * Format API response from different citation sources
         * @param {Object} obj - Raw API response object
         * @param {string} apiType - API source type ('europemc', 'crossref', 'datacite')
         * @returns {Object} - Formatted citation object
         */
        formatCitationResponse(obj, apiType) {
            this.formattedCitationRes = {};

            if (obj) {
                switch (apiType) {
                    case "europemc":
                        this.formatEuropemcCitation(obj);
                        break;
                    case "datacite":
                        this.formatDataciteCitation(obj);
                        break;
                    case "crossref":
                        this.formatCrossrefCitation(obj);
                        break;
                }
            }
            this.loading = false;
            return this.formattedCitationRes;
        },

        /**
         * Formats citation data from EuropePMC API response
         * @param {Object} obj - Raw EuropePMC API response object
         */
        formatEuropemcCitation(obj) {
            const journalInfo = this.extractEuropemcJournalInfo(obj);
            const pageInfo = obj.pageInfo ? obj.pageInfo : "";

            this.formattedCitationRes.title = obj.title ? obj.title : "";
            this.formattedCitationRes.authors = obj.authorString
                ? obj.authorString
                : "";
            this.formattedCitationRes.citation_text = this.buildCitationText(
                journalInfo.journalTitle,
                journalInfo.yearofPublication,
                journalInfo.volume,
                journalInfo.issue,
                pageInfo
            );
            this.formattedCitationRes.doi = obj.doi ? obj.doi : "";
        },

        /**
         * Extracts journal information from EuropePMC response
         * @param {Object} obj - EuropePMC response object
         * @returns {Object} Extracted journal information
         */
        extractEuropemcJournalInfo(obj) {
            const info = {
                journalTitle: "",
                yearofPublication: "",
                volume: "",
                issue: "",
            };

            if (obj.journalInfo) {
                info.journalTitle = obj.journalInfo.journal.title || "";
                info.yearofPublication =
                    obj.journalInfo.yearOfPublication || "";
                info.volume = obj.journalInfo.volume || "";
                info.issue = obj.journalInfo.issue || "";
            }

            return info;
        },

        /**
         * Formats citation data from DataCite API response
         * @param {Object} obj - Raw DataCite API response object
         */
        formatDataciteCitation(obj) {
            const journalTitle = obj.attributes.titles
                ? obj.attributes.titles[0].title
                : "";
            const yearofPublication = obj.attributes.publicationYear || null;

            this.formattedCitationRes.title = journalTitle;
            this.formattedCitationRes.authors =
                this.extractDataciteAuthors(obj);
            this.formattedCitationRes.citation_text =
                journalTitle + " " + yearofPublication;
            this.formattedCitationRes.doi = obj.attributes
                ? obj.attributes["doi"]
                : "";
        },

        /**
         * Extracts author names from DataCite API response
         * @param {Object} obj - DataCite response object
         * @returns {string} Formatted authors string
         */
        extractDataciteAuthors(obj) {
            if (obj.attributes.creators) {
                return obj.attributes.creators
                    .map((author) => author.name)
                    .join(", ");
            }
            return "";
        },

        /**
         * Formats citation data from Crossref API response
         * @param {Object} obj - Raw Crossref API response object
         */
        formatCrossrefCitation(obj) {
            const journalTitle = obj.title[0];
            const yearofPublication = this.extractCrossrefPublicationYear(obj);
            const volume = obj.volume || "";
            const issue = obj.issue || "";
            const pageInfo = obj.page || "";

            this.formattedCitationRes.title = journalTitle;
            this.formattedCitationRes.authors =
                this.extractCrossrefAuthors(obj);
            this.formattedCitationRes.citation_text = this.buildCitationText(
                journalTitle,
                yearofPublication,
                volume,
                issue,
                pageInfo
            );
            this.formattedCitationRes.doi = obj.DOI ? obj.DOI : "";
        },

        /**
         * Extracts publication year from Crossref API response
         * @param {Object} obj - Crossref response object
         * @returns {string} Publication year or empty string
         */
        extractCrossrefPublicationYear(obj) {
            return obj["published-online"] &&
                obj["published-online"]["date-parts"]
                ? obj["published-online"]["date-parts"][0][0]
                : "";
        },

        /**
         * Extracts author names from Crossref API response
         * @param {Object} obj - Crossref response object
         * @returns {string} Formatted authors string
         */
        extractCrossrefAuthors(obj) {
            if (obj.author) {
                return obj.author
                    .map((author) => author.given + " " + author.family)
                    .join(", ");
            }
            return "";
        },

        /**
         * Builds standardized citation text from components
         * @param {string} journalTitle - Journal title
         * @param {string} year - Publication year
         * @param {string} volume - Volume number
         * @param {string} issue - Issue number
         * @param {string} pageInfo - Page information
         * @returns {string} Formatted citation text
         */
        buildCitationText(journalTitle, year, volume, issue, pageInfo) {
            return `${journalTitle} ${year} ${volume} ( ${issue} ) ${pageInfo}`;
        },
    },
};
</script>
