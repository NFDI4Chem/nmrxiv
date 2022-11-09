<template>
    <div>
        <div v-if="dataset" class="hover:shadow-lg">
            <div v-if="mode == 'mini' || mode == 'grid'">
                <div class="flex flex-col rounded-lg shadow-lg">
                    <div class="relative rounded-t lg:h-32 xl:h-32">
                        <div
                            v-if="dataset.dataset_photo_url"
                            class="flex-shrink-0 h-32 p-3 border rounded-t border-gray-100"
                        >
                            <img
                                :src="dataset.dataset_photo_url"
                                alt=""
                                class="w-full h-full object-center object-cover"
                            />
                        </div>
                        <div
                            v-else
                            class="flex-shrink-0 h-32 pattern-diagonal-lines pattern-gray-400 pattern-bg-white pattern-size-2 pattern-opacity-20 border-b border-gray-400"
                        ></div>
                    </div>
                    <div
                        :class="[
                            mode != 'mini' ? '' : 'rounded-b-lg',
                            'flex-1 bg-white flex flex-col justify-between',
                        ]"
                    >
                        <div
                            style="min-height: 100px; max-height: 168px"
                            class="flex-1 p-3"
                        >
                            <Link :href="dataset.public_url" class="block">
                                <small
                                    v-if="dataset.identifier"
                                    class="text-gray-500"
                                    >#{{ dataset.identifier }}</small
                                >
                                <p
                                    class="text-lg font-black text-gray-900 line-clamp-2"
                                >
                                    {{ dataset.name }}
                                </p>
                                <p
                                    class="mt-0 text-sm text-gray-800 line-clamp-4"
                                >
                                    <small class="text-gray-400">Project:</small
                                    ><br />
                                    <Link
                                        :href="dataset.project.public_url"
                                        class="hover:underline hover:text-teal-900"
                                        >{{ dataset.project.name }}</Link
                                    >&nbsp;/&nbsp;<Link
                                        class="hover:underline hover:text-teal-900"
                                        :href="dataset.study.public_url"
                                        >{{ dataset.study.name }}</Link
                                    >
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
                                :src="dataset.owner.profile_photo_url"
                            />
                        </div>
                        <div class="flex-auto pl-4">
                            <p class="text-xs font-xs font-semibold text-black">
                                <Link>
                                    {{ dataset.owner.first_name }}
                                    {{ dataset.owner.last_name }}</Link
                                >
                            </p>
                            <div
                                class="flex-1 space-x-1 text-xs font-xs text-gray-500"
                            >
                                <time datetime="2020-03-16"
                                    >{{ formatDate(dataset.created_at) }}
                                </time>
                            </div>
                        </div>
                        <!-- <div class="flex-0.5 self-center">
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
                                                <Link
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
                                                    Download</Link
                                                >
                                            </MenuItem>
                                            <MenuItem v-if="dataset.license">
                                                <div class="px-4 py-2">
                                                    <p class="pb-2">
                                                        <small
                                                            class="text-gray-500"
                                                            >License</small
                                                        ><br />
                                                        <span
                                                            class="mt-2 float rounded-full border text-xs whitespace-nowrap border-gray-200 items-center py-1.5 pl-3 pr-3 bg-white text-gray-900"
                                                            >{{
                                                                dataset.license
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
                        </div> -->
                    </div>
                </div>
            </div>
            <div v-if="mode == 'list'">
                <li class="flex border-b bprder-gray-100">
                    <div class="flex-shrink-0">
                        <div
                            v-if="dataset.dataset_photo_url"
                            class="flex-shrink-0 w-64 border-r h-36 p-3"
                        >
                            <img
                                :src="dataset.dataset_photo_url"
                                alt=""
                                class="w-full h-full object-center object-cover"
                            />
                        </div>
                        <div
                            v-else
                            class="flex-shrink-0 h-36 w-64 border-r pattern-diagonal-lines pattern-gray-400 pattern-bg-white pattern-size-2 pattern-opacity-20 border-b border-gray-400"
                        ></div>
                    </div>

                    <div
                        class="flex-1 flex flex-col px-4 py-2 sm:px-6 justify-between"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <small
                                    v-if="dataset.identifier"
                                    class="text-gray-500"
                                    >#{{ dataset.identifier }}</small
                                >
                                <p
                                    class="text-lg font-black text-gray-900 line-clamp-2 font-black"
                                >
                                    <Link
                                        :href="dataset.public_url"
                                        class="block"
                                    >
                                        {{ dataset.name }}
                                    </Link>
                                </p>
                                <p
                                    class="mt-0 text-sm text-gray-800 line-clamp-4"
                                >
                                    <small class="text-gray-400">Project:</small
                                    ><br />
                                    <Link
                                        :href="dataset.project.public_url"
                                        class="hover:underline hover:text-teal-900"
                                        >{{ dataset.project.name }}</Link
                                    >&nbsp;/&nbsp;<Link
                                        class="hover:underline hover:text-teal-900"
                                        :href="dataset.study.public_url"
                                        >{{ dataset.study.name }}</Link
                                    >
                                </p>
                            </div>
                        </div>
                        <div class="mt-2 sm:flex sm:justify-between">
                            <div class="sm:flex">
                                <p
                                    class="flex items-center text-sm text-gray-500"
                                >
                                    <img
                                        class="h-7 w-5 mr-2 rounded-full"
                                        :src="dataset.owner.profile_photo_url"
                                    />
                                    {{ dataset.owner.first_name }}
                                    {{ dataset.owner.last_name }}
                                </p>
                                <p
                                    v-if="dataset.license"
                                    class="flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-3"
                                >
                                    <ScaleIcon
                                        class="text-gray-400 h-5 w-5 mr-1.5"
                                    ></ScaleIcon>
                                    {{ dataset.license.title }}
                                </p>
                                <!-- <p
                                    class="flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-2"
                                >
                                    <Link
                                        :href="downloadURL"
                                        class="block px-4 text-sm cursor-pointer hover:text-gray-900', ]"
                                    >
                                        <ArrowDownTrayIcon
                                            class="h-5 w-5 inline"
                                            aria-hidden="true"
                                    /></Link>
                                </p> -->
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
                                        >{{ formatDate(dataset.created_at) }}
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
    props: ["dataset", "mode"],
    setup() {},
    data() {
        return {};
    },
    computed: {
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
                const url = "/projects/" + this.dataset.id + "/toggleUpVote";
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
                        Inertia.reload({ only: ["datasets"] });
                    });
            } else {
                this.$inertia.visit(route("login"));
            }
        },
    },
};
</script>
