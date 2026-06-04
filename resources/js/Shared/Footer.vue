<template>
    <footer
        class="relative overflow-hidden bg-gray-950 text-white"
        aria-labelledby="footer-heading"
    >
        <h2 id="footer-heading" class="sr-only">Footer</h2>

        <!-- Accent glow -->
        <div
            class="pointer-events-none absolute -left-32 top-0 h-72 w-72 rounded-full bg-teal-500/20 blur-3xl"
            aria-hidden="true"
        />
        <div
            class="pointer-events-none absolute -right-24 bottom-0 h-64 w-64 rounded-full bg-teal-600/10 blur-3xl"
            aria-hidden="true"
        />
        <div
            class="pointer-events-none absolute inset-0 bg-gradient-to-b from-white/[0.03] to-transparent"
            aria-hidden="true"
        />

        <div class="relative mx-auto max-w-6xl px-6 sm:px-8 lg:px-12">
            <div class="border-b border-white/10 py-14 lg:py-16">
                <div class="grid grid-cols-1 gap-12 lg:grid-cols-12 lg:gap-10">
                    <!-- Brand -->
                    <div class="lg:col-span-5">
                        <Link href="/" class="inline-block">
                            <JetApplicationLogo
                                class="h-10 w-auto [&_path[fill='black']]:fill-white"
                            />
                        </Link>
                        <p
                            class="mt-5 max-w-md text-[15px] leading-relaxed text-white/70"
                        >
                            Open-source NMR data repository and analysis
                            platform for the chemistry research community.
                        </p>

                        <div class="mt-8 flex flex-wrap gap-3">
                            <a
                                v-for="action in quickActions"
                                :key="action.name"
                                :href="action.href"
                                :target="action.external ? '_blank' : undefined"
                                :rel="
                                    action.external
                                        ? 'noopener noreferrer'
                                        : undefined
                                "
                                class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-4 py-2 text-sm font-medium text-white backdrop-blur-sm transition-all duration-200 hover:border-teal-400/40 hover:bg-white/10"
                            >
                                <component
                                    :is="action.icon"
                                    class="h-4 w-4 text-teal-400"
                                    aria-hidden="true"
                                />
                                {{ action.name }}
                            </a>
                        </div>
                    </div>

                    <!-- Link columns -->
                    <div class="lg:col-span-7">
                        <div
                            class="grid grid-cols-2 gap-10 sm:grid-cols-4 sm:gap-8"
                        >
                            <div
                                v-for="section in linkSections"
                                :key="section.title"
                            >
                                <h3
                                    class="text-xs font-semibold uppercase tracking-[0.2em] text-teal-400"
                                >
                                    {{ section.title }}
                                </h3>
                                <ul role="list" class="mt-5 space-y-3">
                                    <li
                                        v-for="item in section.links"
                                        :key="item.name"
                                    >
                                        <component
                                            :is="item.external ? 'a' : Link"
                                            :href="item.href"
                                            :target="
                                                item.external
                                                    ? '_blank'
                                                    : undefined
                                            "
                                            :rel="
                                                item.external
                                                    ? 'noopener noreferrer'
                                                    : undefined
                                            "
                                            class="group inline-flex items-center gap-1.5 text-[15px] text-white/80 transition-colors duration-200 hover:text-white"
                                        >
                                            <span>{{ item.name }}</span>
                                            <ArrowTopRightOnSquareIcon
                                                v-if="item.external"
                                                class="h-3.5 w-3.5 shrink-0 text-white/30 transition-colors group-hover:text-teal-400"
                                                aria-hidden="true"
                                            />
                                        </component>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom bar -->
            <div class="py-8">
                <div
                    class="flex flex-col items-center justify-between gap-6 sm:flex-row"
                >
                    <p class="text-sm text-white/60">
                        &copy; {{ currentYear }}
                        <span class="text-white">nmrXiv</span>. All rights
                        reserved.
                    </p>

                    <div
                        class="flex flex-wrap items-center justify-center gap-6 sm:justify-end"
                    >
                        <a
                            href="https://github.com/NFDI4Chem/nmrxiv/issues"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-sm text-white/70 transition-colors hover:text-white"
                        >
                            Report an issue
                        </a>
                        <a
                            href="https://www.nfdi4chem.de/"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-sm text-white/70 transition-colors hover:text-white"
                        >
                            NFDI4Chem
                        </a>
                        <div
                            class="flex items-center gap-2 border-l border-white/10 pl-6"
                        >
                            <a
                                href="https://github.com/NFDI4Chem/nmrxiv"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white transition-all duration-200 hover:border-teal-400/50 hover:bg-white/10 hover:text-teal-300"
                            >
                                <span class="sr-only">GitHub</span>
                                <svg
                                    class="h-5 w-5"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.07-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"
                                    />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";
import {
    ArrowTopRightOnSquareIcon,
    BookOpenIcon,
    CodeBracketIcon,
} from "@heroicons/vue/24/outline";
import JetApplicationLogo from "@/Jetstream/ApplicationLogo.vue";

const currentYear = computed(() => new Date().getFullYear());

const quickActions = [
    {
        name: "Documentation",
        href: "https://docs.nmrxiv.org",
        external: true,
        icon: BookOpenIcon,
    },
    {
        name: "GitHub",
        href: "https://github.com/NFDI4Chem/nmrxiv",
        external: true,
        icon: CodeBracketIcon,
    },
];

const linkSections = [
    {
        title: "Explore",
        links: [
            { name: "Projects", href: "/projects", external: false },
            { name: "Spectra Library", href: "/compounds", external: false },
            { name: "FAQs", href: "/faqs", external: false },
        ],
    },
    {
        title: "Support",
        links: [
            {
                name: "Documentation",
                href: "https://docs.nmrxiv.org",
                external: true,
            },
            {
                name: "Guides",
                href: "https://docs.nmrxiv.org/submission-guides/data-lifecycle.html",
                external: true,
            },
            {
                name: "API",
                href: "https://docs.nmrxiv.org/developer-guides/api.html",
                external: true,
            },
            {
                name: "Status",
                href: "https://kuma.nfdi4chem.de/status/nmrxiv",
                external: true,
            },
        ],
    },
    {
        title: "About",
        links: [
            { name: "About nmrXiv", href: "/about-us", external: false },
            {
                name: "Contributors",
                href: "https://docs.nmrxiv.org/contribution/contributors.html",
                external: true,
            },
        ],
    },
    {
        title: "Legal",
        links: [
            { name: "Privacy", href: "/privacy-policy", external: false },
            { name: "Terms", href: "/terms-of-service", external: false },
        ],
    },
];
</script>
