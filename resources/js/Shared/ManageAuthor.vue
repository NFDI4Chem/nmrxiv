<template>
    <jet-dialog-modal
        :show="showDialog"
        :max-width="'6xl'"
        @close="showDialog = false"
    >
        <template #title>
            {{ project.name }} - Manage Authors
            <button
                v-if="!displayAddAuthorForms"
                type="button"
                class="inline-flex float-right items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                @click="displayAddAuthorForms = true"
            >
                <PlusIcon class="w-5 h-5 mr-1 text-white" />
                Add Author
            </button>
            <button
                v-else
                type="button"
                :class="backButtonClasses"
                @click="onBack"
            >
                <ArrowSmallRightIcon class="w-5 h-5 mr-1 text-white" />
                Back
            </button>
        </template>
        <template #content>
            <div>
                <div v-if="displayAddAuthorForms">
                    <div
                        class="relative grid grid-cols-1 gap-x-5 max-w-7xl mx-auto lg:grid-cols-2"
                    >
                        <!--Add Manual Section-->
                        <div
                            class="pb-36 px-4 sm:px-6 lg:pb-5 lg:px-0 lg:row-start-1 lg:col-start-1"
                        >
                            <div>
                                <p
                                    class="text-sm leading-6 font-bold text-gray-900"
                                >
                                    <span v-if="!isEdit">Add</span
                                    ><span v-else>Edit</span> Author
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
                                                v-model="form.title"
                                                type="text"
                                                name="title"
                                                autocomplete="title"
                                                :class="inputClasses"
                                            />
                                        </div>
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label
                                            for="given-name"
                                            class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                                        >
                                            First Name
                                        </label>
                                        <div class="mt-1">
                                            <input
                                                id="given-name"
                                                v-model="form.given_name"
                                                type="text"
                                                name="given-name"
                                                :class="inputClasses"
                                            />
                                        </div>
                                        <jet-input-error
                                            :message="
                                                authorsForm.errors.given_name
                                            "
                                            class="mt-2"
                                        />
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
                                                v-model="form.family_name"
                                                type="text"
                                                name="family-name"
                                                autocomplete="family-name"
                                                :class="inputClasses"
                                            />
                                        </div>
                                        <jet-input-error
                                            :message="
                                                authorsForm.errors.family_name
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
                                                v-model="form.email_id"
                                                name="email"
                                                type="email"
                                                autocomplete="email"
                                                :class="inputClasses"
                                            />
                                            <jet-input-error
                                                :message="form.errors.email_id"
                                                class="mt-2"
                                            />
                                        </div>
                                    </div>
                                    <div class="sm:col-span-3">
                                        <label
                                            for="orcid"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            ORCID iD
                                        </label>
                                        <div class="mt-1">
                                            <input
                                                id="orcid"
                                                v-model="form.orcid_id"
                                                name="orcid"
                                                autocomplete="orcid"
                                                type="text"
                                                :class="inputClasses"
                                            />
                                            <jet-input-error
                                                :message="form.errors.orcid_id"
                                                class="mt-2"
                                            />
                                        </div>
                                    </div>
                                    <div class="sm:col-span-6">
                                        <select-rich
                                            v-model:selected="
                                                form.contributor_type
                                            "
                                            label="Role"
                                            :items="contributorType"
                                        />
                                        <jet-input-error
                                            :message="
                                                form.errors.contributor_type
                                            "
                                            class="mt-2"
                                        />
                                    </div>
                                    <div class="sm:col-span-6">
                                        <label
                                            for="about"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Affiliation
                                        </label>
                                        <div class="mt-1">
                                            <textarea
                                                id="affiliation"
                                                v-model="form.affiliation"
                                                name="affiliation"
                                                rows="3"
                                                :class="textareaClasses"
                                                placeholder="Name and address of affiliated University and Department. e.g. Institut für Anorganische und Analytische Chemie, Friedrich-Schiller-Universität, Schloßgasse 10, 07743 Jena"
                                            />
                                        </div>
                                        <jet-input-error
                                            :message="
                                                authorsForm.errors.affiliation
                                            "
                                            class="mt-2"
                                        />
                                    </div>
                                    <div
                                        v-if="!isEdit"
                                        class="sm:col-span-6 float-left"
                                    >
                                        <jet-secondary-button
                                            class="float-right"
                                            :disabled="!isFormValid"
                                            @click="save('addManually')"
                                        >
                                            Add
                                        </jet-secondary-button>
                                        <jet-secondary-button
                                            class="float-right mr-2"
                                            :disabled="
                                                !(
                                                    form &&
                                                    form.given_name &&
                                                    form.family_name
                                                )
                                            "
                                            @click="
                                                form.reset(),
                                                    authorsForm.reset()
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
                                            :disabled="!isFormValid"
                                            @click="save('addManually')"
                                        >
                                            Update
                                        </jet-secondary-button>
                                        <jet-secondary-button
                                            class="float-right mr-2"
                                            :disabled="
                                                !(
                                                    form &&
                                                    form.given_name &&
                                                    form.family_name
                                                )
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
                            class="pb-36 lg:px-1 lg:row-start-1 lg:col-start-2 border-l"
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
                                            DOI or ORCID iD
                                        </label>
                                        <div
                                            class="mt-1 flex rounded-md shadow-sm"
                                        >
                                            <input
                                                id="name"
                                                v-model="query"
                                                type="text"
                                                name="name"
                                                autocomplete="off"
                                                placeholder="DOI or ORCID iD e.g. 10.1186/s19991-022-00987-0 or 0000-0001-6033-8976"
                                                class="flex-1 focus:ring-teal-500 focus:border-teal-500 block w-full min-w-0 rounded sm:text-sm border-gray-300"
                                            />
                                        </div>
                                        <!-- <jet-input-error :message="importAuthorsForm.errors.input" class="mt-2" /> -->
                                    </div>
                                </div>
                                <div class="sm:col-span-2 mt-4">
                                    <jet-secondary-button
                                        :disabled="query == '' || !query || loading"
                                        @click="fetchAuthors"
                                    >
                                        <span v-if="loading" class="flex items-center">
                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Importing...
                                        </span>
                                        <span v-else>Import</span>
                                    </jet-secondary-button>
                                    <jet-secondary-button
                                        class="ml-2"
                                        :disabled="!hasFetchedAuthors"
                                        @click="
                                            (fetchedAuthors = []),
                                                (query = null)
                                        "
                                    >
                                        Reset
                                    </jet-secondary-button>
                                    <jet-secondary-button
                                        class="ml-2 float-right"
                                        :disabled="
                                            !$page.props.auth.user?.orcid_id
                                        "
                                        @click="addCurrentUser"
                                    >
                                        Add me
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
                                <div>
                                    <div
                                        style="max-height: 40vh"
                                        class="overflow-auto p-2 mt-4"
                                    >
                                        <div
                                            v-for="author in fetchedAuthors"
                                            :key="author.authorId"
                                            class="relative flex items-start mt-2"
                                        >
                                            <div
                                                :class="[
                                                    author.selected
                                                        ? 'bg-gray-200 text-white'
                                                        : '',
                                                    'cursor-pointer min-w-0 flex-1 text-sm font-medium border rounded-md p-4',
                                                ]"
                                                @click="
                                                    addAuthorToSelectedList(
                                                        author
                                                    )
                                                "
                                            >
                                                <label
                                                    for="items"
                                                    class="font-medium text-teal-900"
                                                    >{{ author.firstName }}
                                                    {{ author.lastName }}
                                                </label>
                                                <p
                                                    v-if="author.affiliation"
                                                    id="items-description"
                                                    class="text-xs font-medium text-gray-900"
                                                >
                                                    {{ author.affiliation }}
                                                </p>
                                                <div
                                                    v-if="author.orcidId"
                                                    class="text-xs leading-6 font-medium text-teal-900"
                                                >
                                                    <b class="text-gray-500"
                                                        >ORCID iD:</b
                                                    >
                                                    {{ author.orcidId }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        v-if="hasFetchedAuthors"
                                        class="sm:col-span-6 mt-4"
                                    >
                                        <jet-secondary-button
                                            :disabled="!hasSelectedAuthors"
                                            class="float-right ml-2"
                                            @click="save('addSelected')"
                                        >
                                            Add Selected ({{ selectedAuthorsCount }})
                                        </jet-secondary-button>
                                        <jet-secondary-button
                                            class="float-right"
                                            @click="save('addAll')"
                                        >
                                            Add All
                                        </jet-secondary-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    v-if="authors.length > 0 && !displayAddAuthorForms"
                    style="height: 60vh"
                    class="sm:rounded-md overflow-y-scroll"
                >
                    <p class="text-xs font-large text-red-800 mb-1">
                        *Click and drag authors to sort order.
                    </p>
                    <draggable
                        v-model="authors"
                        item-key="author.id"
                        group="author"
                        @start="drag = true"
                        @end="drag = false"
                        @change="onSort()"
                    >
                        <template #item="{ element }">
                            <div class="overflow-auto">
                                <ul
                                    role="list"
                                    class="divide-y divide-gray-900"
                                >
                                    <li>
                                        <div
                                            class="px-4 border cursor-move hover:bg-gray-200 rounded-md mb-1 py-4 sm:px-6"
                                        >
                                            <div
                                                class="flex items-center cursor justify-between"
                                            >
                                                <p
                                                    class="text-sm font-medium text-teal-900"
                                                >
                                                    {{ element.title }}
                                                    {{ element.given_name }}
                                                    {{ element.family_name }}
                                                </p>
                                                <button
                                                    class="ml-2 flex flex-shrink-0"
                                                    @click="
                                                        (showManageRoleDialog = true),
                                                            (updateRoleForm.author_id =
                                                                element.id)
                                                    "
                                                >
                                                    <p
                                                        v-if="
                                                            element.pivot &&
                                                            element.pivot
                                                                .contributor_type
                                                        "
                                                        class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800"
                                                    >
                                                        {{
                                                            element.pivot
                                                                .contributor_type
                                                                ? element.pivot
                                                                      .contributor_type
                                                                : "Researcher"
                                                        }}
                                                    </p>
                                                </button>
                                            </div>
                                            <div
                                                class="mt-1 sm:flex sm:justify-between"
                                            >
                                                <div class="sm:flex">
                                                    <p
                                                        class="flex items-center text-xs font-small text-gray-500 break-words"
                                                    >
                                                        {{
                                                            element.affiliation
                                                        }}
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
                                                    v-if="element.orcid_id"
                                                    class="text-xs font-medium text-teal-900"
                                                >
                                                    <b class="text-gray-500"
                                                        >ORCID iD:</b
                                                    >
                                                    {{ element.orcid_id }}
                                                </p>
                                            </div>
                                            <div
                                                class="sm:flex sm:justify-between"
                                            >
                                                <p
                                                    v-if="element.email_id"
                                                    class="text-xs font-medium text-gray-900"
                                                >
                                                    <b class="text-gray-500"
                                                        >Email-Id:</b
                                                    >
                                                    {{ element.email_id }}
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
            <div
                v-if="authors.length == 0 && !displayAddAuthorForms"
                class="py-5"
            >
                <div class="text-center">
                    <FolderPlusIcon class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900">
                        No Authors Listed
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Get started by adding a new author.
                    </p>
                    <div class="mt-6">
                        <button
                            type="button"
                            class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            @click="displayAddAuthorForms = true"
                        >
                            <!-- Heroicon name: mini/plus -->
                            <PlusIcon class="w-5 h-5 mr-1 text-white" />
                            Add Author
                        </button>
                    </div>
                </div>
            </div>
            <!-- Delete confirmation dialog -->
            <jet-dialog-modal
                :show="confirmDelete"
                @close="confirmDelete = false"
            >
                <template #title> Delete Author </template>

                <template #content>
                    Are you sure you want to delete this author?
                    <div class="mt-4"></div>
                </template>

                <template #footer>
                    <jet-secondary-button
                        @click="confirmDelete = false && authorsForm.reset()"
                    >
                        Cancel
                    </jet-secondary-button>

                    <jet-danger-button
                        class="ml-2"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteAuthor()"
                    >
                        Delete Author
                    </jet-danger-button>
                </template>
            </jet-dialog-modal>
        </template>
        <template #footer>
            <div class="flex">
                <jet-secondary-button class="float-left" @click="onClose()">
                    Close
                </jet-secondary-button>
            </div>
        </template>
    </jet-dialog-modal>
    <!-- Manage Role Dialog Modal-->
    <jet-dialog-modal
        :show="showManageRoleDialog"
        @close="showManageRoleDialog = false"
    >
        <template #title> Manage Role </template>

        <template #content>
            <div style="height: 30vh" class="overflow-auto p-1">
                <div
                    v-for="item in contributorType"
                    :key="item.title"
                    class="relative flex items-start mt-2"
                >
                    <div
                        class="cursor-pointer flex-1 border rounded-md p-2 bg-white-200 hover:bg-gray-200"
                        @click="updateRole(item)"
                    >
                        <div class="text-gray-900">
                            <b>{{ item.title }}</b> <br />
                            <p
                                class="text-xs align-top"
                                v-text="item.description.replace(/<br\s*\/?>/gi, ' ')"
                            ></p>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
            <jet-secondary-button @click="showManageRoleDialog = false">
                Cancel
            </jet-secondary-button>
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
    ArrowSmallRightIcon,
    PlusIcon,
    FolderPlusIcon,
} from "@heroicons/vue/24/solid";
import JetInputError from "@/Jetstream/InputError.vue";
import LoadingButton from "@/Shared/LoadingButton.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import { router } from "@inertiajs/vue3";
import SelectRich from "@/Shared/SelectRich.vue";
import Draggable from "vuedraggable";
import Global from "@/Mixins/Global.js";

