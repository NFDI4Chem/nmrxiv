<template>
    <jet-dialog-modal
        :show="showDialog"
        :max-width="'6xl'"
        @close="showDialog = false"
    >
        <template #title>
            {{ project.name }} - Manage Citations
            <button
                type="button"
                v-if="!displayAddCitationForms"
                @click="displayAddCitationForms = true"
                class="inline-flex float-right items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                <PlusIcon class="w-5 h-5 mr-1 text-white" />
                Add Citation
            </button>
            <button
                type="button"
                v-else
                @click="onBack"
                class="inline-flex float-right items-center rounded-md border border-transparent bg-gray-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
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
                        <!--Add Manual Section-->
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
                                            for="title"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            DOI
                                        </label>
                                        <div class="mt-1">
                                            <input
                                                id="doi"
                                                v-model="form.doi"
                                                type="text"
                                                name="doi"
                                                :class="[
                                                    isEdit
                                                        ? 'shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100'
                                                        : '',
                                                    'shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md',
                                                ]"
                                            />
                                        </div>
                                    </div>
                                    <div class="sm:col-span-4">
                                        <label
                                            for="family-name"
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
                                            for="given-name"
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
                                            for="abstract"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Abstract
                                        </label>
                                        <div class="mt-1">
                                            <textarea
                                                id="abstract"
                                                v-model="form.abstract"
                                                name="abstract"
                                                type="abstract"
                                                autocomplete="abstract"
                                                :class="[
                                                    isEdit
                                                        ? 'shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100'
                                                        : '',
                                                    'shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md',
                                                ]"
                                            />
                                            <jet-input-error
                                                :message="form.errors.abstract"
                                                class="mt-2"
                                            />
                                        </div>
                                    </div>
                                    <div class="sm:col-span-6">
                                        <label
                                            for="orcid"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Citation Text
                                        </label>
                                        <div class="mt-1">
                                            <textarea
                                                id="citation-text"
                                                v-model="form.citation_text"
                                                name="citation-text"
                                                autocomplete="citation-text"
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
                                            :disabled="
                                                !(this.form && this.form.title)
                                            "
                                            @click="save('addManually')"
                                        >
                                            Add
                                        </jet-secondary-button>
                                        <jet-secondary-button
                                            class="float-right mr-2"
                                            :disabled="
                                                !(this.form && this.form.title)
                                            "
                                            @click="
                                                this.form.reset(),
                                                    this.citationsForm.reset()
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
                                            :disabled="
                                                !(this.form && this.form.title)
                                            "
                                            @click="save('addManually')"
                                        >
                                            Update
                                        </jet-secondary-button>
                                        <jet-secondary-button
                                            class="float-right mr-2"
                                            :disabled="
                                                !(this.form && this.form.title)
                                            "
                                            @click="onCancelEdit()"
                                        >
                                            Cancel
                                        </jet-secondary-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Import Section -->
                        <div
                            class="lg:px-1 lg:row-start-1 lg:col-start-2 border-l"
                        >
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
                                            for="name"
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
                                                autocomplete="off"
                                                class="flex-1 focus:ring-teal-500 focus:border-teal-500 block w-full min-w-0 rounded sm:text-sm border-gray-300"
                                            />
                                        </div>
                                        <!-- <jet-input-error :message="importcitationsForm.errors.input" class="mt-2" /> -->
                                    </div>
                                </div>
                                <div class="sm:col-span-2 mt-4">
                                    <jet-secondary-button
                                        :disabled="query == '' || !query"
                                        @click="fetchCitations"
                                    >
                                        Import
                                    </jet-secondary-button>
                                    <jet-secondary-button
                                        class="ml-2"
                                        :disabled="
                                            (isEmpty(this.fetchedCitations))
                                        "
                                        @click="
                                            (this.fetchedCitations = {}),
                                                (this.query = null)
                                        "
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
                                        !(isEmpty(this.fetchedCitations) || this.fetchCitations == null) &&
                                        !loading
                                    "
                                    class="mt-4"
                                >
                                    <fieldset
                                        class="border rounded border-gray-200 overflow-auto"
                                    >
                                        <legend class="sr-only">
                                            Notifications
                                        </legend>
                                        <div
                                            style="height: 40vh"
                                            class="divide-y divide-gray-200"
                                        >
                                            <div
                                                class="relative flex items-start p-4"
                                            >
                                                <div
                                                    class="min-w-0 flex-1 text-sm"
                                                >
                                                    <label
                                                        for="citation"
                                                        class="font-medium text-gray-700"
                                                    >
                                                        {{
                                                            fetchedCitations.title
                                                        }}
                                                    </label>
                                                    <p
                                                        id="citation-authors"
                                                        class="text-teal-500"
                                                    >
                                                        {{
                                                            fetchedCitations.authors
                                                        }}
                                                    </p>
                                                    <p
                                                        id="citation-text"
                                                        class="text-gray-500"
                                                    >
                                                        {{
                                                            fetchedCitations.citation_text
                                                        }}
                                                    </p>
                                                    <p
                                                        id="citation-doi"
                                                        class="text-gray-500 font-bold"
                                                    >
                                                        DOI -
                                                        {{
                                                            fetchedCitations.doi
                                                        }}
                                                    </p>
                                                    <p
                                                        v-if="
                                                            fetchedCitations.abstract
                                                        "
                                                        id="citation-doi"
                                                        class="font-bold text-gray-500 mt-2"
                                                    >
                                                        Abstract
                                                    </p>
                                                    <p
                                                        id="citation-abstract"
                                                        class="text-xs text-gray-500"
                                                    >
                                                        {{
                                                            fetchedCitations.abstract
                                                        }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <div>
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
                <!-- Show existing citation -->
                <div
                    v-if="citations.length > 0 && !displayAddCitationForms"
                    style="height: 60vh"
                    class="sm:rounded-md overflow-y-scroll"
                >
                    <draggable
                        v-model="this.citations"
                        @start="drag = true"
                        @end="drag = false"
                        item-key="citation.id"
                        group="author"
                    >
                        <template #item="{ element }">
                            <div class="overflow-auto">
                                <ul
                                    role="list"
                                    class="divide-y divide-gray-900"
                                >
                                    <li>
                                        <div
                                            class="px-4 border rounded-md mb-1 py-4 sm:px-6"
                                        >
                                            <div
                                                class="flex items-center cursor justify-between"
                                            >
                                                <p
                                                    class="text-sm font-medium text-teal-900"
                                                >
                                                    {{ element.title }}
                                                </p>
                                            </div>
                                            <div
                                                class="mt-1 sm:flex sm:justify-between"
                                            >
                                                <div class="sm:flex">
                                                    <p
                                                        class="flex items-center text-xs font-small text-gray-500 break-words"
                                                    >
                                                        {{ element.authors }}
                                                    </p>
                                                </div>
                                                <div
                                                    class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0"
                                                >
                                                    <button
                                                        type="button"
                                                        class="inline-flex items-center p-1 border border-transparent"
                                                        @click="edit(element)"
                                                    >
                                                        <PencilIcon
                                                            class="w-3.5 h-3.5 mr-1 text-gray-600"
                                                        />
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="inline-flex items-center p-1 border border-transparent"
                                                        @click="
                                                            confirmDeletion(
                                                                element
                                                            )
                                                        "
                                                    >
                                                        <TrashIcon
                                                            class="w-3.5 h-3.5 mr-1 text-gray-600"
                                                        />
                                                    </button>
                                                </div>
                                            </div>
                                            <div
                                                class="sm:flex sm:justify-between"
                                            >
                                                <p
                                                    v-if="element.doi"
                                                    class="text-xs font-medium text-teal-900"
                                                >
                                                    <b class="text-gray-500"
                                                        >DOI:
                                                    </b>
                                                    {{ element.doi }}
                                                </p>
                                            </div>
                                            <div
                                                class="sm:flex sm:justify-between"
                                            >
                                                <p
                                                    v-if="element.abstract"
                                                    class="text-xs font-medium text-gray-900"
                                                >
                                                    {{ element.abstract }}
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </template>
                    </draggable>
                </div>
            </div>
            <!-- Option to add citations when list is empty -->
            <div
                class="py-5"
                v-if="citations.length == 0 && !displayAddCitationForms"
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
                            @click="displayAddCitationForms = true"
                            class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
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
                        @click="
                            confirmDelete = false && this.citationsForm.reset()
                        "
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
import JetButton from "@/Jetstream/Button.vue";
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
import { Inertia } from "@inertiajs/inertia";
import Draggable from "vuedraggable";
export default {
    components: {
        JetDialogModal,
        JetSecondaryButton,
        JetDangerButton,
        JetButton,
        PencilIcon,
        TrashIcon,
        PlusIcon,
        FolderPlusIcon,
        ArrowSmallRightIcon,
        JetInputError,
        LoadingButton,
        Draggable,
        Inertia,
    },

    props: ["project"],
    data() {
        return {
            form: this.$inertia.form({
                title: "",
                doi: "",
                title: "",
                authors: "",
                abstract: "",
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

    methods: {
        loadInitial() {
            if (this.project && this.project.citations) {
                this.citations = this.project.citations;
            }
        },
        /*Control the display toggling*/
        toggleDialog() {
            this.showDialog = !this.showDialog;
        },
        onClose() {
            this.showDialog = false;
        },
        /*Fetch citation from DOI*/
        fetchCitations() {
            this.loading = true;
            this.error = "";
            this.query = this.query.trim();
            let isDOI = new RegExp(/\b(10[.][0-9]{4,}(?:[.][0-9]+)*)\b/g).test(
                this.query
            );
            axios
                .get(this.$page.props.europemcWSApi, {
                    params: {
                        query: "DOI:" + this.query,
                        format: "json",
                        pageSize: "1",
                        resulttype: "core",
                        synonym: "true",
                    },
                })
                .then((res) => {
                    this.fetchedCitations = this.formatCitationResponse(
                        res.data.resultList.result[0]
                    );
                })
                .catch(() => {
                    this.error =
                        "Something went wrong. Please check the input and try again.";
                })
                .finally(() => {
                    if(this.fetchedCitations == null || Object.keys(this.fetchedCitations).length == 0 ) {
                        this.error =
                            "Something went wrong. Please check the input and try again.";
                    }
                    this.loading = false;
                });
        },
        /*Edit Citation */
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
        /*Make the call to save API on the basis of selection*/
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
        /*Prepare request form and execute save query for citations added manually*/
        addManually() {
            this.validateForm();
            if (!this.form.hasErrors) {
                this.citationsForm.reset();
                let _citation = {};
                for (var key in this.form) {
                    _citation[key] = this.form.hasOwnProperty(key)
                        ? this.form[key]
                        : null;
                }
                this.citationsForm.citations.push(_citation);
                this.executeQuery();
            }
        },
        addFetchedCitation(){
            this.citationsForm.reset();
            if(this.citations.length > 0) {
                this.citations = this.citations.concat(this.fetchedCitations);
            }
            else {
                this.citations.push(this.fetchedCitations);
            }
            this.executeQuery();
        },
        /*Make request to save API*/
        executeQuery() {
            if (this.citations.length > 0) {
                let _citationObj = {};
                this.citations.forEach((citation) => {
                    for (var key in citation) {
                        _citationObj[key] = citation.hasOwnProperty(key)
                            ? citation[key]
                            : null;
                    }
                    this.citationsForm.citations.push(_citationObj);
                    _citationObj = {};
                });
            }
            const keys = ["title"];
            this.citationsForm.citations = this.citationsForm.citations.filter(
                (value, index, self) =>
                    self.findIndex((v) =>
                        keys.every((k) => v[k] === value[k])
                    ) === index
            );
            this.citationsForm.post(route("citation.save", this.project.id), {
                preserveScroll: true,
                onSuccess: () => {
                    Inertia.reload({ only: ["project"] });
                    this.citationsForm.reset();
                    this.loadInitial();
                    this.form.reset();
                    this.displayAddCitationForms = false;
                    this.isEdit = false;
                },
                onError: (err) => console.error(err),
            });
        },
        /*Check for validation error*/
        validateForm() {
            this.form.errors = {};
            this.form.hasErrors = false;
            var hasErrors = false;
            if (!this.form.title) {
                this.form.errors.title = "The title field is required.";
                hasErrors = true;
            }
            if (!this.form.authors) {
                this.form.errors.authors = "The authors field is required.";
                hasErrors = true;
            }
            if (!this.form.abstract) {
                this.form.errors.abstract = "The abstract field is required.";
                hasErrors = true;
            }
            if (!this.form.citation_text) {
                this.form.errors.citation_text =
                    "The citation text field is required.";
                hasErrors = true;
            }
            if (hasErrors) {
                this.form.hasErrors = true;
            }
        },
        formatCitationResponse(obj) {
            var journalTitle = "";
            var yearofPublication = "";
            var volume = "";
            var issue = "";
            var pageInfo = "";
            this.formattedCitationRes = {};
            if (obj) {
                if (obj.journalInfo) {
                    journalTitle = obj.journalInfo.journal.title
                        ? obj.journalInfo.journal.title
                        : "";
                    yearofPublication = obj.journalInfo.yearOfPublication
                        ? obj.journalInfo.yearOfPublication
                        : "";
                    volume = obj.journalInfo.volume
                        ? obj.journalInfo.volume
                        : "";
                    issue = obj.journalInfo.issue ? obj.journalInfo.issue : "";
                }
                pageInfo = obj.pageInfo ? obj.pageInfo : "";
                this.formattedCitationRes.title = obj.title ? obj.title : "";
                this.formattedCitationRes.authors = obj.authorString
                    ? obj.authorString
                    : "";
                this.formattedCitationRes.citation_text =
                    journalTitle +
                    " " +
                    yearofPublication +
                    " " +
                    volume +
                    " ( " +
                    issue +
                    " ) " +
                    pageInfo;
                this.formattedCitationRes.doi = obj.doi ? obj.doi : "";
                this.formattedCitationRes.abstract = obj.abstractText
                    ? obj.abstractText
                    : "";
            }
            return this.formattedCitationRes;
        },
        /*Confirm delete and prepare request*/
        confirmDeletion(citation) {
            this.confirmDelete = true;
            this.citationsForm.reset();
            this.citationsForm.citations = [
                {
                    id: citation.id ? citation.id : null,
                    title: citation.tile ? citation.title : null,
                    doi: citation.doi ? citation.doi : null,
                    title: citation.title ? citation.title : null,
                    authors: citation.authors ? citation.authors : null,
                    abstract: citation.abstract ? citation.abstract : null,
                    citation_text: citation.citation_text
                        ? citation.citation_text
                        : null,
                },
            ];
        },
        /*Make request to delete API*/
        deleteCitation() {
            this.citationsForm.delete(
                route("citation.delete", this.project.id),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        Inertia.reload({ only: ["project"] });
                        this.loadInitial();
                        this.citationsForm.reset();
                        this.confirmDelete = false;
                    },
                    onError: (err) => console.error(err),
                }
            );
        },
        onCancelEdit() {
            if (this.selectedCitation) {
                this.citations.push(this.selectedCitation);
            }
            this.displayAddCitationForms = false;
            this.isEdit = false;
            this.form.reset();
        },
        onBack(){
            this.displayAddCitationForms = false;
            this.isEdit = false;
            this.fetchedCitations = {};
            this.query = "";
        }
    },
};
</script>
