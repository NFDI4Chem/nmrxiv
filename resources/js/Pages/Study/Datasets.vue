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
            :preview="preview"
        >
            <template #study-section>
                <div class="px-4 sm:px-6 md:px-0">
                    <div v-if="study.is_public" class="p-4">
                        <div class="float-right">
                            <div class="flex-0.5 self-center">
                                <Menu
                                    v-if="study.is_public"
                                    as="div"
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
                                                                id="studyPublicURLCopy"
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
                                                                    'studyPublicURLCopy'
                                                                )
                                                            "
                                                        >
                                                            <span
                                                                ><ClipboardDocumentIcon
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
                        <!-- <div
                            class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6"
                        >
                            <div
                                :class="[
                                    canUpdateStudy
                                        ? 'sm:col-span-5'
                                        : 'sm:col-span-6',
                                ]"
                            >
                                <div
                                    class="float-right text-xs cursor-pointer hover:text-blue-700 mb-2"
                                >
                                    <a
                                        href="https://docs.nmrxiv.org/docs/submission-guides/data-model/dataset"
                                        target="_blank"
                                        >Learn more about spectra
                                    </a>
                                </div>
                                <div>
                                    <label
                                        for="location"
                                        class="block text-sm font-medium text-gray-700"
                                        >Select Spectra
                                    </label>
                                    <select
                                        id="location"
                                        v-model="selectedDataset"
                                        name="location"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm rounded-md"
                                    >
                                        <option
                                            v-for="dataset in study.datasets.sort(
                                                (a, b) =>
                                                    a.name > b.name ? 1 : -1
                                            )"
                                            :key="dataset.slug"
                                            :value="dataset"
                                        >
                                            {{ dataset.name }}
                                            <span v-if="dataset.type"
                                                >({{
                                                    dataset.type.replace(
                                                        /,\s*$/,
                                                        ""
                                                    )
                                                }})</span
                                            >
                                            - #{{ dataset.id }}
                                        </option>
                                    </select>
                                    <div class="-mx-4" v-if="selectedDataset">
                                        <Citation
                                            :model="'dataset'"
                                            :doi="selectedDataset.doi"
                                        ></Citation>
                                    </div>
                                </div>
                            </div>

                            <div
                                v-if="canUpdateStudy"
                                class="flex-shrink-0 text-center"
                            >
                                <label
                                    for="last-name"
                                    class="block text-sm font-medium text-gray-700"
                                    >&nbsp;</label
                                >
                                <button
                                    type="button"
                                    class="inline-flex mt-1 items-center px-2.5 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                    @click="openDatasetCreateDialog()"
                                >
                                    + Manage Spectra
                                </button>
                                <div
                                    class="text-xs cursor-pointer hover:text-blue-700 mt-2"
                                >
                                    <a
                                        href="https://docs.nmrxiv.org/docs/submission-guides/submission/upload/#manage-studies-and-datasets"
                                        target="_blank"
                                        >Need help?
                                    </a>
                                </div>
                            </div>
                        </div> -->

                        <div class="mb-7">
                            <SpectraEditor
                                v-if="canUpdateStudy"
                                ref="spectraEditorREF"
                                :project="project"
                                :study="study"
                            ></SpectraEditor>
                            <SpectraViewer
                                v-else
                                ref="spectraViewerREF"
                                :project="project"
                                :study="study"
                            ></SpectraViewer>
                        </div>
                        <div class="my-2">
                            <div>
                                <h2 class="text-sm font-medium text-gray-500">
                                    Spectra Datasets
                                </h2>
                                <ul
                                    role="list"
                                    class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3"
                                >
                                    <li
                                        v-for="dataset in study.datasets.sort(
                                            (a, b) => (a.name > b.name ? 1 : -1)
                                        )"
                                        :key="dataset.slug"
                                        class="col-span-1 flex rounded-md shadow-sm"
                                    >
                                        <!-- <a
                                            class="cursor-pointer"
                                            :href="
                                                '/' +
                                                dataset.identifier.replace(
                                                    'NMRXIV:',
                                                    ''
                                                )
                                            "
                                            target="_blank"
                                        > -->
                                        <!-- <div
                                            class="flex w-16 flex-shrink-0 items-center justify-center bg-pink-600 rounded-l-md text-sm font-medium text-white"
                                        >
                                        
                                        </div> -->
                                        <div
                                            class="flex flex-1 border-l rounded-md items-center justify-between truncate rounded-r-md border-b border-r border-t border-gray-200 bg-white"
                                        >
                                            <div
                                                class="flex-1 truncate px-4 py-2 text-sm"
                                            >
                                                <a
                                                    class="font-medium text-lg text-gray-900 hover:text-gray-600"
                                                >
                                                    {{ dataset.name }}
                                                    <span v-if="dataset.type"
                                                        >({{
                                                            dataset.type.replace(
                                                                /,\s*$/,
                                                                ""
                                                            )
                                                        }})</span
                                                    ></a
                                                >
                                                <p class="text-gray-900">
                                                    DOI: {{ dataset.doi }}
                                                </p>
                                            </div>
                                            <!-- <div class="flex-shrink-0 pr-2">
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke-width="1.5"
                                                        stroke="currentColor"
                                                        class="h-5 w-5 text-gray-600 ml-4"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"
                                                        ></path>
                                                    </svg>
                                                </div> -->
                                        </div>
                                        <!-- </a> -->
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div>&emsp;</div>
                        <div class="pt-3">
                            <div>
                                <label
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
                    </div>
                </div>
            </template>
        </study-content>
    </div>
</template>

<script>
import StudyContent from "@/Pages/Study/Content.vue";
import LoadingButton from "@/Shared/LoadingButton.vue";
import { ShareIcon, ClipboardDocumentIcon } from "@heroicons/vue/24/solid";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import SpectraViewer from "@/Shared/SpectraViewer.vue";
import SpectraEditor from "@/Shared/SpectraEditor.vue";
import Citation from "@/Shared/Citation.vue";

export default {
    components: {
        StudyContent,
        LoadingButton,
        ShareIcon,
        ClipboardDocumentIcon,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        SpectraEditor,
        SpectraViewer,
        Citation,
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
        "preview",
    ],
    data() {
        return {
            autoSaving: false,
            selectedDataset: null,
            currentMolecules: [],
            license: "No license selected",
            loading: false,
            spectraError: null,
            role: this.studyRole,
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
            return this.study.public_url;
        },
        canUpdateStudy() {
            return this.studyPermissions
                ? this.studyPermissions.canUpdateStudy
                : false;
        },
    },
    mounted() {
        // const urlSearchParams = new URLSearchParams(window.location.search);
        // const params = Object.fromEntries(urlSearchParams.entries());
        // let dsId = params["dsid"];
        // this.study.datasets.forEach((ds) => {
        //     if (ds.id == dsId) {
        //         this.selectedDataset = ds;
        //     }
        // });
        // if (!this.selectedDataset) {
        //     this.selectedDataset = this.study.datasets[0];
        // }
        this.$nextTick(function () {
            if (this.$refs.spectraEditorREF) {
                this.$refs.spectraEditorREF.registerEvents();
            }
        });
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
};
</script>
New