export default {
    components: {
        JetDialogModal,
        JetSecondaryButton,
        JetDangerButton,
        JetButton,
        PencilIcon,
        ArrowSmallRightIcon,
        FolderPlusIcon,
        PlusIcon,
        TrashIcon,
        JetInputError,
        LoadingButton,
        SelectRich,
        Draggable,
    },

    mixins: [Global],

    props: ["project"],

    data() {
        return {
            displayAddAuthorForms: false,
            isEdit: false,
            form: this.$inertia.form({
                title: "",
                given_name: "",
                family_name: null,
                affiliation: null,
                orcid_id: null,
                email_id: null,
                contributor_type: null,
            }),
            authorsForm: this.$inertia.form({
                authors: [],
            }),
            updateRoleForm: this.$inertia.form({
                author_id: "",
                role: "",
            }),
            query: "",
            showDialog: false,
            authors: [],
            fetchedAuthors: [],
            selectedAuthor: null,
            loading: false,
            confirmDelete: false,
            error: "",
            showManageRoleDialog: false,
            drag: false,
            formattedAuthors: [],
            // todo : dynamically load this from the config
            contributorType: [
                {
                    title: "Researcher",
                    description:
                        "A person involved in analysing data or the results of an experiment or formal study.<br> May indicate an intern or assistant to one of the authors who helped with research<br> but who was not so “key” as to be listed as an author.",
                },
                {
                    title: "ContactPerson",
                    description:
                        "Person with knowledge of how to access, troubleshoot, or otherwise field issues<br> related to the resource.",
                },
                {
                    title: "DataCollector",
                    description:
                        "Person/institution responsible for finding or gathering/collecting data under the <br> guidelines of the author(s) or Principal Investigator(PI).",
                },
                {
                    title: "DataCurator",
                    description:
                        "Person tasked with reviewing, enhancing, cleaning, or standardizing metadata and<br> the associated data submitted for storage, use, and maintenance within a data centre<br> or repository.",
                },
                {
                    title: "DataManager",
                    description:
                        "Person (or organisation with a staff of data managers, such as a data centre) <br> responsible for maintaining the finished resource.",
                },
                {
                    title: "Distributor",
                    description:
                        "Institution tasked with responsibility to generate/disseminate copies of the resource<br> in either electronic or print form.",
                },
                {
                    title: "Editor",
                    description:
                        "A person who oversees the details related to the publication format of the resource.",
                },
                {
                    title: "HostingInstitution",
                    description:
                        "Typically, the organisation allowing the resource to be available on the internet<br> through the provision of its hardware/software/operating support.",
                },
                {
                    title: "Producer",
                    description:
                        "Typically, a person or organisation responsible for the artistry and form of a media<br> product.",
                },
                {
                    title: "ProjectLeader",
                    description:
                        "Person officially designated as head of project team or subproject team<br> instrumental in the work necessary to development of the resource.",
                },
                {
                    title: "ProjectManager",
                    description:
                        "Person on the membership list of a designated project/project team.",
                },
                {
                    title: "RegistrationAgency",
                    description:
                        "Institution/organisation officially appointed by a Registration Authority to handle<br> specific tasks within a defined area of responsibility.",
                },
                {
                    title: "RelatedPerson",
                    description:
                        "A person without a specifically defined role in the development of the resource,<br> but who is someone the author wishes to recognize.",
                },
                {
                    title: "ResearchGroup",
                    description:
                        "Typically refers to a group of individuals with a lab, department, or division that<br> has a specifically defined focus of activity.",
                },
                {
                    title: "RightsHolder",
                    description:
                        "Person or institution owning or managing property rights, including intellectual<br> property rights over the resource.",
                },
                {
                    title: "Sponsor",
                    description:
                        "Person or organisation that issued a contract or under the auspices of which<br> a work has been written,<br> printed, published, developed, etc.",
                },
                {
                    title: "Supervisor",
                    description:
                        "Designated administrator over one or more groups/teams working to produce<br> a resource, or over one or more steps of a development process.",
                },
                {
                    title: "WorkPackageLeader",
                    description:
                        "A Work Package is a recognized data product, not all of which is included in<br> publication. The package, instead, may include notes, discarded documents,<br> etc.The Work Package Leader is responsible for ensuring the comprehensive<br> contents, versioning, and availability of the Work Package during the development<br> of the resource.",
                },
                {
                    title: "Other",
                    description:
                        "Any person or institution making a significant contribution to the development<br> and/or maintenance of the resource, but whose contribution is not adequately<br> described by any of the other values for contributorType.",
                },
            ],
        };
    },

    computed: {
        /**
         * Returns CSS classes for input fields based on edit state
         * @returns {string} CSS class string for input styling
         */
        inputClasses() {
            const baseClasses = 'shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md';
            const editClasses = 'shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100';
            
            return this.isEdit ? editClasses : baseClasses;
        },
        
        /**
         * Returns CSS classes for textarea fields based on edit state
         * @returns {string} CSS class string for textarea styling
         */
        textareaClasses() {
            const baseClasses = 'shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md';
            const editClasses = 'shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-red-500 rounded-md bg-gray-100';
            
            return this.isEdit ? editClasses : baseClasses;
        },

        /**
         * Returns CSS classes for add button styling
         * @returns {string} CSS class string for add button
         */
        addButtonClasses() {
            return 'inline-flex float-right items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2';
        },

        /**
         * Returns CSS classes for back button styling
         * @returns {string} CSS class string for back button
         */
        backButtonClasses() {
            return 'inline-flex float-right items-center rounded-md border border-transparent bg-gray-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2';
        },

        /**
         * Counts the number of selected authors from fetched results
         * @returns {number} Number of selected authors
         */
        selectedAuthorsCount() {
            return this.fetchedAuthors.filter(a => a.selected).length;
        },

        /**
         * Checks if any authors are currently selected
         * @returns {boolean} True if at least one author is selected
         */
        hasSelectedAuthors() {
            return this.selectedAuthorsCount > 0;
        },

        /**
         * Checks if there are any fetched authors available
         * @returns {boolean} True if authors have been fetched from APIs
         */
        hasFetchedAuthors() {
            return this.fetchedAuthors.length > 0;
        },

        /**
         * Validates if the current form has all required fields filled
         * @returns {boolean} True if form is valid for submission
         */
        isFormValid() {
            return this.form.given_name && this.form.given_name.trim() && 
                   this.form.family_name && this.form.family_name.trim();
        },

        /**
         * Checks if the form has any validation errors
         * @returns {boolean} True if form has validation errors
         */
        hasFormErrors() {
            return Object.keys(this.form.errors || {}).length > 0;
        },
    },

    /**
     * Component lifecycle hook - called after component is mounted
     * Initializes component with default values
     */

    mounted() {
        this.loadInitial();
    },
    methods: {
        /**
         * Returns filtered list of fetched authors based on selection criteria
         * @param {boolean} all - If true, returns all authors; if false, returns only selected authors
         * @returns {Array} Filtered array of authors
         */
        selectedFetchedAuthorsList(all = false) {
            if (all) {
                return this.fetchedAuthors;
            } else {
                return this.fetchedAuthors.filter((a) => a.selected);
            }
        },

        /**
         * Toggles the selection state of an imported author
         * @param {Object} author - The author object to toggle selection for
         */
        addAuthorToSelectedList(author) {
            if (author.selected) {
                author.selected = false;
            } else {
                author.selected = true;
            }
        },

        /**
         * Initializes component with default values and loads existing authors
         * Sets up the initial state for the component
         */
        loadInitial() {
            if (this.project && this.project.authors) {
                this.authors = this.project.authors.sort(
                    (a, b) => a.pivot.sort_order - b.pivot.sort_order
                );
            }
            this.form.contributor_type = {};
            this.form.contributor_type = this.contributorType[0];
        },

        /**
         * Toggles the visibility state of the main dialog
         */
        toggleDialog() {
            this.showDialog = !this.showDialog;
        },

        /**
         * Resets all component variables and closes the dialog
         * Clears form data, errors, and temporary state
         */
        onClose() {
            this.showDialog = false;
            this.form.reset();
            this.form.errors = {};
            this.form.hasErrors = false;
            this.fetchedAuthors = [];
            this.query = "";
            this.form.contributor_type = {};
            this.form.contributor_type = this.contributorType[0];
            this.isEdit = false;
            this.error = "";
            this.formattedAuthors = [];
        },
        /**
         * Prepares an existing author for editing
         * Removes author from list temporarily and populates form with their data
         * @param {Object} author - The author object to edit
         */
        edit(author) {
            this.selectedAuthor = author;
            this.authors = this.authors.filter((author) => {
                return (
                    author.given_name + author.family_name !=
                    this.selectedAuthor.given_name +
                        this.selectedAuthor.family_name
                );
            });
            this.form.title = author.title;
            this.form.given_name = author.given_name;
            this.form.family_name = author.family_name;
            this.form.email_id = author.email_id;
            this.form.affiliation = author.affiliation;
            this.form.orcid_id = author.orcid_id;
            this.form.contributor_type = {};
            this.form.contributor_type.title = author.pivot
                ? author.pivot.contributor_type
                : null;

            this.displayAddAuthorForms = true;
            this.isEdit = true;
        },

        /**
         * Fetches author data from external APIs using DOI or ORCID ID
         * Implements promise chain to try multiple APIs in sequence
         */
        fetchAuthors() {
            this.loading = true;
            this.error = "";
            this.formattedAuthors = []; // Reset previous results
            
            this.query = this.extractQueryParam(this.query);
            if (!this.query.trim()) {
                this.handleFetchError("Please enter a valid DOI or ORCID ID.");
                return;
            }
            
            let isDOI = new RegExp(/\b(10[.][0-9]{4,}(?:[.][0-9]+)*)\b/g).test(this.query);
            
            this.fetchFromEuropePMC(isDOI)
                .catch(() => this.fetchFromCrossref())
                .catch(() => this.fetchFromDatacite())
                .catch(() => this.handleFetchError("No author data found for the provided identifier. Please enter details manually."))
                .finally(() => {
                    this.loading = false;
                });
        },

        /**
         * Fetches author data from EuropePMC API
         * @param {boolean} isDOI - Whether the query is a DOI or ORCID ID
         * @returns {Promise} Promise that resolves on successful data fetch
         */
        fetchFromEuropePMC(isDOI) {
            return axios.get(this.$page.props.europemcWSApi, {
                params: {
                    query: isDOI ? "DOI:" + this.query : this.query,
                    format: "json",
                    pageSize: "1",
                    resulttype: "core",
                    synonym: "true",
                },
            }).then((res) => {
                if (res && res.data && res.data.resultList.result.length > 0) {
                    const authors = isDOI
                        ? res.data.resultList.result[0].authorList.author
                        : res.data.resultList.result[0].authorList.author.filter(
                              (a) => a.authorId && a.authorId.type == "ORCID" && 
                                     a.authorId.value == this.query
                          );
                    
                    if (authors && authors.length > 0) {
                        this.fetchedAuthors = this.formatAuthorResponse(authors, "europemc");
                        return Promise.resolve();
                    }
                }
                return Promise.reject();
            });
        },

        /**
         * Fetches author data from Crossref API
         * @returns {Promise} Promise that resolves on successful data fetch or rejects on failure
         */
        fetchFromCrossref() {
            // Properly encode query parameter to prevent URL injection
            const encodedQuery = encodeURIComponent(this.query.trim());
            const safeUrl = `${this.$page.props.CROSSREF_API}${encodedQuery}`;
            
            return axios.get(safeUrl)
                .then((res) => {
                    if (res.data && res.data.message && res.data.message.author) {
                        this.fetchedAuthors = this.formatAuthorResponse(res.data.message.author, "crossref");
                        return Promise.resolve();
                    }
                    return Promise.reject();
                });
        },

        /**
         * Fetches author data from DataCite API
         * @returns {Promise} Promise that resolves on successful data fetch or rejects on failure
         */
        fetchFromDatacite() {
            // Properly encode query parameter to prevent URL injection
            const encodedQuery = encodeURIComponent(this.query.trim());
            const safeUrl = `${this.$page.props.DATACITE_API}${encodedQuery}`;
            
            return axios.get(safeUrl)
                .then((res) => {
                    if (res && res.data && res.data.data && res.data.data.attributes.creators) {
                        this.fetchedAuthors = this.formatAuthorResponse(res.data.data.attributes.creators, "datacite");
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
            this.fetchedAuthors = [];
            this.loading = false;
        },

        /**
         * Formats raw author data from different APIs into a consistent structure
         * @param {Array} authors - Raw author data from API
         * @param {string} apiType - Type of API ('europemc', 'crossref', 'datacite')
         * @returns {Array} Formatted author data
         */
        formatAuthorResponse(authors, apiType) {
            if (authors && authors.length > 0) {
                switch (apiType) {
                    case "europemc":
                        this.formatEuropemcAuthors(authors);
                        break;
                    case "crossref":
                        this.formatCrossrefAuthors(authors);
                        break;
                    case "datacite":
                        this.formatDataciteAuthors(authors);
                        break;
                }
            }
            this.deduplicateFormattedAuthors();
            this.loading = false;
            return this.formattedAuthors;
        },

        /**
         * Formats authors from EuropePMC API response
         * @param {Array} authors - Raw author data from EuropePMC
         */
        formatEuropemcAuthors(authors) {
            authors.forEach((author) => {
                const a = {
                    firstName: author.firstName,
                    lastName: author.lastName,
                    fullName: author.fullName,
                    orcidId: author.authorId && author.authorId.type == "ORCID"
                        ? author.authorId.value : "",
                    affiliation: author.authorAffiliationDetailsList &&
                        author.authorAffiliationDetailsList.authorAffiliation[0]
                        ? author.authorAffiliationDetailsList.authorAffiliation[0].affiliation : ""
                };
                this.formattedAuthors.push(a);
            });
        },

        /**
         * Formats authors from Crossref API response
         * @param {Array} authors - Raw author data from Crossref
         */
        formatCrossrefAuthors(authors) {
            authors.forEach((author) => {
                const a = {
                    firstName: author.given,
                    lastName: author.family,
                    orcidId: "", // Crossref doesn't provide ORCID ID in response
                    affiliation: author.affiliation[0] ? author.affiliation[0].name : ""
                };
                this.formattedAuthors.push(a);
            });
        },

        /**
         * Formats authors from DataCite API response
         * @param {Array} authors - Raw author data from DataCite
         */
        formatDataciteAuthors(authors) {
            authors.forEach((author) => {
                const a = {
                    firstName: author.givenName,
                    lastName: author.familyName,
                    orcidId: "",
                    affiliation: author.affiliation ? author.affiliation[0] : ""
                };

                if (author.nameIdentifiers && author.nameIdentifiers[0] &&
                    author.nameIdentifiers[0].nameIdentifierScheme == "ORCID") {
                    a.orcidId = this.extractQueryParam(author.nameIdentifiers[0].nameIdentifier);
                }

                this.formattedAuthors.push(a);
            });
        },

        /**
         * Removes duplicate authors from formatted authors array based on key fields
         */
        deduplicateFormattedAuthors() {
            const keys = ["firstName", "lastName", "orcidId"];
            this.formattedAuthors = this.formattedAuthors.filter(
                (value, index, self) =>
                    self.findIndex((v) =>
                        keys.every((k) => v[k] === value[k])
                    ) === index
            );
        },

        /**
         * Converts formatted author objects to the structure expected by the backend
         * @param {Array} authors - Array of formatted author objects
         * @returns {Array} Array of author objects in backend format
         */
        formatAuthors(authors) {
            let authorsList = [];
            authors.forEach((author) => {
                authorsList.push({
                    given_name: author.firstName,
                    family_name: author.lastName,
                    orcid_id: author.orcidId,
                    affiliation: author.affiliation,
                });
            });
            return authorsList;
        },
        /**
         * Routes save operations based on input type
         * @param {string} input - Type of save operation ('addSelected', 'addAll', 'addManually')
         */
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

        /**
         * Adds imported authors from API results to the authors list
         * @param {boolean} addAll - If true, adds all authors; if false, adds only selected
         */
        addImportedAuthor(addAll) {
            this.authorsForm.reset();
            if (this.authors.length > 0) {
                this.authors = this.authors.concat(
                    this.formatAuthors(this.selectedFetchedAuthorsList(addAll))
                );
            } else {
                this.authors = this.formatAuthors(
                    this.selectedFetchedAuthorsList(addAll)
                );
            }
            this.executeQuery();
        },

        /**
         * Adds a manually entered author to the authors list
         * Validates form data before adding
         */
        addManually() {
            this.validateForm();
            if (this.form.hasErrors) return;
            
            this.authorsForm.reset();
            const newAuthor = this.buildNewAuthorObject();
            this.addAuthorToList(newAuthor);
            this.executeQuery();
        },

        /**
         * Creates a new author object from current form data
         * @returns {Object} New author object with form data
         */
        buildNewAuthorObject() {
            return {
                title: this.form.title ? this.form.title.trim() : null,
                given_name: this.form.given_name ? this.form.given_name.trim() : null,
                family_name: this.form.family_name ? this.form.family_name.trim() : null,
                email_id: this.form.email_id ? this.form.email_id.trim() : null,
                orcid_id: this.form.orcid_id ? this.form.orcid_id.trim() : null,
                affiliation: this.form.affiliation ? this.form.affiliation.trim() : null,
                contributor_type: this.form.contributor_type
                    ? this.form.contributor_type.title.trim() : "Researcher",
            };
        },

        /**
         * Adds a new author to the authors list at the appropriate position
         * @param {Object} newAuthor - The author object to add
         */
        addAuthorToList(newAuthor) {
            if (this.authors.length > 0) {
                if (this.selectedAuthor) {
                    this.authors.splice(this.selectedAuthor.pivot.sort_order, 0, newAuthor);
                } else {
                    this.authors.push(newAuthor);
                }
            } else {
                this.authors = [newAuthor];
            }
        },

        /**
         * Executes the API call to save authors to the backend
         * Handles deduplication and API communication
         */
        executeQuery() {
            this.authorsForm.authors = this.deduplicateAuthorsList(this.authors);
            
            this.authorsForm.post(route("author.save", this.project.id), {
                preserveScroll: true,
                onSuccess: () => this.onSaveSuccess(),
                onError: (err) => console.error(err),
            });
        },

        /**
         * Handles successful save operation by resetting component state
         * Reloads initial data and cleans up temporary variables
         */
        onSaveSuccess() {
            this.loadInitial();
            this.form.reset();
            this.form.contributor_type = this.contributorType[0];
            this.displayAddAuthorForms = false;
            this.formattedAuthors = [];
            this.fetchedAuthors = [];
            this.isEdit = false;
            this.query = "";
        },

        /**
         * Removes duplicate authors from the authors list based on name matching
         * @param {Array} authorsList - List of authors to deduplicate
         * @returns {Array} Deduplicated authors list
         */
        deduplicateAuthorsList(authorsList) {
            const keys = ["given_name", "family_name"];
            return authorsList.filter((value, index, self) =>
                self.findIndex((v) => keys.every((k) => v[k] === value[k])) === index
            );
        },

        /**
         * Prepares deletion confirmation dialog and sets up author for deletion
         * @param {Object} author - The author object to delete
         */
        confirmDeletion(author) {
            this.confirmDelete = true;
            this.authorsForm.reset();
            this.authorsForm.authors = [
                {
                    id: author.id,
                    given_name: author.given_name,
                    family_name: author.family_name,
                    orcid_id: author.orcid_id,
                    affiliation: author.affiliation,
                },
            ];
        },

        /**
         * Executes author deletion via API call
         * Handles success/error states and UI updates
         */
        deleteAuthor() {
            this.authorsForm.delete(route("author.delete", this.project.id), {
                preserveScroll: true,
                onSuccess: () => {
                    router.reload({ only: ["project"] });
                    this.loadInitial();
                    this.authorsForm.reset();
                    this.confirmDelete = false;
                },
                onError: (err) => console.error(err),
            });
        },

        /**
         * Validates all form fields and sets error states
         * Checks required fields, email format, ORCID format, and contributor type
         */
        validateForm() {
            this.form.errors = {};
            this.form.hasErrors = false;
            
            // Validate required fields
            if (!this.form.given_name || !this.form.given_name.trim()) {
                this.form.errors.given_name = "First name is required.";
                this.form.hasErrors = true;
            }
            
            if (!this.form.family_name || !this.form.family_name.trim()) {
                this.form.errors.family_name = "Family name is required.";
                this.form.hasErrors = true;
            }
            
            // Validate email format if provided
            if (this.form.email_id && this.form.email_id.trim()) {
                if (!this.isValidEmail(this.form.email_id.trim())) {
                    this.form.errors.email_id = "Please enter a valid email address.";
                    this.form.hasErrors = true;
                }
            }
            
            // Validate ORCID format if provided
            if (this.form.orcid_id && this.form.orcid_id.trim()) {
                if (!this.isValidOrcid(this.form.orcid_id.trim())) {
                    this.form.errors.orcid_id = "Please enter a valid ORCID ID.";
                    this.form.hasErrors = true;
                }
            }
            
            // Validate contributor type is selected
            if (!this.form.contributor_type || !this.form.contributor_type.title) {
                this.form.errors.contributor_type = "Please select a contributor type.";
                this.form.hasErrors = true;
            }
        },

        /**
         * Validates ORCID ID format against standard pattern
         * @param {string} orcidId - ORCID ID string to validate
         * @returns {boolean} True if ORCID ID format is valid
         */
        isValidOrcid(orcidId) {
            // ORCID format: 0000-0000-0000-000X (where X can be 0-9 or X)
            const orcidRegex = /^\d{4}-\d{4}-\d{4}-\d{3}[\dX]$/;
            return orcidRegex.test(orcidId);
        },

        /**
         * Updates an author's role/contributor type via API
         * @param {Object} role - The new role object containing title and description
         */
        updateRole(role) {
            this.updateRoleForm.role = role.title;
            this.updateRoleForm.post(
                route("author.updateRole", this.project.id),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        router.reload({ only: ["project"] });
                        this.loadInitial();
                        this.updateRoleForm.reset();
                        this.showManageRoleDialog = false;
                        this.authorId = "";
                    },
                    onError: (err) => console.error(err),
                }
            );
        },

        /**
         * Handles author list reordering by executing save query
         * Called when drag-and-drop sorting is completed
         */
        onSort() {
            this.executeQuery();
        },

        /**
         * Cancels edit operation and restores component to initial state
         * Restores previously selected author back to the list if applicable
         */
        onCancelEdit() {
            if (this.selectedAuthor) {
                this.authors.splice(
                    this.selectedAuthor.pivot.sort_order,
                    0,
                    this.selectedAuthor
                );
            }
            this.displayAddAuthorForms = false;
            this.isEdit = false;
            this.form.reset();
            this.form.contributor_type = this.contributorType[0];
        },

        /**
         * Handles back navigation from add/edit form to main view
         * Restores previously selected author if in edit mode
         */
        onBack() {
            if (this.selectedAuthor) {
                this.authors.splice(
                    this.selectedAuthor.pivot.sort_order,
                    0,
                    this.selectedAuthor
                );
            }
            this.displayAddAuthorForms = false;
            this.isEdit = false;
        },

        /**
         * Adds the currently logged-in user as an author
         * Uses user's profile data including ORCID ID and affiliation
         */
        addCurrentUser() {
            if (this.$page.props.auth.user && this.$page.props.auth.user) {
                let user = {};
                let affiliation = {};
                this.fetchedAuthors = [];
                user.firstName = this.$page.props.auth.user.first_name;
                user.lastName = this.$page.props.auth.user.last_name;
                user.authorId = {};
                user.authorId.type = "ORCID";
                user.authorId.value = this.$page.props.auth.user.orcid_id;
                user.authorAffiliationDetailsList = {};
                user.authorAffiliationDetailsList.authorAffiliation = [];
                affiliation.affiliation = this.$page.props.auth.user.affiliation;
                user.authorAffiliationDetailsList.authorAffiliation.push(
                    affiliation
                );
                this.fetchedAuthors.push(user);
            }
        },
    },
};
</script>
