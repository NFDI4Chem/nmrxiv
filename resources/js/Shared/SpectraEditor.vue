<template>
    <div>
        <div>
            <div class="mb-2 float-right">
                <small>
                    <a
                        class="text-xs cursor-pointer hover:text-blue-700 mr-2"
                        href="https://docs.nmrxiv.org/docs/submission-guides/data-model/sharing"
                        target="_blank"
                        >Learn more
                    </a>
                    <a
                        @click="loadFromURL(null)"
                        class="cursor-pointer mr-3 border px-2 py-1 rounded-md"
                        >Reset</a
                    >
                    <a
                        @click="showVersions"
                        class="cursor-pointer mr-3 border px-2 py-1 rounded-md"
                        >Edit History</a
                    >

                    <a
                        @click="exportPreview()"
                        class="cursor-pointer border px-2 py-1 rounded-md"
                    >
                        <ArrowPathIcon class="w-4 h-4 inline" />
                        Preview</a
                    >
                </small>
            </div>
            <small class="text-gray-400">
                <span v-if="info">{{ info }}</span>
            </small>
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
                        <h3 class="text-sm font-medium text-red-800">
                            There were errors loading spectra
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul role="list" class="list-disc space-y-1 pl-5">
                                <li>
                                    {{ spectraError }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <iframe
            name="submissionNMRiumIframe"
            frameborder="0"
            allowfullscreen
            class="rounded-md border"
            style="width: 100%; height: 75vh; max-height: 600px"
            :src="nmriumURL"
            @load="loadSpectra()"
        ></iframe>
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
                        v-if="molecule.svg"
                        class="group flex justify-center block w-full aspect-w-10 aspect-h-7 rounded-lg bg-gray-100 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-offset-gray-100 focus-within:ring-teal-500 overflow-hidden"
                    >
                        <div
                            class="p-4 object-cover pointer-events-none group-hover:opacity-75"
                            v-html="molecule.svg"
                        ></div>
                    </div>
                    <div v-else>
                        <div
                            class="rounded-md border my-3 flex justify-center items-center"
                        >
                            <span v-html="loadMol(molecule.molfile)"></span>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="grid grid-cols-2 mt-3">
            <div v-if="selectedSpectraData" class="p-1 pr-2">
                <label
                    id="tour-step-spectra-info"
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
                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-bold text-blue-900 sm:pl-6 lg:pl-8"
                                >
                                    Spectra ::
                                    {{ spectra.id }}
                                </th>
                            </tr>
                        </thead>
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
                            <tr
                                v-for="key in Object.keys(spectra.info)"
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
                            <tr>
                                <td
                                    class="whitespace-nowrap py-2 pl-2 pr-3 text-sm font-medium text-gray-900 sm:pl-6 bg-gray-100 lg:pl-8"
                                    colspan="2"
                                >
                                    Meta
                                </td>
                            </tr>
                            <tr
                                v-for="key in Object.keys(spectra.meta)"
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
                                    {{ spectra.meta[key] }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div
                v-if="selectedSpectraData && selectedSpectraData.length > 0"
                class="p-1 pr-2"
            >
                <span
                    v-if="
                        selectedSpectraData[0]['peaks'] &&
                        selectedSpectraData[0]['peaks']['values'].length > 0
                    "
                >
                    <label
                        id="tour-step-spectra-info"
                        for="location"
                        class="block text-sm font-medium text-gray-700"
                        >Peaks</label
                    >
                    <div
                        class="overflow-hidden shadow mt-3 ring-1 ring-black ring-opacity-5 md:rounded-lg"
                    >
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6"
                                    >
                                        #
                                    </th>
                                    <th
                                        scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6"
                                    >
                                        δ (ppm)
                                    </th>
                                    <th
                                        scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6"
                                    >
                                        Intensity
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <tr
                                    :key="$index"
                                    v-for="(
                                        peak, $index
                                    ) in selectedSpectraData[0]['peaks'][
                                        'values'
                                    ]"
                                >
                                    <td
                                        class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6"
                                    >
                                        {{ $index + 1 }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                                    >
                                        {{ peak.x }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                                    >
                                        {{ peak.y }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </span>
                <div>&nbsp;</div>
                <span
                    v-if="
                        selectedSpectraData[0]['ranges'] &&
                        selectedSpectraData[0]['ranges']['values'].length > 0
                    "
                >
                    <label
                        id="tour-step-spectra-info"
                        for="location"
                        class="block text-sm font-medium text-gray-700"
                        >Ranges</label
                    >
                    <div
                        class="overflow-hidden shadow mt-3 ring-1 ring-black ring-opacity-5 md:rounded-lg"
                    >
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6"
                                    >
                                        #
                                    </th>
                                    <th
                                        scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6"
                                    >
                                        δ (ppm)
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <tr
                                    :key="$index"
                                    v-for="(
                                        range, $index
                                    ) in selectedSpectraData[0]['ranges'][
                                        'values'
                                    ]"
                                >
                                    <td
                                        class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6"
                                    >
                                        {{ $index + 1 }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                                    >
                                        {{ range.from }} - {{ range.to }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </span>
            </div>
        </div>
        <Versions ref="versionsElement" :dataset="dataset" />
    </div>
</template>

<script>
import Versions from "./Versions.vue";
import { ArrowPathIcon } from "@heroicons/vue/24/outline";
import { ref } from "vue";

export default {
    components: {
        Versions,
        ArrowPathIcon,
    },
    props: {
        dataset: Object,
        project: Object,
        study: Object,
    },
    watch: {
        dataset: {
            immediate: true,
            handler() {
                this.loadSpectra();
            },
        },
    },
    setup() {
        const versionsElement = ref(null);
        return {
            versionsElement,
        };
    },
    data() {
        return {
            spectraError: null,
            selectedSpectraData: null,
            autoSaving: false,
            currentMolecules: [],
            reset: false,
            info: null,
            version: null,
        };
    },
    computed: {
        url() {
            return String(this.$page.props.url);
        },
        nmriumURL() {
            return this.$page.props.nmriumURL
                ? String(this.$page.props.nmriumURL + "?id=" + Math.random())
                : "http://nmriumdev.nmrxiv.org?id=" + Math.random();
        },

        mailFromAddress() {
            return String(this.$page.props.mailFromAddress);
        },
    },
    methods: {
        registerEvents() {
            const saveNMRiumUpdates = (e) => {
                if (
                    e.origin != "https://nmriumdev.nmrxiv.org" &&
                    e.origin != "https://nmrium.nmrxiv.org"
                ) {
                    return;
                }
                if (e.data.data.type == "nmr-wrapper:error") {
                    this.spectraError = e.data.data;
                    this.updateLoadingStatus(false);
                    return;
                }
                if (e.data.data.type == "nmr-wrapper:action-response") {
                    let actionType = e.data.data.type;
                    if (actionType == "exportSpectraViewerAsBlob") {
                        this.saveStudyPreview(e.data.data.data);
                    }
                }
                this.version = e.data.data.version;
                if (e.data.data.type == "nmr-wrapper:data-change") {
                    let actionType = e.data.data.data.actionType;
                    if (
                        actionType == "" ||
                        (actionType == "INITIATE" &&
                            this.dataset &&
                            this.dataset.has_nmrium &&
                            !this.reset)
                    ) {
                        this.infoLog("Spectra loaded successfully...", true);
                        return;
                    }
                    this.selectedSpectraData = e.data.data.data.spectra;
                    if (
                        actionType == "ADD_MOLECULE" ||
                        actionType == "DELETE_MOLECULE" ||
                        e.data.data.data.molecules
                    ) {
                        this.currentMolecules = e.data.data.data.molecules;
                    }

                    if (this.study && this.dataset) {
                        if (this.dataset.dataset_photo_url == "") {
                            this.infoLog("Saving Preview");
                            setTimeout(
                                function () {
                                    this.exportPreview();
                                }.bind(this),
                                500
                            );
                        }
                        this.updateDataSet();
                    }
                }
            };
            if (!this.$props.eventRegistered) {
                // console.log("registering");
                window.addEventListener("message", saveNMRiumUpdates);
                this.$props.eventRegistered = true;
            } else {
                // console.log("unregistering");
                window.removeEventListener("message", saveNMRiumUpdates);
                this.$props.eventRegistered = false;
            }
        },
        loadSpectra() {
            this.infoLog("Loading Spectra..");
            const iframe = window.frames.submissionNMRiumIframe;
            this.spectraError = null;
            this.currentMolecules = [];
            this.updateLoadingStatus(true);
            let username = this.$page.props.team
                ? this.$page.props.team.owner.username
                : this.project.owner.username;
            let url =
                this.url +
                "/" +
                username +
                "/datasets/" +
                this.project.slug +
                "/" +
                this.study.slug +
                "/" +
                this.dataset.slug;
            if (iframe) {
                let spectra = [];
                let nmrium_info = null;
                if (this.dataset && this.dataset.has_nmrium) {
                    this.infoLog("Loading Spectra from NMRium JSON..");
                    axios
                        .get(
                            "/dashboard/datasets/" +
                                this.dataset.id +
                                "/nmriumInfo"
                        )
                        .then((response) => {
                            this.updateLoadingStatus(false);
                            nmrium_info = JSON.parse(response.data.nmrium_info);
                            if (nmrium_info && nmrium_info["spectra"]) {
                                if (this.isString(nmrium_info["spectra"])) {
                                    spectra = JSON.parse(nmrium_info.spectra);
                                } else {
                                    spectra = nmrium_info.spectra;
                                }
                            }
                            if (spectra && spectra.length <= 0) {
                                this.loadFromURL(url);
                            } else {
                                let nmriumVersion = 3;
                                if (nmrium_info && nmrium_info["version"]) {
                                    nmriumVersion = nmrium_info["version"];
                                }
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
                                        version: nmriumVersion,
                                    },
                                    type: "nmrium",
                                };
                                iframe.postMessage(
                                    { type: `nmr-wrapper:load`, data },
                                    "*"
                                );
                                this.selectedSpectraData = spectra;
                            }
                        });
                } else {
                    this.loadFromURL(url);
                }
            }
        },
        loadFromURL(url) {
            this.infoLog("Loading Spectra from URL..");
            this.dataset.type = null;
            this.selectedSpectraData = null;
            this.reset = true;
            let ownerUserName = this.$page.props.team
                ? this.$page.props.team.owner.username
                : this.project.owner.username;
            const iframe = window.frames.submissionNMRiumIframe;
            if (!url) {
                url =
                    this.url +
                    "/" +
                    ownerUserName +
                    "/datasets/" +
                    this.project.slug +
                    "/" +
                    this.study.slug +
                    "/" +
                    this.dataset.slug;
            }
            let data = {
                data: [url],
                type: "url",
            };
            iframe.postMessage({ type: `nmr-wrapper:load`, data: data }, "*");
        },
        updateDataSet() {
            // console.log("updating dataset");
            if (this.dataset != null && this.selectedSpectraData.length > 0) {
                this.updateLoadingStatus(true);
                axios
                    .post(
                        "/dashboard/datasets/" +
                            this.dataset.id +
                            "/nmriumInfo",
                        {
                            spectra: this.selectedSpectraData,
                            molecules: this.currentMolecules,
                            version: this.version ? this.version : 3,
                        }
                    )
                    .then((response) => {
                        this.infoLog("Spectra saved successfully", true);
                        this.updateLoadingStatus(false);
                        this.autoSaving = false;
                        this.dataset.has_nmrium = response.data.has_nmrium;
                    })
                    .catch((err) => {
                        this.updateLoadingStatus(false);
                        this.infoLog("Error saving spectra info");
                        console.error(
                            "Error saving the nmrium info. Please contact us at {{mailFromAddress}} if the error persist."
                        );
                        this.autoSaving = false;
                    });
            }
        },
        loadMol(molFile) {
            let svgString = null;
            let mol = OCL.Molecule.fromMolfile(molFile);
            if (mol.toIsomericSmiles() != "") {
                svgString = mol.toSVG(300, 300);
            }
            return svgString;
        },
        exportPreview() {
            this.infoLog("Updating Preview");
            const iframe = window.frames.submissionNMRiumIframe;
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
        },
        saveStudyPreview(data) {
            if (this.dataset) {
                this.updateLoadingStatus(true);
                const reader = new FileReader();
                reader.addEventListener("loadend", () => {
                    let svg = reader.result;
                    axios
                        .post(
                            "/dashboard/datasets/" +
                                this.dataset.id +
                                "/preview",
                            {
                                img: svg,
                            }
                        )
                        .then((response) => {
                            this.infoLog("Saved preview successfully", true);
                            this.updateLoadingStatus(false);
                        });
                });
                reader.readAsText(data.blob);
            }
        },
        getSVGString(molecule) {
            if (molecule.MOL) {
                let mol = OCL.Molecule.fromMolfile(
                    "\n  " + molecule.MOL.replaceAll('"', "")
                );
                return mol.toSVG(200, 200);
            }
        },
        updateLoadingStatus(status) {
            this.$emit("loading", status);
        },
        infoLog(message, reset) {
            this.info = message;
            if (reset) {
                setTimeout(() => {
                    this.info = "";
                }, 5000);
            }
        },
        showVersions() {
            this.versionsElement.toggleVersions();
        },
    },
};
</script>
