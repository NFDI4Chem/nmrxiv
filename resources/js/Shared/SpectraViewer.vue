<template>
    <div>
        <div v-if="spectraError" role="status">
            <div
                class="rounded-md border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-950/50"
            >
                <div class="flex">
                    <div class="flex-shrink-0">
                        <ExclamationTriangleIcon
                            class="h-5 w-5 text-amber-500 dark:text-amber-400"
                            aria-hidden="true"
                        />
                    </div>
                    <div class="ml-3">
                        <h3
                            class="text-sm font-medium text-amber-900 dark:text-amber-100"
                        >
                            Some spectra loaded with warnings
                        </h3>
                        <div
                            class="mt-2 text-sm text-amber-800 dark:text-amber-200"
                        >
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
        <div class="mb-2 clearfix">
            <DefaultSpectrumTabSelect
                v-if="!viewerLoading"
                @changed="onDefaultSpectrumTabChanged"
            />
        </div>
        <iframe
            name="NMRiumIframe"
            frameborder="0"
            allowfullscreen
            class="rounded-md border"
            :style="'width: ' + width + '%; height: ' + height + 'px;'"
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
                            v-html="sanitizeHtml(molecule.svg)"
                        ></div>
                    </div>
                    <div v-else>
                        <div
                            class="rounded-md border my-3 flex justify-center items-center"
                        >
                            <span
                                v-html="sanitizeHtml(loadMol(molecule.molfile))"
                            ></span>
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
            <div v-if="selectedSpectraData" class="p-1 pr-2">
                <span
                    v-if="
                        selectedSpectraData[0]['peaks'] &&
                        selectedSpectraData[0]['peaks']['values'].length
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
                                    v-for="(
                                        peak, $index
                                    ) in selectedSpectraData[0]['peaks'][
                                        'values'
                                    ]"
                                    :key="$index"
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
                        selectedSpectraData[0]['ranges']['values'].length
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
                                    v-for="(
                                        range, $index
                                    ) in selectedSpectraData[0]['ranges'][
                                        'values'
                                    ]"
                                    :key="$index"
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
    </div>
</template>

<script>
import { ExclamationTriangleIcon } from "@heroicons/vue/24/outline";
import DefaultSpectrumTabSelect from "./DefaultSpectrumTabSelect.vue";
import {
    getDefaultSpectrumTab,
    postNmriumLoad,
    requestSelectTab,
    resolveNmriumTargetOrigin,
} from "@/Utils/nmriumTabPreference.js";

