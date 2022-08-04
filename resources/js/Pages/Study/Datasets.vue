<template>
  <div>
    <study-content
      model="study"
      :project="project"
      :study="study"
      :team="team"
      :members="members"
      :availableRoles="availableRoles"
      :studyPermissions="studyPermissions"
      :studyRole="studyRole"
      current="Datasets"
    >
      <template #study-section>
        <div class="px-4 sm:px-6 md:px-0">
          <div class="p-6">
            <div>
              <label for="location" class="block text-sm font-medium text-gray-700"
                >Select Experiment
                <small
                  ><span class="float-right" v-if="autoSaving">Saving...</span></small
                >
              </label>
              <select
                @change="loadSpectra"
                v-model="selectedDataset"
                id="location"
                name="location"
                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm rounded-md"
              >
                <option
                  :value="dataset"
                  v-for="dataset in study.datasets.sort((a, b) =>
                    a.name > b.name ? 1 : -1
                  )"
                  :key="dataset.slug"
                >
                  {{ dataset.name }}
                </option>
              </select>
            </div>
            <div class="my-7">
              <iframe
                v-on:load="loadSpectra()"
                name="datasetNMRiumIframe"
                frameborder="0"
                allowfullscreen
                class="rounded-md border"
                style="width: 100%; height: 75vh; max-height: 600px"
                :src="nmriumURL"
              ></iframe>
            </div>
            <div v-if="currentMolecules.length > 0">
              <ul
                role="list"
                class="grid grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-3 sm:gap-x-6 lg:grid-cols-4 xl:gap-x-8"
              >
                <li
                  v-for="molecule in currentMolecules"
                  :key="molecule.key"
                  class="relative"
                >
                  <div
                    class="group block w-full aspect-w-10 aspect-h-7 rounded-lg bg-gray-100 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-offset-gray-100 focus-within:ring-teal-500 overflow-hidden"
                  >
                    <div
                      class="object-cover pointer-events-none group-hover:opacity-75"
                      v-html="molecule.svg"
                    ></div>
                  </div>
                  <!-- <p
                    class="mt-2 block text-sm font-medium text-gray-900 truncate pointer-events-none"
                  >
                    {{ file.title }}
                  </p>
                  <p class="block text-sm font-medium text-gray-500 pointer-events-none">
                    {{ file.size }}
                  </p> -->
                </li>
              </ul>
            </div>
            <div class="pt-3">
              <div>
                <label for="about" class="block text-sm font-medium text-gray-700">
                  License
                </label>
                <div class="p-1 pr-2 grid grid-cols-2">
                  <div>
                    <loading-button :loading="this.loading" />
                    <input
                      v-if="!this.loading"
                      readonly
                      v-model="this.license"
                      type="text"
                      name="study-name"
                      id="study-name"
                      class="block w-full shadow-sm sm:text-sm focus:ring-gray-500 focus:border-gray-500 border-gray-300 rounded-md"
                    />
                  </div>
                </div>
              </div>
            </div>
            <div class="grid grid-cols-2">
              <div class="p-1 pr-2">
                <label for="location" class="block text-sm font-medium text-gray-700"
                  >Info</label
                >
                <div
                  class="overflow-hidden ring-1 mt-3 ring-black ring-opacity-5 md:rounded-lg"
                  v-if="selectedSpectraData"
                >
                  <table class="min-w-full border divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                      <tr>
                        <th
                          scope="col"
                          class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 lg:pl-8"
                        >
                          Field
                        </th>
                        <th
                          scope="col"
                          class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                        >
                          Value
                        </th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                      <tr v-for="key in Object.keys(selectedSpectraData.info)" :key="key">
                        <td
                          class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 lg:pl-8"
                        >
                          {{ key }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                          {{ selectedSpectraData.info[key] }}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="p-1 pr-2">
                <div class="px-4 py-5 sm:p-6">
                  <div>
                    <div class="pt-3">
                      <div>
                        <label
                          for="about"
                          class="block text-sm font-medium text-gray-700"
                        >
                          Extraction
                        </label>
                        <div class="mt-1">
                          <textarea
                            readonly
                            id="about"
                            name="about"
                            rows="3"
                            class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                          ></textarea>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                          Describe your extraction protocol. Protocol parameters can be
                          provided below.
                        </p>
                      </div>
                    </div>
                    <div class="mt-3">
                      <label for="about" class="block text-sm font-medium text-gray-700">
                        NMR Sample preparation
                      </label>
                      <div class="mt-1">
                        <textarea
                          readonly
                          id="about"
                          name="about"
                          rows="3"
                          class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                        ></textarea>
                      </div>
                      <p class="mt-2 text-sm text-gray-500">
                        Describe NMR sample preparation protocol details. Protocol
                        parameters can be provided below.
                      </p>
                    </div>
                  </div>
                  <div class="mt-3">
                    <div>
                      <label for="about" class="block text-sm font-medium text-gray-700">
                        Data transformation
                      </label>
                      <div class="mt-1">
                        <textarea
                          readonly
                          id="about"
                          name="about"
                          rows="3"
                          class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                        ></textarea>
                      </div>
                      <p class="mt-2 text-sm text-gray-500">
                        Describe any data transformation protocol performed. Protocol
                        parameters can be provided below.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
    </study-content>
  </div>
</template>

<script>
import StudyContent from "@/Pages/Study/Content.vue";
import LoadingButton from "@/Shared/LoadingButton.vue";
export default {
  props: [
    "study",
    "project",
    "current",
    "team",
    "members",
    "availableRoles",
    "studyPermissions",
    "studyRole",
    "model",
  ],
  components: {
    StudyContent,
    LoadingButton,
  },
  mounted() {
    const saveNMRiumUpdates = (e) => {
      if (e.origin != "https://nmriumdev.nmrxiv.org") {
        return;
      }
      if (e.data.type == "nmr-wrapper:action-response") {
        let actionType = e.data.data.type;
        if (actionType == "exportSpectraViewerAsBlob") {
          console.log("hihi")
          this.saveStudyPreview(e.data.data.data);
        }
      }
      if (e.data.type == "nmr-wrapper:data-change") {
        let actionType = e.data.data.actionType;

        if (
          actionType == "" ||
          (actionType == "INITIATE" &&
            this.selectedDataset &&
            this.selectedDataset.type != null)
        ) {
          return;
        }

        this.selectedSpectraData = e.data.data.spectra[0];
        if (
          actionType == "ADD_MOLECULE" ||
          actionType == "DELETE_MOLECULE" ||
          e.data.data.molecules
        ) {
          this.currentMolecules = e.data.data.molecules;
        }

        if (this.selectedDataset) {
          if (this.selectedDataset.dataset_photo_url == "") {
            console.log("Saving spectra preview");
            const iframe = window.frames.datasetNMRiumIframe;
            if (iframe) {
              let data = {
                type: "exportSpectraViewerAsBlob",
              };
              iframe.postMessage({ type: `nmr-wrapper:action-request`, data }, "*");
            }
          }
        }

        this.updateDataSet();
      }
    };
    window.addEventListener("message", saveNMRiumUpdates);
  },
  beforeMount() {
    if (this.study.license_id) {
      this.loading = true;
      axios
        .get(route("license", this.study.license_id))
        .then((res) => {
          this.license = res.data[0].title;
        })
        .finally(() => {
          this.loading = false;
        });
    }
  },
  data() {
    return {
      autoSaving: false,
      selectedDataset: null,
      currentMolecules: [],
      selectedSpectraData: null,
      license: "No license selected",
      loading: false,
    };
  },
  methods: {
    saveStudyPreview(data) {
      const reader = new FileReader();
      reader.addEventListener("loadend", () => {
        let svg = reader.result;
        axios.post("/dashboard/datasets/" + this.selectedDataset.id + "/preview", {
          img: svg,
        });
      });
      reader.readAsText(data.blob);
    },
    updateDataSet() {
      if (this.selectedSpectraData != null) {
        this.autoSaving = true;
        axios
          .post("/dashboard/datasets/" + this.selectedDataset.id + "/nmriumInfo", {
            spectra: this.selectedSpectraData,
            molecules: this.currentMolecules,
          })
          .then((response) => {
            this.autoSaving = false;
          });
      }
    },
    loadSpectra() {
      if (this.selectedDataset == null) {
        this.selectedDataset = this.study.datasets[0];
      }
      const iframe = window.frames.datasetNMRiumIframe;
      this.currentMolecules = [];
      if (iframe) {
        if (!this.selectedDataset.nmrium_info) {
          console.log("Loading spectra from file");
          let data = {
            data: [
              this.url + "/" + this.$page.props.user.username +  "/datasets/" + this.project.slug + "/" + this.study.slug  + "/" + this.selectedDataset.slug
            ],
            type: "url",
          };
          iframe.postMessage({ type: `nmr-wrapper:load`, data }, "*");
        } else {
          console.log("Loading spectra from object");
          let nmrium_info = JSON.parse(this.selectedDataset.nmrium_info);
          let mols = JSON.parse(nmrium_info.molecules);
          if (mols) {
            this.currentMolecules = mols;
          }
          mols.forEach((mol) => {
            mol.molfile = "\n" + mol.molfile + "\n";
          });
          let spectra = JSON.parse(nmrium_info.spectra);
          this.selectedSpectraData = spectra;
          let data = {
            data: {
              spectra: [spectra],
              molecules: mols,
            },
            type: "nmrium",
          };
          iframe.postMessage({ type: `nmr-wrapper:load`, data }, "*");
        }
      }
    },
  },
  computed: {
    currentStep() {
      return this.steps.filter((s) => s.status == "current")[0];
    },
    url() {
      return String(this.$page.props.url);
    },
    nmriumURL() {
      return this.$page.props.nmriumURL
        ? String(this.$page.props.nmriumURL + "?workspace=embedded&id=" + Math.random())
        : "http://nmriumdev.nmrxiv.org?workspace=embedded&id=" + Math.random();
    },
  },
};
</script>
