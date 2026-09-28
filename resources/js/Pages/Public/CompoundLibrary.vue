<template>
    <app-layout :title="pageTitle">
        <template #header>
            <div class="relative overflow-hidden border-b border-zinc-900/5">
                <!-- Animated mesh gradient background -->
                <div
                    class="absolute inset-0 bg-gradient-to-br from-blue-50/30 via-indigo-50/30 to-purple-50/30"
                ></div>
                <div class="absolute inset-0 opacity-20">
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

                <div class="relative mx-8 py-10 sm:py-12">
                    <div
                        class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between"
                    >
                        <div class="flex min-w-0 items-start gap-4">
                            <img
                                v-if="library.photo_url"
                                :src="library.photo_url"
                                :alt="library.name"
                                class="h-14 w-14 shrink-0 rounded-full object-cover ring-4 ring-white shadow-sm"
                            />
                            <div class="min-w-0">
                                <p
                                    class="text-xs font-semibold uppercase tracking-widest text-indigo-600"
                                >
                                    Compound library
                                </p>
                                <h1
                                    class="mt-1 text-4xl font-bold tracking-tight text-gray-900"
                                >
                                    {{ library.name }}
                                </h1>
                                <p
                                    class="mt-3 max-w-2xl leading-relaxed text-gray-700"
                                >
                                    {{ description }}
                                </p>
                                <p
                                    v-if="lastUpdatedLabel"
                                    class="mt-2 text-sm text-gray-500"
                                >
                                    Most recent publication:
                                    {{ lastUpdatedLabel }}
                                </p>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="inline-flex shrink-0 items-center gap-2 self-start rounded-full bg-white/90 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-gray-200 backdrop-blur transition-colors hover:bg-white hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                            @click="copyShareLink"
                        >
                            <CheckIcon
                                v-if="copied"
                                class="h-4 w-4 text-teal-600"
                                aria-hidden="true"
                            />
                            <LinkIcon
                                v-else
                                class="h-4 w-4"
                                aria-hidden="true"
                            />
                            {{ copied ? "Link copied" : "Copy link" }}
                        </button>
                    </div>

                    <dl
                        class="mt-8 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4"
                    >
                        <div
                            v-for="tile in statTiles"
                            :key="tile.label"
                            class="flex items-center gap-3 rounded-xl bg-white/70 px-4 py-4 shadow-sm ring-1 ring-gray-900/5 backdrop-blur"
                        >
                            <span
                                :class="[
                                    tile.iconClasses,
                                    'inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg',
                                ]"
                            >
                                <component
                                    :is="tile.icon"
                                    class="h-5 w-5"
                                    aria-hidden="true"
                                />
                            </span>
                            <div class="min-w-0">
                                <dt
                                    class="truncate text-xs font-medium text-gray-500"
                                >
                                    {{ tile.label }}
                                </dt>
                                <dd
                                    class="text-2xl font-semibold tabular-nums tracking-tight text-gray-900"
                                >
                                    {{ formatNumber(tile.value) }}
                                </dd>
                                <dd
                                    v-if="tile.unpublished > 0"
                                    class="mt-0.5 inline-flex items-center gap-1 text-xs tabular-nums text-gray-500"
                                >
                                    <LockClosedIcon
                                        class="h-3 w-3"
                                        aria-hidden="true"
                                    />
                                    {{ formatNumber(tile.unpublished) }}
                                    unpublished
                                </dd>
                            </div>
                        </div>
                    </dl>
                </div>
            </div>
        </template>

        <div>
            <div class="bg-white">
                <section aria-label="Search and filter compounds">
                    <div class="border-y bg-gray-100">
                        <div
                            class="mx-auto flex flex-col gap-3 px-4 py-3 sm:flex-row sm:items-center sm:px-6 lg:px-8"
                        >
                            <div
                                class="relative flex w-full min-w-0 flex-1 items-center rounded-full bg-white shadow"
                            >
                                <MagnifyingGlassIcon
                                    class="pointer-events-none absolute left-5 h-5 w-5 text-gray-400"
                                    aria-hidden="true"
                                />
                                <label for="library-search" class="sr-only"
                                    >Search compounds</label
                                >
                                <input
                                    id="library-search"
                                    v-model="form.q"
                                    type="search"
                                    autocomplete="off"
                                    name="library-search"
                                    placeholder="Search by name, formula, SMILES, InChIKey or compound ID…"
                                    class="w-full rounded-full border-0 bg-transparent py-3 pl-12 pr-6 text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-indigo-500"
                                    @input="onSearchInput"
                                />
                            </div>
                            <div
                                v-if="techniques.length > 0"
                                class="flex shrink-0 items-center"
                            >
                                <label for="library-technique" class="sr-only"
                                    >Filter by technique</label
                                >
                                <select
                                    id="library-technique"
                                    :value="form.technique"
                                    class="block w-full rounded-full border-0 bg-white py-3 pl-5 pr-10 text-sm text-gray-700 shadow focus:ring-2 focus:ring-indigo-500 sm:w-60"
                                    @change="setTechnique($event.target.value)"
                                >
                                    <option value="">All techniques</option>
                                    <option
                                        v-for="technique in techniques"
                                        :key="technique.label"
                                        :value="technique.label"
                                    >
                                        {{ technique.label }} ({{
                                            techniqueTotal(technique)
                                        }})
                                    </option>
                                </select>
                            </div>
                            <div
                                v-if="showVisibilityFilter"
                                class="flex shrink-0 items-center"
                            >
                                <label for="library-visibility" class="sr-only"
                                    >Filter by publication status</label
                                >
                                <select
                                    id="library-visibility"
                                    :value="form.visibility"
                                    class="block w-full rounded-full border-0 bg-white py-3 pl-5 pr-10 text-sm text-gray-700 shadow focus:ring-2 focus:ring-indigo-500 sm:w-48"
                                    @change="setVisibility($event.target.value)"
                                >
                                    <option
                                        v-for="option in visibilityOptions"
                                        :key="option.value"
                                        :value="option.value"
                                    >
                                        {{ option.name }}
                                    </option>
                                </select>
                            </div>
                            <button
                                v-if="hasActiveFilters"
                                type="button"
                                class="shrink-0 text-sm text-gray-500 hover:text-gray-700 focus:text-indigo-500"
                                @click="reset"
                            >
                                Reset
                            </button>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="mb-24 min-h-[calc(100vh-500px)] w-full px-6 lg:px-8">
            <section
                v-if="techniques.length > 0"
                aria-labelledby="library-techniques-heading"
                class="mt-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm"
            >
                <div
                    class="flex flex-wrap items-baseline justify-between gap-2"
                >
                    <div>
                        <h2
                            id="library-techniques-heading"
                            class="text-sm font-semibold text-gray-900"
                        >
                            Techniques
                            <span class="ml-1 font-normal text-gray-500"
                                >({{ techniques.length }})</span
                            >
                        </h2>
                        <p class="mt-0.5 text-xs text-gray-500">
                            Number of compounds with at least one spectrum of
                            each type. Select a technique to filter.
                        </p>
                        <p
                            v-if="hasUnpublishedTechniques"
                            class="mt-2 flex items-center gap-4 text-[11px] text-gray-500"
                        >
                            <span class="inline-flex items-center gap-1.5">
                                <span
                                    class="h-2 w-2 rounded-full bg-indigo-500"
                                    aria-hidden="true"
                                ></span>
                                Published
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <span
                                    class="h-2 w-2 rounded-full bg-gray-300"
                                    aria-hidden="true"
                                ></span>
                                Unpublished
                            </span>
                        </p>
                    </div>
                    <button
                        v-if="techniques.length > techniquePreviewCount"
                        type="button"
                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-500"
                        @click="showAllTechniques = !showAllTechniques"
                    >
                        {{
                            showAllTechniques
                                ? "Show fewer"
                                : `Show all ${techniques.length}`
                        }}
                    </button>
                </div>
                <ul
                    role="list"
                    class="mt-4 grid gap-x-8 gap-y-1 sm:grid-cols-2 xl:grid-cols-3"
                >
                    <li
                        v-for="technique in visibleTechniques"
                        :key="technique.label"
                    >
                        <button
                            type="button"
                            :aria-pressed="form.technique === technique.label"
                            :class="[
                                form.technique === technique.label
                                    ? 'bg-indigo-50 ring-1 ring-indigo-200'
                                    : 'hover:bg-gray-50',
                                'group w-full rounded-lg px-2 py-1.5 text-left transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500',
                            ]"
                            @click="toggleTechnique(technique.label)"
                        >
                            <span
                                class="flex items-center justify-between gap-3 text-xs"
                            >
                                <span
                                    :class="[
                                        form.technique === technique.label
                                            ? 'font-semibold text-indigo-700'
                                            : 'font-medium text-gray-700',
                                        'min-w-0 truncate',
                                    ]"
                                    :title="technique.label"
                                    >{{ technique.label }}</span
                                >
                                <span
                                    class="inline-flex shrink-0 items-center gap-1 tabular-nums text-gray-500"
                                >
                                    <span class="font-semibold text-gray-900">{{
                                        formatNumber(technique.compounds)
                                    }}</span>
                                    <template
                                        v-if="
                                            technique.unpublished_compounds > 0
                                        "
                                    >
                                        <span aria-hidden="true">·</span>
                                        <LockClosedIcon
                                            class="h-3 w-3"
                                            aria-hidden="true"
                                        />
                                        {{
                                            formatNumber(
                                                technique.unpublished_compounds
                                            )
                                        }}
                                        <span class="sr-only">unpublished</span>
                                    </template>
                                </span>
                            </span>
                            <span
                                class="mt-1.5 flex h-1.5 w-full overflow-hidden rounded-full bg-gray-100"
                            >
                                <span
                                    :class="[
                                        form.technique === technique.label
                                            ? 'bg-indigo-600'
                                            : 'bg-indigo-400 group-hover:bg-indigo-500',
                                        'block h-full transition-all',
                                    ]"
                                    :style="{
                                        width: techniqueBarWidth(
                                            technique.compounds
                                        ),
                                    }"
                                ></span>
                                <span
                                    class="block h-full bg-gray-300 transition-all"
                                    :style="{
                                        width: techniqueBarWidth(
                                            technique.unpublished_compounds
                                        ),
                                    }"
                                ></span>
                            </span>
                        </button>
                    </li>
                </ul>
            </section>

            <div
                class="mt-6 flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-3"
            >
                <div class="flex flex-wrap items-center gap-2">
                    <h2
                        class="text-md font-bold text-gray-600"
                        role="status"
                        aria-live="polite"
                    >
                        Results ({{ formatNumber(compounds.total || 0) }})
                    </h2>
                    <span
                        v-if="filters.technique"
                        class="inline-flex items-center gap-1 rounded-full bg-indigo-50 py-0.5 pl-2.5 pr-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-200"
                    >
                        {{ filters.technique }}
                        <button
                            type="button"
                            class="inline-flex h-4 w-4 items-center justify-center rounded-full hover:bg-indigo-100"
                            @click="setTechnique('')"
                        >
                            <span class="sr-only">Remove technique filter</span>
                            <XMarkIcon class="h-3 w-3" aria-hidden="true" />
                        </button>
                    </span>
                    <span
                        v-if="
                            filters.visibility && filters.visibility !== 'all'
                        "
                        class="inline-flex items-center gap-1 rounded-full bg-gray-100 py-0.5 pl-2.5 pr-1 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-200"
                    >
                        {{ currentVisibilityLabel }}
                        <button
                            type="button"
                            class="inline-flex h-4 w-4 items-center justify-center rounded-full hover:bg-gray-200"
                            @click="setVisibility('all')"
                        >
                            <span class="sr-only"
                                >Remove publication status filter</span
                            >
                            <XMarkIcon class="h-3 w-3" aria-hidden="true" />
                        </button>
                    </span>
                </div>

                <Menu as="div" class="relative inline-block text-left">
                    <MenuButton
                        class="group inline-flex justify-center text-sm font-medium text-gray-700 hover:text-gray-900"
                    >
                        Sort by:&nbsp;<span class="font-black text-gray-900">{{
                            currentSortLabel
                        }}</span>
                        <ChevronDownIcon
                            class="-mr-1 ml-1 h-5 w-5 shrink-0 text-gray-400 group-hover:text-gray-500"
                            aria-hidden="true"
                        />
                    </MenuButton>
                    <transition
                        enter-active-class="transition ease-out duration-100"
                        enter-from-class="transform opacity-0 scale-95"
                        enter-to-class="transform opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-75"
                        leave-from-class="transform opacity-100 scale-100"
                        leave-to-class="transform opacity-0 scale-95"
                    >
                        <MenuItems
                            class="absolute right-0 z-10 mt-2 w-52 origin-top-right rounded-md bg-white shadow-2xl ring-1 ring-black ring-opacity-5 focus:outline-none"
                        >
                            <div class="py-1">
                                <MenuItem
                                    v-for="option in sortOptions"
                                    :key="option.value"
                                    v-slot="{ active }"
                                >
                                    <button
                                        type="button"
                                        :class="[
                                            form.sort === option.value
                                                ? 'font-medium text-gray-900'
                                                : 'text-gray-500',
                                            active ? 'bg-gray-100' : '',
                                            'block w-full px-4 py-2 text-left text-sm',
                                        ]"
                                        @click="setSort(option.value)"
                                    >
                                        {{ option.name }}
                                    </button>
                                </MenuItem>
                            </div>
                        </MenuItems>
                    </transition>
                </Menu>
            </div>

            <div v-if="compounds.total > 0" class="mt-5">
                <compound-cards
                    class="block w-full min-w-0 max-w-none"
                    :molecules="compounds.data"
                />
                <div
                    v-if="compounds.last_page > 1"
                    class="mt-8 flex flex-col gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:items-center sm:justify-between"
                >
                    <p class="text-sm text-gray-600">
                        Showing
                        <span class="tabular-nums">{{ compounds.from }}</span>
                        –
                        <span class="tabular-nums">{{ compounds.to }}</span>
                        of
                        <span class="tabular-nums">{{ compounds.total }}</span>
                        compounds
                    </p>
                    <Pagination
                        :links="compounds.links"
                        navigation-label="Compound library pagination"
                    />
                </div>
            </div>
            <div v-else :class="publicEmptyStateSectionClasses">
                <EmptySearchState
                    layout="public"
                    entity-type="compounds"
                    :search-query="filters.q || filters.technique || ''"
                    :title="
                        hasActiveFilters
                            ? 'No compounds match your filters'
                            : 'No compounds deposited yet'
                    "
                    :message="
                        hasActiveFilters
                            ? 'Refine your search terms or select a different technique or publication status, or reset the filters to view the full library.'
                            : 'Compounds will be listed here once samples from this workspace are deposited on nmrXiv.'
                    "
                    :show-clear-button="hasActiveFilters"
                    @clear-search="reset"
                />
            </div>
        </div>
    </app-layout>
