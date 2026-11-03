<template>
    <app-layout :title="pageTitle">
        <template #header>
            <div class="relative overflow-hidden border-b border-zinc-900/5">
                <div
                    class="pointer-events-none absolute inset-0 bg-gradient-to-br from-blue-50/40 via-indigo-50/30 to-purple-50/30"
                ></div>
                <div class="pointer-events-none absolute inset-0 opacity-20">
                    <div
                        class="absolute top-0 left-1/4 h-96 w-96 rounded-full bg-purple-300 mix-blend-multiply blur-3xl filter animate-blob"
                    ></div>
                    <div
                        class="absolute top-0 right-1/4 h-96 w-96 rounded-full bg-blue-300 mix-blend-multiply blur-3xl filter animate-blob animation-delay-2000"
                    ></div>
                    <div
                        class="absolute -bottom-32 left-1/3 h-96 w-96 rounded-full bg-pink-300 mix-blend-multiply blur-3xl filter animate-blob animation-delay-4000"
                    ></div>
                </div>

                <div class="relative px-6 py-8 sm:px-8 lg:py-10">
                    <div v-if="!hasMolecule">
                        <div
                            class="mb-3 text-4xl font-bold tracking-tight text-gray-900"
                        >
                            Browse Samples (Spectra)
                        </div>
                        <p>
                            Explore, analyse, and share raw spectra and
                            assignments. Learn more about
                            <a
                                class="text-teal-900"
                                href="https://docs.nmrxiv.org/introduction/intro"
                                target="_blank"
                                rel="noopener noreferrer"
                                >spectra</a
                            >.
                        </p>
                    </div>

                    <div
                        v-else
                        class="flex flex-col gap-6 lg:flex-row lg:items-start lg:gap-8"
                    >
                        <div
                            v-if="structureUrl"
                            class="mx-auto shrink-0 lg:mx-0"
                        >
                            <div
                                class="overflow-hidden rounded-2xl bg-white p-3 shadow-sm ring-1 ring-gray-900/5"
                            >
                                <img
                                    :src="structureUrl"
                                    :alt="`2D structure of ${displayName}`"
                                    class="h-44 w-44 bg-white object-contain sm:h-52 sm:w-52"
                                />
                            </div>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div
                                class="flex flex-wrap items-center justify-between gap-3"
                            >
                                <div
                                    class="flex flex-wrap items-center gap-x-3 gap-y-1"
                                >
                                    <p
                                        class="text-xs font-semibold uppercase tracking-widest text-indigo-600"
                                    >
                                        Compound
                                    </p>
                                    <span
                                        class="inline-flex items-center rounded-md bg-white/80 px-2 py-0.5 font-mono text-xs font-medium text-gray-600 ring-1 ring-gray-900/5"
                                    >
                                        {{ molecule.identifier }}
                                    </span>
                                </div>
                                <button
                                    type="button"
                                    class="inline-flex shrink-0 items-center gap-2 self-start rounded-full bg-white/90 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-gray-200 backdrop-blur transition-colors hover:bg-white hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                                    @click="copyText('link', pageUrl)"
                                >
                                    <CheckIcon
                                        v-if="copiedKey === 'link'"
                                        class="h-4 w-4 text-teal-600"
                                        aria-hidden="true"
                                    />
                                    <LinkIcon
                                        v-else
                                        class="h-4 w-4"
                                        aria-hidden="true"
                                    />
                                    {{
                                        copiedKey === "link"
                                            ? "Link copied"
                                            : "Copy link"
                                    }}
                                </button>
                            </div>

                            <h1
                                class="mt-2 break-words text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl"
                            >
                                {{ displayName }}
                            </h1>

                            <section
                                aria-label="Data completeness"
                                class="mt-4 rounded-xl bg-white/70 px-4 py-3.5 shadow-sm ring-1 ring-gray-900/5 backdrop-blur"
                            >
                                <div
                                    class="flex items-start justify-between gap-4"
                                >
                                    <DataCompletenessBadge
                                        class="min-w-0 flex-1"
                                        :tier="molecule.annotation_level"
                                        :breakdown="molecule.quality_breakdown"
                                        variant="detailed"
                                    />
                                    <button
                                        type="button"
                                        class="shrink-0 cursor-pointer pt-0.5 text-xs font-semibold text-indigo-600 underline underline-offset-2 hover:text-indigo-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                                        @click="showQualityInfo = true"
                                    >
                                        How is this scored?
                                    </button>
                                </div>
                            </section>

                            <dl
                                v-if="factTiles.length"
                                class="mt-5 grid grid-cols-2 gap-3 xl:grid-cols-4"
                            >
                                <div
                                    v-for="tile in factTiles"
                                    :key="tile.key"
                                    class="rounded-xl bg-white/70 px-4 py-3 shadow-sm ring-1 ring-gray-900/5 backdrop-blur"
                                >
                                    <dt
                                        class="text-xs font-medium text-gray-500"
                                    >
                                        {{ tile.label }}
                                    </dt>
                                    <dd
                                        class="mt-1 text-lg font-semibold tracking-tight text-gray-900"
                                    >
                                        <MolecularFormula
                                            v-if="tile.kind === 'formula'"
                                            :formula="tile.value"
                                        />
                                        <template v-else>
                                            <span class="tabular-nums">{{
                                                tile.value
                                            }}</span>
                                            <span
                                                v-if="tile.unit"
                                                class="ml-1 text-sm font-medium text-gray-400"
                                                >{{ tile.unit }}</span
                                            >
                                        </template>
                                    </dd>
                                </div>
                            </dl>

                            <div
                                v-if="identifierRows.length"
                                class="mt-4 space-y-2"
                            >
                                <div
                                    v-for="row in identifierRows"
                                    :key="row.key"
                                    class="flex items-center gap-3 rounded-xl bg-white/70 px-3 py-2 shadow-sm ring-1 ring-gray-900/5 backdrop-blur"
                                >
                                    <span
                                        class="w-16 shrink-0 text-[11px] font-semibold uppercase tracking-wider text-gray-400"
                                    >
                                        {{ row.label }}
                                    </span>
                                    <code
                                        class="min-w-0 flex-1 truncate font-mono text-sm text-gray-800"
                                        :title="row.value"
                                    >
                                        {{ row.value }}
                                    </code>
                                    <button
                                        type="button"
                                        class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-gray-500 transition-colors hover:bg-white hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                                        :aria-label="
                                            copiedKey === row.key
                                                ? `${row.label} copied`
                                                : `Copy ${row.label}`
                                        "
                                        @click="copyText(row.key, row.value)"
                                    >
                                        <CheckIcon
                                            v-if="copiedKey === row.key"
                                            class="h-4 w-4 text-teal-600"
                                            aria-hidden="true"
                                        />
                                        <ClipboardDocumentIcon
                                            v-else
                                            class="h-4 w-4"
                                            aria-hidden="true"
                                        />
                                    </button>
                                </div>
                            </div>

                            <div v-if="synonymList.length" class="mt-5">
                                <p
                                    class="text-xs font-semibold uppercase tracking-wider text-gray-500"
                                >
                                    Also known as
                                </p>
                                <div
                                    class="mt-2 flex flex-wrap gap-1.5"
                                    :class="
                                        showAllSynonyms
                                            ? 'max-h-36 overflow-y-auto'
                                            : ''
                                    "
                                >
                                    <span
                                        v-for="synonym in visibleSynonyms"
                                        :key="synonym"
                                        class="inline-flex items-center rounded-full bg-white/80 px-2.5 py-1 text-xs font-medium text-gray-600 ring-1 ring-gray-900/5"
                                    >
                                        {{ synonym }}
                                    </span>
                                    <button
                                        v-if="
                                            !showAllSynonyms &&
                                            hiddenSynonymCount > 0
                                        "
                                        type="button"
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold text-indigo-600 hover:bg-white/80 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                                        @click="showAllSynonyms = true"
                                    >
                                        +{{ hiddenSynonymCount }} more
                                    </button>
                                </div>
                                <button
                                    v-if="showAllSynonyms"
                                    type="button"
                                    class="mt-2 text-xs font-semibold text-indigo-600 underline underline-offset-2 hover:text-indigo-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                                    @click="showAllSynonyms = false"
                                >
                                    Show less
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div>
            <div class="bg-white">
                <section aria-labelledby="filter-heading">
                    <div class="bg-gray-100 border-t border-b">
                        <div
                            class="mx-auto py-3 px-4 sm:flex sm:items-center sm:px-6 lg:px-8"
                        >
                            <study-search
                                v-model="form.search"
                                class="mr-4 w-full"
                                @reset="reset"
                            >
                            </study-search>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="min-h-[calc(100vh-500px)] px-6 px-md-12 mb-24 mx-auto">
            <div class="relative border-gray-200 pt-4">
                <div class="mx-auto flex items-center justify-between">
                    <Menu as="div" class="relative inline-block text-left z-10">
                        <div>
                            <MenuButton
                                class="group inline-flex justify-center text-sm font-medium text-gray-700 hover:text-gray-900"
                            >
                                Sort by:&nbsp;<span
                                    class="capitalize font-black text-gray-900"
                                    >{{ form.sort }}</span
                                >
                                <ChevronDownIcon
                                    class="flex-shrink-0 -mr-1 ml-1 h-5 w-5 text-gray-400 group-hover:text-gray-500"
                                    aria-hidden="true"
                                />
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
                                class="origin-top-left absolute left-0 mt-2 w-40 rounded-md shadow-2xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10"
                            >
                                <div class="py-1">
                                    <MenuItem
                                        v-for="option in sortOptions"
                                        :key="option.name"
                                        v-slot="{ active }"
                                    >
                                        <a
                                            :class="[
                                                option.current
                                                    ? 'font-medium text-gray-900'
                                                    : 'text-gray-500',
                                                active ? 'bg-gray-100' : '',
                                                'block px-4 py-2 text-sm cursor-pointer',
                                            ]"
                                            @click="form.sort = option.value"
                                        >
                                            {{ option.name }}
                                        </a>
                                    </MenuItem>
                                </div>
                            </MenuItems>
                        </transition>
                    </Menu>
                    <div class="float-right">
                        <span
                            class="relative z-0 inline-flex shadow-sm rounded-md"
                        >
                            <button
                                type="button"
                                :class="[
                                    filters.mode == 'list'
                                        ? 'bg-gray-300 text-gray-900'
                                        : '',
                                    'relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50',
                                ]"
                                @click="form.mode = 'list'"
                            >
                                <span class="sr-only">List</span>
                                <QueueListIcon
                                    class="h-5 w-5"
                                    aria-hidden="true"
                                />
                            </button>
                            <button
                                type="button"
                                :class="[
                                    filters.mode == 'grid'
                                        ? 'bg-gray-300 text-gray-900'
                                        : '',
                                    '-ml-px relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50',
                                ]"
                                @click="form.mode = 'grid'"
                            >
                                <span class="sr-only">Grid</span>
                                <Squares2X2Icon
                                    class="h-5 w-5"
                                    aria-hidden="true"
                                />
                            </button>
                        </span>
                    </div>
                </div>
            </div>

            <div v-if="studies.meta && studies.meta.total > 0">
                <div class="border-t mt-3 border-gray-100">
                    <h2 class="text-gray-600 text-md mt-4 font-bold">
                        Results ({{ studies.meta.total }})
                    </h2>
                </div>
                <div v-if="filters.mode == 'grid'">
                    <div
                        class="mt-4 mx-auto grid gap-8 lg:grid-cols-4 2xl:grid-cols-6"
                    >
                        <span v-for="study in studies.data" :key="study.id">
                            <StudyPublicCard
                                :mode="filters.mode"
                                :study="study"
                            ></StudyPublicCard>
                        </span>
                    </div>
                </div>
                <div v-if="filters.mode == 'list'">
                    <div class="mt-4 bg-white overflow-hidden sm:rounded-md">
                        <ul role="list" class="rounded-md">
                            <div
                                v-for="study in studies.data"
                                :key="study.id"
                                class="mb-3"
                            >
                                <StudyPublicCard
                                    :mode="filters.mode ? filters.mode : 'grid'"
                                    :study="study"
                                ></StudyPublicCard>
                            </div>
                        </ul>
                    </div>
                </div>
                <div
                    v-if="studies.meta.total > studies.meta.per_page"
                    class="py-10"
                >
                    <Pagination :links="studies.meta.links"></Pagination>
                </div>
            </div>
            <div v-else>
                <div
                    class="text-center rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mt-5"
                >
                    <svg
                        class="mx-auto h-16 w-16 text-gray-300"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.5"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"
                        />
                    </svg>
                    <h3 class="mt-4 text-base font-semibold text-gray-900">
                        No studies available
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">
                        Published studies will appear here for public access.
                    </p>
                </div>
            </div>
        </div>
        <DataCompletenessInfoModal
            :show="showQualityInfo"
            :tier="molecule?.annotation_level"
            :breakdown="molecule?.quality_breakdown"
            @close="showQualityInfo = false"
        />
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import MolecularFormula from "@/Shared/MolecularFormula.vue";
import StudySearch from "@/Shared/StudySearch.vue";
import StudyPublicCard from "@/Shared/StudyCardPublic.vue";
import DataCompletenessBadge from "@/Shared/DataCompletenessBadge.vue";
import DataCompletenessInfoModal from "@/Shared/DataCompletenessInfoModal.vue";
import { ref } from "vue";
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import Pagination from "@/Shared/Pagination.vue";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import {
    ChevronDownIcon,
    QueueListIcon,
    Squares2X2Icon,
} from "@heroicons/vue/24/solid";
import {
    CheckIcon,
    ClipboardDocumentIcon,
    LinkIcon,
} from "@heroicons/vue/24/outline";

