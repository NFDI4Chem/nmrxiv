<template>
    <app-layout title="Spectra Snapshots">
        <template #header>
            <div class="bg-white border-b">
                <div class="px-12">
                    <div class="flex flex-nowrap justify-between py-6">
                        <div>
                            <bread-crumbs :pages="pages" />
                            <div
                                class="mt-2 md:flex md:items-center md:justify-between"
                            >
                                <div class="flex-1 min-w-0">
                                    <div
                                        class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest mt-3"
                                    >
                                        Spectra SnapShots
                                        <span v-if="currentId"
                                            >(Spectra ID: {{ currentId }})</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            v-if="
                                datasets.length > 0 &&
                                this.datasets.length >= this.index + 1
                            "
                            class="mx-auto flex max-w-2xl items-center justify-between gap-x-8 lg:mx-0 lg:max-w-none"
                        >
                            <div class="flex items-center gap-x-4 sm:gap-x-6">
                                <a
                                    class="text-sm font-semibold leading-6 text-gray-900 sm:block"
                                >
                                    {{ index + 1 }} / {{ datasets.length }}
                                </a>
                                <a
                                    v-if="!autoImport"
                                    @click="toggleAutoImport"
                                    class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                >
                                    Import
                                </a>
                                <a
                                    @click="toggleAutoImport"
                                    v-else
                                    class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                >
                                    Pause
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div v-if="datasets.length > 0">
            <div class="p-10" v-if="this.datasets.length == this.index">
                <button
                    type="button"
                    class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    <svg
                        class="mx-auto h-12 w-12 text-green-400"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>

                    <span class="mt-2 block text-sm font-semibold text-gray-900"
                        >Import complete</span
                    >
                </button>
            </div>
            <div v-else>
                <div v-if="currentId">
                    <SpectraSnapshot
                        ref="spectraEditorREF"
                        :id="currentId"
                        @loading="status"
                    ></SpectraSnapshot>
                </div>
                <div class="p-10">
                    <button
                        type="button"
                        class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="mx-auto h-12 w-12 text-blue-400"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"
                            />
                        </svg>

                        <div class="mt-6">
                            <button
                                @click="toggleAutoImport"
                                type="button"
                                class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                            >
                                <svg
                                    class="-ml-0.5 mr-1.5 h-5 w-5"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"
                                    />
                                </svg>
                                Import
                            </button>
                        </div>
                    </button>
                </div>
            </div>
        </div>
        <div class="p-10" v-else>
            <button
                type="button"
                class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                <svg
                    class="mx-auto h-12 w-12 text-gray-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"
                    ></path>
                </svg>
                <span class="mt-2 block text-sm font-semibold text-gray-900"
                    >Nothing to import</span
                >
            </button>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import SearchFilter from "@/Shared/SearchFilter.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import BreadCrumbs from "../../../Jetstream/BreadCrumbs.vue";
import { Link } from "@inertiajs/vue3";
import SpectraSnapshot from "@/Shared/SpectraSnapshot.vue";

export default {
    components: {
        AppLayout,
        SearchFilter,
        JetDialogModal,
        JetSecondaryButton,
        JetButton,
        Link,
        BreadCrumbs,
        SpectraSnapshot,
    },
    props: {
        users: Array,
        datasets: Array,
    },
    data() {
        return {
            currentId: null,
            pages: [
                { name: "Console", route: "console", current: false },
                { name: "Curation", route: "console.spectra", current: true },
                {
                    name: "Spectra Snapshots",
                    route: "console.spectra.snapshots",
                    current: true,
                },
            ],
            index: 0,
            autoImport: false,
        };
    },
    watch: {},
    mounted() {
        if (this.datasets && this.autoImport) {
            this.currentId = this.datasets[this.index];
        }
    },
    methods: {
        status(e) {
            if (!e.status && e.message == "dataset loaded") {
                if (this.autoImport) {
                    this.index = this.index + 1;
                    this.loadSpectra();
                }
            }
        },
        toggleAutoImport() {
            this.autoImport = !this.autoImport;
            if (this.autoImport) {
                this.loadSpectra();
            }
        },
        loadSpectra() {
            this.currentId = this.datasets[this.index];
            this.$refs.spectraEditorREF.loadSpectra();
        },
    },
};
</script>
