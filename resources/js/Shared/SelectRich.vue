<template>
    <Listbox v-model="proxySelected" as="div">
        <ListboxLabel
            v-if="label"
            class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
            >{{ label }}</ListboxLabel
        >
        <div class="mt-1 relative">
            <ListboxButton
                class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 sm:text-sm"
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
                                            v-html="item.description"
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
import { ref } from "vue";
import {
    Listbox,
    ListboxButton,
    ListboxLabel,
    ListboxOption,
    ListboxOptions,
} from "@headlessui/vue";
import { CheckIcon, ChevronUpDownIcon } from "@heroicons/vue/24/solid";
import ToolTip from "@/Shared/ToolTip.vue";

export default {
    components: {
        Listbox,
        ListboxButton,
        ListboxLabel,
        ListboxOption,
        ListboxOptions,
        CheckIcon,
        ChevronUpDownIcon,
        ToolTip,
    },
    props: ["items", "selected", "label"],
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
            if (!this.items || !Array.isArray(this.items)) {
                console.log("SelectRich: No items or not array", this.items);
                return null;
            }

            // Debug: Log first few items to see structure
            console.log("SelectRich: First 3 items:", this.items.slice(0, 3));

            // Check if items have category field, if not return null to use ungrouped view
            const hasCategories = this.items.some((item) => item.category);
            console.log("SelectRich: Has categories?", hasCategories);
            console.log(
                "SelectRich: Categories found:",
                this.items.map((item) => item.category).filter(Boolean)
            );

            if (!hasCategories) {
                console.log(
                    "SelectRich: No categories found, using ungrouped view"
                );
                return null;
            }

            // Group items by category
            const grouped = this.items.reduce((groups, item) => {
                const category = item.category || "Other";
                if (!groups[category]) {
                    groups[category] = [];
                }
                groups[category].push(item);
                return groups;
            }, {});

            console.log("SelectRich: Grouped items:", grouped);

            // Sort categories and items within each category
            const sortedGrouped = {};
            Object.keys(grouped)
                .sort()
                .forEach((category) => {
                    sortedGrouped[category] = grouped[category].sort((a, b) =>
                        a.title.localeCompare(b.title)
                    );
                });

            console.log(
                "SelectRich: Final sorted grouped items:",
                sortedGrouped
            );
            return sortedGrouped;
        },
    },
};
</script>
