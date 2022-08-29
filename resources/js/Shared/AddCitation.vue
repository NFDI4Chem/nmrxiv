<template>
    <jet-dialog-modal
        :show="addCitationDialog"
        :max-width="'6xl'"
        @close="addCitationDialog = false"
    >
        <template #title> Update Citation </template>
        <template #content>
            <div
                class="relative grid grid-cols-1 gap-x-5 max-w-7xl mx-auto lg:grid-cols-2 divide-x"
            >
                <div
                    class="pb-36 px-4 sm:px-6 lg:pb-16 lg:px-0 lg:row-start-1 lg:col-start-1"
                >
                    <!-- Import Section-->
                    <div>
                        <p class="text-sm leading-6 font-bold text-gray-900">
                            Import From
                        </p>
                        <div
                            class="mt-3 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2"
                        >
                            <div class="sm:col-span-2">
                                <label
                                    for="name"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    DOI
                                </label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input
                                        id="name"
                                        v-model="importCitationForm.doi"
                                        type="text"
                                        name="name"
                                        autocomplete="off"
                                        class="flex-1 focus:ring-teal-500 focus:border-teal-500 block w-full min-w-0 rounded sm:text-sm border-gray-300"
                                    />
                                </div>
                                <jet-input-error
                                    :message="importCitationForm.errors.doi"
                                    class="mt-2"
                                />
                            </div>
                        </div>
                        <div class="sm:col-span-2 mt-4">
                            <jet-secondary-button @click="importCitation">
                                Import
                            </jet-secondary-button>
                        </div>
                        <div
                            v-if="loading && importCitationForm.doi"
                            class="sm:col-span-9 mt-4 align-centre"
                        >
                            <loading-button :loading="loading" />
                        </div>
                        <!-- List Imported Citation -->
                        <div
                            v-if="this.formattedCitationRes != null && !loading"
                            class="mt-4"
                        >
                            <fieldset
                                class="border rounded border-gray-200 overflow-auto"
                            >
                                <legend class="sr-only">Notifications</legend>
                                <div
                                    style="height: 40vh"
                                    class="divide-y divide-gray-200"
                                >
                                    <div class="relative flex items-start p-4">
                                        <div class="min-w-0 flex-1 text-sm">
                                            <label
                                                for="citation"
                                                class="font-medium text-gray-700"
                                            >
                                                {{ formattedCitationRes.title }}
                                            </label>
                                            <p
                                                id="citation-authors"
                                                class="text-teal-500"
                                            >
                                                {{
                                                    formattedCitationRes.authors
                                                }}
                                            </p>
                                            <p
                                                id="citation-text"
                                                class="text-gray-500"
                                            >
                                                {{
                                                    formattedCitationRes.citation_text
                                                }}
                                            </p>
                                            <p
                                                id="citation-doi"
                                                class="text-gray-500 font-bold"
                                            >
                                                DOI -
                                                {{ formattedCitationRes.doi }}
                                            </p>
                                            <p
                                                v-if="
                                                    formattedCitationRes.abstract
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
                                                    formattedCitationRes.abstract
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <jet-secondary-button
                                class="float-right text-md font-bold text-teal-900 mt-4"
                                @click="addCitationTemp('DOI')"
                            >
                                Add
                            </jet-secondary-button>
                        </div>
                    </div>
                </div>
                <div
                    class="pb-36 px-4 sm:px-6 lg:pb-16 lg:px-0 lg:row-start-1 lg:col-start-2"
                >
                    <div class="p-2">
                        <!--Add Manual Section-->
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
                                        v-model="manualAddCitationForm.doi"
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
                                        v-model="manualAddCitationForm.title"
                                        type="text"
                                        name="title"
                                        autocomplete="title"
                                        class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    />
                                </div>
                                <jet-input-error
                                    :message="
                                        manualAddCitationForm.errors.title
                                    "
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
                                        v-model="manualAddCitationForm.authors"
                                        name="authors"
                                        rows="3"
                                        class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                                        placeholder="Enter authors seperated by comma."
                                    />
                                </div>
                                <jet-input-error
                                    :message="
                                        manualAddCitationForm.errors.authors
                                    "
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
                                        v-model="manualAddCitationForm.abstract"
                                        name="abstract"
                                        rows="3"
                                        class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                                        placeholder=""
                                    />
                                </div>
                                <jet-input-error
                                    :message="
                                        manualAddCitationForm.errors.abstract
                                    "
                                    class="mt-2"
                                />
                            </div>

                            <div class="sm:col-span-6">
                                <label
                                    for="citation-text"
                                    class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                                >
                                    Citation Text
                                </label>
                                <div class="mt-1">
                                    <textarea
                                        id="citation-text"
                                        v-model="
                                            manualAddCitationForm.citation_text
                                        "
                                        name="citation-text"
                                        rows="3"
                                        class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                                        placeholder=""
                                    />
                                </div>
                                <jet-input-error
                                    :message="
                                        manualAddCitationForm.errors
                                            .citation_text
                                    "
                                    class="mt-2"
                                />
                            </div>
                            <div class="sm:col-span-6 float-left">
                                <jet-secondary-button
                                    class="float-right text-md font-bold text-teal-900"
                                    @click="addCitationTemp('Manual')"
                                >
                                    Add
                                </jet-secondary-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Added Citation Summary -->
            <div v-if="selectedCitationList.length > 0">
                <div class="ml-2 mt-2 overflow-y-scroll h-64">
                    <table
                        class="divide-y divide-gray-200 w-full table-fixed overflow-y-scroll"
                    >
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th
                                    colspan="3"
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Citation
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="citation in selectedCitationList"
                                :key="citation.id"
                            >
                                <td colspan="3" class="py-4">
                                    <div class="ml-4">
                                        <h3
                                            class="text-sm leading-6 font-medium text-gray-900"
                                        >
                                            {{ citation.title }}
                                        </h3>
                                        <p
                                            class="mt-1 text-xs font-small text-teal-900 break-words"
                                        >
                                            {{ citation.authors }}
                                        </p>
                                        <p
                                            class="mt-1 text-xs font-small text-blue-gray-900 break-words"
                                        >
                                            {{ citation.citation_text }}
                                        </p>
                                        <div
                                            v-if="citation.doi"
                                            class="text-xs font-bold leading-6 font-medium text-gray-900"
                                        >
                                            <p>DOI - {{ citation.doi }}</p>
                                        </div>
                                        <div
                                            v-if="citation.abstract"
                                            class="text-xs text-gray-500 truncate ..."
                                        >
                                            <p>{{ citation.abstract }} ...</p>
                                        </div>
                                    </div>
                                </td>
                                <td
                                    class="px-6 py-4 text-sm leading-5 text-gray-500 whitespace-no-wrap border-gray-200"
                                >
                                    <!-- <button
                                        type="button"
                                        class="inline-flex items-center p-1 border border-transparent"
                                        @click="editCitation(citation)"
                                    >
                                        <PencilIcon
                                            class="w-5 h-5 mr-1 text-gray-600"
                                        />
                                    </button> -->
                                    <button
                                        type="button"
                                        class="inline-flex items-center p-1 border border-transparent"
                                        @click="
                                            confirmCitationDeletion(citation)
                                        "
                                    >
                                        <TrashIcon
                                            class="w-5 h-5 mr-1 text-gray-600"
                                        />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <jet-dialog-modal
                :show="confirmingCitationDeletion"
                @close="closeModal"
            >
                <template #title> Delete Citation </template>

                <template #content>
                    Are you sure you want to delete this citation?
                    <div class="mt-4"></div>
                </template>

                <template #footer>
                    <jet-secondary-button @click="closeModal">
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
            <jet-secondary-button class="float-left" @click="onClose">
                Close
            </jet-secondary-button>

            <jet-button class="ml-2" @click="updateCitation"> Save </jet-button>
        </template>
    </jet-dialog-modal>
