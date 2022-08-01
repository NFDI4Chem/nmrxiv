<template>
    <jet-dialog-modal :show="addAuthorDialog" :max-width="'5xl'" @close="addAuthorDialog = false" >
        <template #title> New Author </template>
        <template #content>
            <div class="space-y-8 divide-y divide-gray-200 overflow-auto">
                <div>
                    <div>
                        <h3
                            class="text-sm leading-6 font-bold text-gray-900"
                        >
                            Import From
                        </h3>
                    </div>

                    <div
                        class="mt-1 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-9"
                    >
                        <div class="sm:col-span-4">
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
                        </div>
                        <div>
                            <p class="mt-7 ml-5 text-md font-medium text-gray-600">or</p>
                        </div>
                        <div class="sm:col-span-4">
                            <label
                                for="name"
                                class="block text-sm font-medium text-gray-700"
                            >
                                DOI
                            </label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input
                                    v-model="this.doi"
                                    type="text"
                                    name="name"
                                    id="name"
                                    autocomplete="off"
                                    class="flex-1 focus:ring-teal-500 focus:border-teal-500 block w-full min-w-0 rounded sm:text-sm border-gray-300"
                                />
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <jet-secondary-button @click="importAuthors">
                                Import
                            </jet-secondary-button>
                        </div>
                        <div v-if="authorsList.length>0" class="sm:col-span-9">
                            <checkboxrich 
                            :items="authorsList"
                            v-model:checked="selectedAuthorsList"
                            />
                        </div>

                    </div>
                </div>

                    <!-- Add Manually -->
                    <div class="pt-4">
                            <div>
                            <h3 class="text-sm leading-6 font-bold text-gray-900">Add Manually</h3>
                            </div>
                            <div class="mt-1 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700"> Title </label>
                                <div class="mt-1">
                                <input v-model="manualAddAuthor.title" type="text" name="title" id="title" autocomplete="title" class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md" />
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="family-name" class="block text-sm font-medium text-gray-700"> Family Name </label>
                                <div class="mt-1">
                                <input v-model="manualAddAuthor.familyName" type="text" name="family-name" id="family-name" autocomplete="family-name" class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md" />
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="given-name" class="block text-sm font-medium text-gray-700"> Given Name </label>
                                <div class="mt-1">
                                <input v-model="manualAddAuthor.givenName" type="text" name="given-name" id="given-name" autocomplete="given-name" class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md" />
                                </div>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="email" class="block text-sm font-medium text-gray-700"> Email address </label>
                                <div class="mt-1">
                                <input v-model="manualAddAuthor.email"  id="email" name="email" type="email" autocomplete="email" class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md" />
                                </div>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="orcid" class="block text-sm font-medium text-gray-700"> ORCID ID </label>
                                <div class="mt-1">
                                <input v-model="manualAddAuthor.orcidId" id="orcid" name="orcid" autocomplete="orcid" type="text" class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md" />
                                </div>
                            </div>
                            <div class="sm:col-span-6">
                                <label for="about" class="block text-sm font-medium text-gray-700"> Affiliation </label>
                                <div class="mt-1">
                                <textarea v-model="manualAddAuthor.affiliation" id="affiliation" name="affiliation" rows="3" class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md" />
                                </div>
                            </div>
                            <div class="sm:col-span-6">
                                <jet-secondary-button class="float-right text-md font-bold text-teal-900 " @click="addAuthorManually">
                                    +
                                </jet-secondary-button>
                            </div>
                    </div>
                                            <div class="mt-5 overflow-auto">
                                                <table
                                                    class="min-w-full rounded border divide-y divide-gray-300"
                                                >
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th
                                                                scope="col"
                                                                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 lg:pl-8"
                                                            >
                                                                Name
                                                            </th>
                                                            <th
                                                                scope="col"
                                                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                                            >
                                                                Affiliation
                                                            </th>
                                                            <th
                                                                scope="col"
                                                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                                            >
                                                                ORCID ID
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody
                                                        class="divide-y divide-gray-200 bg-white"
                                                    >
                                                        <tr v-for="author in selectedAuthorsList" :key="author.given"
                                                        >
                                                            <td
                                                                class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-600 sm:pl-6 lg:pl-8"
                                                            >
                                                                {{ author.title }} {{ author.givenName }} {{ author.familyName }}
                                                            </td>
                                                            <td
                                                                class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-600 sm:pl-6 lg:pl-8 break-normal"
                                                            >
                                                                {{ author.affiliation }}
                                                            </td>
                                                            <td
                                                                class="whitespace-nowrap px-3 py-4 text-sm text-teal-600"
                                                            >
                                                                <a :href="author.orcidId" >{{author.orcidId}}</a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                </div>
            </div>
        </template>

        <template #footer>
            <jet-secondary-button @click="onCancel">
                Cancel
            </jet-secondary-button>

            <jet-button
                class="ml-2"
                @click="addAuthor"
            >
                Add
            </jet-button>
        </template>
    </jet-dialog-modal>
</template>

<script>
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import Checkboxrich from "@/Shared/CheckboxRich.vue";

export default {
    components: {
        JetDialogModal,
        JetSecondaryButton,
        JetButton,
        Checkboxrich,
    },

    data() {
        return {
            addAuthorForm: this.$inertia.form({
                _method: "POST",
                selectedAuthorsList: [],
            }),
            doi: "10.1021%2Facs.orglett.0c01297",
            addAuthorDialog: false,
            authorsList: [],
            manualAddAuthor: {},
            selectedAuthorsList: [],
        };
    },

    props: ["project"],

    methods: {
        toggleAddAuthorDialog() {
            this.addAuthorDialog = !this.addAuthorDialog;
        },
        onCancel() {
            this.addAuthorDialog = false;
            // this.createAnnouncementForm.clearErrors();
            // this.$page.props.errors = new Object();
        },
        importAuthors(){
            axios.get(route("get-details-by-DOI", {doi:this.doi} )).then((res) => {
                this.authorsList = res.data;
            });
        },
        addAuthorManually(){
            this.selectedAuthorsList.push(this.manualAddAuthor);
        },
        addAuthor(){
            for(var i=0; i<this.selectedAuthorsList.length; i++){
                this.addAuthorForm.selectedAuthorsList.push(this.selectedAuthorsList[i]);
            }
            this.addAuthorForm.post(route("add-author"), {
                preserveScroll: true,
                onSuccess: () => {
                this.addAuthorDialog = false;
                this.addAuthorForm.reset();
                },
                onError: (err) => console.error(err),
            });
        },
    },
};
</script>
