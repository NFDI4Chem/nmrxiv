<template>
    <!-- Searchable variant powered by Combobox -->
    <Combobox v-if="searchable" v-model="proxySelected" as="div" by="id">
        <ComboboxLabel
            v-if="label"
            class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
            >{{ label }}</ComboboxLabel
        >
        <div class="mt-1 relative">
            <div
                class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm focus-within:ring-1 focus-within:ring-primary-500 focus-within:border-primary-500"
            >
                <ComboboxInput
                    class="w-full border-none bg-transparent pl-3 pr-10 py-2 text-sm text-gray-900 focus:ring-0 focus:outline-none"
                    :placeholder="placeholder || '--Select--'"
                    :display-value="(item) => (item ? item.title : '')"
                    @change="query = $event.target.value"
                    @focus="$event.target.select()"
                />
                <ComboboxButton
                    class="absolute inset-y-0 right-0 flex items-center pr-2"
                >
                    <ChevronUpDownIcon
                        class="h-5 w-5 text-gray-400"
                        aria-hidden="true"
                    />
                </ComboboxButton>
            </div>
            <transition
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
                @after-leave="query = ''"
            >
                <ComboboxOptions
                    class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
                >
                    <div
                        v-if="!filteredItems.length && query !== ''"
                        class="relative cursor-default select-none py-2 px-4 text-sm text-gray-500"
                    >
                        No matches found.
                    </div>
                    <template v-if="filteredGroupedItems">
                        <div
                            v-for="(group, categoryName) in filteredGroupedItems"
                            :key="categoryName"
                        >
                            <div
                                class="sticky top-0 z-10 px-4 py-3 text-sm font-bold text-gray-700 uppercase tracking-wider bg-gray-100 border-b border-gray-300 shadow-sm"
                            >
                                <span class="flex items-center">
                                    <svg
                                        class="w-4 h-4 mr-2 text-gray-500"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                    {{ categoryName }}
                                </span>
                            </div>
                            <ComboboxOption
                                v-for="item in group"
                                :key="item.id"
                                v-slot="{ active, selected }"
                                as="template"
                                :value="item"
                            >
                                <li
                                    :class="[
                                        active
                                            ? 'text-white bg-gray-600'
                                            : 'text-gray-900',
                                        'cursor-default border-b select-none relative py-2 pl-8 pr-4',
                                    ]"
                                >
                                    <span
                                        :class="[
                                            selected
                                                ? 'font-semibold'
                                                : 'font-normal',
                                            'block truncate',
                                        ]"
                                    >
                                        <b>{{ item.title }}</b> <br />
                                        <small
                                            v-if="item.description"
                                            v-html="
                                                sanitizeHtml(item.description)
                                            "
                                        >
                                        </small>
                                    </span>
                                    <span
                                        v-if="selected"
                                        :class="[
                                            active
                                                ? 'text-white'
                                                : 'text-gray-600',
                                            'absolute inset-y-0 left-0 flex items-center pl-1.5',
                                        ]"
                                    >
                                        <CheckIcon
                                            class="h-5 w-5"
                                            aria-hidden="true"
                                        />
                                    </span>
                                </li>
                            </ComboboxOption>
                        </div>
                    </template>
                    <template v-else>
                        <ComboboxOption
                            v-for="item in filteredItems"
                            :key="item.id"
                            v-slot="{ active, selected }"
                            as="template"
                            :value="item"
                        >
                            <li
                                :class="[
                                    active
                                        ? 'text-white bg-gray-600'
                                        : 'text-gray-900',
                                    'cursor-default border-b select-none relative py-2 pl-8 pr-4',
                                ]"
                            >
                                <span
                                    :class="[
                                        selected
                                            ? 'font-semibold'
                                            : 'font-normal',
                                        'block truncate',
                                    ]"
                                >
                                    <b>{{ item.title }}</b> <br />
                                    <small
                                        v-if="item.description"
                                        v-html="sanitizeHtml(item.description)"
                                    >
                                    </small>
                                </span>
                                <span
                                    v-if="selected"
                                    :class="[
                                        active ? 'text-white' : 'text-gray-600',
                                        'absolute inset-y-0 left-0 flex items-center pl-1.5',
                                    ]"
                                >
                                    <CheckIcon
                                        class="h-5 w-5"
                                        aria-hidden="true"
                                    />
                                </span>
                            </li>
                        </ComboboxOption>
                    </template>
                </ComboboxOptions>
            </transition>
        </div>
    </Combobox>
    <Listbox v-else v-model="proxySelected" as="div">
        <ListboxLabel
            v-if="label"
            class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
            >{{ label }}</ListboxLabel
        >
        <div class="mt-1 relative">
            <ListboxButton
                class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
            >
                <span v-if="selected" class="block truncate">{{
                    selected.title
                }}</span>
                <span v-else class="block truncate">--Select--</span>
                <span
                    class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none"
                >
                    <ChevronUpDownIcon
                        class="h-5 w-5 text-gray-400"
                        aria-hidden="true"
                    />
                </span>
            </ListboxButton>
            <transition
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <ListboxOptions
                    class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
                >
                    <template v-if="groupedItems">
                        <div
                            v-for="(group, categoryName) in groupedItems"
                            :key="categoryName"
                        >
                            <!-- Category Header -->
                            <div
                                class="sticky top-0 z-10 px-4 py-3 text-sm font-bold text-gray-700 uppercase tracking-wider bg-gray-100 border-b border-gray-300 shadow-sm"
                            >
                                <span class="flex items-center">
                                    <svg
                                        class="w-4 h-4 mr-2 text-gray-500"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                    {{ categoryName }}
                                </span>
                            </div>
                            <!-- Category Items -->
                            <ListboxOption
                                v-for="item in group"
                                :key="item.id"
                                v-slot="{ active, selected }"
                                as="template"
                                :value="item"
                            >
                                <li
                                    :class="[
                                        active
                                            ? 'text-white bg-gray-600'
                                            : 'text-gray-900',
                                        'cursor-default border-b select-none relative py-2 pl-8 pr-4',
                                    ]"
                                >
                                    <span
                                        :class="[
                                            selected
                                                ? 'font-semibold'
                                                : 'font-normal',
                                            'block truncate',
                                        ]"
                                    >
                                        <b>{{ item.title }}</b> <br />
                                        <small
                                            v-if="item.description"
                                            v-html="
                                                sanitizeHtml(item.description)
                                            "
                                        >
                                        </small>
                                    </span>
                                    <span
                                        v-if="selected"
                                        :class="[
                                            active
                                                ? 'text-white'
                                                : 'text-gray-600',
                                            'absolute inset-y-0 left-0 flex items-center pl-1.5',
                                        ]"
                                    >
                                        <CheckIcon
                                            class="h-5 w-5"
                                            aria-hidden="true"
                                        />
                                    </span>
                                </li>
                            </ListboxOption>
                        </div>
                    </template>
                    <template v-else>
                        <ListboxOption
                            v-for="item in items"
                            :key="item.id"
                            v-slot="{ active, selected }"
                            as="template"
                            :value="item"
                        >
                            <li
                                :class="[
                                    active
                                        ? 'text-white bg-gray-600'
                                        : 'text-gray-900',
                                    'cursor-default border-b select-none relative py-2 pl-8 pr-4',
                                ]"
                            >
                                <span
                                    :class="[
                                        selected
                                            ? 'font-semibold'
                                            : 'font-normal',
                                        'block truncate',
                                    ]"
                                >
                                    <b>{{ item.title }}</b> <br />
                                    <small
                                        v-if="item.description"
                                        v-html="item.description"
                                    >
                                    </small>
                                </span>
                                <span
                                    v-if="selected"
                                    :class="[
                                        active ? 'text-white' : 'text-gray-600',
                                        'absolute inset-y-0 left-0 flex items-center pl-1.5',
                                    ]"
                                >
                                    <CheckIcon
                                        class="h-5 w-5"
                                        aria-hidden="true"
                                    />
                                </span>
                            </li>
                        </ListboxOption>
                    </template>
                </ListboxOptions>
            </transition>
        </div>
    </Listbox>