export default {
    components: {
        ExclamationTriangleIcon,
        DefaultSpectrumTabSelect,
    },
    props: {
        dataset: {
            type: Object,
            default: null,
        },
        project: {
            type: Object,
            default: null,
        },
        study: {
            type: Object,
            default: null,
        },
        height: {
            type: Number,
            default: 600,
        },
        width: {
            type: Number,
            default: 100,
        },
    },
    data() {
        return {
            spectraError: null,
            selectedSpectraData: null,
            currentMolecules: [],
            info: null,
            defaultTabApplied: false,
            nmriumMessageHandler: null,
            viewerLoading: false,
        };
    },
    computed: {
        url() {
            return String(this.$page.props.url);
        },
        nmriumURL() {
            const raw = this.$page.props.nmriumURL;
            if (!raw) {
                return (
                    "https://nmriumdev.nmrxiv.org?defaultEmptyMessage=''&workspace=embedded&id=" +
                    Math.random()
                );
            }
            const base = String(raw);
            const sep = base.includes("?") ? "&" : "?";

            return `${base}${sep}id=${Math.random()}`;
        },
    },
    watch: {
        // dataset: {
        //     immediate: true,
        //     handler() {
        //        this.loadSpectra();
        //     },
        // },
    },
    mounted() {
        this.attachNmriumMessageListener();
    },
    beforeUnmount() {
        this.detachNmriumMessageListener();
    },
    methods: {
        attachNmriumMessageListener() {
            if (this.nmriumMessageHandler) {
                return;
            }

            this.nmriumMessageHandler = (event) => {
                this.handleNmriumWindowMessage(event);
            };
            window.addEventListener("message", this.nmriumMessageHandler);
        },
        detachNmriumMessageListener() {
            if (!this.nmriumMessageHandler) {
                return;
            }

            window.removeEventListener("message", this.nmriumMessageHandler);
            this.nmriumMessageHandler = null;
        },
        isAllowedNmriumOrigin(origin) {
            const allowed = [
                resolveNmriumTargetOrigin(this.$page.props.nmriumURL),
                "https://nmriumdev.nmrxiv.org",
                "https://nmrium.nmrxiv.org",
            ];

            return allowed.includes(origin);
        },
        handleNmriumWindowMessage(event) {
            const { data, type } = event.data ?? {};

            if (!this.isAllowedNmriumOrigin(event.origin)) {
                return;
            }

            if (type === "nmr-wrapper:error") {
                this.spectraError = data;
                this.updateLoadingStatus(false);

                return;
            }

            if (type !== "nmr-wrapper:data-change" || data?.source !== "data") {
                return;
            }

            const state = data.state;
            const actionType = state?.data?.actionType;

            if (actionType === "INITIATE" && state?.data?.spectra?.length > 0) {
                this.applyDefaultSpectrumTab();
                this.updateLoadingStatus(false);
            }
        },
        nmriumTargetOrigin() {
            return resolveNmriumTargetOrigin(this.$page.props.nmriumURL);
        },
        applyDefaultSpectrumTab() {
            const tab = getDefaultSpectrumTab(this.$page);
            if (!tab || this.defaultTabApplied) {
                return;
            }

            this.defaultTabApplied = true;
            requestSelectTab(
                window.frames.NMRiumIframe,
                tab,
                this.nmriumTargetOrigin()
            );
        },
        onDefaultSpectrumTabChanged() {
            this.defaultTabApplied = false;
            this.applyDefaultSpectrumTab();
        },
        postLoadToIframe(iframe, payload) {
            this.defaultTabApplied = false;
            postNmriumLoad(
                iframe,
                payload,
                this.nmriumTargetOrigin(),
                getDefaultSpectrumTab(this.$page)
            );
        },
        loadSpectra() {
            if (this.study) {
                const iframe = window.frames.NMRiumIframe;
                this.spectraError = null;
                this.currentMolecules = [];
                this.defaultTabApplied = false;
                this.updateLoadingStatus(true);

                if (iframe) {
                    if (this.dataset && this.dataset.has_nmrium) {
                        this.infoLog("Loading Spectra from NMRium JSON..");
                        axios
                            .get("/datasets/" + this.dataset.id + "/nmriumInfo")
                            .then((response) => {
                                let nmrium_info = response.data;
                                if (nmrium_info) {
                                    this.postLoadToIframe(iframe, {
                                        data: nmrium_info,
                                        type: "nmrium",
                                    });
                                } else {
                                    let urls = [];
                                    urls.push(this.study.download_url);
                                    this.loadFromURL(urls);
                                }
                            });
                    } else {
                        if (this.study.has_nmrium) {
                            this.infoLog("Loading Spectra from NMRium JSON..");
                            axios
                                .get(
                                    "/studies/" + this.study.id + "/nmriumInfo"
                                )
                                .then((response) => {
                                    let nmrium_info = response.data;
                                    if (nmrium_info) {
                                        this.postLoadToIframe(iframe, {
                                            data: nmrium_info,
                                            type: "nmrium",
                                        });
                                    } else {
                                        let urls = [];
                                        urls.push(this.study.download_url);
                                        this.loadFromURL(urls);
                                    }
                                });
                        } else {
                            if (this.study.download_url) {
                                let urls = [];
                                urls.push(this.study.download_url);
                                this.loadFromURL(urls);
                            }
                        }
                    }
                }
            }
        },
        loadFromURL(urls) {
            this.infoLog("Loading Spectra from URL..");
            const iframe = window.frames.NMRiumIframe;
            this.postLoadToIframe(iframe, {
                data: urls,
                type: "url",
            });
        },
        loadMol(molFile) {
            let svgString = null;
            let mol = OCL.Molecule.fromMolfile(molFile);
            if (mol.toIsomericSmiles() != "") {
                svgString = mol.toSVG(300, 300);
            }
            return svgString;
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
            this.viewerLoading = status === true;
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
    },
};
</script>
