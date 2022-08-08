<template>
    <Head :title="title" />
    <AppTour />
    <jet-banner />
    <announcement-banner />
    <div class="h-full flex min-h-screen">
        <TransitionRoot as="template" :show="sidebarOpen">
            <Dialog
                as="div"
                class="fixed inset-0 flex z-40 md:hidden"
                @close="sidebarOpen = false"
            >
                <TransitionChild
                    as="template"
                    enter="transition-opacity ease-linear duration-300"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="transition-opacity ease-linear duration-300"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <DialogOverlay
                        class="fixed inset-0 bg-gray-600 bg-opacity-75"
                    />
                </TransitionChild>
                <TransitionChild
                    as="template"
                    enter="transition ease-in-out duration-300 transform"
                    enter-from="-translate-x-full"
                    enter-to="translate-x-0"
                    leave="transition ease-in-out duration-300 transform"
                    leave-from="translate-x-0"
                    leave-to="-translate-x-full"
                >
                    <div
                        class="relative flex-1 flex flex-col max-w-xs w-full pt-5 pb-4 bg-white"
                    >
                        <TransitionChild
                            as="template"
                            enter="ease-in-out duration-300"
                            enter-from="opacity-0"
                            enter-to="opacity-100"
                            leave="ease-in-out duration-300"
                            leave-from="opacity-100"
                            leave-to="opacity-0"
                        >
                            <div class="absolute top-0 right-0 -mr-12 pt-2">
                                <button
                                    type="button"
                                    class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                                    @click="sidebarOpen = false"
                                >
                                    <span class="sr-only">Close sidebar</span>
                                    <XIcon
                                        class="h-6 w-6 text-white"
                                        aria-hidden="true"
                                    />
                                </button>
                            </div>
                        </TransitionChild>
                        <div class="flex-shrink-0 flex items-center px-4">
                            <Link :href="route('dashboard')">
                                <jet-application-logo
                                    class="block h-8 w-auto"
                                />
                            </Link>
                        </div>
                        <div class="mt-1 flex-1 h-0 overflow-y-auto">
                            <div class="my-4 mx-4">
                                <create mode="button"></create>
                            </div>
                            <nav
                                v-for="item in filteredNavigation"
                                :key="item.name"
                                class="flex-1 px-2 bg-white space-y-1"
                            >
                                <Link
                                    :href="item.href"
                                    class="my-6 text-gray-900 group flex items-center px-2 text-sm font-medium rounded-md"
                                >
                                    <div class="pl-2 pr-4">
                                        <component
                                            :is="item.icon"
                                            class="h-6 w-6"
                                            aria-hidden="true"
                                        />
                                    </div>
                                    {{ item.name }}
                                </Link>
                            </nav>
                        </div>
                    </div>
                </TransitionChild>
                <div class="flex-shrink-0 w-14" aria-hidden="true">
                    <!-- Dummy element to force sidebar to shrink to fit close icon -->
                </div>
            </Dialog>
        </TransitionRoot>
        <div
            v-if="collapseSidebar"
            class="hidden bg-white border-r md:flex md:flex-shrink-0 md:inset-y-0 z-10"
        >
            <div class="flex flex-col w-20">
                <div class="flex-1 flex flex-col min-h-0 overflow-y-auto">
                    <div class="flex-1">
                        <div
                            class="bg-white border-b pb-4 pt-4 flex flex-shrink-0 px-4"
                        >
                            <Link :href="route('welcome')">
                                <jet-application-mark
                                    class="block h-8 p-0.5 ml-1.5 w-auto"
                                />
                            </Link>
                        </div>
                        <div class="px-4 flex flex-col mt-3 mb-1">
                            <create mode="icon"></create>
                        </div>
                        <nav
                            aria-label="Sidebar"
                            class="flex flex-col items-center py-4 px-0"
                        >
                            <Link
                                v-for="item in filteredNavigation"
                                :key="item.name"
                                :href="item.href"
                                :class="[
                                    $page.url === item.href
                                        ? ' border-r-4 bg-gray-200 border-r-black'
                                        : '',
                                    'w-full px-7 hover:bg-gray-700 hover:text-white group flex items-center py-3 text-sm font-medium ' +
                                        item.bg,
                                ]"
                            >
                                <component
                                    :is="item.icon"
                                    class="h-6 w-6"
                                    aria-hidden="true"
                                />
                                <span class="sr-only"> {{ item.name }} </span>
                            </Link>
                        </nav>
                    </div>
                    <div class="flex-shrink-0 flex pb-5"></div>
                </div>
            </div>
        </div>
        <div
            v-if="!collapseSidebar"
            class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 z-10"
        >
            <div
                class="flex flex-col flex-grow border-r border-gray-200 bg-white overflow-y-auto"
            >
                <div class="flex py-3 pb-4 items-center flex-shrink-0 px-4">
                    <!-- <button
          type="button"
          class="rounded px-1 mx-1 mr-3 border-gray-200 text-gray-500"
          @click="toggleCollapseSidebar()"
        >
          <MenuIcon class="h-6 w-6" aria-hidden="true" />
        </button> -->
                    <Link class="ml-2" :href="route('welcome')">
                        <jet-application-logo class="block h-10 w-auto" />
                    </Link>
                </div>
                <div class="flex-grow flex flex-col -mt-1.5">
                    <div class="px-4 flex flex-col mt-3 mb-1">
                        <create mode="button"></create>
                    </div>
                    <nav class="py-4 px-0 bg-white">
                        <div
                            v-for="item in filteredNavigation"
                            :key="item.name"
                            :class="[item.auth ? 'pb-2 border-b' : '']"
                        >
                            <Link
                                :href="item.href"
                                :class="[
                                    $page.url === item.href
                                        ? ' border-r-4 bg-gray-200 border-r-black'
                                        : '',
                                    'w-full px-7 hover:bg-gray-700 hover:text-white group flex items-center py-3 text-sm font-medium ' +
                                        item.bg,
                                ]"
                            >
                                <component
                                    :is="item.icon"
                                    class="mr-3 ml-2 h-6 w-6"
                                    aria-hidden="true"
                                />
                                <span class="flex-1"> {{ item.name }} </span>
                            </Link>
                            <Link
                                v-for="child in item.children"
                                :key="child.name"
                                :href="child.href"
                                :class="[
                                    $page.url === child.href
                                        ? ' border-r-4 bg-gray-200 border-r-black'
                                        : '',
                                    'w-full px-10 hover:bg-gray-700 hover:text-white group flex items-center py-1.5 text-sm font-small ' +
                                        child.bg,
                                ]"
                            >
                                <component
                                    :is="child.icon"
                                    class="mr-3 ml-2 h-4 w-4"
                                    aria-hidden="true"
                                />
                                <span class="flex-1"> {{ child.name }} </span>
                            </Link>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
        <div
            :class="[
                collapseSidebar ? 'md:pl-20 md:-ml-20' : 'md:pl-64',
                'flex flex-col flex-1 z-0',
            ]"
        >
            <div
                class="sticky top-0 z-10 flex-shrink-0 flex h-16 bg-white shadow"
            >
                <button
                    type="button"
                    class="hidden md:inline-flex p-4 rounded mx-1 mr-3 border-red-200 text-gray-500"
                    @click="toggleCollapseSidebar()"
                >
                    <MenuIcon class="h-6 w-6" aria-hidden="true" />
                </button>
                <button
                    type="button"
                    class="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-teal-500 md:hidden"
                    @click="sidebarOpen = true"
                >
                    <span class="sr-only">Open sidebar</span>
                    <MenuIcon class="h-6 w-6" aria-hidden="true" />
                </button>
                <div class="flex-1 px-4 py-2 flex justify-between">
                    <div class="flex-1 flex">
                        <search
                            :host="MEILISEARCH_HOST"
                            :akey="MEILISEARCH_PUBLICKEY"
                        ></search>
                        <!-- <form class="w-full flex md:ml-0" action="#" method="GET">
              <label for="search-field" class="sr-only">Search</label>
              <div class="relative w-full text-gray-400 focus-within:text-gray-600">
                <div
                  class="absolute inset-y-0 left-0 flex items-center pointer-events-none"
                >
                  <SearchIcon class="h-5 w-5" aria-hidden="true" />
                </div>
                <input
                  id="search-field"
                  class="block w-full h-full pl-8 pr-3 py-2 border-transparent text-gray-900 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-0 focus:border-transparent sm:text-sm"
                  placeholder="Search"
                  type="search"
                  name="search"
                />
              </div>
            </form> -->
                    </div>
                    <flash-messages />
                    <div class="ml-4 flex items-center md:ml-6">
                        <div>
                            <a href="https://docs.nmrxiv.org" target="_blank"
                                ><svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    class="h-6 w-6"
                                >
                                    <path
                                        d="M12 21a2 2 0 0 1-1.41-.59l-.83-.82A2 2 0 0 0 8.34 19H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h4a5 5 0 0 1 4 2v16z"
                                        class="fill-current text-gray-400"
                                    ></path>
                                    <path
                                        d="M12 21V5a5 5 0 0 1 4-2h4a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-4.34a2 2 0 0 0-1.42.59l-.83.82A2 2 0 0 1 12 21z"
                                        class="fill-current text-gray-600"
                                    ></path></svg
                            ></a>
                        </div>
                        <div
                            v-if="$page.props.user.first_name != null"
                            class="ml-5"
                        >
                            <a
                                class="cursor-pointer text-gray-600"
                                target="_blank"
                                @click="startTour"
                                ><svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M18 3a1 1 0 00-1.447-.894L8.763 6H5a3 3 0 000 6h.28l1.771 5.316A1 1 0 008 18h1a1 1 0 001-1v-4.382l6.553 3.276A1 1 0 0018 15V3z"
                                        clip-rule="evenodd"
                                    /></svg
                            ></a>
                        </div>
                        <Menu
                            v-if="$page.props.user"
                            as="div"
                            class="ml-3 relative"
                        >
                            <div
                                v-if="
                                    $page.props.user.current_team &&
                                    $page.props.user.current_team.personal_team
                                "
                            >
                                <MenuButton
                                    v-if="
                                        !$page.props.jetstream
                                            .managesProfilePhotos
                                    "
                                    class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"
                                >
                                    <img
                                        class="h-8 w-8 rounded-full object-cover"
                                        :src="
                                            $page.props.user.profile_photo_url
                                        "
                                        :alt="$page.props.user.first_name"
                                    />
                                </MenuButton>
                                <span v-else class="inline-flex rounded-md">
                                    <MenuButton
                                        type="button"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition"
                                    >
                                        <img
                                            class="h-8 w-8 rounded-full object-cover mr-2"
                                            :src="
                                                $page.props.user
                                                    .profile_photo_url
                                            "
                                            :alt="$page.props.user.first_name"
                                        />
                                        <span class="flex md:block hidden">{{
                                            $page.props.user.first_name
                                        }}</span>

                                        <svg
                                            class="ml-2 -mr-0.5 h-4 w-4"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </MenuButton>
                                </span>
                            </div>
                            <div v-else>
                                <MenuButton
                                    v-if="
                                        $page.props.user &&
                                        $page.props.user.current_team
                                    "
                                    type="button"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition"
                                >
                                    <img
                                        class="h-8 w-8 rounded-full object-cover mr-2"
                                        :src="
                                            $page.props.user.current_team
                                                .profile_photo_url
                                        "
                                        :alt="
                                            $page.props.user.current_team.name
                                        "
                                    />
                                    <span class="flex md:block hidden">{{
                                        $page.props.user.current_team.name
                                    }}</span>

                                    <svg
                                        class="ml-2 -mr-0.5 h-4 w-4"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
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
                                    class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                                >
                                    <span
                                        v-if="
                                            hasAnyPermission([
                                                'manage platform',
                                            ])
                                        "
                                    >
                                        <div
                                            class="block px-4 pt-2 text-xs text-gray-400"
                                        >
                                            Management
                                        </div>
                                        <jet-dropdown-link
                                            :href="route('console')"
                                        >
                                            Admin Console
                                        </jet-dropdown-link>
                                    </span>
                                    <div class="border-t border-gray-100"></div>
                                    <span
                                        v-if="
                                            $page.props.jetstream
                                                .hasTeamFeatures
                                        "
                                    >
                                        <div
                                            v-if="
                                                !$page.props.user.current_team
                                                    .personal_team
                                            "
                                            class="block px-4 pt-2 text-xs text-gray-400"
                                        >
                                            Personal Account
                                        </div>
                                        <form
                                            v-if="
                                                !$page.props.user.current_team
                                                    .personal_team
                                            "
                                            @submit.prevent="
                                                switchToTeam(personalTeam)
                                            "
                                        >
                                            <jet-dropdown-link as="button">
                                                <div class="flex items-center">
                                                    <svg
                                                        class="mr-2 h-5 w-5 text-gray-400"
                                                        fill="none"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                        ></path>
                                                    </svg>
                                                    <div>
                                                        {{
                                                            $page.props.user
                                                                .first_name
                                                        }}
                                                        {{
                                                            $page.props.user
                                                                .last_name
                                                        }}
                                                    </div>
                                                </div>
                                            </jet-dropdown-link>
                                        </form>
                                        <div
                                            class="block px-4 pt-2 text-xs text-gray-400"
                                        >
                                            Your Teams
                                        </div>
                                        <template
                                            v-if="
                                                $page.props.jetstream
                                                    .hasTeamFeatures
                                            "
                                        >
                                            <template
                                                v-for="team in $page.props.user
                                                    .all_teams"
                                                :key="team.id"
                                            >
                                                <form
                                                    @submit.prevent="
                                                        switchToTeam(team)
                                                    "
                                                >
                                                    <jet-dropdown-link
                                                        v-if="
                                                            !team.personal_team
                                                        "
                                                        as="button"
                                                    >
                                                        <div
                                                            class="flex items-center"
                                                        >
                                                            <svg
                                                                v-if="
                                                                    team.id ==
                                                                    $page.props
                                                                        .user
                                                                        .current_team_id
                                                                "
                                                                class="mr-2 h-5 w-5 text-green-400"
                                                                fill="none"
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                stroke="currentColor"
                                                                viewBox="0 0 24 24"
                                                            >
                                                                <path
                                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                                ></path>
                                                            </svg>
                                                            <svg
                                                                v-else
                                                                class="mr-2 h-5 w-5 text-gray-400"
                                                                fill="none"
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                stroke="currentColor"
                                                                viewBox="0 0 24 24"
                                                            >
                                                                <path
                                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                                ></path>
                                                            </svg>
                                                            <div>
                                                                {{ team.name }}
                                                            </div>
                                                        </div>
                                                    </jet-dropdown-link>
                                                </form>
                                            </template>
                                            <jet-dropdown-link
                                                v-if="
                                                    $page.props.jetstream
                                                        .canCreateTeams
                                                "
                                                :href="route('teams.create')"
                                            >
                                                Create New Team
                                            </jet-dropdown-link>
                                        </template>
                                    </span>

                                    <div class="border-t border-gray-100"></div>

                                    <div
                                        class="block px-4 pt-2 text-xs text-gray-400"
                                    >
                                        Manage Account
                                    </div>

                                    <jet-dropdown-link
                                        :href="route('profile.show')"
                                    >
                                        Account
                                    </jet-dropdown-link>

                                    <jet-dropdown-link
                                        v-if="
                                            $page.props.jetstream.hasApiFeatures
                                        "
                                        :href="route('api-tokens.index')"
                                    >
                                        API Tokens
                                    </jet-dropdown-link>

                                    <div class="border-t border-gray-100"></div>

                                    <form @submit.prevent="logout">
                                        <jet-dropdown-link as="button">
                                            Log Out
                                        </jet-dropdown-link>
                                    </form>
                                </MenuItems>
                            </transition>
                        </Menu>
                        <Menu
                            v-if="!$page.props.user.first_name"
                            as="div"
                            class="ml-3 relative"
                        >
                            <MenuButton
                                class="inline-flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"
                            >
                            </MenuButton>
                            <div class="inline-flex">
                                <a
                                    href="/login"
                                    class="px-3 py-2 whitespace-nowrap text-base font-medium text-gray-500 hover:text-gray-900"
                                >
                                    Login
                                </a>
                                <a
                                    href="/register"
                                    type="button"
                                    class="inline-flex ml-3 items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                >
                                    Register
                                </a>
                            </div>
                        </Menu>
                    </div>
                </div>
            </div>
            <main
                class="flex-1 relative overflow-y-auto bg-white focus:outline-none"
            >
                <slot name="header"></slot>
                <slot></slot>
                <project-create></project-create>
                <submission></submission>
            </main>
        </div>
    </div>
