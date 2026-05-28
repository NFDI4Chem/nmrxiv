<!--
  Project Card Component

  A versatile project display component that supports multiple layout modes (mini, grid, list).
-->
<template>
    <div v-if="project">
        <!-- Grid/Mini Mode Layout -->
        <div v-if="mode == 'mini' || mode == 'grid'">
            <div
                class="group relative flex flex-col overflow-visible rounded-xl border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:border-gray-300 hover:shadow-lg"
            >
                <Link :href="project.public_url" class="block">
                    <div
                        class="relative h-36 overflow-hidden rounded-t-xl lg:h-36 xl:h-36"
                    >
                        <img
                            v-if="project.photo_url && project.photo_url != ''"
                            :src="project.photo_url"
                            :alt="project.name"
                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.02]"
                        />
                        <SeededCoverBackground
                            v-else
                            :seed="project"
                            container-class="h-full w-full"
                        />

                        <span
                            v-if="project.identifier"
                            class="pointer-events-none absolute bottom-2 left-2 z-10 inline-flex max-w-[calc(100%-1rem)] items-center rounded-md bg-white/90 px-2 py-0.5 font-mono text-[11px] font-semibold tabular-nums leading-none text-gray-700 shadow-sm ring-1 ring-black/5 backdrop-blur-sm"
                        >
                            #{{ project.identifier }}
                        </span>
                    </div>
                </Link>

                <div v-if="project.stats" class="absolute right-2 top-2 z-20">
                    <span
                        class="inline-flex overflow-hidden rounded-full bg-white/90 shadow-sm ring-1 ring-black/5 backdrop-blur-sm"
                    >
                        <button
                            type="button"
                            class="inline-flex items-center px-2 py-1.5 text-gray-600 transition-colors hover:bg-gray-50 hover:text-teal-700"
                            aria-label="Upvote project"
                            @click.prevent.stop="toggleUpVote()"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                        <span
                            class="inline-flex min-w-[1.75rem] items-center justify-center border-l border-gray-200/80 px-2.5 py-1.5 text-xs font-semibold tabular-nums text-gray-900"
                        >
                            {{ project.stats.likes }}
                        </span>
                    </span>
                </div>

                <div class="flex flex-1 flex-col">
                    <Link
                        :href="project.public_url"
                        class="flex min-h-[7.5rem] flex-1 flex-col px-4 pb-3 pt-4"
                    >
                        <h3
                            class="line-clamp-2 text-[0.9375rem] font-semibold leading-snug tracking-tight text-gray-900 transition-colors group-hover:text-teal-700"
                        >
                            {{ project.name }}
                        </h3>

                        <p
                            v-if="hasDescription"
                            class="mt-2 line-clamp-2 text-sm leading-relaxed text-gray-600"
                        >
                            {{ project.description }}
                        </p>
                        <p v-else class="mt-2 text-sm italic text-gray-400">
                            No description provided
                        </p>

                        <div
                            v-if="hasTags"
                            class="mt-3 max-h-14 overflow-hidden"
                        >
                            <Tag :tags="project.tags" size="sm" />
                        </div>
                    </Link>

                    <div
                        v-if="mode != 'mini'"
                        class="mt-auto flex items-center gap-3 border-t border-gray-100 bg-gray-50/60 px-4 py-3"
                    >
                        <div class="min-w-0 flex-1">
                            <p
                                class="truncate text-sm font-medium text-gray-900"
                            >
                                {{ ownerDisplayName }}
                            </p>
                            <p class="mt-0.5 text-xs text-gray-500">
                                <time :datetime="project.created_at">
                                    {{ formatShortDate(project.created_at) }}
                                </time>
                            </p>
                            <p
                                v-if="licenseTitle"
                                class="mt-2.5 line-clamp-2 text-[11px] font-medium leading-snug text-gray-600"
                                :title="licenseTitle"
                            >
                                {{ licenseTitle }}
                            </p>
                        </div>

                        <Menu as="div" class="relative shrink-0 text-left">
                            <div>
                                <MenuButton
                                    class="flex items-center rounded-full p-1 text-gray-400 transition-colors hover:bg-white hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-1"
                                >
                                    <span class="sr-only">Open options</span>
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
                                    class="absolute right-0 top-full z-50 mt-1 w-56 origin-top-right rounded-lg bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-none"
                                >
                                    <div>
                                        <MenuItem
                                            v-if="project.download_url"
                                            v-slot="{ active }"
                                            class="border-b border-gray-100"
                                        >
                                            <a
                                                :href="project.download_url"
                                                :class="[
                                                    active
                                                        ? 'bg-gray-50 text-gray-900'
                                                        : 'text-gray-700',
                                                    'flex items-center gap-2 px-4 py-3 text-sm',
                                                ]"
                                            >
                                                <ArrowDownTrayIcon
                                                    class="h-5 w-5 shrink-0"
                                                    aria-hidden="true"
                                                />
                                                Download
                                            </a>
                                        </MenuItem>

                                        <MenuItem v-if="licenseTitle">
                                            <div class="px-4 py-3">
                                                <p
                                                    class="text-[11px] font-semibold uppercase tracking-wide text-gray-500"
                                                >
                                                    License
                                                </p>
                                                <p
                                                    class="mt-1 break-words text-sm leading-snug text-gray-900"
                                                >
                                                    {{ licenseTitle }}
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

        <!-- List Mode Layout -->
        <div v-if="mode == 'list'">
            <li
                class="flex gap-4 border-b border-gray-100 px-4 py-4 transition-colors hover:bg-gray-50/80 sm:px-6"
            >
                <Link
                    :href="project.public_url"
                    class="relative shrink-0 overflow-hidden rounded-lg border border-gray-200"
                >
                    <img
                        v-if="project.photo_url && project.photo_url != ''"
                        :src="project.photo_url"
                        :alt="project.name"
                        class="h-28 w-28 object-cover sm:h-32 sm:w-32"
                    />
                    <SeededCoverBackground
                        v-else
                        :seed="project"
                        container-class="h-28 w-28 sm:h-32 sm:w-32"
                    />
                </Link>

                <div class="flex min-w-0 flex-1 flex-col justify-between">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <p
                                v-if="project.identifier"
                                class="font-mono text-[11px] font-semibold uppercase tracking-wide text-gray-500"
                            >
                                #{{ project.identifier }}
                            </p>
                            <h3
                                class="mt-0.5 line-clamp-2 text-base font-semibold text-gray-900"
                            >
                                <Link
                                    :href="project.public_url"
                                    class="hover:text-teal-800"
                                >
                                    {{ project.name }}
                                </Link>
                            </h3>
                            <p
                                v-if="hasDescription"
                                class="mt-1.5 line-clamp-2 text-sm leading-relaxed text-gray-600"
                            >
                                {{ project.description }}
                            </p>
                            <p
                                v-else
                                class="mt-1.5 text-sm italic text-gray-400"
                            >
                                No description provided
                            </p>
                        </div>

                        <div v-if="project.stats" class="shrink-0">
                            <span
                                class="inline-flex overflow-hidden rounded-full border border-gray-200 bg-white shadow-sm"
                            >
                                <button
                                    type="button"
                                    class="inline-flex items-center px-2 py-1.5 text-gray-600 hover:bg-gray-50 hover:text-teal-700"
                                    aria-label="Upvote project"
                                    @click.prevent.stop="toggleUpVote()"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                                <span
                                    class="inline-flex min-w-[1.75rem] items-center justify-center border-l border-gray-200 px-2.5 py-1.5 text-xs font-semibold tabular-nums text-gray-900"
                                >
                                    {{ project.stats.likes }}
                                </span>
                            </span>
                        </div>
                    </div>

                    <div
                        class="mt-3 flex flex-col gap-1.5 text-sm text-gray-500"
                    >
                        <p class="min-w-0 truncate font-medium text-gray-700">
                            {{ ownerDisplayName }}
                        </p>
                        <p
                            v-if="licenseTitle"
                            class="mt-1 flex min-w-0 items-center gap-1.5"
                        >
                            <ScaleIcon class="h-4 w-4 shrink-0 text-gray-400" />
                            <span
                                class="line-clamp-2 text-xs font-medium text-gray-600"
                                :title="licenseTitle"
                            >
                                {{ licenseTitle }}
                            </span>
                        </p>
                        <div
                            class="flex flex-wrap items-center gap-x-4 gap-y-1"
                        >
                            <p
                                v-if="project.download_url"
                                class="flex items-center"
                            >
                                <a
                                    :href="project.download_url"
                                    class="inline-flex items-center gap-1 text-teal-700 hover:text-teal-900"
                                >
                                    <ArrowDownTrayIcon
                                        class="h-4 w-4"
                                        aria-hidden="true"
                                    />
                                    Download
                                </a>
                            </p>
                            <p class="flex items-center gap-1.5">
                                <svg
                                    class="h-4 w-4 shrink-0 text-gray-400"
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
                                <time :datetime="project.created_at">
                                    {{ formatShortDate(project.created_at) }}
                                </time>
                            </p>
                        </div>
                    </div>
                </div>
            </li>
        </div>
    </div>
