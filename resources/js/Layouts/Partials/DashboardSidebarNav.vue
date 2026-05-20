<template>
    <nav :class="navClass" aria-label="Dashboard">
        <div v-for="(section, sectionIndex) in sections" :key="section.title">
            <div
                v-if="iconOnly && sectionIndex > 0"
                class="mx-2 my-2 h-px bg-gray-200"
            />

            <div
                v-if="!iconOnly && section.title"
                :class="sectionIndex > 0 ? 'mt-6' : ''"
            >
                <h3
                    class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500"
                >
                    {{ section.title }}
                </h3>
            </div>

            <div :class="iconOnly ? '' : 'space-y-0.5'">
                <div
                    v-for="item in section.items"
                    :key="item.name || item.href"
                >
                    <div
                        v-if="item.name && item.href"
                        :class="iconOnly ? 'relative group px-2 py-1' : ''"
                    >
                        <Link
                            :id="item.id"
                            :href="item.href"
                            :aria-current="
                                primaryNavItemActive(item) ? 'page' : undefined
                            "
                            :class="linkClasses(primaryNavItemActive(item))"
                            :title="iconOnly ? item.name : undefined"
                        >
                            <component
                                :is="item.icon"
                                :class="iconClasses(primaryNavItemActive(item))"
                                aria-hidden="true"
                            />
                            <span v-if="!iconOnly" class="truncate">{{
                                item.name
                            }}</span>
                        </Link>
                        <div
                            v-if="iconOnly"
                            class="pointer-events-none absolute left-full top-1/2 z-50 ml-2 -translate-y-1/2 whitespace-nowrap rounded bg-gray-900 px-2 py-1 text-xs text-white opacity-0 transition-opacity duration-200 group-hover:opacity-100"
                        >
                            {{ item.name }}
                        </div>
                    </div>

                    <div
                        v-if="item.children?.length && !iconOnly"
                        class="ml-4 mt-0.5 space-y-0.5 pl-2"
                    >
                        <Link
                            v-for="child in item.children"
                            :id="child.id"
                            :key="child.name"
                            :href="child.href"
                            :aria-current="
                                sidebarChildNavActive(child)
                                    ? 'page'
                                    : undefined
                            "
                            :class="
                                linkClasses(sidebarChildNavActive(child), {
                                    sub: true,
                                })
                            "
                        >
                            <component
                                :is="child.icon"
                                :class="
                                    iconClasses(sidebarChildNavActive(child), {
                                        sub: true,
                                    })
                                "
                                aria-hidden="true"
                            />
                            <span class="truncate">{{ child.name }}</span>
                        </Link>
                    </div>

                    <template v-if="item.children?.length && iconOnly">
                        <div
                            v-for="child in item.children"
                            :key="child.name"
                            class="relative group px-2 py-1"
                        >
                            <Link
                                :id="child.id"
                                :href="child.href"
                                :aria-current="
                                    sidebarChildNavActive(child)
                                        ? 'page'
                                        : undefined
                                "
                                :class="
                                    linkClasses(sidebarChildNavActive(child))
                                "
                                :title="child.name"
                            >
                                <component
                                    :is="child.icon"
                                    :class="
                                        iconClasses(
                                            sidebarChildNavActive(child),
                                            { sub: true }
                                        )
                                    "
                                    aria-hidden="true"
                                />
                            </Link>
                            <div
                                class="pointer-events-none absolute left-full top-1/2 z-50 ml-2 -translate-y-1/2 whitespace-nowrap rounded bg-gray-900 px-2 py-1 text-xs text-white opacity-0 transition-opacity duration-200 group-hover:opacity-100"
                            >
                                {{ child.name }}
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
import { Link, usePage } from "@inertiajs/vue3";

export default {
    components: {
        Link,
    },
    props: {
        sections: {
            type: Array,
            required: true,
        },
        iconOnly: {
            type: Boolean,
            default: false,
        },
    },
    setup() {
        return {
            page: usePage(),
        };
    },
    computed: {
        navClass() {
            return this.iconOnly
                ? "flex flex-col py-2"
                : "flex-1 space-y-1 px-3 py-2";
        },
    },
    methods: {
        primaryNavItemActive(item) {
            if (!item?.href) {
                return false;
            }

            if (item.href === "/dashboard") {
                return this.page.props.dashboardWorkspace === "default";
            }

            return this.page.url === item.href;
        },
        sidebarChildNavActive(child) {
            if (child.workspace) {
                return this.page.props.dashboardWorkspace === child.workspace;
            }

            return this.page.url === child.href;
        },
        linkClasses(active, { sub = false } = {}) {
            if (this.iconOnly) {
                return [
                    active
                        ? "bg-gray-900 text-white shadow-sm"
                        : "text-gray-600 hover:bg-gray-100 hover:text-gray-900",
                    "flex h-12 w-12 items-center justify-center rounded-full transition-colors duration-150",
                ].join(" ");
            }

            const size = sub ? "py-2" : "py-2.5";

            return [
                active
                    ? "bg-gray-900 text-white shadow-sm"
                    : "text-gray-600 hover:bg-gray-100 hover:text-gray-900",
                `group flex w-full items-center gap-3 rounded-full px-4 ${size} text-sm font-medium transition-colors duration-150`,
            ].join(" ");
        },
        iconClasses(active, { sub = false } = {}) {
            const size = this.iconOnly
                ? sub
                    ? "h-5 w-5"
                    : "h-6 w-6"
                : sub
                ? "h-4 w-4"
                : "h-5 w-5";

            return [
                size,
                "flex-shrink-0 transition-colors duration-150",
                active
                    ? "text-white"
                    : this.iconOnly
                    ? "text-gray-500 group-hover:text-gray-700"
                    : "text-gray-400 group-hover:text-gray-500",
            ].join(" ");
        },
    },
};
</script>
