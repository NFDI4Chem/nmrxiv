<template>
  <div>
    <div class="hover:shadow-lg" v-if="project">
      <div class="flex flex-col rounded-lg shadow-lg">
        <div
          class="rounded-t-lg bg-gradient-to-tl from-sky-400 to-cyan-300 w-full h-36 bg-gray-200 overflow-hidden group-hover:opacity-75 lg:h-36 xl:h-36"
        >
          <img
            :src="project.photo_url"
            alt=""
            class="w-full h-full object-center object-cover"
          />
          <div class="float-right place-self-end">
            <div class="p-2 flex items-center">
              <div class="flex-shrink-0">
                <span
                  v-if="project.stats"
                  class="relative z-0 inline-flex shadow-sm rounded-md"
                >
                  <button
                    type="button"
                    @click="toggleUpVote()"
                    class="relative inline-flex items-center px-1 py-1 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-900 hover:bg-gray-50"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </button>
                  <a
                    class="-ml-px relative inline-flex items-center px-2 py-1 rounded-r-md border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    {{ project.stats.likes }}
                  </a>
                </span>
              </div>
            </div>
          </div>
        </div>

        <div
          :class="[
            mode != 'mini' ? '' : 'rounded-b-lg',
            'flex-1 bg-white flex flex-col justify-between',
          ]"
        >
          <div style="min-height: 150px; max-height: 168px" class="flex-1 p-3">
            <Link :href="'/projects/' + project.slug" class="block cursor-pointer">
              <p class="text-lg font-semibold text-gray-900 line-clamp-2">
                {{ project.name }}
              </p>
              <p class="mt-2 text-sm text-gray-900 line-clamp-3">
                {{ project.description }}
              </p>
            </Link>
          </div>
        </div>
        <div v-if="mode != 'mini'" class="p-3 rounded-b-lg bg-white border-t flex">
          <div class="flex-0.5 self-center align-middle">
            <img class="h-7 w-7 rounded-full" :src="project.owner.profile_photo_url" />
          </div>
          <div class="flex-auto pl-4">
            <p class="text-xs font-xs text-gray-900">
              <a>
                {{ project.owner.first_name }} {{ project.owner.last_name }}</a
              >
            </p>
            <div class="flex-1 space-x-1 text-xs font-xs text-gray-500">
              <time datetime="2020-03-16">{{ formatDate(project.created_at) }} </time>
            </div>
          </div>
          <div class="flex-0.5 self-center">
            <Menu as="div" class="relative text-left">
              <div>
                <MenuButton
                  class="bg-white rounded-full flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500"
                >
                  <span class="sr-only">Open options</span>
                  <DotsVerticalIcon class="h-5 w-5" aria-hidden="true" />
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
                  class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                >
                  <div class="py-1">
                    <MenuItem class="border-b" v-slot="{ active }">
                      <a
                        :class="[
                          active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                          'block px-4 py-2 text-sm',
                        ]"
                        >Download</a
                      >
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                      <a
                        :class="[
                          active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                          'block px-4 py-2 text-sm',
                        ]"
                        ><small class="text-gray-500">License</small> <br />
                        {{ project.license.title }}</a
                      >
                    </MenuItem>
                  </div>
                </MenuItems>
              </transition>
            </Menu>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { LockClosedIcon } from "@heroicons/vue/solid";
import { LockOpenIcon } from "@heroicons/vue/solid";
import { PencilIcon } from "@heroicons/vue/solid";
import { MailIcon } from "@heroicons/vue/solid";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { DotsVerticalIcon } from "@heroicons/vue/solid";
import { Inertia } from "@inertiajs/inertia";
import { Head, Link } from "@inertiajs/inertia-vue3";

export default {
  components: {
    Head,
    Link,
    LockClosedIcon,
    LockOpenIcon,
    MailIcon,
    PencilIcon,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    DotsVerticalIcon,
  },
  setup() {},
  props: ["project", "mode"],
  data() {
    return {};
  },
  methods: {
    toggleUpVote() {
      const url = "/projects/" + this.project.id + "/toggleUpVote";
      axios
        .get(url)
        .catch((err) => {
          if (err.response.status !== 200 || err.response.status !== 201) {
            throw new Error(
              `API call failed with status code: ${err.response.status} after multiple attempts`
            );
          }
        })
        .then(function (response) {
          console.log(response);
          Inertia.reload({ only: ["projects"] });
        });
    },
  },
};
</script>
