<template>
  <Menu as="div" class="relative inline-block">
    <div>
      <span v-if="mode == 'button'">
        <MenuButton
          id="v-step-0"
          class="p-3 inline-flex items-center text-center border border-transparent text-base rounded-full shadow-sm text-white inline-flex items-center bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
        >
          <PlusIcon class="mr-3 ml-2 h-6 w-6 text-dark" aria-hidden="true" />
          Create &emsp;
        </MenuButton>
      </span>
      <span v-if="mode == 'icon'">
        <MenuButton
          id="v-step-0"
          class="inline-flex items-center text-center p-3 border border-transparent text-base font-medium rounded-full shadow-sm inline-flex items-center bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
        >
          <PlusIcon class="h-6 w-6 text-dark" aria-hidden="true" />
        </MenuButton>
      </span>
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
        class="z-50 origin-top-right fixed mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none"
      >
        <div class="py-1">
          <MenuItem v-slot="{ active }">
            <a
              href="#"
              :class="[
                active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                'group flex items-center px-4 py-2 text-sm',
              ]"
            >
              <PencilAltIcon
                class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500"
                aria-hidden="true"
              />
              Project
            </a>
          </MenuItem>
          <MenuItem v-slot="{ active }">
            <a
              href="#"
              :class="[
                active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                'group flex items-center px-4 py-2 text-sm',
              ]"
            >
              <ArchiveIcon
                class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500"
                aria-hidden="true"
              />
              Study
            </a>
          </MenuItem>
          <MenuItem v-slot="{ active }">
            <a
              href="#"
              :class="[
                active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                'group flex items-center px-4 py-2 text-sm',
              ]"
            >
              <UserAddIcon
                class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500"
                aria-hidden="true"
              />
              Dataset
            </a>
          </MenuItem>
        </div>
      </MenuItems>
    </transition>
  </Menu>
</template>

<script>
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import {
  ArchiveIcon,
  ArrowCircleRightIcon,
  ChevronDownIcon,
  DuplicateIcon,
  HeartIcon,
  PencilAltIcon,
  TrashIcon,
  UserAddIcon,
  PlusIcon,
} from "@heroicons/vue/solid";

export default {
  props: {
    mode: String,
  },
  components: {
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    ArchiveIcon,
    ArrowCircleRightIcon,
    ChevronDownIcon,
    DuplicateIcon,
    HeartIcon,
    PencilAltIcon,
    TrashIcon,
    UserAddIcon,
    PlusIcon,
  },
  setup() {},
  methods: {},
  computed: {},
};
</script>