</template>

<script>
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import AuthorCheckbox from "@/Shared/AuthorCheckbox.vue";
import { TrashIcon, PencilIcon } from "@heroicons/vue/solid";
import JetInputError from "@/Jetstream/InputError.vue";
import LoadingButton from "@/Shared/LoadingButton.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
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
    },

    props: ["project"],

    data() {
        return {
            form: this.$inertia.form({}),
            importCitationForm: this.$inertia.form({
                doi: "",
                _method: "POST",
                errors: {},
            }),
            updateCitationForm: this.$inertia.form({
                _method: "POST",
                citationList: [],
            }),
            manualAddCitationForm: this.$inertia.form({
                _method: "POST",
                doi: "",
                title: "",
                authors: "",
                abstract: "",
                citation_text: "",
                errors: {},
            }),
            formattedCitationRes: null,
            importedCitationByDOI: [],
            addCitationDialog: false,
            selectedCitationList: [],
            confirmingCitationDeletion: false,
            selectedCitationforDelete: {},
            loading: false,
        };
    },
    /* mounted() {
        console.log('citation called..')
        const initialise = () => {
            this.addCitationDialog = true;
        };
        this.emitter.all.set("openAddCitationDialog", [initialise]);
    },*/
    methods: {
        toggleAddCitationDialog() {
            this.selectedCitationList = [];
            this.addCitationDialog = !this.addCitationDialog;
            if (this.project.citations) {
                for (var i = 0; i < this.project.citations.length; i++) {
                    this.selectedCitationList.push(this.project.citations[i]);
                }
            }
        },
        /*Add citations to temp list either added Manually or imported from DOI.*/
        addCitationTemp(source) {
            if (source == "Manual") {
                this.validateForm();
                if (!this.manualAddCitationForm.hasErrors) {
                    this.selectedCitationList.push(this.manualAddCitationForm);
                    this.manualAddCitationForm = this.$inertia.form({});
                }
            } else if (source == "DOI") {
                //Adding citation to tmp list imported from DOI.
                this.selectedCitationList.push(this.formattedCitationRes);
            }

            //filtering duplicate values from the temp list.
            if (this.selectedCitationList.length > 0) {
                const keys = ["doi"];
                this.selectedCitationList = this.selectedCitationList.filter(
                    (value, index, self) =>
                        self.findIndex((v) =>
                            keys.every((k) => v[k] === value[k])
                        ) === index
                );
            }
        },
        /*Import Citation from DOI*/
        importCitation() {
            this.loading = true;
            this.importedCitationByDOI = [];
            this.importCitationForm.errors = {};
            this.formattedCitationRes = null;
            if (!this.importCitationForm.doi) {
                this.importCitationForm.errors.doi =
                    "The DOI field is required.";
            } else {
                axios
                    .get(this.$page.props.getCitationbyDOIApi, {
                        params: {
                            query: this.importCitationForm.doi,
                            format: "json",
                            pageSize: "1",
                            resulttype: "core",
                            synonym: "true",
                        },
                    })
                    .then((res) => {
                        this.formattedCitationRes = this.formatCitationResponse(
                            res.data.resultList.result[0]
                        );
                    })
                    .catch((error) => {
                        this.importCitationForm.errors.doi =
                            "Something went wrong. Please check the DOI and try again.";
                    })
                    .finally(() => {
                        if (this.formattedCitationRes == null) {
                            this.importCitationForm.errors.doi =
                                "Something went wrong. Please check the DOI and try again.";
                        }
                        this.loading = false;
                    });
            }
        },
        formatCitationResponse(obj) {
            var journalTitle = "";
            var yearofPublication = "";
            var volume = "";
            var issue = "";
            var pageInfo = "";
            if (obj) {
                this.formattedCitationRes = {};
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
        /*Update the Citation table and relationship*/
        updateCitation() {
            this.updateCitationForm.reset();
            for (var i = 0; i < this.selectedCitationList.length; i++) {
                this.updateCitationForm.citationList.push(
                    this.selectedCitationList[i]
                );
            }
            this.updateCitationForm.post(
                route("update-citation", this.project.id),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.addCitationDialog = false;
                        this.resetData();
                    },
                    onError: (err) => console.error(err),
                }
            );
        },
        /*Validation form*/
        validateForm() {
            this.manualAddCitationForm.errors = {};
            this.manualAddCitationForm.hasErrors = false;
            var hasErrors = false;
            if (!this.manualAddCitationForm.title) {
                this.manualAddCitationForm.errors.title =
                    "The title field is required.";
                hasErrors = true;
            }
            if (!this.manualAddCitationForm.authors) {
                this.manualAddCitationForm.errors.authors =
                    "The authors field is required.";
                hasErrors = true;
            }
            if (!this.manualAddCitationForm.abstract) {
                this.manualAddCitationForm.errors.abstract =
                    "The abstract field is required.";
                hasErrors = true;
            }
            if (!this.manualAddCitationForm.citation_text) {
                this.manualAddCitationForm.errors.citation_text =
                    "The citation text field is required.";
                hasErrors = true;
            }
            if (hasErrors) {
                this.manualAddCitationForm.hasErrors = true;
            }
        },
        editCitation() {
            //
        },
        /*Confirming before deletion*/
        confirmCitationDeletion(citation) {
            this.selectedCitationforDelete = citation;
            this.confirmingCitationDeletion = true;
        },
        /*Delete citation from the temp list*/
        deleteCitation(citation) {
            var index = this.selectedCitationforDelete
                ? this.selectedCitationList.indexOf(
                      this.selectedCitationforDelete
                  )
                : null;
            if (index > -1) {
                this.selectedCitationList.splice(index, 1);
            }
            this.closeModal();
        },
        closeModal() {
            this.confirmingCitationDeletion = false;
        },
        /*Resetting local variables and forms*/
        resetData() {
            this.importCitationForm = this.$inertia.form({});
            this.manualAddCitationForm = this.$inertia.form({});
            this.formattedCitationRes = null;
            this.importedCitationByDOI = [];
            this.addCitationDialog = false;
            this.selectedCitationList = [];
            this.confirmingCitationDeletion = false;
            this.selectedCitationforDelete = {};
            this.loading = false;
        },
        onClose() {
            this.addCitationDialog = false;
            this.resetData();
        },
    },
};
</script>