</template>

<script>
import {
    Combobox,
    ComboboxButton,
    ComboboxInput,
    ComboboxLabel,
    ComboboxOption,
    ComboboxOptions,
    Listbox,
    ListboxButton,
    ListboxLabel,
    ListboxOption,
    ListboxOptions,
} from "@headlessui/vue";
import { CheckIcon, ChevronUpDownIcon } from "@heroicons/vue/24/solid";

export default {
    components: {
        Combobox,
        ComboboxButton,
        ComboboxInput,
        ComboboxLabel,
        ComboboxOption,
        ComboboxOptions,
        Listbox,
        ListboxButton,
        ListboxLabel,
        ListboxOption,
        ListboxOptions,
        CheckIcon,
        ChevronUpDownIcon,
    },
    props: {
        items: {
            type: Array,
            default: () => [],
        },
        selected: {
            type: Object,
            default: null,
        },
        label: {
            type: String,
            default: null,
        },
        searchable: {
            type: Boolean,
            default: false,
        },
        placeholder: {
            type: String,
            default: null,
        },
    },
    data() {
        return {
            query: "",
        };
    },
    computed: {
        proxySelected: {
            get() {
                return this.selected;
            },

            set(val) {
                this.$emit("update:selected", val);
            },
        },
        groupedItems() {
            return this.groupByCategory(this.items);
        },
        filteredItems() {
            if (!this.items || !Array.isArray(this.items)) {
                return [];
            }

            const q = this.query.trim().toLowerCase();
            if (q === "") {
                return this.items;
            }

            return this.items.filter((item) => {
                const haystack = [
                    item.title,
                    item.description,
                    item.category,
                ]
                    .filter(Boolean)
                    .join(" ")
                    .toLowerCase();

                return haystack.includes(q);
            });
        },
        filteredGroupedItems() {
            return this.groupByCategory(this.filteredItems);
        },
    },
    methods: {
        groupByCategory(items) {
            if (!items || !Array.isArray(items) || items.length === 0) {
                return null;
            }

            const hasCategories = items.some((item) => item.category);
            if (!hasCategories) {
                return null;
            }

            const grouped = items.reduce((groups, item) => {
                const category = item.category || "Other";
                if (!groups[category]) {
                    groups[category] = [];
                }
                groups[category].push(item);
                return groups;
            }, {});

            const sortedGrouped = {};
            Object.keys(grouped)
                .sort()
                .forEach((category) => {
                    sortedGrouped[category] = grouped[category].sort((a, b) =>
                        a.title.localeCompare(b.title)
                    );
                });

            return sortedGrouped;
        },
    },
};
</script>
