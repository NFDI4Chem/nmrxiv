<template>
    <jet-dialog-modal
        :show="showDialog"
        :max-width="'6xl'"
        @close="showDialog = false"
    >
        <template #title> Manage Citation </template>
        <template #content>
            <div
                class="relative grid grid-cols-1 gap-x-14 max-w-7xl mx-auto lg:grid-cols-2 divide-x"
            >
                <!--Add Manual Section-->
                <div
                    class="pb-36 px-4 sm:px-6 lg:pb-16 lg:px-0 lg:row-start-1 lg:col-start-2"
                >
                    <div class="p-2">
                        <p class="text-sm leading-6 font-bold text-gray-900">
                            Add Manually
                        </p>
                        <div
                            class="mt-1 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2"
                        >
                            <div class="sm:col-span-1">
                                <label
                                    for="doi"
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
                                        autocomplete="doi"
                                        class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    />
                                </div>
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
                                        autocomplete="title"
                                        class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
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
                                        name="authors"
                                        rows="3"
                                        class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                                        placeholder="Enter authors seperated by comma."
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
                                    class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                                >
                                    Abstract
                                </label>
                                <div class="mt-1">
                                    <textarea
                                        id="abstract"
                                        v-model="form.abstract"
                                        name="abstract"
                                        rows="3"
                                        class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                                        placeholder=""
                                    />
                                </div>
                                <jet-input-error
                                    :message="form.errors.abstract"
                                    class="mt-2"
                                />
                            </div>

                            <div class="sm:col-span-6">
                                <label
                                    for="citation-text"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Citation Text
                                </label>
                                <div class="mt-1">
                                    <textarea
                                        id="citation-text"
                                        v-model="form.citation_text"
                                        name="citation-text"
                                        rows="3"
                                        class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                                        placeholder=""
                                    />
                                </div>
                                <jet-input-error
                                    :message="form.errors.citation_text"
                                    class="mt-2"
                                />
                            </div>
                            <div class="sm:col-span-6 float-left">
                                <div>
                                    <jet-secondary-button
                                        class="float-right text-md font-bold text-teal-900"
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Existing citations summary -->
            <div
                v-if="citations.length > 0"
                class="shadow sm:rounded-md overflow-y-scroll h-64"
            >
                <template v-for="citation in citations" :key="citation.id">
                    <div class="overflow-auto bg-gray shadow sm:rounded-md m-1">
                        <ul role="list" class="divide-y divide-gray-900">
                            <li>
                                <div class="px-4 py-4 sm:px-6">
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <p
                                            class="text-sm font-medium text-teal-900"
                                        >
                                            {{ citation.title }}
                                        </p>
                                    </div>
                                    <div
                                        class="mt-1 sm:flex sm:justify-between"
                                    >
                                        <div class="sm:flex">
                                            <p
                                                class="flex items-center text-xs font-small text-gray-500 break-words"
                                            >
                                                {{ citation.authors }}
                                            </p>
                                        </div>
                                        <div
                                            class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0"
                                        >
                                            <button
                                                type="button"
                                                class="inline-flex items-center p-1 border border-transparent"
                                                @click="edit(citation)"
                                            >
                                                <PencilIcon
                                                    class="w-3.5 h-3.5 mr-1 text-gray-600"
                                                />
                                            </button>
                                            <button
                                                type="button"
                                                class="inline-flex items-center p-1 border border-transparent"
                                                @click="
                                                    confirmDeletion(citation)
                                                "
                                            >
                                                <TrashIcon
                                                    class="w-3.5 h-3.5 mr-1 text-gray-600"
                                                />
                                            </button>
                                        </div>
                                    </div>
                                    <div class="sm:flex sm:justify-between">
                                        <p
                                            v-if="citation.doi"
                                            class="text-xs font-medium text-teal-900"
                                        >
                                            <b class="text-gray-500">DOI:</b>
                                            {{ citation.doi }}
                                        </p>
                                    </div>
                                    <div
                                        v-if="citation.abstract"
                                        class="sm:flex sm:justify-between text-xs text-gray-500 truncate ..."
                                    >
                                        <p>{{ citation.abstract }} ...</p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </template>
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

                    <jet-danger-button class="ml-2" @click="deleteCitation()">
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
import AuthorCheckbox from "@/Shared/AuthorCheckbox.vue";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";
import JetInputError from "@/Jetstream/InputError.vue";
import LoadingButton from "@/Shared/LoadingButton.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import { Inertia } from "@inertiajs/inertia";
export default {
    components: {
        JetDialogModal,
        JetSecondaryButton,
        JetDangerButton,
        JetButton,
        AuthorCheckbox,
        PencilIcon,
        TrashIcon,
        JetInputError,
        LoadingButton,
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
            showDialog: false,
            citations: [],
            confirmDelete: false,
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
        /*Edit Citation */
        edit(citation) {
            let _selectedCitation = [];
            _selectedCitation = citation;
            this.citations = this.citations.filter((citation) => {
                return citation.title != _selectedCitation.title;
            });
            for (var key in citation) {
                this.form[key] = citation.hasOwnProperty(key)
                    ? citation[key]
                    : null;
            }
        },
        /*Make the call to save API on the basis of selection*/
        save(input) {
            switch (input) {
                case "addSelected":
                    this.addImportedAuthor(false);
                    break;
                case "addAll":
                    this.addImportedAuthor(true);
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
                console.log(_citation);
                this.citationsForm.citations.push(_citation);
                this.executeQuery();
            }
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
                    this.loadInitial();
                    this.form.reset();
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
                this.form.errors.title =
                    "The title field is required.";
                hasErrors = true;
            }
            if (!this.form.authors) {
                this.form.errors.authors =
                    "The authors field is required.";
                hasErrors = true;
            }
            if (!this.form.abstract) {
                this.form.errors.abstract =
                    "The abstract field is required.";
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
    },
};
</script>
