<template>
    <div class="relative">
        <Menu as="div" class="relative inline-block text-left">
            <div>
                <MenuButton
                    class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 bg-white text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none"
                >
                    <AdjustmentsHorizontalIcon class="h-5 w-5 text-gray-400" />
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
                    class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                >
                    <div v-if="variant === 'workflow'" class="py-1">
                        <MenuItem v-slot="{ active }">
                            <button
                                :class="[
                                    active
                                        ? 'bg-gray-100 text-gray-900'
                                        : 'text-gray-700',
                                    modelValue === 'all'
                                        ? 'font-semibold'
                                        : 'font-normal',
                                    'group flex w-full items-center px-4 py-2 text-sm',
                                ]"
                                type="button"
                                @click="$emit('update:modelValue', 'all')"
                            >
                                All Status
                            </button>
                        </MenuItem>
                        <MenuItem v-slot="{ active }">
                            <button
                                :class="[
                                    active
                                        ? 'bg-gray-100 text-gray-900'
                                        : 'text-gray-700',
                                    modelValue === 'draft'
                                        ? 'font-semibold'
                                        : 'font-normal',
                                    'group flex w-full items-center px-4 py-2 text-sm',
                                ]"
                                type="button"
                                @click="$emit('update:modelValue', 'draft')"
                            >
                                Draft
                            </button>
                        </MenuItem>
                        <MenuItem v-slot="{ active }">
                            <button
                                :class="[
                                    active
                                        ? 'bg-gray-100 text-gray-900'
                                        : 'text-gray-700',
                                    modelValue === 'published'
                                        ? 'font-semibold'
                                        : 'font-normal',
                                    'group flex w-full items-center px-4 py-2 text-sm',
                                ]"
                                type="button"
                                @click="$emit('update:modelValue', 'published')"
                            >
                                Published
                            </button>
                        </MenuItem>
                        <MenuItem v-slot="{ active }">
                            <button
                                :class="[
                                    active
                                        ? 'bg-gray-100 text-gray-900'
                                        : 'text-gray-700',
                                    modelValue === 'archived'
                                        ? 'font-semibold'
                                        : 'font-normal',
                                    'group flex w-full items-center px-4 py-2 text-sm',
                                ]"
                                type="button"
                                @click="$emit('update:modelValue', 'archived')"
                            >
                                Archived
                            </button>
                        </MenuItem>
                        <MenuItem v-slot="{ active }">
                            <button
                                :class="[
                                    active
                                        ? 'bg-gray-100 text-gray-900'
                                        : 'text-gray-700',
                                    modelValue === 'embargo'
                                        ? 'font-semibold'
                                        : 'font-normal',
                                    'group flex w-full items-center px-4 py-2 text-sm',
                                ]"
                                type="button"
                                @click="$emit('update:modelValue', 'embargo')"
                            >
                                Embargo
                            </button>
                        </MenuItem>
                    </div>

                    <div v-else class="py-1">
                        <MenuItem v-slot="{ active }">
                            <button
                                :class="[
                                    active
                                        ? 'bg-gray-100 text-gray-900'
                                        : 'text-gray-700',
                                    modelValue === 'all'
                                        ? 'font-semibold'
                                        : 'font-normal',
                                    'group flex w-full items-center px-4 py-2 text-sm',
                                ]"
                                type="button"
                                @click="$emit('update:modelValue', 'all')"
                            >
                                All
                            </button>
                        </MenuItem>
                        <MenuItem v-slot="{ active }">
                            <button
                                :class="[
                                    active
                                        ? 'bg-gray-100 text-gray-900'
                                        : 'text-gray-700',
                                    modelValue === 'public'
                                        ? 'font-semibold'
                                        : 'font-normal',
                                    'group flex w-full items-center px-4 py-2 text-sm',
                                ]"
                                type="button"
                                @click="$emit('update:modelValue', 'public')"
                            >
                                Public
                            </button>
                        </MenuItem>
                        <MenuItem v-slot="{ active }">
                            <button
                                :class="[
                                    active
                                        ? 'bg-gray-100 text-gray-900'
                                        : 'text-gray-700',
                                    modelValue === 'private'
                                        ? 'font-semibold'
                                        : 'font-normal',
                                    'group flex w-full items-center px-4 py-2 text-sm',
                                ]"
                                type="button"
                                @click="$emit('update:modelValue', 'private')"
                            >
                                Private
                            </button>
                        </MenuItem>
                    </div>
                </MenuItems>
            </transition>
        </Menu>
    </div>
</template>

<script>
import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
import { AdjustmentsHorizontalIcon } from "@heroicons/vue/24/outline";

export default {
    components: {
        Menu,
        MenuButton,
        MenuItems,
        MenuItem,
        AdjustmentsHorizontalIcon,
    },
    props: {
        modelValue: {
            type: String,
            default: "all",
        },
        /**
         * workflow: draft / published / … (projects).
         * visibility: public / private (compound library studies).
         */
        variant: {
            type: String,
            default: "workflow",
            validator: (v) => ["workflow", "visibility"].includes(v),
        },
    },
};
</script>