</template>

<script>
import {
    EllipsisVerticalIcon,
    ScaleIcon,
    ArrowDownTrayIcon,
} from "@heroicons/vue/24/solid";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { router } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import Tag from "@/Shared/Tag.vue";
import SeededCoverBackground from "@/Shared/SeededCoverBackground.vue";

export default {
    name: "ProjectCard",

    components: {
        Link,
        EllipsisVerticalIcon,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        ArrowDownTrayIcon,
        ScaleIcon,
        Tag,
        SeededCoverBackground,
    },

    props: ["project", "mode"],

    computed: {
        hasDescription() {
            const description = this.project?.description;

            return Boolean(description && String(description).trim());
        },
        hasTags() {
            return (
                Array.isArray(this.project?.tags) &&
                this.project.tags.length > 0
            );
        },
        ownerDisplayName() {
            const owner = this.project?.owner;

            if (!owner) {
                return "";
            }

            return [owner.first_name, owner.last_name]
                .filter(Boolean)
                .join(" ");
        },
        licenseTitle() {
            return this.project?.license?.title ?? "";
        },
    },

    methods: {
        toggleUpVote() {
            if (
                this.$page.props.auth.username &&
                this.$page.props.auth.username != ""
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
                    .then(function () {
                        router.reload({ only: ["projects"] });
                    });
            } else {
                this.$inertia.visit(route("login"));
            }
        },
    },
};
</script>
