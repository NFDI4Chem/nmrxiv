<template>
    <header class="relative">
        <Popover :class="popoverClass">
            <div
                class="flex justify-between items-center mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8"
            >
                <div class="flex items-center gap-8 lg:gap-10 min-w-0">
                    <Link :href="'/'" class="flex-shrink-0">
                        <jet-application-logo class="block h-9 w-auto" />
                    </Link>

                    <PopoverGroup
                        as="nav"
                        aria-label="Main"
                        class="hidden md:flex items-center gap-0.5"
                    >
                        <Link
                            v-for="item in publicNavItems"
                            :key="item.href"
                            :href="item.href"
                            :class="desktopLinkClass(item)"
                        >
                            <component
                                :is="item.icon"
                                class="size-[1.125rem] shrink-0"
                                aria-hidden="true"
                            />
                            <span>{{ item.name }}</span>
                        </Link>
                    </PopoverGroup>
                </div>

                <div class="md:hidden">
                    <PopoverButton
                        class="rounded-lg p-2 inline-flex items-center justify-center text-gray-700 hover:text-gray-900 hover:bg-black/[0.04] focus:outline-none focus:ring-2 ring-brand transition-colors"
                    >
                        <span class="sr-only">Open menu</span>
                        <Bars3Icon class="size-6" aria-hidden="true" />
                    </PopoverButton>
                </div>

                <div
                    v-if="isAuthenticated"
                    class="hidden md:flex items-center gap-2"
                >
                    <Link :href="route('dashboard')" :class="authLinkClass">
                        Dashboard
                    </Link>
                    <NavDepositDataLink />
                </div>
                <div v-else class="hidden md:flex items-center gap-2">
                    <Link :href="route('login')" :class="authLinkClass">
                        Login/Register
                    </Link>
                    <NavDepositDataLink />
                </div>
            </div>

            <transition
                enter-active-class="duration-200 ease-out"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="duration-100 ease-in"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
            >
                <PopoverPanel
                    focus
                    class="absolute z-30 top-0 inset-x-0 p-2 transition transform origin-top-right md:hidden"
                >
                    <div
                        class="rounded-xl shadow-lg ring-1 ring-black/5 bg-white divide-y divide-gray-100"
                    >
                        <div class="pt-5 pb-4 px-5">
                            <div class="flex items-center justify-between">
                                <jet-application-logo
                                    class="block h-10 p-0.5 ml-1.5 w-auto"
                                />
                                <PopoverButton
                                    class="rounded-lg p-2 inline-flex items-center justify-center text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset ring-brand"
                                >
                                    <span class="sr-only">Close menu</span>
                                    <XMarkIcon
                                        class="size-6"
                                        aria-hidden="true"
                                    />
                                </PopoverButton>
                            </div>
                            <div v-if="$slots['mobile-extra']" class="mt-6">
                                <slot name="mobile-extra" />
                            </div>
                        </div>
                        <div class="py-5 px-5">
                            <nav class="flex flex-col gap-0.5">
                                <Link
                                    v-for="item in publicNavItems"
                                    :key="item.href"
                                    :href="item.href"
                                    :class="mobileLinkClass(item)"
                                >
                                    <span
                                        class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-700"
                                    >
                                        <component
                                            :is="item.icon"
                                            class="size-5"
                                            aria-hidden="true"
                                        />
                                    </span>
                                    <span>{{ item.name }}</span>
                                </Link>
                            </nav>
                            <div class="mt-6">
                                <NavDepositDataLink full-width />
                                <p
                                    class="mt-5 text-center text-[0.9375rem] font-medium text-gray-500"
                                >
                                    <Link
                                        v-if="isAuthenticated"
                                        :href="route('dashboard')"
                                        class="text-gray-900 hover:text-gray-700 transition-colors"
                                    >
                                        Dashboard
                                    </Link>
                                    <template v-else>
                                        <Link
                                            :href="route('login')"
                                            class="text-gray-900 hover:text-gray-700 transition-colors"
                                        >
                                            Login/Register
                                        </Link>
                                    </template>
                                </p>
                            </div>
                        </div>
                    </div>
                </PopoverPanel>
            </transition>
        </Popover>
    </header>
</template>

<script setup>
import { computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import {
    Popover,
    PopoverButton,
    PopoverGroup,
    PopoverPanel,
} from "@headlessui/vue";
import { Bars3Icon, XMarkIcon } from "@heroicons/vue/24/outline";
import JetApplicationLogo from "@/Jetstream/ApplicationLogo.vue";
import NavDepositDataLink from "@/Shared/NavDepositDataLink.vue";
import { publicNavItems, isPublicNavActive } from "@/Utils/publicNavItems";

const props = defineProps({
    variant: {
        type: String,
        default: "plain",
        validator: (value) => ["plain", "hero"].includes(value),
    },
    bordered: {
        type: Boolean,
        default: true,
    },
});

const page = usePage();

const isAuthenticated = computed(() => Boolean(page.props.auth?.user?.id));

const popoverClass = computed(() => {
    if (!props.bordered) {
        return "relative";
    }

    return props.variant === "hero"
        ? "relative border-b border-white/20"
        : "relative border-b border-gray-100";
});

const navLinkBase =
    "inline-flex items-center gap-2 rounded-full px-3 py-2 text-[0.9375rem] leading-snug transition-colors duration-150";

const authLinkClass =
    "whitespace-nowrap inline-flex items-center rounded-full px-3 py-2 text-[0.9375rem] font-medium text-gray-600 hover:text-gray-900 hover:bg-black/[0.04] transition-colors duration-150";

function desktopLinkClass(item) {
    const active = isPublicNavActive(page.url, item);

    return [
        navLinkBase,
        active
            ? "font-semibold text-gray-900 bg-black/[0.06]"
            : "font-medium text-gray-600 hover:text-gray-900 hover:bg-black/[0.04]",
    ];
}

function mobileLinkClass(item) {
    const active = isPublicNavActive(page.url, item);

    return [
        "flex items-center gap-3 rounded-full px-3 py-2.5 text-[0.9375rem] leading-snug transition-colors duration-150",
        active
            ? "font-semibold text-gray-900 bg-gray-50"
            : "font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900",
    ];
}
</script>

<style scoped>
.ring-brand {
    --tw-ring-color: #fd0039;
}
</style>
