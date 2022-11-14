<template>
    <jet-dialog-modal
        :show="showDialog"
        :max-width="'6xl'"
        @close="showDialog = false"
    >
        <template #title> {{ project.name }} - Manage Authors </template>
        <template #content>
            <div
                class="relative grid grid-cols-1 gap-x-14 max-w-7xl mx-auto lg:grid-cols-2 divide-x"
            >
                <div
                    class="pb-36 px-4 sm:px-6 lg:pb-5 lg:px-0 lg:row-start-1 lg:col-start-1"
                >
                    <!-- Import Section -->
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
                                    DOI or ORCID ID
                                </label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input
                                        id="name"
                                        v-model="query"
                                        type="text"
                                        name="name"
                                        autocomplete="off"
                                        class="flex-1 focus:ring-teal-500 focus:border-teal-500 block w-full min-w-0 rounded sm:text-sm border-gray-300"
                                    />
                                </div>
                                <!-- <jet-input-error :message="importAuthorsForm.errors.input" class="mt-2" /> -->
                            </div>
                        </div>
                        <div class="sm:col-span-2 mt-4">
                            <jet-secondary-button
                                :disabled="query == '' || !query"
                                @click="fetchAuthors"
                            >
                                Import
                            </jet-secondary-button>
                            <jet-secondary-button
                                class="ml-2"
                                :disabled="!(fetchedAuthors.length > 0)"
                                @click="
                                    (this.fetchedAuthors = []),
                                        (this.query = null)
                                "
                            >
                                Reset
                            </jet-secondary-button>
                        </div>
                        <jet-input-error :message="error" class="mt-2" />
                        <div
                            v-if="loading"
                            class="sm:col-span-9 mt-4 align-centre"
                        >
                            <loading-button :loading="loading" />
                        </div>
                        <div>
                            <div
                                style="height: 40vh"
                                class="overflow-auto p-2 mt-4"
                            >
                                <div
                                    v-for="author in fetchedAuthors"
                                    :key="author.authorId"
                                    class="relative flex items-start mt-2"
                                >
                                    <div
                                        @click="addAuthorToSelectedList(author)"
                                        :class="[
                                            author.selected
                                                ? 'bg-gray-200 text-white'
                                                : '',
                                            'cursor-pointer min-w-0 flex-1 text-sm font-medium border rounded-md p-4',
                                        ]"
                                    >
                                        <label
                                            for="items"
                                            class="font-medium text-teal-900"
                                            >{{ author.firstName }}
                                            {{ author.lastName }}
                                        </label>
                                        <p
                                            v-if="
                                                author.authorAffiliationDetailsList
                                            "
                                            id="items-description"
                                            class="text-xs font-medium text-gray-900"
                                        >
                                            {{
                                                author
                                                    .authorAffiliationDetailsList
                                                    .authorAffiliation[0]
                                                    .affiliation
                                            }}
                                        </p>
                                        <div
                                            v-if="
                                                author.authorId &&
                                                author.authorId.type == 'ORCID'
                                            "
                                            class="text-xs leading-6 font-medium text-teal-900"
                                        >
                                            {{ author.authorId.value }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                v-if="fetchedAuthors.length > 0"
                                class="sm:col-span-6 mt-4"
                            >
                                <jet-secondary-button
                                    :disabled="
                                        !(
                                            this.selectedFetchedAuthorsList(
                                                false
                                            ).length > 0
                                        )
                                    "
                                    class="float-right ml-2"
                                    @click="save('addSelected')"
                                >
                                    Add Selected ({{
                                        selectedFetchedAuthorsList(false)
                                            .length
                                    }})
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
                <div
                    class="pb-36 px-4 sm:px-6 lg:pb-5 lg:px-0 lg:row-start-1 lg:col-start-2"
                >
                    <div class="p-2">
                        <!--Add Manual Section-->
                        <p class="text-sm leading-6 font-bold text-gray-900">
                            Add Author
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
                                        v-model="form.family_name"
                                        type="text"
                                        name="family-name"
                                        autocomplete="family-name"
                                        class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    />
                                </div>
                                <jet-input-error
                                    :message="authorsForm.errors.family_name"
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
                                        v-model="form.given_name"
                                        type="text"
                                        name="given-name"
                                        autocomplete="given-name"
                                        class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    />
                                </div>
                                <jet-input-error
                                    :message="authorsForm.errors.given_name"
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
                                        class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
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
                                    ORCID ID
                                </label>
                                <div class="mt-1">
                                    <input
                                        id="orcid"
                                        v-model="form.orcid_id"
                                        name="orcid"
                                        autocomplete="orcid"
                                        type="text"
                                        class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    />
                                </div>
                            </div>
                            <div class="sm:col-span-6">
                                <select-rich
                                    v-model:selected="form.contributor_type"
                                    label="Role"
                                    :items="this.contributorType"
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
                                        class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                                        placeholder="Name and address of affiliated University and Department. e.g. Institut für Anorganische und Analytische Chemie, Friedrich-Schiller-Universität, Schloßgasse 10, 07743 Jena"
                                    />
                                </div>
                                <jet-input-error
                                    :message="authorsForm.errors.affiliation"
                                    class="mt-2"
                                />
                            </div>
                            <div class="sm:col-span-6 float-left">
                                <jet-secondary-button
                                    class="float-right"
                                    :disabled="
                                        !(
                                            this.form &&
                                            this.form.family_name &&
                                            this.form.given_name
                                        )
                                    "
                                    @click="save('addManually')"
                                >
                                    Add
                                </jet-secondary-button>
                                <jet-secondary-button
                                    class="float-right mr-2"
                                    :disabled="
                                        !(
                                            this.form &&
                                            this.form.family_name &&
                                            this.form.given_name
                                        )
                                    "
                                    @click="
                                        this.form.reset(),
                                            this.authorsForm.reset()
                                    "
                                >
                                    Clear
                                </jet-secondary-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div
                v-if="authors.length > 0"
                class="shadow sm:rounded-md overflow-y-scroll h-64"
            >
                <draggable
                    v-model="this.authors"
                    @start="drag = true"
                    @end="drag = false"
                    @change="onSort()"
                    item-key="author.id"
                    group="author"
                >
                    <template #item="{ element }">
                        <div class="overflow-auto bg-gray shadow sm:rounded-md">
                            <ul role="list" class="divide-y divide-gray-900">
                                <li>
                                    <div class="px-4 py-4 sm:px-6">
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <p
                                                class="text-sm font-medium text-teal-900"
                                            >
                                                {{ element.title }}
                                                {{ element.family_name }}
                                                {{ element.given_name }}
                                                {{ element.id }}
                                            </p>
                                            <button
                                                class="ml-2 flex flex-shrink-0"
                                                @click="
                                                    (showManageRoleDialog = true),
                                                        (this.updateRoleForm.author_id =
                                                            element.id)
                                                "
                                            >
                                                <p
                                                    v-if="
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
                                                    {{ element.affiliation }}
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
                                                        confirmDeletion(element)
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
                                                v-if="element.orcid_id"
                                                class="text-xs font-medium text-teal-900"
                                            >
                                                <b class="text-gray-500"
                                                    >ORCID:</b
                                                >
                                                {{ element.orcid_id }}
                                            </p>
                                        </div>
                                        <div class="sm:flex sm:justify-between">
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
                        @click="
                            confirmDelete = false && this.authorsForm.reset()
                        "
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
                <jet-secondary-button class="float-left" @click="onClose">
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
                        @click="updateRole(item)"
                        class="cursor-pointer flex-1 border rounded-md p-2 bg-white-200 hover:bg-gray-200"
                    >
                        <div class="text-gray-900">
                            <b>{{ item.title }}</b> <br />
                            <p
                                class="text-xs align-top"
                                v-html="item.description"
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
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";
import JetInputError from "@/Jetstream/InputError.vue";
import LoadingButton from "@/Shared/LoadingButton.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import { Inertia } from "@inertiajs/inertia";
import SelectRich from "@/Shared/SelectRich.vue";
import Draggable from "vuedraggable";

export default {
    components: {
        JetDialogModal,
        JetSecondaryButton,
        JetDangerButton,
        JetButton,
        PencilIcon,
        TrashIcon,
        JetInputError,
        LoadingButton,
        Inertia,
        SelectRich,
        Draggable,
    },

    props: ["project"],

    data() {
        return {
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
            contributorType: [
                {
                    title: "Researcher",
                    description:
                        "A person involved in analysing data or the results of an experiment or formal study. May indicate an <br> intern or assistant to one of the authors who helped with research but who was not so “key” as to be listed as an author.",
                },
                {
                    title: "ContactPerson",
                    description:
                        "Person with knowledge of how to access, troubleshoot, or otherwise field <br> issues related to the resource",
                },
                {
                    title: "DataCollector",
                    description:
                        "Person/institution responsible for finding or gathering/collecting data under the <br> guidelines of the author(s) or Principal Investigator(PI)",
                },
                {
                    title: "DataCurator",
                    description:
                        "Person tasked with reviewing, enhancing, cleaning, or standardizing metadata and the associated data <br> submitted for storage, use, and maintenance within a data centre or repository",
                },
                {
                    title: "DataManager",
                    description:
                        "Person (or organisation with a staff of data managers, such as a data centre) <br> responsible for maintaining the finished resource",
                },
                {
                    title: "Distributor",
                    description:
                        "Institution tasked with responsibility to generate/disseminate copies <br> of the resource in either electronic or print form",
                },
                {
                    title: "Editor",
                    description:
                        "A person who oversees the details related to the publication format <br> of the resource ",
                },
                {
                    title: "HostingInstitution",
                    description:
                        "Typically, the organisation allowing the resource to be available on the internet through <br> the provision of its hardware/software/operating support",
                },
                {
                    title: "Producer",
                    description:
                        "Typically, a person or organisation responsible for the artistry and form <br> of a media product",
                },
                {
                    title: "ProjectLeader",
                    description:
                        "Person officially designated as head of project team or subproject team instrumental <br> in the work necessary to development of the resource",
                },
                {
                    title: "ProjectManager",
                    description:
                        "Person on the membership list of a designated project/project team",
                },
                {
                    title: "RegistrationAgency",
                    description:
                        "Institution/organisation officially appointed by a Registration Authority to handle specific <br> tasks within a defined area of responsibility",
                },
                {
                    title: "RelatedPerson",
                    description:
                        "A person without a specifically defined role in the development of the resource, but who is <br> someone the author wishes to recognize",
                },
                {
                    title: "Researcher",
                    description:
                        "A person involved in analysing data or the results of an experiment or formal study. May indicate an intern or assistant to one of the authors who helped with research but who was not so “key” as to be listed as an author.",
                },
                {
                    title: "ResearchGroup",
                    description:
                        "Typically refers to a group of individuals with a lab, department, or division that has a specifically <br> defined focus of activity.",
                },
                {
                    title: "RightsHolder",
                    description:
                        "Person or institution owning or managing property rights, including intellectual property rights over the resource",
                },
                {
                    title: "Sponsor",
                    description:
                        "Person or organisation that issued a contract or under the auspices of which a work has been written,<br> printed, published, developed, etc",
                },
                {
                    title: "Supervisor",
                    description:
                        "Designated administrator over one or more groups/teams working to produce a resource, or over one or <br> more steps of a development process",
                },
                {
                    title: "WorkPackageLeader",
                    description:
                        "A Work Package is a recognized data product, not all of which is included in publication. The package, instead, may include notes, discarded documents,<br> etc.The Work Package Leader is responsible for ensuring the comprehensive contents, versioning, and availability of the Work Package during the development of the resource. ",
                },
                {
                    title: "Other",
                    description:
                        "Any person or institution making a significant contribution to the development and/or maintenance of the resource, but whose contribution is not adequately described by any of the other values for contributorType",
                },
            ],
        };
    },
    mounted() {
        this.loadInitial();
    },
    methods: {
        /*Return filtered fetched authors list on the basis of selection*/
        selectedFetchedAuthorsList(all = false) {
            if (all) {
                return this.fetchedAuthors;
            } else {
                return this.fetchedAuthors.filter((a) => a.selected);
            }
        },
        /*Add imported authors to selected list*/
        addAuthorToSelectedList(author) {
            if (author.selected) {
                author.selected = false;
            } else {
                author.selected = true;
            }
        },
        /*Fetch and populate initial & default values*/
        loadInitial() {
            console.log(this.project.authors);
            if (this.project && this.project.authors) {
                this.authors = this.project.authors.sort((a,b) =>a.pivot.sort_order - b.pivot.sort_order);
            }
            this.form.contributor_type = {};
            this.form.contributor_type = this.contributorType[0];
        },
        /*Control the display toggling*/
        toggleDialog() {
            this.showDialog = !this.showDialog;
        },
        /*Reset variables on close of dialog*/
        onClose() {
            this.showDialog = false;
            this.form.reset();
            this.fetchedAuthors = [];
            this.query = "";
            this.form.contributor_type = {};
            this.form.contributor_type = this.contributorType[0];
        },
        /*Edit author*/
        edit(author) {
            this.selectedAuthor = author;

            this.authors = this.authors.filter((author) => {
                return (
                    author.family_name + author.given_name !=
                    this.selectedAuthor.family_name +
                        this.selectedAuthor.given_name
                );
            });
            this.form.title = author.title;
            this.form.family_name = author.family_name;
            this.form.given_name = author.given_name;
            this.form.email_id = author.email_id;
            this.form.affiliation = author.affiliation;
            this.form.orcid_id = author.orcid_id;
            this.form.contributor_type = {};
            this.form.contributor_type.title = author.pivot
                ? author.pivot.contributor_type
                : null;
        },
        /*Fetch author from DOI or ORCID id*/
        fetchAuthors() {
            this.loading = true;
            this.error = "";
            this.query = this.query.trim();
            let isDOI = new RegExp(/\b(10[.][0-9]{4,}(?:[.][0-9]+)*)\b/g).test(
                this.query
            );
            axios
                .get(this.$page.props.europemcWSApi, {
                    params: {
                        query: isDOI ? "DOI:" + this.query : this.query,
                        format: "json",
                        pageSize: "1",
                        resulttype: "core",
                        synonym: "true",
                    },
                })
                .then((res) => {
                    this.fetchedAuthors = isDOI
                        ? res.data.resultList.result[0].authorList.author
                        : res.data.resultList.result[0].authorList.author.filter(
                              (a) =>
                                  a.authorId &&
                                  a.authorId.type == "ORCID" &&
                                  a.authorId.value == this.query
                          );
                })
                .catch(() => {
                    this.error =
                        "Something went wrong. Please check the input and try again.";
                })
                .finally(() => {
                    if (this.fetchedAuthors.length == 0) {
                        this.error =
                            "Something went wrong. Please check the input and try again.";
                    }
                    this.loading = false;
                });
        },
        /*Format the response*/
        formatAuthors(authors) {
            let authorsList = [];
            authors.forEach((author) => {
                authorsList.push({
                    family_name: author.firstName,
                    given_name: author.lastName,
                    orcid_id:
                        author.authorId && author.authorId.type == "ORCID"
                            ? author.authorId.value
                            : "",
                    affiliation: author.authorAffiliationDetailsList
                        .authorAffiliation[0].affiliation
                        ? author.authorAffiliationDetailsList
                              .authorAffiliation[0].affiliation
                        : "",
                });
            });
            return authorsList;
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
        /*Prepare request form and execute save query for imported authors*/
        addImportedAuthor(addAll) {
            this.authorsForm.reset();
            this.authorsForm.authors = this.formatAuthors(
                this.selectedFetchedAuthorsList(addAll)
            );
            this.executeQuery();
        },
        /*Prepare request form and execute save query for authors added manually*/
        addManually() {
            this.validateForm();
            if (!this.form.hasErrors) {
                this.authorsForm.reset();
                this.authorsForm.authors = [
                    {
                        title: this.form.title ? this.form.title.trim() : null,
                        family_name: this.form.family_name
                            ? this.form.family_name.trim()
                            : null,
                        given_name: this.form.given_name
                            ? this.form.given_name.trim()
                            : null,
                        email_id: this.form.email_id
                            ? this.form.email_id.trim()
                            : null,
                        orcid_id: this.form.orcid_id
                            ? this.form.orcid_id.trim()
                            : null,
                        affiliation: this.form.affiliation
                            ? this.form.affiliation.trim()
                            : null,
                        contributor_type: this.form.contributor_type
                            ? this.form.contributor_type.title.trim()
                            : "Researcher",
                    },
                ];
                this.executeQuery();
            }
        },
        /*Make request to save API*/
        executeQuery() {
            if (this.authors.length > 0) {
                let _authorObj = {};
                this.authors.forEach((author) => {
                    for (var key in author) {
                        _authorObj[key] = author.hasOwnProperty(key)
                            ? author[key]
                            : null;
                    }
                    this.authorsForm.authors.push(_authorObj);
                    _authorObj = {};
                });
            }
            const keys = ["family_name", "given_name"];
            this.authorsForm.authors = this.authorsForm.authors.filter(
                (value, index, self) =>
                    self.findIndex((v) =>
                        keys.every((k) => v[k] === value[k])
                    ) === index
            );
            this.authorsForm.post(route("author.save", this.project.id), {
                preserveScroll: true,
                onSuccess: () => {
                    Inertia.reload({ only: ["project"] });
                    this.loadInitial();
                    this.form.reset();
                    this.form.contributor_type = {};
                    this.form.contributor_type = this.contributorType[0];
                },
                onError: (err) => console.error(err),
            });
        },
        /*Confirm delete and prepare request*/
        confirmDeletion(author) {
            this.confirmDelete = true;
            this.authorsForm.reset();
            this.authorsForm.authors = [
                {
                    id: author.id,
                    family_name: author.family_name,
                    given_name: author.given_name,
                    orcid_id: author.orcid_id,
                    affiliation: author.affiliation,
                },
            ];
        },
        /*Make request to delete API*/
        deleteAuthor() {
            this.authorsForm.delete(route("author.delete", this.project.id), {
                preserveScroll: true,
                onSuccess: () => {
                    Inertia.reload({ only: ["project"] });
                    this.loadInitial();
                    this.authorsForm.reset();
                    this.confirmDelete = false;
                },
                onError: (err) => console.error(err),
            });
        },
        /*Check form for validation errors*/
        validateForm() {
            this.form.errors = {};
            this.form.hasErrors = false;
            let _hasErrors = false;
            if (this.form.email_id) {
                if (!this.isValidEmail(this.form.email_id)) {
                    this.form.errors.email_id = "Valid email required.";
                    _hasErrors = true;
                }
            }
            if (_hasErrors) {
                this.form.hasErrors = true;
            }
        },
        /*Make request to update role API*/
        updateRole(role) {
            this.updateRoleForm.role = role.title;
            this.updateRoleForm.post(
                route("author.updateRole", this.project.id),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        Inertia.reload({ only: ["project"] });
                        this.loadInitial();
                        this.updateRoleForm.reset();
                        this.showManageRoleDialog = false;
                        this.authorId = "";
                    },
                    onError: (err) => console.error(err),
                }
            );
        },
        onSort() {
            console.log("Draggable on change event fired..");
            this.executeQuery();
        },
    },
};
</script>
