<template>
    <div>
        <div v-if="project" class="hover:shadow-lg">
            <div v-if="mode == 'mini' || mode == 'grid'">
                <div class="flex flex-col rounded-lg shadow-lg">
                    <div class="relative rounded-t-lg lg:h-36 xl:h-36">
                        <img
                            v-if="project.photo_url && project.photo_url != ''"
                            :src="project.photo_url"
                            alt=""
                            class="w-full h-full object-center rounded-t-lg object-cover"
                        />
                        <div
                            v-else
                            class="flex-shrink-0 lg:h-36 xl:h-36 pattern-diagonal-lines pattern-gray-400 pattern-bg-white pattern-size-2 pattern-opacity-20 border-b border-gray-400"
                        ></div>
                        <div class="absolute top-0 right-0">
                            <div class="p-2 flex items-center">
                                <div class="flex-shrink-0">
                                    <span
                                        v-if="project.stats"
                                        class="relative z-0 inline-flex shadow-sm rounded-md"
                                    >
                                        <button
                                            type="button"
                                            class="relative inline-flex items-center px-1 py-1 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-900 hover:bg-gray-50"
                                            @click="toggleUpVote()"
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
                        <div style="min-height: 168px" class="flex-1 p-3">
                            <small
                                v-if="project.identifier"
                                class="text-gray-500"
                                >#{{ project.identifier }}</small
                            >
                            {{}}
                            <Link
                                :href="project.public_url"
                                class="block cursor-pointer"
                            >
                                <p
                                    class="text-lg font-black text-gray-900 line-clamp-2"
                                >
                                    {{ project.name }}
                                </p>
                                <div>
                                    <dd
                                        class="text-xs text-gray-900 space-y-5 mt-1"
                                    >
                                        <p>
                                            <span
                                                v-for="tag in project.tags"
                                                :key="tag.id"
                                                class="mr-1"
                                            >
                                                <span
                                                    class="mb-1 inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-indigo-100 text-indigo-800"
                                                >
                                                    <svg
                                                        class="-ml-0.5 mr-1.5 h-2 w-2 text-indigo-400"
                                                        fill="currentColor"
                                                        viewBox="0 0 8 8"
                                                    >
                                                        <circle
                                                            cx="4"
                                                            cy="4"
                                                            r="3"
                                                        />
                                                    </svg>
                                                    {{ tag.name["en"] }}
                                                </span>
                                            </span>
                                        </p>
                                    </dd>
                                </div>
                                <p class="text-xs text-gray-500 line-clamp-3">
                                    {{ project.description }}
                                </p>
                            </Link>
                        </div>
                    </div>
                    <div
                        v-if="mode != 'mini'"
                        class="p-3 rounded-b-lg bg-white border-t flex"
                    >
                        <div class="flex-0.5 self-center align-middle">
                            <img
                                class="h-7 w-7 rounded-full"
                                :src="project.owner.profile_photo_url"
                            />
                        </div>
                        <div class="flex-auto pl-4">
                            <p class="text-xs font-xs font-semibold text-black">
                                <a>
                                    {{ project.owner.first_name }}
                                    {{ project.owner.last_name }}</a
                                >
                            </p>
                            <div
                                class="flex-1 space-x-1 text-xs font-xs text-gray-500"
                            >
                                <time datetime="2020-03-16"
                                    >{{ formatDate(project.created_at) }}
                                </time>
                            </div>
                        </div>
                        <div class="flex-0.5 self-center">
                            <Menu as="div" class="relative text-left">
                                <div>
                                    <MenuButton
                                        class="bg-white rounded-full flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500"
                                    >
                                        <span class="sr-only"
                                            >Open options</span
                                        >
                                        <EllipsisVerticalIcon
                                            class="h-5 w-5"
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
                                        class="absolute -right-2 mt-2 z-50 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                                    >
                                        <div>
                                            <MenuItem
                                                v-slot="{ active }"
                                                class="border-b"
                                            >
                                                <a
                                                    :href="downloadURL"
                                                    :class="[
                                                        active
                                                            ? 'bg-gray-100 text-gray-600'
                                                            : 'text-gray-700',
                                                        'block px-4 py-4 text-sm cursor-pointer hover:text-gray-900',
                                                    ]"
                                                >
                                                    <ArrowDownTrayIcon
                                                        class="h-5 w-5 inline"
                                                        aria-hidden="true"
                                                    />
                                                    Download</a
                                                >
                                            </MenuItem>
                                            <MenuItem>
                                                <div class="px-4 py-2">
                                                    <p class="pb-2">
                                                        <small
                                                            class="text-gray-500"
                                                            >License</small
                                                        ><br />
                                                        <span
                                                            class="mt-2 float rounded-full border text-xs whitespace-nowrap border-gray-200 items-center py-1.5 pl-3 pr-3 bg-white text-gray-900"
                                                            >{{
                                                                project.license
                                                                    .title
                                                            }}</span
                                                        >
                                                    </p>
                                                </div>
                                            </MenuItem>
                                        </div>
                                    </MenuItems>
                                </transition>
                            </Menu>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="mode == 'list'">
                <!-- This example requires Tailwind CSS v2.0+ -->
                <li class="flex border-b bprder-gray-100">
                    <div class="flex-shrink-0">
                        <img
                            v-if="project.photo_url && project.photo_url != ''"
                            :src="project.photo_url"
                            alt=""
                            class="border w-36 lg:h-36 xl:h-36 m-2 mr-0 h-full object-center object-cover"
                        />
                        <div
                            v-else
                            class="w-36 m-2 flex-shrink-0 border mr-0 lg:h-36 xl:h-36 pattern-diagonal-lines pattern-gray-400 pattern-bg-white pattern-size-2 pattern-opacity-20 border-b border-gray-400"
                        ></div>
                    </div>

                    <div
                        class="flex-1 flex flex-col px-4 py-2 sm:px-6 justify-between"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <small
                                    v-if="project.identifier"
                                    class="text-gray-500"
                                    >#{{ project.identifier }}</small
                                >
                                <p
                                    class="text-lg font-black text-gray-900 line-clamp-2 font-black"
                                >
                                    <Link
                                        :href="project.public_url"
                                        class="block cursor-pointer"
                                    >
                                        {{ project.name }}
                                    </Link>
                                </p>
                                <p
                                    class="my-2 text-sm text-gray-500 line-clamp-2 pr-8"
                                >
                                    {{ project.description }}
                                </p>
                            </div>
                            <div class="ml-2 flex-shrink-0 flex">
                                <div>
                                    <div class="p-2 flex items-center">
                                        <div class="flex-shrink-0">
                                            <span
                                                v-if="project.stats"
                                                class="relative z-0 inline-flex shadow-sm rounded-md"
                                            >
                                                <button
                                                    type="button"
                                                    class="relative inline-flex items-center px-1 py-1 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-900 hover:bg-gray-50"
                                                    @click="toggleUpVote()"
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
                        </div>
                        <div class="mt-2 sm:flex sm:justify-between">
                            <div class="sm:flex">
                                <p
                                    class="flex items-center text-sm text-gray-500"
                                >
                                    <img
                                        class="h-7 w-5 mr-2 rounded-full"
                                        :src="project.owner.profile_photo_url"
                                    />
                                    {{ project.owner.first_name }}
                                    {{ project.owner.last_name }}
                                </p>
                                <p
                                    class="flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-3"
                                >
                                    <ScaleIcon
                                        class="text-gray-400 h-5 w-5 mr-1.5"
                                    ></ScaleIcon>
                                    {{ project.license.title }}
                                </p>
                                <p
                                    class="flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-2"
                                >
                                    <a
                                        :href="downloadURL"
                                        class="block px-4 text-sm cursor-pointer hover:text-gray-900', ]"
                                    >
                                        <ArrowDownTrayIcon
                                            class="h-5 w-5 inline"
                                            aria-hidden="true"
                                    /></a>
                                </p>
                            </div>
                            <div
                                class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0"
                            >
                                <!-- Heroicon name: solid/calendar -->
                                <svg
                                    class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <p>
                                    <time datetime="2020-03-16"
                                        >{{ formatDate(project.created_at) }}
                                    </time>
                                </p>
                            </div>
                        </div>
                    </div>
                </li>
            </div>
        </div>
    </div>
