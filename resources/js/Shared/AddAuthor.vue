<template>
  <jet-dialog-modal :show="showDialog" :max-width="'6xl'" @close="showDialog = false">
    <template #title> {{ project.name }} - Manage Authors </template>
    <template #content>
      <div
        class="relative grid grid-cols-1 gap-x-16 max-w-7xl mx-auto lg:grid-cols-2 divide-x"
      >
        <div class="pb-36 px-4 sm:px-6 lg:pb-16 lg:px-0 lg:row-start-1 lg:col-start-1">
          <div>
            <p class="text-sm leading-6 font-bold text-gray-900">Import From</p>
            <div class="mt-1 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
              <div class="sm:col-span-2">
                <label for="name" class="block text-sm font-medium text-gray-700">
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
            </div>
            <div
              v-if="loading"
              class="sm:col-span-9 mt-4 align-centre"
            >
              <loading-button :loading="loading" />
            </div>
            <div><div v-for="author in fetchedAuthors" :key="author.authorId" class="relative flex items-start mt-2">
                <div @click="addAuthorToSelectedList(author)" :class="[ author.selected ? 'bg-teal-800 text-white' : '', 'cursor-pointer min-w-0 flex-1 text-sm border rounded-md p-4']">
                    <label for="items" class="font-medium"
                        >{{ author.firstName }} {{ author.lastName }}
                    </label>
                    <p id="items-description">
                        {{ author.authorAffiliationDetailsList.authorAffiliation[0].affiliation }}
                    </p>
                    <a
                        v-if="author.authorId && author.authorId.type == 'ORCID'"
                        >ORCID ID - {{ author.authorId.value }}</a
                    >
                </div> 
            </div>
            <div v-if="selectedFetchedAuthorsList.length > 0" class="sm:col-span-6 float-right mt-3">
                <jet-secondary-button
                  class="float-right text-md font-bold text-teal-900"
                  @click="save(true)"
                >
                  Add ({{ selectedFetchedAuthorsList.length }})
                </jet-secondary-button>
              </div></div>
          </div>
        </div>
        <div class="pb-36 px-4 sm:px-6 lg:pb-16 lg:px-0 lg:row-start-1 lg:col-start-2">
          <div class="p-2">
            <!--Add Manual Section-->
            <p class="text-sm leading-6 font-bold text-gray-900">Add Author</p>
            <div class="mt-1 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
              <div class="sm:col-span-2">
                <label for="title" class="block text-sm font-medium text-gray-700">
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
                <jet-input-error :message="form.errors.family_name" class="mt-2" />
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
                <jet-input-error :message="form.errors.given_name" class="mt-2" />
              </div>
              <div class="sm:col-span-3">
                <label for="email" class="block text-sm font-medium text-gray-700">
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
                    @blur="validateEmail"
                  />
                  <jet-input-error :message="form.errors.email_id" class="mt-2" />
                </div>
              </div>
              <div class="sm:col-span-3">
                <label for="orcid" class="block text-sm font-medium text-gray-700">
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
                <label for="about" class="block text-sm font-medium text-gray-700">
                  Affiliation
                </label>
                <div class="mt-1">
                  <textarea
                    id="affiliation"
                    v-model="form.affiliation"
                    name="affiliation"
                    rows="3"
                    class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                    placeholder="Name and address of affiliated University and Department. e.g. Institut für Anorganische und Analytische Chemie,Friedrich-Schiller-Universität,Schloßgasse 10, 07743 Jena"
                  />
                </div>
                <jet-input-error :message="form.errors.affiliation" class="mt-2" />
              </div>
              <div class="sm:col-span-6 float-left">
                <jet-secondary-button
                  class="float-right text-md font-bold text-teal-900"
                  :disabled="
                    !(this.form && this.form.family_name && this.form.given_name)
                  "
                  @click="save()"
                >
                  Add
                </jet-secondary-button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-if="authors.length > 0">
        <div class="ml-2 mt-2 overflow-y-scroll h-64">
          <!-- <p class="float-left text-xs font-bold text-red-900 mt-4">
              *Please review your changes below and click on Save button to save your
              changes.
            </p> -->
          <table
            class="divide-y border divide-gray-200 w-full table-fixed overflow-y-scroll"
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
                  class="px-6 text-center py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="author in authors" :key="author.id">
                <td colspan="3" class="py-4">
                  <div class="ml-4">
                    <h3 class="text-sm leading-6 font-medium text-gray-900">
                      {{ author.title }}
                      {{ author.given_name }}
                      {{ author.family_name }}
                    </h3>
                    <p class="mt-1 text-xs font-small text-blue-gray-900 break-words">
                      {{ author.affiliation }}
                    </p>
                    <div
                      v-if="author.orcid_id"
                      class="text-xs leading-6 font-medium text-gray-900"
                    >
                      <!-- <p>
                          Orcid Id -
                          <a
                            :href="getOrcidLink(author.orcid_id)"
                            target="_blank"
                            class="text-teal-900"
                            >{{ author.orcid_id }}</a
                          >
                        </p> -->
                    </div>
                    <div
                      v-if="author.email_id"
                      class="text-xs leading-6 font-medium text-gray-900"
                    >
                      <p>Email Id - {{ author.email_id }}</p>
                    </div>
                  </div>
                </td>
                <td
                  class="text-center px-6 py-4 text-sm leading-5 text-gray-500 whitespace-no-wrap border-gray-200"
                >
                  <button
                    type="button"
                    class="inline-flex items-center p-1 border border-transparent"
                    @click="edit(author)"
                  >
                    <PencilIcon class="w-5 h-5 mr-1 text-gray-600" />
                  </button>
                  <button
                    type="button"
                    class="inline-flex items-center p-1 border border-transparent"
                    @click="confirmAuthorDeletion(author.id)"
                  >
                    <TrashIcon class="w-5 h-5 mr-1 text-gray-600" />
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
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
        given_name: "",
        family_name: null,
        affiliation: null,
        orcid_id: null,
        email_id: null,
      }),
      authorsForm: this.$inertia.form({
        authors: []
      }),
      query: "10.1002/mrc.4737",
      showDialog: false,
      authors: [],
      fetchedAuthors: [],
      selectedAuthor: null,
      loading: false,
    };
  },
  mounted() {
    this.loadAuthors();
  },
  computed:{
    selectedFetchedAuthorsList(){
        return this.fetchedAuthors.filter(a => a.selected)
    }
  },
  methods: {
    addAuthorToSelectedList(author){
        author.selected = true
    },
    loadAuthors() {
      if (this.project && this.project.authors) {
        this.authors = this.project.authors;
      }
    },
    toggleDialog() {
      this.showDialog = !this.showDialog;
    },
    onClose() {
      this.showDialog = false;
    },
    edit(author) {
      this.selectedAuthor = author;

      this.authors = this.authors.filter((author) => {
        return (
          author.family_name + author.given_name !=
          this.selectedAuthor.family_name + this.selectedAuthor.given_name
        );
      });

      this.form.title = author.title;
      this.form.family_name = author.family_name;
      this.form.given_name = author.given_name;
      this.form.email_id = author.email_id;
      this.form.affiliation = author.affiliation;
      this.form.orcid_id = author.orcid_id;
    },
    fetchAuthors() {
      this.loading = true;
      this.query = this.query.trim()
      let isDOI = (new RegExp(/\b(10[.][0-9]{4,}(?:[.][0-9]+)*)\b/g)).test(this.query);
      axios
        .get(this.$page.props.europemcWSApi, {
          params: {
            query: isDOI ?  'DOI:' + this.query : this.query,
            format: "json",
            pageSize: "1",
            resulttype: "core",
            synonym: "true",
          },
        })
        .then((res) => {
            this.fetchedAuthors = isDOI ? res.data.resultList.result[0].authorList.author : res.data.resultList.result[0].authorList.author.filter(a => a.authorId && a.authorId.type == 'ORCID' && a.authorId.value == this.query);
        })
        .catch(() => {
            //   this.importAuthorsForm.errors.input =
            //     "Something went wrong. Please check the input and try again.";
        })
        .finally(() => {
            //   if (this.authorsListDOI == null) {
            //     this.importAuthorsForm.errors.input =
            //       "Something went wrong. Please check the input and try again.";
            //   }
            this.loading = false;
        });
    },
    formatAuthors(authors){
        let authorsList = [];
        authors.forEach(author => {
            authorsList.push({
                family_name: author.firstName,
                given_name: author.lastName,
                orcid_id: author.authorId && author.authorId.type == 'ORCID' ? author.authorId.value : '',
                affiliation: author.authorAffiliationDetailsList.authorAffiliation[0].affiliation ?  author.authorAffiliationDetailsList.authorAffiliation[0].affiliation : ''
            })
        });
        return authorsList;
    },
    save(multiple = false) {
      if(multiple){
        this.authorsForm.authors = this.formatAuthors(this.selectedFetchedAuthorsList)
        this.executeQuery()
      }else{
        this.authorsForm.authors = [{
            family_name: this.form.family_name,
            given_name: this.form.given_name,
            orcid_id: this.form.orcid_id,
            affiliation: this.form.affiliation
        }]
        this.executeQuery()
      }
    },
    executeQuery(){
        this.authorsForm.post(route("save-author", this.project.id), {
            preserveScroll: true,
            onSuccess: () => {
            Inertia.reload({ only: ["project"] });
            this.loadAuthors();
            this.form.reset();
            },
            onError: (err) => console.error(err),
      });
    },
    delete() {},
  },
};
</script>
