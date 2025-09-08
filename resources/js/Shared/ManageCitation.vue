<template>
    <jet-dialog-modal
        :show="showDialog"
        :max-width="'6xl'"
        @close="showDialog = false"
    >
        <template #title>
            {{ project.name }} - Manage Citations
            <button
                v-if="!displayAddCitationForms"
                type="button"
                class="inline-flex float-right items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                @click="displayAddCitationForms = true"
            >
                <PlusIcon class="w-5 h-5 mr-1 text-white" />
                Add Citation
            </button>
            <button
                v-else
                type="button"
                class="inline-flex float-right items-center rounded-md border border-transparent bg-gray-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                @click="onBack"
            >
                <ArrowSmallRightIcon class="w-5 h-5 mr-1 text-white" />
                Back
            </button>
        </template>
        <template #content>
            <div>
                <div v-if="displayAddCitationForms">
                    <div
                        class="relative grid grid-cols-1 gap-x-4 max-w-7xl mx-auto lg:grid-cols-2"
                    >
                        <!-- Add Manual Section -->
                        <div
                            class="px-4 sm:px-6 lg:pb-5 lg:px-0 lg:row-start-1 lg:col-start-1"
                        >
                            <div>
                                <p
                                    class="text-sm leading-6 font-bold text-gray-900"
                                >
                                    <span v-if="!isEdit">Add</span
                                    ><span v-else>Update</span> Citation
                                </p>
                                <div
                                    class="mt-1 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2"
                                >
                                    <div class="sm:col-span-1">
                                        <label
                                            for="doi"
                                            class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
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
                                                    'shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md',
                                                ]"
                                            />
                                        </div>
                                        <jet-input-error
                                            :message="form.errors.doi"
                                            class="mt-2"
                                        />
                                    </div>
                                    <div class="sm:col-span-4">
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
                                                    'shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md',
                                                ]"
                                            />
                                        </div>
                                        <jet-input-error
                                            :message="form.errors.title"
                                            class="mt-2"
                                        />
                                    </div>

                                    <div class="sm:col-span-6">
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
                                                    'shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md',
                                                ]"
                                            />
                                        </div>
                                        <jet-input-error
                                            :message="form.errors.authors"
                                            class="mt-2"
                                        />
                                    </div>
                                    <div class="sm:col-span-6">
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
                                                    'shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md',
                                                ]"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        v-if="!isEdit"
                                        class="sm:col-span-6 float-left"
                                    >
                                        <jet-secondary-button
                                            class="float-right"
                                            @click="save('addManually')"
                                        >
                                            Add
                                        </jet-secondary-button>
                                        <jet-secondary-button
                                            class="float-right mr-2"
                                            :disabled="form.processing"
                                            @click="
                                                form.reset(),
                                                    citationsForm.reset(),
                                                    clearFormErrors()
                                            "
                                        >
                                            Clear
                                        </jet-secondary-button>
                                    </div>
                                    <div
                                        v-else
                                        class="sm:col-span-6 float-left"
                                    >
                                        <jet-secondary-button
                                            class="float-right"
                                            :disabled="!isFormValid || form.processing"
                                            @click="save('addManually')"
                                        >
                                            Update
                                        </jet-secondary-button>
                                        <jet-secondary-button
                                            class="float-right mr-2"
                                            :disabled="form.processing"
                                            @click="onCancelEdit()"
                                        >
                                            Cancel
                                        </jet-secondary-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Import Section -->
                        <div class="lg:px-1 lg:row-start-1 lg:col-start-2 border-l">
                            <div class="pl-2">
                                <p
                                    class="text-sm leading-6 font-bold text-gray-900"
                                >
                                    Import From
                                </p>
                                <div
                                    class="mt-1 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2"
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
                                                class="flex-1 focus:ring-teal-500 focus:border-teal-500 block w-full min-w-0 rounded sm:text-sm border-gray-300"
                                            />
                                        </div>
                                        <!-- <jet-input-error :message="importcitationsForm.errors.input" class="mt-2" /> -->
                                    </div>
                                </div>
                                <div class="sm:col-span-2 mt-4">
                                    <jet-secondary-button
                                        :disabled="!isQueryValid || loading"
                                        @click="fetchCitations"
                                    >
                                        Import
                                    </jet-secondary-button>
                                    <jet-secondary-button
                                        class="ml-2"
                                        :disabled="isEmpty(fetchedCitations) && !query"
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
                                    class="sm:col-span-9 mt-4 align-centre"
                                >
                                    <loading-button :loading="loading" />
                                </div>
                                <!-- List Fetched Citation -->
                                <div
                                    v-if="
                                        !(
                                            isEmpty(fetchedCitations) ||
                                            fetchCitations == null
                                        ) && !loading
                                    "
                                    class="mt-4"
                                >
                                    <!-- Refactored to reuse CitationCard component -->
                                    <CitationCard :citations="[fetchedCitations]" />
                                    <div class="mt-2">
                                        <jet-secondary-button
                                            class="float-right text-md font-bold text-teal-900 mt-4"
                                            @click="save('addFetched')"
                                        >
                                            Add
                                        </jet-secondary-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Show existing citations -->
                <div
                    v-if="citations.length > 0 && !displayAddCitationForms"
                    style="height: 60vh"
                    class="sm:rounded-md overflow-y-scroll"
                >
                    <draggable
                        v-model="citations"
                        item-key="citation.id"
                        group="author"
                        @start="drag = true"
                        @end="drag = false"
                    >
                        <template #item="{ element }">
                            <div class="relative mb-2">
                                <CitationCard :citations="[element]" />
                                <div class="absolute top-2 right-2 flex space-x-1">
                                    <button
                                        type="button"
                                        class="inline-flex items-center p-1 border border-transparent bg-white/70 rounded hover:bg-white"
                                        @click="edit(element)"
                                    >
                                        <PencilIcon class="w-3.5 h-3.5 mr-1 text-gray-600" />
                                    </button>
                                    <button
                                        type="button"
                                        class="inline-flex items-center p-1 border border-transparent bg-white/70 rounded hover:bg-white"
                                        @click="confirmDeletion(element)"
                                    >
                                        <TrashIcon class="w-3.5 h-3.5 mr-1 text-gray-600" />
                                    </button>
                                </div>
                            </div>
                        </template>
                    </draggable>
                </div>
            </div>
            <!-- Option to add citations when list is empty -->
            <div
                v-if="citations.length == 0 && !displayAddCitationForms"
                class="py-5"
            >
                <div class="text-center">
                    <FolderPlusIcon class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900">
                        No Citations Listed
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Get started by adding a new citation.
                    </p>
                    <div class="mt-6">
                        <button
                            type="button"
                            class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            @click="displayAddCitationForms = true"
                        >
                            <!-- Heroicon name: mini/plus -->
                            <PlusIcon class="w-5 h-5 mr-1 text-white" />
                            Add Citation
                        </button>
                    </div>
                </div>
            </div>
            <!-- Delete confirmation dialog -->
            <jet-dialog-modal
                :show="confirmDelete"
                @close="confirmDelete = false"
            >
                <template #title> Delete Citation </template>

                <template #content>
                    Are you sure you want to delete this citation?
                    <div class="mt-4"></div>
                </template>

                <template #footer>
                    <jet-secondary-button
                        @click="confirmDelete = false && citationsForm.reset()"
                    >
                        Cancel
                    </jet-secondary-button>

                    <jet-danger-button
                        class="ml-2"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteCitation()"
                    >
                        Delete Citation
                    </jet-danger-button>
                </template>
            </jet-dialog-modal>
        </template>
        <template #footer>
            <div class="flex">
                <jet-secondary-button class="float-left" @click="onClose">
                    Close
                </jet-secondary-button>
            </div>
        </template>
    </jet-dialog-modal>