</template>

<script>
import { LockClosedIcon } from "@heroicons/vue/24/solid";
import { LockOpenIcon, ArrowDownTrayIcon } from "@heroicons/vue/24/solid";
import { PencilIcon } from "@heroicons/vue/24/solid";
import { EnvelopeIcon } from "@heroicons/vue/24/solid";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { EllipsisVerticalIcon, ScaleIcon } from "@heroicons/vue/24/solid";
import { Inertia } from "@inertiajs/inertia";
import { Head, Link } from "@inertiajs/inertia-vue3";

export default {
    components: {
        Head,
        Link,
        LockClosedIcon,
        LockOpenIcon,
        ArrowDownTrayIcon,
        EnvelopeIcon,
        PencilIcon,
        ScaleIcon,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        EllipsisVerticalIcon,
    },
    props: ["project", "mode"],
    setup() {},
    data() {
        return {};
    },
    computed: {
        downloadURL() {
            return (
                this.url +
                "/" +
                this.project.owner.username +
                "/datasets/" +
                this.project.slug
            );
        },
        url() {
            return String(this.$page.props.url);
        },
    },
    methods: {
        toggleUpVote() {
            if (
                this.$page.props.user.username &&
                this.$page.props.user.username != ""
            ) {
                const url = "/projects/" + this.project.id + "/toggleUpVote";
                axios
                    .get(url)
                    .catch((err) => {
                        if (
                            err.response.status !== 200 ||
                            err.response.status !== 201
                        ) {
                            throw new Error(
                                `API call failed with status code: ${err.response.status} after multiple attempts`
                            );
                        }
                    })
                    .then(function (response) {
                        Inertia.reload({ only: ["projects"] });
                    });
            } else {
                this.$inertia.visit(route("login"));
            }
        },
    },
};
</script>