</template>
<script>
import JetApplicationLogo from "@/Jetstream/ApplicationLogo.vue";
import Search from "@/Shared/Search.vue";
import Create from "@/Shared/CreateButton.vue";
import JetApplicationMark from "@/Jetstream/ApplicationMark.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import JetBanner from "@/Jetstream/Banner.vue";
import JetDropdown from "@/Jetstream/Dropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink.vue";
import FlashMessages from "@/Shared/FlashMessages.vue";
import AnnouncementBanner from "@/Shared/AnnouncementBanner.vue";
import AppTour from "@/App/Tour.vue";
import ProjectCreate from "@/Pages/Project/Partials/Create.vue";
import StudyCreate from "@/Pages/Study/Partials/Create.vue";
import Submission from "@/Shared/Submission.vue";
import {
    BookmarkAltIcon,
    FireIcon,
    HomeIcon,
    InboxIcon,
    UserIcon,
} from "@heroicons/vue/outline";
import { ref } from "vue";
import {
    DialogOverlay,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    Dialog,
    DialogPanel,
    TransitionChild,
    TransitionRoot,
} from "@headlessui/vue";
import {
    BellIcon,
    MenuIcon,
    XIcon,
    ClockIcon,
    UsersIcon,
    StarIcon,
    FolderIcon,
    ViewGridIcon,
    TrashIcon,
} from "@heroicons/vue/outline";
import { SearchIcon, PlusIcon } from "@heroicons/vue/solid";

