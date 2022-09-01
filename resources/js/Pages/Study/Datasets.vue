<template>
    <div>
        <study-content
            model="study"
            :project="project"
            :study="study"
            :team="team"
            :members="members"
            :available-roles="availableRoles"
            :study-permissions="studyPermissions"
            :study-role="studyRole"
            current="Datasets"
        >
            <template #study-section>
                <div class="px-4 sm:px-6 md:px-0">
                    <div v-if="selectedDataset && study.is_public" class="p-4">
                        <div class="float-right">
                            <div class="flex-0.5 self-center">
                                <Menu
                                    as="div"
                                    v-if="selectedDataset && study.is_public"
                                    class="relative text-left"
                                >
                                    <div>
                                        <MenuButton
                                            class="bg-white text-sm rounded-full flex items-center text-gray-400 hover:text-gray-600"
                                        >
                                            <ShareIcon
                                                class="h-4 w-4 text-gray-800 flex-shrink-0 mr-2"
                                                aria-hidden="true"
                                            ></ShareIcon
                                            >Share
                                        </MenuButton>
                                    </div>
                                    <transition
                                        enter-active-class="transition ease-out duration-100"
                                        enter-from-class="transform opacity-0 scale-95"
                                        enter-to-class="transform opacity-100 scale-100"
                                        leave-active-class="transition ease-in duration-75"
                                        leave-from-class="transform opacity-100 scale-100"
                                        leave-to-class="transform opacity-0 scale-95"
                                    >
                                        <MenuItems
                                            class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                        >
                                            <div class="py-1">
                                                <MenuItem v-slot="{ active }">
                                                    <div
                                                        :class="[
                                                            active
                                                                ? 'bg-gray-100 text-gray-900'
                                                                : 'text-gray-700',
                                                            'block px-4 py-2 text-sm flex',
                                                        ]"
                                                    >
                                                        <div class="flex-grow">
                                                            <input
                                                                id="datasetPublicURLCopy"
                                                                readonly
                                                                type="text"
                                                                :value="
                                                                    shareURL
                                                                "
                                                                class="rounded-l-md focus:ring-gray-500 focus:border-gray-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                                                                @focus="
                                                                    $event.target.select()
                                                                "
                                                            />
                                                        </div>
                                                        <button
                                                            type="button"
                                                            class="-ml-px relative inline-flex items-center space-x-2 px-2 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500"
                                                            @click="
                                                                copyToClipboard(
                                                                    shareURL,
                                                                    'datasetPublicURLCopy'
                                                                )
                                                            "
                                                        >
                                                            <span
                                                                ><ClipboardCopyIcon
                                                                    class="h-5 w-5"
                                                                    aria-hidden="true"
                                                            /></span>
                                                        </button>
                                                    </div>
                                                </MenuItem>
                                            </div>
                                        </MenuItems>
                                    </transition>
                                </Menu>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div>
                            <label
                                for="location"
                                class="block text-sm font-medium text-gray-700"
                                >Select Experiment
                                <small
                                    ><span v-if="autoSaving" class="float-right"
                                        >Saving...</span
                                    ></small
                                >
                            </label>
                            <select
                                id="location"
                                v-model="selectedDataset"
                                name="location"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm rounded-md"
                                @change="loadSpectra"
                            >
                                <option
                                    v-for="dataset in study.datasets.sort(
                                        (a, b) => (a.name > b.name ? 1 : -1)
                                    )"
                                    :key="dataset.slug"
                                    :value="dataset"
                                >
                                    {{ dataset.name }}
                                </option>
                            </select>
                        </div>
                        <div class="text-sm my-2">
                            <button
                                class="cursort-pointer"
                                @click.stop="loadFromURL()"
                            >
                                Reset
                            </button>
                        </div>
                        <div v-if="spectraError">
                            <div class="rounded-md bg-red-50 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg
                                            class="h-5 w-5 text-red-400"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                            aria-hidden="true"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3
                                            class="text-sm font-medium text-red-800"
                                        >
                                            There were errors loading spectra
                                        </h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <ul
                                                role="list"
                                                class="list-disc space-y-1 pl-5"
                                            >
                                                <li>{{ spectraError }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="my-7">
                            <iframe
                                name="datasetNMRiumIframe"
                                frameborder="0"
                                allowfullscreen
                                class="rounded-md border"
                                style="
                                    width: 100%;
                                    height: 75vh;
                                    max-height: 600px;
                                "
                                :src="nmriumURL"
                                @load="loadSpectra()"
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
                    class="mt-2 block text-sm font-medium truncate text-gray-900 truncate pointer-events-none"
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
                                <label
                                    for="about"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    License
                                </label>
                                <div class="p-1 pr-2 grid grid-cols-2">
                                    <div>
                                        <loading-button :loading="loading" />
                                        <input
                                            v-if="!loading"
                                            id="study-name"
                                            v-model="license"
                                            readonly
                                            type="text"
                                            name="study-name"
                                            class="block w-full shadow-sm sm:text-sm focus:ring-gray-500 focus:border-gray-500 border-gray-300 rounded-md"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2">
                            <div class="p-1 pr-2">
                                <label
                                    v-if="selectedSpectraData"
                                    for="location"
                                    class="block text-sm font-medium text-gray-700"
                                    >Info</label
                                >
                                <div
                                    v-if="selectedSpectraData"
                                    class="overflow-hidden ring-1 mt-3 ring-black ring-opacity-5 md:rounded-lg"
                                >
                                    <table
                                        v-for="spectra in selectedSpectraData"
                                        :key="spectra.id"
                                        class="min-w-full border divide-y divide-gray-300"
                                    >
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    scope="col"
                                                    colspan="2"
                                                    class="py-3.5 pl-4 pr-3 text-left text-sm border-b font-semibold text-gray-900 sm:pl-6 lg:pl-8"
                                                >
                                                    Spectra Id ::
                                                    {{ spectra.id }}
                                                </th>
                                            </tr>
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
                                        <tbody
                                            class="divide-y divide-gray-200 bg-white"
                                        >
                                            <tr
                                                v-for="key in Object.keys(
                                                    spectra.info
                                                )"
                                                :key="key"
                                            >
                                                <td
                                                    class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 lg:pl-8"
                                                >
                                                    {{ key }}
                                                </td>
                                                <td
                                                    class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                                                >
                                                    {{ spectra.info[key] }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- <div class="p-1 pr-2">
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
                                                        id="about"
                                                        readonly
                                                        name="about"
                                                        rows="3"
                                                        class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                                                    ></textarea>
                                                </div>
                                                <p
                                                    class="mt-2 text-sm text-gray-500"
                                                >
                                                    Describe your extraction
                                                    protocol. Protocol
                                                    parameters can be provided
                                                    below.
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <label
                                                for="about"
                                                class="block text-sm font-medium text-gray-700"
                                            >
                                                NMR Sample preparation
                                            </label>
                                            <div class="mt-1">
                                                <textarea
                                                    id="about"
                                                    readonly
                                                    name="about"
                                                    rows="3"
                                                    class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                                                ></textarea>
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-gray-500"
                                            >
                                                Describe NMR sample preparation
                                                protocol details. Protocol
                                                parameters can be provided
                                                below.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div>
                                            <label
                                                for="about"
                                                class="block text-sm font-medium text-gray-700"
                                            >
                                                Data transformation
                                            </label>
                                            <div class="mt-1">
                                                <textarea
                                                    id="about"
                                                    readonly
                                                    name="about"
                                                    rows="3"
                                                    class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                                                ></textarea>
                                            </div>
                                            <p
                                                class="mt-2 text-sm text-gray-500"
                                            >
                                                Describe any data transformation
                                                protocol performed. Protocol
                                                parameters can be provided
                                                below.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
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
import { ShareIcon, ClipboardCopyIcon } from "@heroicons/vue/solid";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";

export default {
    components: {
        StudyContent,
        LoadingButton,
        ShareIcon,
        ClipboardCopyIcon,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
    },
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
    data() {
        return {
            autoSaving: false,
            selectedDataset: null,
            currentMolecules: [],
            selectedSpectraData: null,
            license: "No license selected",
            loading: false,
            spectraError: null,
        };
    },
    computed: {
        currentStep() {
            return this.steps.filter((s) => s.status == "current")[0];
        },
        url() {
            return String(this.$page.props.url);
        },
        shareURL() {
            return (
                this.url +
                "/projects/" +
                this.team.owner.username +
                "/" +
                this.project.slug +
                "?tab=study&id=" +
                this.study.slug +
                "&dsid=" +
                this.selectedDataset.slug
            );
        },
        nmriumURL() {
            return this.$page.props.nmriumURL
                ? String(
                      this.$page.props.nmriumURL +
                          "?workspace=embedded&id=" +
                          Math.random()
                  )
                : "http://nmriumdev.nmrxiv.org?workspace=embedded&id=" +
                      Math.random();
        },
    },
    mounted() {
        const saveNMRiumUpdates = (e) => {
            if (
                e.origin != "https://nmriumdev.nmrxiv.org" &&
                e.origin != "https://nmrium.nmrxiv.org"
            ) {
                return;
            }
            if (e.data.type == "nmr-wrapper:action-response") {
                let actionType = e.data.data.type;
                if (actionType == "exportSpectraViewerAsBlob") {
                    this.saveStudyPreview(e.data.data.data);
                }
            }
            if (e.data.type == "nmr-wrapper:error") {
                this.spectraError = e.data.data;
                return;
            }
            if (e.data.type == "nmr-wrapper:data-change") {
                let actionType = e.data.data.actionType;
                if (
                    actionType == "" ||
                    (actionType == "INITIATE" &&
                        this.selectedDataset &&
                        this.selectedDataset.type != null)
                ) {
                    if (this.selectedSpectraData == null) {
                        this.selectedSpectraData = e.data.data.spectra;
                    }
                    return;
                }

                this.selectedSpectraData = e.data.data.spectra;
                if (
                    actionType == "ADD_MOLECULE" ||
                    actionType == "DELETE_MOLECULE" ||
                    e.data.data.molecules
                ) {
                    this.currentMolecules = e.data.data.molecules;
                }

                if (this.study && this.selectedDataset) {
                    if (this.selectedDataset.dataset_photo_url == "") {
                        console.info("Saving spectra preview");
                        let iframe = window.frames.datasetNMRiumIframe;
                        if (iframe) {
                            let data = {
                                type: "exportSpectraViewerAsBlob",
                            };
                            iframe.postMessage(
                                {
                                    type: `nmr-wrapper:action-request`,
                                    data,
                                },
                                "*"
                            );
                        }
                    }
                    this.updateDataSet();
                }
            }
        };

        if (!this.$page.props.dsEventRegistered) {
            console.log("registering");
            window.addEventListener("message", saveNMRiumUpdates);
            this.$page.props.dsEventRegistered = true;
        }
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
    methods: {
        saveStudyPreview(data) {
            const reader = new FileReader();
            reader.addEventListener("loadend", () => {
                let svg = reader.result;
                axios.post(
                    "/dashboard/datasets/" +
                        this.selectedDataset.id +
                        "/preview",
                    {
                        img: svg,
                    }
                );
            });
            reader.readAsText(data.blob);
        },
        updateDataSet() {
            if (this.selectedDataset) {
                if (this.selectedSpectraData != null) {
                    this.autoSaving = true;
                    axios
                        .post(
                            "/dashboard/datasets/" +
                                this.selectedDataset.id +
                                "/nmriumInfo",
                            {
                                spectra: this.selectedSpectraData,
                                molecules: this.currentMolecules,
                            }
                        )
                        .catch((error) => {
                            console.log(error.data);
                        })
                        .then((response) => {
                            let i = 0;
                            this.study.datasets.forEach((ds) => {
                                if (ds.id == response.data.id) {
                                    this.study.datasets[i] = response.data;
                                    this.selectedDataset =
                                        this.study.datasets[i];
                                    let nmrium_info = JSON.parse(
                                        this.selectedDataset.nmrium_info
                                    );
                                    if (nmrium_info) {
                                        this.selectedSpectraData =
                                            nmrium_info["spectra"];
                                    }
                                }
                                i = i + 1;
                            });
                            this.autoSaving = false;
                        });
                }
            }
        },
        loadSpectra() {
            if (this.selectedDataset == null) {
                this.selectedDataset = this.study.datasets[0];
            }
            this.spectraError = null;
            this.currentMolecules = [];
            const iframe = window.frames.datasetNMRiumIframe;
            if (iframe) {
                let spectra = [];
                let nmrium_info = null;
                this.selectedSpectraData = null;

                if (
                    this.selectedDataset &&
                    this.selectedDataset.nmrium_info &&
                    this.selectedDataset.nmrium_info != ""
                ) {
                    nmrium_info = JSON.parse(this.selectedDataset.nmrium_info);
                }

                if (nmrium_info && nmrium_info["spectra"]) {
                    if (this.isString(nmrium_info["spectra"])) {
                        spectra = JSON.parse(nmrium_info.spectra);
                    } else {
                        spectra = nmrium_info.spectra;
                    }
                }

                if (spectra && spectra.length <= 0) {
                    this.loadFromURL();
                } else {
                    let mols = [];
                    if (this.isString(nmrium_info.molecules)) {
                        mols = JSON.parse(nmrium_info.molecules);
                    } else {
                        mols = nmrium_info.molecules;
                    }

                    if (mols && mols.length > 0) {
                        this.currentMolecules = mols;
                        mols.forEach((mol) => {
                            mol.molfile = "\n" + mol.molfile + "\n";
                        });
                    }

                    let data = {
                        data: {
                            spectra: spectra,
                            molecules: mols,
                        },
                        type: "nmrium",
                    };
                    iframe.postMessage({ type: `nmr-wrapper:load`, data }, "*");
                    this.selectedSpectraData = spectra;
                }
            }
        },
        loadFromURL(url) {
            this.selectedDataset.type = null;
            this.selectedSpectraData = null;
            const iframe = window.frames.datasetNMRiumIframe;
            if (!url) {
                url =
                    this.url +
                    "/" +
                    this.team.owner.username +
                    "/datasets/" +
                    this.project.slug +
                    "/" +
                    this.study.slug +
                    "/" +
                    this.selectedDataset.slug;
            }
            let data = {
                data: [url],
                type: "url",
            };
            iframe.postMessage({ type: `nmr-wrapper:load`, data }, "*");
        },
    },
};
</script>
