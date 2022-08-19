<template>
    <project-layout :project="project" :selectedTab="tab">
        <template #project-content>
            <div
                class="pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
            >
                <h2 class="mt-2 text-xl font-bold break-words text-gray-900">
                    STUDY:
                    <span class="text-blue-500">
                        {{ study.name }}
                    </span>
                </h2>
                <div class="mt-4">
                    <div class="relative">
                        <div
                            class="absolute inset-0 flex items-center"
                            aria-hidden="true"
                        >
                            <div class="w-full border-t border-gray-100"></div>
                        </div>
                        <div class="relative flex items-center justify-between">
                            <span
                                class="pr-3 text-md bg-white font-medium text-gray-400"
                            >
                                Description
                            </span>
                        </div>
                    </div>
                    <div class="prose" v-html="md(study.description)"></div>
                </div>
                <div class="mt-4">
                    <p>
                        <span
                            v-for="tag in study.tags"
                            :key="tag.id"
                            class="mr-2"
                        >
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-indigo-100 text-indigo-800"
                            >
                                <svg
                                    class="-ml-0.5 mr-1.5 h-2 w-2 text-indigo-400"
                                    fill="currentColor"
                                    viewBox="0 0 8 8"
                                >
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                {{ tag.name["en"] }}
                            </span>
                        </span>
                    </p>
                </div>
                <div class="mt-4">
                    <div class="relative">
                        <div
                            class="absolute inset-0 flex items-center"
                            aria-hidden="true"
                        >
                            <div class="w-full border-t border-gray-100"></div>
                        </div>
                        <div class="relative flex items-center justify-between">
                            <span
                                class="pr-3 text-md bg-white font-medium text-gray-400"
                            >
                                Datasets
                            </span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label
                            for="location"
                            class="block text-sm font-medium text-gray-700"
                            >Select
                        </label>
                        <select
                            id="location"
                            v-model="selectedDataset"
                            name="location"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm rounded-md"
                            @change="loadSpectra"
                        >
                            <option
                                v-for="dataset in study.datasets.sort((a, b) =>
                                    a.name > b.name ? 1 : -1
                                )"
                                :key="dataset.slug"
                                :value="dataset"
                            >
                                {{ dataset.name }}
                            </option>
                        </select>
                    </div>
                    <div class="my-7">
                        <iframe
                            name="datasetNMRiumPublicIframe"
                            frameborder="0"
                            allowfullscreen
                            class="rounded-md border"
                            style="width: 100%; height: 75vh; max-height: 600px"
                            :src="nmriumURL"
                            @load="loadSpectra()"
                        ></iframe>
                    </div>
                </div>
                <div class="grid grid-cols-2">
                    <div class="p-1 pr-2">
                        <label
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
                                <tbody
                                    class="divide-y divide-gray-200 bg-white"
                                >
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
                </div>
            </div>
        </template>
    </project-layout>
</template>

<script>
import ProjectLayout from "@/Pages/Public/Project/Layout.vue";

export default {
    components: {
        ProjectLayout,
    },
    props: ["project", "tab", "study"],
    data() {
        return {
            selectedDataset: null,
            selectedSpectraData: null,
        };
    },
    computed: {
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
        url() {
            return String(this.$page.props.url);
        },
    },
    mounted() {},
    methods: {
        loadSpectra() {
            this.selectedSpectraData = null;
            if (this.selectedDataset == null) {
                const urlSearchParams = new URLSearchParams(
                    window.location.search
                );
                const params = Object.fromEntries(urlSearchParams.entries());
                let dsId = params["dsid"];
                this.study.datasets.forEach((ds) => {
                    if (ds.slug == dsId) {
                        this.selectedDataset = ds;
                    }
                });
                if (!this.selectedDataset) {
                    this.selectedDataset = this.study.datasets[0];
                }
            }
            const iframe = window.frames.datasetNMRiumPublicIframe;
            this.currentMolecules = [];
            if (iframe) {
                if (!this.selectedDataset.nmrium_info) {
                    let data = {
                        data: [
                            this.url +
                                "/" +
                                this.project.data.owner.username +
                                "/datasets/" +
                                this.project.data.slug +
                                "/" +
                                this.study.slug +
                                "/" +
                                this.selectedDataset.slug,
                        ],
                        type: "url",
                    };
                    iframe.postMessage({ type: `nmr-wrapper:load`, data }, "*");
                } else {
                    let nmrium_info = this.parseJSON(this.selectedDataset.nmrium_info)
                    let mols = []
                    if(nmrium_info.molecules){
                        let mols = this.parseJSON(nmrium_info.molecules);
                        if (mols) {
                            this.currentMolecules = mols;
                            mols.forEach((mol) => {
                                mol.molfile = "\n" + mol.molfile + "\n";
                            });
                        }
                    }
                    let spectra = nmrium_info.spectra;
                    this.selectedSpectraData = spectra;
                    let data = {
                        data: {
                            spectra: spectra,
                            molecules: mols,
                        },
                        type: "nmrium",
                    };
                    iframe.postMessage({ type: `nmr-wrapper:load`, data }, "*");
                }
            }
        },
    },
};
</script>
