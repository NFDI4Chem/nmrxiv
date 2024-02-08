<template>
    <div>
        <div>
            <div class="mb-2 float-right">
                <small>
                    <a
                        class="text-xs cursor-pointer hover:text-blue-700 mr-2"
                        href="https://docs.nmrxiv.org/advanced-guides/nmrium/nmrium.html"
                        target="_blank"
                        >Learn more
                    </a>
                    <a
                        class="cursor-pointer mr-3 border px-2 py-1 rounded-md"
                        @click="resetStudy"
                        >Reset</a
                    >
                    <a
                        class="cursor-pointer border px-2 py-1 rounded-md"
                        @click="exportPreview()"
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
        <iframe
            name="submissionNMRiumIframe"
            frameborder="0"
            allowfullscreen
            class="rounded-md border"
            style="width: 100%; height: 75vh; max-height: 600px"
            :src="nmriumURL"
            @load="loadSpectra()"
        ></iframe>
        <div class="mt-4" v-if="spectraError && spectraError.length > 0">
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
                                <li v-for="error in spectraError">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
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
    emits: ["loading"],
    setup() {
        const versionsElement = ref(null);
        return {
            versionsElement,
        };
    },
    data() {
        return {
            spectraError: [],
            selectedSpectraData: null,
            autoSaving: false,
            currentMolecules: [],
            reset: false,
            resetInProgress: false,
            info: null,
            version: null,
            eventRegistered: false,
        };
    },
    computed: {
        url() {
            return String(this.$page.props.url)
                ? String(this.$page.props.url)
                : "https://dev.nmrxiv.org";
        },
        nmriumURL() {
            return this.$page.props.nmriumURL
                ? String(this.$page.props.nmriumURL + "&id=" + Math.random())
                : "https://nmriumdev.nmrxiv.org?defaultEmptyMessage=''&workspace=embedded&id=" +
                      Math.random();
        },
        mailFromAddress() {
            return String(this.$page.props.mailFromAddress);
        },
    },
    watch: {
        // dataset: {
        //     immediate: true,
        //     handler() {
        //         this.loadSpectra();
        //     },
        // },
        study: {
            immediate: true,
            handler() {
                this.loadSpectra();
            },
        },
    },
    methods: {
        registerEvents() {
            const saveNMRiumUpdates = (e) => {
                const { data, type } = e.data;
                if (type == "nmr-wrapper:action-response") {
                    if (
                        e.origin != "https://nmriumdev.nmrxiv.org" &&
                        e.origin != "https://nmrium.nmrxiv.org"
                    ) {
                        return;
                    }
                    let actionType = data.type;
                    if (actionType == "exportSpectraViewerAsBlob") {
                        this.saveStudyPreview(data.data);
                    }
                }
                if (type == "nmr-wrapper:error") {
                    if (this.spectraError) {
                        this.spectraError.push(data);
                    } else {
                        this.spectraError = [data];
                    }
                    this.updateLoadingStatus(false);
                    return;
                }
                if (data && data.source == "data") {
                    if (
                        e.origin != "https://nmriumdev.nmrxiv.org" &&
                        e.origin != "https://nmrium.nmrxiv.org"
                    ) {
                        return;
                    }
                    let state = data.state;
                    this.version = state.version;
                    let actionType = state.data.actionType;

                    if (type == "nmr-wrapper:data-change") {
                        if (state.data.spectra.length > 0) {
                            this.selectedSpectraData = state.data;
                            if (actionType == "INITIATE") {
                                if (
                                    !this.study.has_nmrium ||
                                    this.resetInProgress
                                ) {
                                    delete this.selectedSpectraData[
                                        "actionType"
                                    ];
                                    this.selectedSpectraData.spectra.forEach(
                                        (spec) => {
                                            spec.info = spec["originalData"];
                                            delete spec["data"];
                                            delete spec["meta"];
                                            delete spec["originalData"];
                                            delete spec["originalInfo"];
                                        }
                                    );
                                    if (this.study.study_photo_url == "") {
                                        setTimeout(
                                            function () {
                                                this.exportPreview();
                                            }.bind(this),
                                            500
                                        );
                                    }
                                    this.resetInProgress = false;
                                    this.updateStudyNMRiumInfo();
                                    return;
                                } else {
                                    this.infoLog(
                                        "Spectra loaded successfully...",
                                        true
                                    );
                                    this.updateLoadingStatus(false);
                                    return;
                                }
                            } else {
                                this.updateLoadingStatus(
                                    true,
                                    "Saving updates: " + this.study.name
                                );
                                delete this.selectedSpectraData["actionType"];
                                this.selectedSpectraData.spectra.forEach(
                                    (spec) => {
                                        delete spec["data"];
                                        delete spec["meta"];
                                        delete spec["originalData"];
                                        delete spec["originalInfo"];
                                    }
                                );
                                this.updateStudyNMRiumInfo();
                                return;
                            }
                        }
                    }
                }
            };
            if (!this.eventRegistered) {
                console.log("events registered");
                window.addEventListener("message", saveNMRiumUpdates);
                this.eventRegistered = true;
            }
            // else {
            //     window.removeEventListener("message", saveNMRiumUpdates);
            //     this.$props.eventRegistered = false;
            // }
        },
        loadSpectra() {
            this.infoLog("Loading Spectra..");
            const iframe = window.frames.submissionNMRiumIframe;
            this.spectraError = null;
            this.currentMolecules = [];
            let username = this.$page.props.team.owner
                ? this.$page.props.team.owner.username
                : this.project.owner.username;
            if (iframe) {
                this.updateLoadingStatus(
                    true,
                    "Loading spectra: " + this.study.name
                );
                if (this.study && this.study.has_nmrium) {
                    this.infoLog("Loading Spectra from NMRium JSON..");
                    axios
                        .get(
                            "/dashboard/studies/" +
                                this.study.id +
                                "/nmriumInfo"
                        )
                        .then((response) => {
                            let nmrium_info = response.data;
                            if (nmrium_info) {
                                let data = {
                                    data: nmrium_info,
                                    type: "nmrium",
                                };
                                iframe.postMessage(
                                    { type: `nmr-wrapper:load`, data },
                                    "*"
                                );
                                this.updateMolecularData(
                                    nmrium_info.data.molecules
                                );
                            } else {
                                this.loadFromURLs(null);
                            }
                        });
                } else {
                    if (this.study.datasets.length > 0) {
                        let urls = [];
                        let url = null;
                        if (this.study.download_url) {
                            url = this.study.download_url;
                        } else {
                            url =
                                this.url +
                                "/" +
                                username +
                                "/datasets/" +
                                this.project.slug +
                                "/" +
                                this.study.slug;
                        }
                        urls.push(url);
                        this.loadFromURLs(urls);
                    }
                }
            }
        },
        loadFromURLs(urls) {
            this.infoLog("Loading Spectra from URL..");
            const iframe = window.frames.submissionNMRiumIframe;
            if (!urls) {
                urls = [];
                let url = null;
                if (this.study.download_url) {
                    url = this.study.download_url;
                } else {
                    let username = this.$page.props.team.owner
                        ? this.$page.props.team.owner.username
                        : this.project.owner.username;
                    url =
                        this.url +
                        "/" +
                        username +
                        "/datasets/" +
                        this.project.slug +
                        "/" +
                        this.study.slug;
                    urls.push(url);
                }
                urls.push(url);
                this.loadFromURLs(urls);
            }

            let data = {
                data: urls,
                type: "url",
            };

            iframe.postMessage({ type: `nmr-wrapper:load`, data: data }, "*");
        },
        updateStudyNMRiumInfo() {
            if (this.study != null && this.selectedSpectraData) {
                let _version = this.version ? this.version : 4;
                axios
                    .post(
                        "/dashboard/studies/" + this.study.id + "/nmriumInfo",
                        {
                            version: _version,
                            data: this.selectedSpectraData,
                        }
                    )
                    .then((response) => {
                        this.infoLog("Spectra saved successfully", true);
                        this.updateLoadingStatus(false);
                        this.autoSaving = false;
                        this.study.has_nmrium = response.data.has_nmrium;
                        this.updateMolecularData(
                            this.selectedSpectraData.molecules
                        );
                    })
                    .catch(() => {
                        this.updateLoadingStatus(false);
                        this.infoLog("Error saving spectra info");
                        console.error(
                            "Error saving the nmrium info. Please contact us at " +
                                this.mailFromAddress +
                                " if the error persist."
                        );
                        this.autoSaving = false;
                    });
            }
        },
        fixLineError(mol) {
            let lineNumber = mol.molfile
                .substring(0, mol.molfile.indexOf("V2000"))
                .split("\n").length;
            if (lineNumber == 3) {
                mol.molfile = "\n" + mol.molfile;
            }
            return mol;
        },
        updateMolecularData(molecules) {
            if (molecules.length > 0) {
                molecules.forEach((mol) => {
                    mol = this.fixLineError(mol);
                    axios
                        .post(
                            "https://api.naturalproducts.net/latest/chem/standardize",
                            mol.molfile
                        )
                        .then((res) => {
                            let _mol = res.data;
                            axios
                                .post(
                                    "/dashboard/studies/" +
                                        this.study.id +
                                        "/molecule",
                                    {
                                        InChI: _mol.inchi,
                                        InChIKey: _mol.inchikey,
                                        percentage: 0,
                                        mol: _mol.standardized_mol,
                                        canonical_smiles: _mol.canonical_smiles,
                                    }
                                )
                                .then((res) => {
                                    this.study.sample.molecules = res.data;
                                });
                        });
                });
            }
        },
        updateDataSet() {
            if (this.dataset != null && this.selectedSpectraData.length > 0) {
                axios
                    .post(
                        "/dashboard/datasets/" +
                            this.dataset.id +
                            "/nmriumInfo",
                        {
                            spectra: this.selectedSpectraData,
                            molecules: this.currentMolecules,
                            version: this.version || 4,
                        }
                    )
                    .then((response) => {
                        this.infoLog("Spectra saved successfully", true);
                        // this.updateLoadingStatus(false);
                        this.autoSaving = false;
                        this.dataset.has_nmrium = response.data.has_nmrium;
                    })
                    .catch((err) => {
                        // this.updateLoadingStatus(false);
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
            if (this.study) {
                const reader = new FileReader();
                reader.addEventListener("loadend", () => {
                    let svg = reader.result;
                    axios
                        .post(
                            "/dashboard/studies/" + this.study.id + "/snapshot",
                            {
                                img: svg,
                            }
                        )
                        .then((response) => {
                            this.infoLog("Saved preview successfully", true);
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
        updateLoadingStatus(status, message) {
            this.$emit("loading", {
                status: status,
                message: message ? message : "",
            });
        },
        infoLog(message, reset) {
            this.info = message;
            if (reset) {
                setTimeout(() => {
                    this.info = "";
                }, 5000);
            }
        },
        resetStudy() {
            this.resetInProgress = true;
            this.loadFromURLs(null);
        },
        showVersions() {
            this.versionsElement.toggleVersions();
        },
    },
};
</script>