</template>

<script>
import { router } from "@inertiajs/vue3";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { ChevronDownIcon } from "@heroicons/vue/24/solid";
import {
    BeakerIcon,
    ChartBarIcon,
    CheckIcon,
    FolderIcon,
    LinkIcon,
    MagnifyingGlassIcon,
    RectangleStackIcon,
    XMarkIcon,
} from "@heroicons/vue/24/outline";
import { LockClosedIcon } from "@heroicons/vue/20/solid";
import AppLayout from "@/Layouts/AppLayout.vue";
import CompoundCards from "@/Shared/CompoundCards.vue";
import Pagination from "@/Shared/Pagination.vue";
import EmptySearchState from "@/Shared/EmptySearchState.vue";
import { publicEmptyStateSectionClasses } from "@/Utils/publicEmptyStateClasses.js";

export default {
    components: {
        AppLayout,
        CompoundCards,
        Pagination,
        EmptySearchState,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        ChevronDownIcon,
        CheckIcon,
        LinkIcon,
        LockClosedIcon,
        MagnifyingGlassIcon,
        XMarkIcon,
    },
    props: {
        library: {
            type: Object,
            required: true,
        },
        compounds: {
            type: Object,
            required: true,
        },
        stats: {
            type: Object,
            default: () => ({}),
        },
        unpublished: {
            type: Object,
            default: () => ({ compounds: 0, samples: 0, spectra: 0 }),
        },
        techniques: {
            type: Array,
            default: () => [],
        },
        filters: {
            type: Object,
            default: () => ({
                q: "",
                technique: "",
                sort: "recent",
                visibility: "all",
            }),
        },
    },
    data() {
        return {
            publicEmptyStateSectionClasses,
            form: {
                q: this.filters.q || "",
                technique: this.filters.technique || "",
                sort: this.filters.sort || "recent",
                visibility: this.filters.visibility || "all",
            },
            visibilityOptions: [
                { name: "All compounds", value: "all" },
                { name: "Published", value: "public" },
                { name: "Unpublished", value: "private" },
            ],
            sortOptions: [
                { name: "Recently added", value: "recent" },
                { name: "Name (A–Z)", value: "name" },
                { name: "Most samples", value: "samples" },
                { name: "Molecular weight (low → high)", value: "weight_asc" },
                { name: "Molecular weight (high → low)", value: "weight_desc" },
            ],
            searchTimer: null,
            copied: false,
            copiedTimer: null,
            showAllTechniques: false,
            techniquePreviewCount: 6,
        };
    },
    computed: {
        description() {
            const subject = this.library.personal_team
                ? `Compounds characterised by ${this.library.name}`
                : "Compounds characterised by members of this workspace";

            return `${subject} and deposited on nmrXiv, together with their NMR spectra. Published entries link to the full compound record; unpublished entries are shown for reference and become accessible once released.`;
        },
        pageTitle() {
            return `${this.library.name} · Compound library`;
        },
        lastUpdatedLabel() {
            if (!this.stats.last_updated_at) {
                return null;
            }
            const date = new Date(this.stats.last_updated_at);

            return Number.isNaN(date.getTime())
                ? null
                : date.toLocaleDateString(undefined, {
                      year: "numeric",
                      month: "short",
                      day: "numeric",
                  });
        },
        statTiles() {
            return [
                {
                    label: "Published compounds",
                    value: this.stats.compounds,
                    unpublished: this.unpublished.compounds,
                    icon: BeakerIcon,
                    iconClasses: "bg-indigo-50 text-indigo-600",
                },
                {
                    label: "Samples",
                    value: this.stats.samples,
                    unpublished: this.unpublished.samples,
                    icon: RectangleStackIcon,
                    iconClasses: "bg-teal-50 text-teal-600",
                },
                {
                    label: "Spectra",
                    value: this.stats.spectra,
                    unpublished: this.unpublished.spectra,
                    icon: ChartBarIcon,
                    iconClasses: "bg-purple-50 text-purple-600",
                },
                {
                    label: "Projects",
                    value: this.stats.projects,
                    unpublished: 0,
                    icon: FolderIcon,
                    iconClasses: "bg-amber-50 text-amber-600",
                },
            ];
        },
        visibleTechniques() {
            if (this.showAllTechniques) {
                return this.techniques;
            }
            const preview = this.techniques.slice(
                0,
                this.techniquePreviewCount
            );
            const active = this.techniques.find(
                (t) => t.label === this.form.technique
            );
            if (active && !preview.includes(active)) {
                preview.push(active);
            }

            return preview;
        },
        maxTechniqueCount() {
            return this.techniques.reduce(
                (max, t) => Math.max(max, this.techniqueTotal(t)),
                0
            );
        },
        hasUnpublishedTechniques() {
            return this.techniques.some(
                (t) => Number(t.unpublished_compounds) > 0
            );
        },
        showVisibilityFilter() {
            return (
                Number(this.unpublished.compounds) > 0 ||
                this.form.visibility !== "all"
            );
        },
        currentSortLabel() {
            return (
                this.sortOptions.find((o) => o.value === this.form.sort)
                    ?.name ?? "Recently added"
            );
        },
        currentVisibilityLabel() {
            return (
                this.visibilityOptions.find(
                    (o) => o.value === this.filters.visibility
                )?.name ?? "All compounds"
            );
        },
        hasActiveFilters() {
            return Boolean(
                this.filters.q ||
                    this.filters.technique ||
                    (this.filters.visibility &&
                        this.filters.visibility !== "all")
            );
        },
    },
    beforeUnmount() {
        clearTimeout(this.searchTimer);
        clearTimeout(this.copiedTimer);
    },
    methods: {
        formatNumber(value) {
            return (Number(value) || 0).toLocaleString();
        },
        techniqueTotal(technique) {
            return (
                (Number(technique.compounds) || 0) +
                (Number(technique.unpublished_compounds) || 0)
            );
        },
        techniqueBarWidth(count) {
            const n = Number(count) || 0;
            if (this.maxTechniqueCount === 0 || n === 0) {
                return "0%";
            }

            return `${Math.max(3, (n / this.maxTechniqueCount) * 100)}%`;
        },
        visit() {
            const params = {};
            if (this.form.q.trim() !== "") {
                params.q = this.form.q.trim();
            }
            if (this.form.technique !== "") {
                params.technique = this.form.technique;
            }
            if (this.form.sort !== "recent") {
                params.sort = this.form.sort;
            }
            if (this.form.visibility !== "all") {
                params.visibility = this.form.visibility;
            }

            router.get(
                this.route("public.compound-library", {
                    team: this.library.code,
                }),
                params,
                { preserveState: true, preserveScroll: true, replace: true }
            );
        },
        onSearchInput() {
            clearTimeout(this.searchTimer);
            this.searchTimer = setTimeout(() => this.visit(), 300);
        },
        setTechnique(label) {
            this.form.technique = label;
            this.visit();
        },
        toggleTechnique(label) {
            this.setTechnique(this.form.technique === label ? "" : label);
        },
        setSort(sort) {
            this.form.sort = sort;
            this.visit();
        },
        setVisibility(visibility) {
            this.form.visibility = visibility;
            this.visit();
        },
        reset() {
            clearTimeout(this.searchTimer);
            this.form.q = "";
            this.form.technique = "";
            this.form.visibility = "all";
            this.visit();
        },
        async copyShareLink() {
            const url = this.library.share_url;
            try {
                await navigator.clipboard.writeText(url);
            } catch {
                const el = document.createElement("textarea");
                el.value = url;
                el.setAttribute("readonly", "");
                el.style.position = "fixed";
                el.style.left = "-9999px";
                document.body.appendChild(el);
                el.select();
                document.execCommand("copy");
                document.body.removeChild(el);
            }

            this.copied = true;
            clearTimeout(this.copiedTimer);
            this.copiedTimer = setTimeout(() => {
                this.copied = false;
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
