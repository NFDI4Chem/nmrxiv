<template>
    <jet-dialog-modal
        :show="addCitationDialog"
        :max-width="'6xl'"
        @close="addCitationDialog = false"
    >
        <template #title> New Citation </template>
        <template #content>
            <div
                class="relative grid grid-cols-1 gap-x-16 max-w-7xl mx-auto lg:grid-cols-2 divide-x"
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
                            class="mt-1 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2"
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
                            <jet-secondary-button @click="importAuthors">
                                Import
                            </jet-secondary-button>
                        </div>
                        <div
                            v-if="loading && importCitationForm.doi"
                            class="sm:col-span-9 mt-4 align-centre"
                        >
                            <loading-button :loading="loading" />
                        </div>
                        <div
                            v-if="authorsListDOI.length > 0 && !loading"
                            class="sm:col-span-9 mt-4"
                        >
                            <author-checkbox
                                v-model:checked="doiSelectedAuthorList"
                                :items="authorsListDOI"
                            />
                        </div>
                        <div
                            v-if="authorsListDOI.length > 0"
                            class="sm:col-span-6 pt-3"
                        >
                            <jet-secondary-button
                                class="float-right text-md font-bold text-teal-900"
                                @click="addAuthor('DOI')"
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
                            <div class="sm:col-span-2">
                                <label
                                    for="title"
                                    class="block text-sm font-medium text-gray-700"
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
                            </div>
                            <div class="sm:col-span-2">
                                <label
                                    for="family-name"
                                    class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                                >
                                    Family Name
                                </label>
                                <div class="mt-1">
                                    <input
                                        id="family-name"
                                        v-model="
                                            manualAddCitationForm.family_name
                                        "
                                        type="text"
                                        name="family-name"
                                        autocomplete="family-name"
                                        class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    />
                                </div>
                                <jet-input-error
                                    :message="
                                        manualAddCitationForm.errors.family_name
                                    "
                                    class="mt-2"
                                />
                            </div>

                            <div class="sm:col-span-2">
                                <label
                                    for="given-name"
                                    class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                                >
                                    Given Name
                                </label>
                                <div class="mt-1">
                                    <input
                                        id="given-name"
                                        v-model="
                                            manualAddCitationForm.given_name
                                        "
                                        type="text"
                                        name="given-name"
                                        autocomplete="given-name"
                                        class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    />
                                </div>
                                <jet-input-error
                                    :message="
                                        manualAddCitationForm.errors.given_name
                                    "
                                    class="mt-2"
                                />
                            </div>
                            <div class="sm:col-span-3">
                                <label
                                    for="email"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Email address
                                </label>
                                <div class="mt-1">
                                    <input
                                        id="email"
                                        v-model="manualAddCitationForm.email_id"
                                        name="email"
                                        type="email"
                                        autocomplete="email"
                                        class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        @blur="validateEmail"
                                    />
                                    <jet-input-error
                                        :message="
                                            manualAddCitationForm.errors
                                                .email_id
                                        "
                                        class="mt-2"
                                    />
                                </div>
                            </div>
                            <div class="sm:col-span-3">
                                <label
                                    for="orcid"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    ORCID ID
                                </label>
                                <div class="mt-1">
                                    <input
                                        id="orcid"
                                        v-model="manualAddCitationForm.orcid_id"
                                        name="orcid"
                                        autocomplete="orcid"
                                        type="text"
                                        class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    />
                                </div>
                            </div>
                            <div class="sm:col-span-6">
                                <label
                                    for="about"
                                    class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                                >
                                    Affiliation
                                </label>
                                <div class="mt-1">
                                    <textarea
                                        id="affiliation"
                                        v-model="
                                            manualAddCitationForm.affiliation
                                        "
                                        name="affiliation"
                                        rows="3"
                                        class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                                        placeholder="Name and address of affiliated University and Department. e.g. Institut für Anorganische und Analytische Chemie,Friedrich-Schiller-Universität,Schloßgasse 10, 07743 Jena"
                                    />
                                </div>
                                <jet-input-error
                                    :message="
                                        manualAddCitationForm.errors.affiliation
                                    "
                                    class="mt-2"
                                />
                            </div>
                            <div class="sm:col-span-6 float-left">
                                <jet-secondary-button
                                    class="float-right text-md font-bold text-teal-900"
                                    @click="addAuthor('Manual')"
                                >
                                    Add
                                </jet-secondary-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Added Author Summary -->
            <div v-if="selectedAuthorsList.length > 0">
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
                                    Author
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
                                v-for="author in selectedAuthorsList"
                                :key="author.id"
                            >
                                <td colspan="3" class="py-4">
                                    <div class="ml-4">
                                        <h3
                                            class="text-sm leading-6 font-medium text-gray-900"
                                        >
                                            {{ author.title }}
                                            {{ author.given_name }}
                                            {{ author.family_name }}
                                        </h3>
                                        <p
                                            class="mt-1 text-xs font-small text-blue-gray-900 break-words"
                                        >
                                            {{ author.affiliation }}
                                        </p>
                                        <div
                                            v-if="author.orcid_id"
                                            class="text-xs leading-6 font-medium text-gray-900"
                                        >
                                            <p>
                                                Orcid Id -
                                                <a
                                                    :href="author.orcid_id"
                                                    class="text-teal-900"
                                                    >{{ author.orcid_id }}</a
                                                >
                                            </p>
                                        </div>
                                        <div
                                            v-if="author.email_id"
                                            class="text-xs leading-6 font-medium text-gray-900"
                                        >
                                            <p>
                                                Email Id - {{ author.email_id }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td
                                    class="px-6 py-4 text-sm leading-5 text-gray-500 whitespace-no-wrap border-gray-200"
                                >
                                    <button
                                        type="button"
                                        class="inline-flex items-center p-1 border border-transparent"
                                        @click="editAuthor(author)"
                                    >
                                        <PencilIcon
                                            class="w-5 h-5 mr-1 text-gray-600"
                                        />
                                    </button>
                                    <button
                                        type="button"
                                        class="inline-flex items-center p-1 border border-transparent"
                                        @click="
                                            confirmAuthorDeletion(author.id)
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
                :show="confirmingAuthorDeletion"
                @close="closeModal"
            >
                <template #title> Delete Author </template>

                <template #content>
                    Are you sure you want to delete this author?
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
                        @click="deleteAuthor(authorId)"
                    >
                        Delete Author
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
            importCitationForm: this.$inertia.form({
                doi: "",
                errors: {},
            }),
            manualAddCitationForm: this.$inertia.form({
                _method: "POST",
                errors: {},
            }),
        };
    },
    mounted() {
        const initialise = () => {
            this.addCitationDialog = true;
        };
        this.emitter.all.set("openAddCitationDialog", [initialise]);
    },
    methods: {
        updateCitation() {},
        onClose() {
            this.addCitationDialog = false;
            //this.resetData();
        },
    },
};
</script>