const userNavigation = [];

const secondaryNavigation = [
    { name: "Starred", href: "#" },
    { name: "Shared with me", href: "#" },
];

const navigation = [
    {
        auth: true,
        name: "Dashboard",
        href: "/dashboard",
        icon: HomeIcon,
        bg: "bg-gray-50",
        children: [
            {
                auth: true,
                name: "Shared with me",
                href: "/dashboard/shared-with-me",
                icon: UsersIcon,
                bg: "bg-white",
            },
            {
                auth: false,
                name: "Recent",
                href: "/dashboard/recent",
                icon: ClockIcon,
                bg: "bg-white",
            },
            {
                auth: false,
                name: "Starred",
                href: "/dashboard/starred",
                icon: StarIcon,
                bg: "bg-white",
            },
            {
                auth: false,
                name: "Trashed",
                href: "/dashboard/trashed",
                icon: TrashIcon,
                bg: "bg-white",
            },
        ],
    },
    {
        auth: false,
        name: "Projects",
        href: "/projects",
        icon: FolderIcon,
        bg: "bg-white",
    },
    {
        auth: false,
        name: "Datasets",
        href: "/datasets",
        icon: ViewGridIcon,
        bg: "bg-white",
    },
];

export default {
    components: {
        ProjectCreate,
        AppTour,
        JetBanner,
        JetApplicationLogo,
        JetApplicationMark,
        JetDropdownLink,
        JetDropdown,
        Link,
        Head,
        Dialog,
        DialogOverlay,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        TransitionChild,
        TransitionRoot,
        BellIcon,
        MenuIcon,
        SearchIcon,
        XIcon,
        PlusIcon,
        FlashMessages,
        DialogPanel,
        AnnouncementBanner,
        Search,
        Create,
        ClockIcon,
        UsersIcon,
        StarIcon,
        TrashIcon,
        FolderIcon,
        ViewGridIcon,
        StudyCreate,
        Submission,
    },
    props: {
        title: String,
    },
    setup() {
        var collapseSidebarStatus = JSON.parse(
            localStorage.getItem("collapseSidebarStatus")
        );
        if (!collapseSidebarStatus) {
            collapseSidebarStatus = false;
        }
        const sidebarOpen = ref(false);
        const collapseSidebar = ref(collapseSidebarStatus);

        return {
            userNavigation,
            secondaryNavigation,
            sidebarOpen,
            collapseSidebar,
            navigation,
        };
    },
    computed: {
        filteredNavigation() {
            if (this.$page.props.user.first_name) {
                return this.navigation;
            } else {
                return this.navigation.filter((i) => !i.auth);
            }
        },
        personalTeam() {
            return this.$page.props.user.all_teams.filter(
                (t) => t.personal_team
            )[0];
        },
        MEILISEARCH_HOST() {
            return this.$page.props.MEILISEARCH_HOST;
        },
        MEILISEARCH_PUBLICKEY() {
            return this.$page.props.MEILISEARCH_PUBLICKEY;
        },
    },
    mounted() {},
    methods: {
        switchToTeam(team) {
            this.$inertia.put(
                route("current-team.update"),
                {
                    team_id: team.id,
                },
                {
                    preserveState: false,
                }
            );
        },
        toggleCollapseSidebar() {
            this.collapseSidebar = !this.collapseSidebar;
            localStorage.setItem("collapseSidebarStatus", this.collapseSidebar);
        },
        logout() {
            this.$inertia.post(route("logout"));
        },
        startTour() {
            this.$tours["appTour"].start();
        },
    },
};
</script>