</template>
<script>
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import {
    TrashIcon,
    PencilIcon,
    PlusIcon,
    ArrowSmallRightIcon,
    FolderPlusIcon,
} from "@heroicons/vue/24/solid";
import JetInputError from "@/Jetstream/InputError.vue";
import LoadingButton from "@/Shared/LoadingButton.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import { router } from "@inertiajs/vue3";
import Draggable from "vuedraggable";
import Global from "@/Mixins/Global.js";
import CitationCard from "@/Shared/CitationCard.vue";
export default {
    mixins: [Global],
    components: {
        JetDialogModal,
        JetSecondaryButton,
        JetDangerButton,
        PencilIcon,
        TrashIcon,
        PlusIcon,
        FolderPlusIcon,
        ArrowSmallRightIcon,
        JetInputError,
        LoadingButton,
        Draggable,
        CitationCard,
    },

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
            displayAddCitationForms: false,
            selectedCitation: null,
            query: "",
            loading: false,
            isEdit: false,
            error: "",
        };
    },

    mounted() {
        this.loadInitial();
    },

    computed: {
        /*Check if form has valid required fields*/
        isFormValid() {
            return (
                this.form.doi &&
                this.form.doi.trim() !== '' &&
                this.form.title &&
                this.form.title.trim() !== '' &&
                this.form.authors &&
                this.form.authors.trim() !== '' &&
                !this.form.hasErrors &&
                Object.keys(this.form.errors || {}).length === 0
            );
        },

        /*Check if query is valid for import*/
        isQueryValid() {
            if (!this.query || this.query.trim() === '') {
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
        }
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
         * Toggle dialog visibility
         */
        toggleDialog() {
            this.showDialog = !this.showDialog;
        },

        /**
         * Close dialog
         */
        onClose() {
            this.showDialog = false;
            this.form.reset();
            this.form.errors = {};
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
                .catch(() => this.handleFetchError("No citation data found for the provided DOI. Please enter details manually."))
                .finally(() => {
                    this.loading = false;
                });
        },

        /**
         * Fetches citation data from EuropePMC API
         * @returns {Promise} Promise that resolves on successful data fetch
         */
        fetchFromEuropePMC() {
            return axios.get(this.$page.props.europemcWSApi, {
                params: {
                    query: `DOI:${this.query}`,
                    format: "json",
                    pageSize: "1",
                    resulttype: "core",
                    synonym: "true",
                },
            }).then((res) => {
                if (res && res.data && res.data.resultList.result.length > 0) {
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
            
            return axios.get(safeUrl)
                .then((res) => {
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
            
            return axios.get(safeUrl)
                .then((res) => {
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
            this.citations = this.citations.filter((citation) => {
                return citation.title != this.selectedCitation.title;
            });
            for (var key in citation) {
                this.form[key] = citation.hasOwnProperty(key)
                    ? citation[key]
                    : null;
            }
            this.displayAddCitationForms = true;
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
                _citation[key] = this.form.hasOwnProperty(key)
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
                console.error('Execute query error:', error);
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
                citationObj[key] = citation.hasOwnProperty(key) ? citation[key] : null;
            }
            return citationObj;
        },

        /**
         * Remove duplicate citations based on DOI
         */
        deduplicateCitations() {
            const keys = ["doi"];
            this.citationsForm.citations = this.citationsForm.citations.filter(
                (value, index, self) =>
                    self.findIndex((v) =>
                        keys.every((k) => v[k] === value[k])
                    ) === index
            );
        },

        /**
         * Submit citations to backend API
         */
        submitCitationsToBackend() {
            this.citationsForm.post(route("citation.save", this.project.id), {
                preserveScroll: true,
                onSuccess: () => this.handleSaveSuccess(),
                onError: (err) => this.handleSaveError(err),
            });
        },

        /**
         * Handle successful citation save operation
         */
        handleSaveSuccess() {
            router.reload({ only: ["project"] });
            this.citationsForm.reset();
            this.loadInitial();
            this.form.reset();
            this.displayAddCitationForms = false;
            this.isEdit = false;
        },

        /**
         * Handle citation save error
         * @param {Object} err - Error object from API
         */
        handleSaveError(err) {
            console.error('Citation save error:', err);
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
            hasErrors = this.validateCitationText() || hasErrors;

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
            if (!this.form.title || this.form.title.trim() === '') {
                this.form.errors.title = "The title field is required.";
                return true;
            } else if (this.form.title.length < 5) {
                this.form.errors.title = "Title must be at least 5 characters long.";
                return true;
            } else if (this.form.title.length > 500) {
                this.form.errors.title = "Title must not exceed 500 characters.";
                return true;
            }
            return false;
        },

        /**
         * Validate authors field
         * @returns {boolean} True if validation failed
         */
        validateAuthors() {
            if (!this.form.authors || this.form.authors.trim() === '') {
                this.form.errors.authors = "The authors field is required.";
                return true;
            } else if (this.form.authors.length < 3) {
                this.form.errors.authors = "Authors must be at least 3 characters long.";
                return true;
            } else if (this.form.authors.length > 1000) {
                this.form.errors.authors = "Authors must not exceed 1000 characters.";
                return true;
            }
            return false;
        },

        /**
         * Validate DOI field
         * @returns {boolean} True if validation failed
         */
        validateDoi() {
            if (!this.form.doi || this.form.doi.trim() === '') {
                this.form.errors.doi = "The DOI field is required.";
                return true;
            } else {
                const doiRegex = /^10\.[\d]{4,}(?:\.[\d]+)*\/[^\s]+$/;
                if (!doiRegex.test(this.form.doi.trim())) {
                    this.form.errors.doi = "Please enter a valid DOI format (e.g., 10.1000/journal.2023.0001).";
                    return true;
                }
            }
            return false;
        },

        /**
         * Validate citation text field
         * @returns {boolean} True if validation failed
         */
        validateCitationText() {
            if (this.form.citation_text && this.form.citation_text.trim() !== '') {
                if (this.form.citation_text.length > 2000) {
                    this.form.errors.citation_text = "Citation text must not exceed 2000 characters.";
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
            if (!this.query || this.query.trim() === '') {
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
            this.citationsForm.delete(
                route("citation.delete", this.project.id),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        router.reload({ only: ["project"] });
                        this.loadInitial();
                        this.citationsForm.reset();
                        this.confirmDelete = false;
                    },
                    onError: (err) => console.error(err),
                }
            );
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
            this.displayAddCitationForms = false;
            this.isEdit = false;
            this.resetForm();
        },

        /**
         * Go back from add/import forms to main view
         */
        onBack() {
            if (this.selectedCitation) {
                this.citations.push(this.selectedCitation);
            }
            this.displayAddCitationForms = false;
            this.isEdit = false;
            this.clearImportData();
        },

        // =============================================================================
        // UTILITY & HELPER METHODS
        // =============================================================================

        /**
         * Clear import section data
         */
        clearImportData() {
            this.fetchedCitations = {};
            this.query = null;
            this.error = "";
        },

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
            this.formattedCitationRes.authors = obj.authorString ? obj.authorString : "";
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
                issue: ""
            };

            if (obj.journalInfo) {
                info.journalTitle = obj.journalInfo.journal.title || "";
                info.yearofPublication = obj.journalInfo.yearOfPublication || "";
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
            const journalTitle = obj.attributes.titles ? obj.attributes.titles[0].title : "";
            const yearofPublication = obj.attributes.publicationYear || null;
            const volume = obj.attributes.volume || "";
            const issue = obj.attributes.issue || "";
            const pageInfo = obj.attributes.page || "";

            this.formattedCitationRes.title = journalTitle;
            this.formattedCitationRes.authors = this.extractDataciteAuthors(obj);
            this.formattedCitationRes.citation_text = journalTitle + " " + yearofPublication;
            this.formattedCitationRes.doi = obj.attributes ? obj.attributes["doi"] : "";
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
            this.formattedCitationRes.authors = this.extractCrossrefAuthors(obj);
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
            return obj["published-online"] && obj["published-online"]["date-parts"]
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
