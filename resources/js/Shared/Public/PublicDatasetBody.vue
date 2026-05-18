<template>
    <header class="mt-2 space-y-4">
        <nav
            class="text-sm text-gray-500 dark:text-gray-400"
            aria-label="Breadcrumb"
        >
            <ol class="flex flex-wrap items-center gap-x-2 gap-y-1">
                <li class="min-w-0">
                    <Link
                        v-if="studyPublicHref"
                        :href="studyPublicHref"
                        class="font-medium text-gray-600 transition-colors hover:text-teal-600 dark:text-gray-300 dark:hover:text-teal-400"
                    >
                        {{ study.data.name }}
                    </Link>
                    <span
                        v-else
                        class="font-medium text-gray-600 dark:text-gray-300"
                    >
                        {{ study.data.name }}
                    </span>
                </li>
                <li
                    aria-hidden="true"
                    class="text-gray-300 dark:text-gray-600"
                >
                    /
                </li>
                <li
                    class="truncate font-medium text-gray-700 dark:text-gray-200"
                    aria-current="page"
                >
                    {{ dataset.data.name }}
                </li>
            </ol>
        </nav>

        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between sm:gap-6"
        >
            <div class="min-w-0 flex-1">
                <h1
                    class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-3xl"
                >
                    <span class="break-words">{{ dataset.data.name }}</span>
                </h1>
                <p
                    v-if="datasetTypeLabel"
                    class="mt-1.5 text-sm text-gray-500 dark:text-gray-400"
                >
                    {{ datasetTypeLabel }}
                </p>
            </div>
            <div class="flex flex-shrink-0 flex-wrap items-center gap-3">
                <DOIBadge
                    :doi="dataset.data.doi"
                    color="bg-green-100"
                ></DOIBadge>
                <Menu
                    v-if="dataset && study.data.is_public"
                    as="div"
                    class="relative text-left"
                >
                    <MenuButton
                        type="button"
                        class="inline-flex items-center rounded-full border border-gray-200 bg-white px-3 py-1.5 text-sm font-medium text-gray-600 shadow-sm transition-colors hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"
                    >
                        <ShareIcon
                            class="mr-2 h-4 w-4 flex-shrink-0 text-gray-700 dark:text-gray-300"
                            aria-hidden="true"
                        />
                        Share
                    </MenuButton>
                    <transition
                        enter-active-class="transition ease-out duration-100"
                        enter-from-class="transform scale-95 opacity-0"
                        enter-to-class="transform scale-100 opacity-100"
                        leave-active-class="transition ease-in duration-75"
                        leave-from-class="transform scale-100 opacity-100"
                        leave-to-class="transform scale-95 opacity-0"
                    >
                        <MenuItems
                            class="absolute right-0 z-50 mt-2 w-72 origin-top-right rounded-lg bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-gray-800 dark:ring-white/10"
                        >
                            <MenuItem v-slot="{ active }">
                                <div
                                    :class="[
                                        active
                                            ? 'bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-gray-100'
                                            : 'text-gray-700 dark:text-gray-200',
                                        'flex px-3 py-2 text-sm',
                                    ]"
                                >
                                    <div class="min-w-0 flex-1">
                                        <input
                                            id="datasetPublicURLCopy"
                                            readonly
                                            type="text"
                                            :value="shareURL"
                                            class="block w-full rounded-l-md border border-gray-300 bg-white text-sm focus:border-gray-500 focus:ring-gray-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                            @focus="$event.target.select()"
                                        />
                                    </div>
                                    <button
                                        type="button"
                                        class="-ml-px inline-flex shrink-0 items-center rounded-r-md border border-gray-300 bg-gray-50 px-2.5 py-2 text-gray-700 transition hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                                        @click="
                                            copyToClipboard(
                                                shareURL,
                                                'datasetPublicURLCopy',
                                            )
                                        "
                                    >
                                        <ClipboardDocumentIcon
                                            class="h-5 w-5"
                                            aria-hidden="true"
                                        />
                                    </button>
                                </div>
                            </MenuItem>
                        </MenuItems>
                    </transition>
                </Menu>
            </div>
        </div>
    </header>

    <div
        v-if="study.data.tags && study.data.tags.length > 0"
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
                    class="pr-3 text-md bg-white font-medium text-gray-400 dark:bg-gray-900"
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
                            class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-950/60 dark:text-indigo-200"
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

    <div class="mt-4 md:grid md:grid-cols-12 md:gap-x-6 md:gap-y-6">
        <div
            :class="[
                'min-w-0 space-y-6',
                hasMolecularCompositionSidebar
                    ? 'md:col-span-9'
                    : 'md:col-span-12',
            ]"
        >
            <div
                v-if="
                    study.data.sample?.description &&
                    study.data.sample.description.length > 0
                "
                class="rounded-lg border border-gray-100 bg-gray-50/80 p-4 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-900/40 dark:text-gray-300"
            >
                <p class="whitespace-pre-wrap">
                    {{ study.data.sample.description }}
                </p>
            </div>

            <div class="mt-6">
                <SpectraViewer
                    ref="spectraViewerREF"
                    :dataset="dataset.data"
                    :project="project?.data ?? null"
                    :study="study.data"
                ></SpectraViewer>
            </div>

            <section
                v-if="firstSpectrumInfoRows.length > 0"
                class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900/80"
                aria-labelledby="spectrum-info-heading"
            >
                <div
                    class="border-b border-gray-200 px-4 py-3 dark:border-gray-700"
                >
                    <h2
                        id="spectrum-info-heading"
                        class="text-sm font-semibold text-gray-900 dark:text-gray-100"
                    >
                        Spectrum info
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table
                        class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                    >
                        <thead class="bg-gray-50 dark:bg-gray-800/80">
                            <tr>
                                <th
                                    scope="col"
                                    class="py-3 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 sm:pl-6"
                                >
                                    Field
                                </th>
                                <th
                                    scope="col"
                                    class="py-3 pl-3 pr-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 sm:pr-6"
                                >
                                    Value
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900/40"
                        >
                            <tr
                                v-for="row in firstSpectrumInfoRows"
                                :key="row.key"
                            >
                                <th
                                    scope="row"
                                    class="whitespace-nowrap py-3 pl-4 pr-3 text-left text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-6"
                                >
                                    {{ row.key }}
                                </th>
                                <td
                                    class="max-w-md break-words py-3 pl-3 pr-4 text-sm text-gray-600 dark:text-gray-300 sm:max-w-xl sm:pr-6"
                                >
                                    {{ row.value }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <aside
            v-if="hasMolecularCompositionSidebar"
            class="mt-8 min-w-0 md:mt-0 md:col-span-3"
        >
            <div
                class="flex min-w-0 flex-col gap-6 bg-white py-5 pl-0 pr-0 dark:bg-gray-900/80 md:sticky md:top-6"
            >
                <div class="min-w-0 shrink-0">
                    <h3
                        class="text-sm font-bold text-gray-900 dark:text-gray-100"
                    >
                        Molecular info
                    </h3>
                    <MolecularInfoPanel :molecules="compositionMolecules" />
                </div>
            </div>
        </aside>
    </div>
</template>

<script>
import { ShareIcon, ClipboardDocumentIcon } from "@heroicons/vue/24/solid";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import SpectraViewer from "@/Shared/SpectraViewer.vue";
import DOIBadge from "@/Shared/DOIBadge.vue";
import MolecularInfoPanel from "@/Shared/MolecularInfoPanel.vue";
import { Link } from "@inertiajs/vue3";

export default {
    components: {
        ShareIcon,
        ClipboardDocumentIcon,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        SpectraViewer,
        DOIBadge,
        MolecularInfoPanel,
        Link,
    },
    props: {
        study: {
            type: Object,
            required: true,
        },
        dataset: {
            type: Object,
            required: true,
        },
        project: {
            type: Object,
            default: null,
        },
    },
    computed: {
        shareURL() {
            return this.dataset.data.public_url;
        },
        studyPublicHref() {
            return this.study?.data?.public_url ?? null;
        },
        datasetTypeLabel() {
            const raw = this.dataset?.data?.type;
            if (!raw || typeof raw !== "string") {
                return "";
            }

            return raw.replace(/,\s*$/, "");
        },
        firstSpectrumInfo() {
            const spectra = this.dataset?.data?.nmrium_info?.data?.spectra;
            if (!Array.isArray(spectra) || spectra.length === 0) {
                return null;
            }
            const raw = spectra[0]?.info;
            if (raw == null || typeof raw !== "object") {
                return null;
            }

            return raw;
        },
        firstSpectrumInfoRows() {
            const info = this.firstSpectrumInfo;
            if (!info) {
                return [];
            }

            return Object.keys(info).map((key) => ({
                key,
                value: this.formatSpectrumInfoCell(info[key]),
            }));
        },
        compositionMolecules() {
            const fromSample = this.study?.data?.sample?.molecules;
            if (Array.isArray(fromSample) && fromSample.length > 0) {
                return fromSample;
            }
            const top = this.study?.data?.molecules;
            if (Array.isArray(top) && top.length > 0) {
                return top;
            }

            return [];
        },
        hasMolecularCompositionSidebar() {
            return this.compositionMolecules.length > 0;
        },
    },
    methods: {
        formatSpectrumInfoCell(val) {
            if (val === null || val === undefined) {
                return "—";
            }
            if (Array.isArray(val)) {
                return val
                    .map((v) =>
                        v !== null && typeof v === "object"
                            ? JSON.stringify(v)
                            : String(v),
                    )
                    .join(", ");
            }
            if (typeof val === "object") {
                return JSON.stringify(val);
            }

            return String(val);
        },
    },
};
</script>
