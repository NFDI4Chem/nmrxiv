<template>
    <project-layout :project="project" :selectedTab="tab">
        <template #project-content>
            <div
                class="pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
            >
                <h1 class="mt-2 text-2xl font-bold break-words text-gray-900">
                    <span class="text-blue-500">
                        {{ study.data.name }}
                    </span>

                    <div class="float-right">
                        <span class="flex-0.5 self-center">
                            <Menu
                                as="div"
                                v-if="dataset && study.data.is_public"
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
                                                            :value="shareURL"
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
                        </span>
                    </div>

                    <div class="text-sm float-right mr-4">
                        <div
                            class="inline hover:text-blue-600 hover:cursor-pointer text-gray-200"
                        >
                            #{{ study.data.identifier }}
                        </div>
                    </div>
                </h1>
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
                    <p
                        style="max-width: 100ch !important"
                        class="prose mt-1 text-sm text-blue-gray-500"
                        v-html="md(study.data.description)"
                    ></p>
                </div>
                <div v-if="study.data.tags.length > 0" class="mt-4">
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
                                Keywords
                            </span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <dd class="mt-1 text-md text-gray-900 space-y-5">
                            <p>
                                <span
                                    v-for="tag in study.data.tags"
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
                        </dd>
                    </div>
                </div>
                <div
                    v-if="
                        study.data.sample.molecules.length > 0 ||
                        study.data.sample.description == ''
                    "
                    class="mt-4"
                >
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
                                Sample
                            </span>
                        </div>
                    </div>
                    <div
                        v-if="study.data.sample.molecules.length > 0"
                        class="mt-3"
                    >
                        <label>Molecular Composition</label>
                        <div class="grid md:grid-cols-2 gap-2 mt-2">
                            <div class="pr-2">
                                <div
                                    v-if="
                                        study.data.sample.molecules.length > 0
                                    "
                                    class="flow-root"
                                >
                                    <ul role="list" class="-mb-8">
                                        <li
                                            v-for="molecule in study.data.sample
                                                .molecules"
                                            :key="molecule.STANDARD_INCHI"
                                        >
                                            <div class="relative pb-8">
                                                <span
                                                    class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200"
                                                    aria-hidden="true"
                                                ></span>
                                                <div
                                                    class="relative flex items-start space-x-3"
                                                >
                                                    <div
                                                        v-if="
                                                            molecule &&
                                                            molecule.pivot
                                                        "
                                                        class="relative"
                                                    >
                                                        <div
                                                            class="rounded-full border p-2 z-10 bg-gray-100 text-sm"
                                                        >
                                                            {{
                                                                molecule.pivot
                                                                    .percentage_composition
                                                            }}%
                                                        </div>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <div>
                                                            <div
                                                                class="text-sm"
                                                            >
                                                                <a
                                                                    class="font-medium text-gray-900"
                                                                    >{{
                                                                        molecule.STANDARD_INCHI
                                                                    }}</a
                                                                >
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="mt-2 text-sm text-gray-700"
                                                        >
                                                            <div
                                                                class="rounded-md border my-3 flex justify-center items-center"
                                                            >
                                                                <span
                                                                    v-html="
                                                                        getSVGString(
                                                                            molecule
                                                                        )
                                                                    "
                                                                ></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                    <div
                                        class="rounded-full border p-2 z-10 bg-gray-100 text-sm mt-14 text-center"
                                    >
                                        Sample chemical composition
                                    </div>
                                </div>
                                <div v-else>
                                    <div class="text-center my-10 py-10">
                                        <h3
                                            class="mt-2 text-sm font-medium text-gray-900"
                                        >
                                            No structures associated with the
                                            sample yet!
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Get started by adding a new
                                            molecule.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                Dataset
                            </span>
                        </div>
                    </div>
                    <h1
                        class="mt-2 text-2xl font-bold break-words text-gray-900"
                    >
                        {{ dataset.data.name }}
                    </h1>
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
                <div class="grid md:grid-cols-2">
                    <div class="p-1 pr-2" v-if="selectedSpectraData">
                        <label
                            for="location"
                            class="pr-3 text-md bg-white font-medium text-gray-400"
                            >Info</label
                        >
                        <div
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
import { ShareIcon, ClipboardCopyIcon } from "@heroicons/vue/solid";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";

export default {
    components: {
        ProjectLayout,
        ShareIcon,
        ClipboardCopyIcon,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
    },
    props: ["project", "tab", "study", "dataset"],
    data() {
        return {
            selectedSpectraData: null,
        };
    },
    computed: {
        shareURL() {
            return (
                this.url +
                "/projects/" +
                this.project.data.owner.username +
                "/" +
                this.project.data.slug +
                "?tab=study&id=" +
                this.study.data.slug +
                "&dsid=" +
                this.dataset.data.slug
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
        url() {
            return String(this.$page.props.url);
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
            if (e.data.type == "nmr-wrapper:error") {
                this.spectraError = e.data.data;
                return;
            }
            if (e.data.type == "nmr-wrapper:data-change") {
                let actionType = e.data.data.actionType;
                if (actionType == "" || actionType == "INITIATE") {
                    if (
                        this.selectedSpectraData == null ||
                        (this.selectedSpectraData &&
                            this.selectedSpectraData.length == 0)
                    ) {
                        this.selectedSpectraData = e.data.data.spectra;
                    }
                    return;
                }
            }
        };

        if (!this.$page.props.dsEventRegistered) {
            window.addEventListener("message", saveNMRiumUpdates);
            this.$page.props.dsEventRegistered = true;
        }
    },
    methods: {
        getSVGString(molecule) {
            if (molecule.MOL) {
                let mol = OCL.Molecule.fromMolfile(
                    "\n  " + molecule.MOL.replaceAll('"', "")
                );
                return mol.toSVG(200, 200);
            } else {
                console.log(molecule);
            }
        },
        loadSpectra() {
            this.selectedSpectraData = null;
            if (this.dataset == null) {
                this.dataset = this.dataset;
            }
            const iframe = window.frames.datasetNMRiumPublicIframe;
            this.currentMolecules = [];
            if (iframe) {
                if (!this.dataset.data.nmrium_info) {
                    let data = {
                        data: [
                            this.url +
                                "/" +
                                this.project.data.owner.username +
                                "/datasets/" +
                                this.project.data.slug +
                                "/" +
                                this.study.data.slug +
                                "/" +
                                this.dataset.data.slug,
                        ],
                        type: "url",
                    };
                    iframe.postMessage({ type: `nmr-wrapper:load`, data }, "*");
                } else {
                    let nmrium_info = this.parseJSON(
                        this.dataset.data.nmrium_info
                    );
                    let mols = [];
                    if (nmrium_info.molecules) {
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
