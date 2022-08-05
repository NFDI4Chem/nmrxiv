<template>
    <jet-dialog-modal
        :show="addAuthorDialog"
        :max-width="'6xl'"
        @close="addAuthorDialog = false"
    >
        <template #title> New Author </template>
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
                            <!--  Hiding Import from Orcid Id Option -->
                            <!-- <div class="sm:col-span-1">
                  <label
                    for="name"
                    class="block text-sm font-medium text-gray-700"
                  >
                    ORCID ID
                  </label>
                  <div class="mt-1 flex rounded-md shadow-sm">
                    <input
                      type="text"
                      name="name"
                      id="name"
                      autocomplete="off"
                      class="flex-1 focus:ring-teal-500 focus:border-teal-500 block w-full min-w-0 rounded sm:text-sm border-gray-300"
                    />
                  </div>
                </div> -->
                            <div class="sm:col-span-2">
                                <label
                                    for="name"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    DOI
                                </label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input
                                        v-model="this.importAuthorsForm.doi"
                                        type="text"
                                        name="name"
                                        id="name"
                                        autocomplete="off"
                                        class="flex-1 focus:ring-teal-500 focus:border-teal-500 block w-full min-w-0 rounded sm:text-sm border-gray-300"
                                    />
                                </div>
                                <jet-input-error
                                    :message="importAuthorsForm.errors.doi"
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
                            v-if="this.loading && this.importAuthorsForm.doi"
                            class="sm:col-span-9 mt-4 align-centre"
                        >
                            <loading-button :loading="this.loading" />
                        </div>
                        <div
                            v-if="authorsListDOI.length > 0 && !this.loading"
                            class="sm:col-span-9 mt-4"
                        >
                            <author-checkbox
                                :items="authorsListDOI"
                                v-model:checked="doiSelectedAuthorList"
                            />
                        </div>
                        <div
                            v-if="this.authorsListDOI.length > 0"
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
                                        v-model="manualAddAuthorForm.title"
                                        type="text"
                                        name="title"
                                        id="title"
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
                                        v-model="
                                            manualAddAuthorForm.family_name
                                        "
                                        type="text"
                                        name="family-name"
                                        id="family-name"
                                        autocomplete="family-name"
                                        class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    />
                                </div>
                                <jet-input-error
                                    :message="
                                        this.manualAddAuthorForm.errors
                                            .family_name
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
                                        v-model="manualAddAuthorForm.given_name"
                                        type="text"
                                        name="given-name"
                                        id="given-name"
                                        autocomplete="given-name"
                                        class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    />
                                </div>
                                <jet-input-error
                                    :message="
                                        this.manualAddAuthorForm.errors
                                            .given_name
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
                                        v-model="manualAddAuthorForm.email_id"
                                        @blur="validateEmail"
                                        id="email"
                                        name="email"
                                        type="email"
                                        autocomplete="email"
                                        class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    />
                                    <jet-input-error
                                        :message="
                                            this.manualAddAuthorForm.errors
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
                                        v-model="manualAddAuthorForm.orcid_id"
                                        id="orcid"
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
                                        v-model="
                                            manualAddAuthorForm.affiliation
                                        "
                                        id="affiliation"
                                        name="affiliation"
                                        rows="3"
                                        class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                                        placeholder="Name and address of affiliated University and Department. e.g. Institut für Anorganische und Analytische Chemie,Friedrich-Schiller-Universität,Schloßgasse 10, 07743 Jena"
                                    />
                                </div>
                                <jet-input-error
                                    :message="
                                        manualAddAuthorForm.errors.affiliation
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
            <div v-if="this.selectedAuthorsList.length > 0">
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
                                        @click="deleteAuthor(author.id)"
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
        </template>

        <template #footer>
            <jet-secondary-button class="float-left" @click="onClose">
                Close
            </jet-secondary-button>

            <jet-button class="ml-2" @click="updateAuthor"> Save </jet-button>
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
export default {
    components: {
        JetDialogModal,
        JetSecondaryButton,
        JetButton,
        AuthorCheckbox,
        PencilIcon,
        TrashIcon,
        JetInputError,
        LoadingButton,
    },

    data() {
        return {
            addAuthorForm: this.$inertia.form({
                _method: "POST",
                selectedAuthorsList: [],
            }),
            manualAddAuthorForm: this.$inertia.form({
                _method: "POST",
                errors: {},
            }),
            importAuthorsForm: this.$inertia.form({
                doi: "10.1021%2Facs.orglett.0c01297",
                orcid: "",
                errors: {},
            }),
            addAuthorDialog: false,
            authorsListDOI: [],
            doiSelectedAuthorList: [],
            selectedAuthorsList: [],
            getDetailsbyDOIApi: null,
            loading: false,
        };
    },

    props: ["project"],

    methods: {
        toggleAddAuthorDialog() {
            this.addAuthorDialog = !this.addAuthorDialog;
            if (this.project.authors) {
                for (var i = 0; i < this.project.authors.length; i++) {
                    this.project.authors[i].id = (
                        this.project.authors[i].family_name +
                        "-" +
                        this.project.authors[i].given_name
                    ).replace(/\s/g, "");
                    this.selectedAuthorsList.push(this.project.authors[i]);
                }
            }
        },
        onClose() {
            this.addAuthorDialog = false;
            this.resetData();
        },
        /*Import authors from DOI or ORCID ID.*/
        importAuthors() {
            this.loading = true;
            this.authorsListDOI = [];
            this.getDetailsbyDOIApi = this.$page.props.getDetailsbyDOIApi;
            this.importAuthorsForm.errors = {};
            if (!this.importAuthorsForm.doi) {
                this.importAuthorsForm.errors.doi =
                    "The DOI field is required.";
            } else {
                this.getDetailsbyDOIApi =
                    this.getDetailsbyDOIApi + "/" + this.importAuthorsForm.doi;
                axios
                    .get(this.getDetailsbyDOIApi)
                    .then((res) => {
                        this.authorsListDOI = this.formatAuthorResponse(
                            res.data
                        );
                    })
                    .catch((error) => {
                        this.importAuthorsForm.errors.doi = "Invalid DOI.";
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            }
        },
        /*Add authors to the temp list either added Manually or selected from imported list*/
        addAuthor(source) {
            if (source == "Manual") {
                //Added Manually.
                this.validateForm();
                if (!this.manualAddAuthorForm.hasError) {
                    if (!this.manualAddAuthorForm.id) {
                        this.manualAddAuthorForm.id = (
                            this.manualAddAuthorForm.family_name +
                            "-" +
                            this.manualAddAuthorForm.given_name
                        ).replace(/\s/g, "");
                    }
                    this.selectedAuthorsList.push(this.manualAddAuthorForm);
                    this.manualAddAuthorForm = this.$inertia.form({});
                }
            } else if (source == "DOI") {
                //Added from authors list retured from DOI.
                for (var i = 0; i < this.doiSelectedAuthorList.length; i++) {
                    if (!this.doiSelectedAuthorList[i].id) {
                        this.doiSelectedAuthorList[i].id = (
                            this.doiSelectedAuthorList[i].family_name +
                            "-" +
                            this.doiSelectedAuthorList[i].given_name
                        ).replace(/\s/g, "");
                    }
                    this.selectedAuthorsList.push(
                        this.doiSelectedAuthorList[i]
                    );
                }
            }
            //filtering duplicate values from the temp list.
            const keys = ["id"];
            this.selectedAuthorsList = this.selectedAuthorsList.filter(
                (value, index, self) =>
                    self.findIndex((v) =>
                        keys.every((k) => v[k] === value[k])
                    ) === index
            );
        },
        /*Edit author */
        editAuthor(object) {
            for (var i = 0; i < this.selectedAuthorsList.length; i++) {
                if (this.selectedAuthorsList[i].id == object.id) {
                    this.selectedAuthorsList.splice(i, 1);
                }
            }
            this.manualAddAuthorForm = JSON.parse(JSON.stringify(object));
            this.manualAddAuthorForm.errors = {};
        },
        /*Delete authors from the temp list*/
        deleteAuthor(id) {
            for (var i = 0; i < this.selectedAuthorsList.length; i++) {
                if (this.selectedAuthorsList[i].id == id) {
                    this.selectedAuthorsList.splice(i, 1);
                }
            }
        },
        /*Update the database*/
        updateAuthor() {
            this.addAuthorForm.reset();
            for (var i = 0; i < this.selectedAuthorsList.length; i++) {
                this.addAuthorForm.selectedAuthorsList.push(
                    this.selectedAuthorsList[i]
                );
            }
            this.addAuthorForm.post(route("update-author", this.project.id), {
                preserveScroll: true,
                onSuccess: () => {
                    this.addAuthorDialog = false;
                    this.resetData();
                },
                onError: (err) => console.error(err),
            });
        },
        /*Format the response*/
        formatAuthorResponse(response) {
            var authorsList = [];
            var author = {};
            if (response && response["status"] == "ok") {
                var authors = response["message"]["author"];
                authors.forEach((item) => {
                    author["given_name"] = item.hasOwnProperty("given")
                        ? item["given"]
                        : null;
                    author["family_name"] = item.hasOwnProperty("family")
                        ? item["family"]
                        : null;
                    author["affiliation"] = item.hasOwnProperty("affiliation")
                        ? item["affiliation"][0]["name"]
                        : null;
                    author["orcid_id"] = item.hasOwnProperty("ORCID")
                        ? item["ORCID"]
                        : null;
                    authorsList.push(author);
                    author = {};
                });
            }
            return authorsList;
        },
        /*Validating form*/
        validateForm() {
            this.manualAddAuthorForm.errors = {};
            this.manualAddAuthorForm.hasError = false;
            var hasError = false;
            if (this.manualAddAuthorForm.email_id) {
                if (!this.validEmail(this.manualAddAuthorForm.email_id))
                    this.manualAddAuthorForm.errors.email_id =
                        "Valid email required.";
                hasError = true;
            }
            if (!this.manualAddAuthorForm.given_name) {
                this.manualAddAuthorForm.errors.given_name =
                    "The given name field is required.";
                hasError = true;
            }
            if (!this.manualAddAuthorForm.family_name) {
                this.manualAddAuthorForm.errors.family_name =
                    "The family name field is required.";
                hasError = true;
            }
            if (!this.manualAddAuthorForm.affiliation) {
                this.manualAddAuthorForm.errors.affiliation =
                    "The affiliation field is required.";
                hasError = true;
            }
            if (hasError) {
                this.manualAddAuthorForm.hasError = true;
            }
        },
        /*Validating email*/
        validEmail(email) {
            var re =
                /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        },
        /*Reset forms and local variables*/
        resetData() {
            this.addAuthorForm.reset();
            this.doiSelectedAuthorList = [];
            this.authorsListDOI = [];
            this.manualAddAuthorForm = this.$inertia.form({});
            this.importAuthorsForm.reset();
            this.selectedAuthorsList = [];
            this.loading = false;
        },
    },
};
</script>