export default {
    components: {
        AppLayout,
        MolecularFormula,
        StudySearch,
        Pagination,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        ChevronDownIcon,
        QueueListIcon,
        Squares2X2Icon,
        StudyPublicCard,
        DataCompletenessBadge,
        DataCompletenessInfoModal,
        CheckIcon,
        ClipboardDocumentIcon,
        LinkIcon,
    },
    props: {
        studies: {
            default: () => ({}),
            type: Object,
        },
        filters: {
            default: () => ({
                search: "",
                sort: "newest",
                mode: "grid",
            }),
            type: Object,
        },
        molecule: {
            default: () => ({}),
            type: Object,
        },
    },
    data() {
        return {
            sortOptions: [
                { name: "Creation", value: "creation", current: true },
                { name: "Newest", value: "newest", current: false },
            ],
            open: ref(false),
            showQualityInfo: false,
            showAllSynonyms: false,
            copiedKey: null,
            copiedTimer: null,
            form: {
                search: this.filters.search,
                sort: "newest",
                mode: "grid",
            },
        };
    },
    computed: {
        hasMolecule() {
            return Boolean(this.molecule?.identifier);
        },
        displayName() {
            return (
                this.molecule?.iupac_name ||
                this.molecule?.name ||
                this.molecule?.identifier ||
                "Compound"
            );
        },
        structureUrl() {
            const smiles = this.molecule?.canonical_smiles;
            if (!smiles || !this.$page.props.CM_API) {
                return null;
            }

            return `${
                this.$page.props.CM_API
            }depict/2D?smiles=${encodeURIComponent(
                smiles
            )}&height=400&width=400&CIP=false&toolkit=cdk`;
        },
        pageUrl() {
            if (typeof window === "undefined") {
                return this.molecule?.public_url || "";
            }

            return window.location.href;
        },
        casNumber() {
            const raw = this.molecule?.cas;
            if (raw) {
                const first = String(raw)
                    .split(/[,;\n]/)[0]
                    .trim();
                if (first) {
                    return first;
                }
            }

            const synonyms = String(this.molecule?.synonyms || "")
                .split(",")
                .map((synonym) => synonym.trim());

            return (
                synonyms.find((synonym) =>
                    /^\d{2,7}-\d{2}-\d$/.test(synonym)
                ) || ""
            );
        },
        formattedWeight() {
            const value = this.molecule?.molecular_weight;
            if (value === null || value === undefined || value === "") {
                return "";
            }

            const number = Number(value);
            if (!Number.isFinite(number)) {
                return String(value);
            }

            return number.toLocaleString(undefined, {
                maximumFractionDigits: 2,
            });
        },
        publishedSampleCount() {
            const total = this.studies?.meta?.total;

            return typeof total === "number" ? total : null;
        },
        factTiles() {
            const tiles = [];

            if (this.molecule?.molecular_formula) {
                tiles.push({
                    key: "formula",
                    label: "Formula",
                    kind: "formula",
                    value: this.molecule.molecular_formula,
                });
            }

            if (this.formattedWeight) {
                tiles.push({
                    key: "weight",
                    label: "Molecular weight",
                    kind: "text",
                    value: this.formattedWeight,
                    unit: "g/mol",
                });
            }

            if (this.casNumber) {
                tiles.push({
                    key: "cas",
                    label: "CAS",
                    kind: "text",
                    value: this.casNumber,
                });
            }

            if (this.publishedSampleCount !== null) {
                tiles.push({
                    key: "samples",
                    label:
                        this.publishedSampleCount === 1
                            ? "Published sample"
                            : "Published samples",
                    kind: "text",
                    value: String(this.publishedSampleCount),
                });
            }

            return tiles;
        },
        identifierRows() {
            const rows = [];
            const smiles = this.molecule?.canonical_smiles;
            const inchiKey =
                this.molecule?.standard_inchi_key || this.molecule?.inchi_key;
            const inchi = this.molecule?.standard_inchi || this.molecule?.inchi;

            if (smiles) {
                rows.push({ key: "smiles", label: "SMILES", value: smiles });
            }

            if (inchiKey) {
                rows.push({
                    key: "inchikey",
                    label: "InChIKey",
                    value: inchiKey,
                });
            } else if (inchi) {
                rows.push({ key: "inchi", label: "InChI", value: inchi });
            }

            return rows;
        },
        synonymList() {
            const raw = this.molecule?.synonyms;
            if (!raw) {
                return [];
            }

            const excluded = new Set(
                [this.displayName, this.casNumber]
                    .map((value) =>
                        String(value || "")
                            .trim()
                            .toLowerCase()
                    )
                    .filter(Boolean)
            );
            const seen = new Set();

            return String(raw)
                .split(",")
                .map((synonym) => synonym.trim())
                .filter((synonym) => {
                    if (!synonym) {
                        return false;
                    }

                    const key = synonym.toLowerCase();
                    if (excluded.has(key) || seen.has(key)) {
                        return false;
                    }

                    seen.add(key);

                    return true;
                });
        },
        visibleSynonyms() {
            if (this.showAllSynonyms) {
                return this.synonymList;
            }

            return this.synonymList.slice(0, 6);
        },
        hiddenSynonymCount() {
            return Math.max(0, this.synonymList.length - 6);
        },
        compoundNumericId() {
            const raw = this.molecule?.identifier;
            if (raw === null || raw === undefined) {
                return "";
            }
            const s = String(raw).replace(/^NMRXIV:M/i, "");
            const lead = s.match(/^(\d+)/);

            return lead ? lead[1] : "";
        },
        listUrl() {
            if (this.compoundNumericId) {
                return `/compound/M${this.compoundNumericId}`;
            }

            return "/projects";
        },
        pageTitle() {
            if (this.molecule && this.molecule.identifier) {
                let title = `${this.molecule.identifier}`;
                if (this.molecule.name) {
                    title += ` (${this.molecule.name})`;
                }
                return title;
            }
            return "Spectra";
        },
    },
    mounted() {
        if (this.filters) {
            if (this.filters.search == null) {
                this.filters.search = "";
            }
            if (this.filters.mode == null) {
                this.filters.mode = "grid";
            }
            if (this.filters.sort == null) {
                this.filters.sort = "newest";
            }
        }
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                const params = pickBy(this.form);
                if (this.compoundNumericId) {
                    params.compound = this.compoundNumericId;
                }
                this.$inertia.get(this.listUrl, params, {
                    preserveState: true,
                });
            }, 150),
        },
    },
    beforeUnmount() {
        clearTimeout(this.copiedTimer);
    },
    methods: {
        reset() {
            this.form = {
                search: "",
                sort: "newest",
                mode: "grid",
            };
        },
        async copyText(key, value) {
            if (!value) {
                return;
            }

            try {
                await navigator.clipboard.writeText(value);
            } catch {
                const element = document.createElement("textarea");
                element.value = value;
                element.setAttribute("readonly", "");
                element.style.position = "fixed";
                element.style.left = "-9999px";
                document.body.appendChild(element);
                element.select();
                document.execCommand("copy");
                document.body.removeChild(element);
            }

            this.copiedKey = key;
            clearTimeout(this.copiedTimer);
            this.copiedTimer = setTimeout(() => {
                this.copiedKey = null;
            }, 2000);
        },
    },
};
</script>

<style scoped>
/* Blob animations */
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}
</style>
