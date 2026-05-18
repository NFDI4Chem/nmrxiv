<template>
    <footer
        class="bg-gradient-to-b from-white to-gray-50 border-t border-gray-200"
        aria-labelledby="footer-heading"
    >
        <h2 id="footer-heading" class="sr-only">Footer</h2>
        <div class="max-w-6xl mx-auto px-6 sm:px-8 lg:px-12">
            <!-- Main Footer Content -->
            <div class="py-16 lg:py-20">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8">
                    <!-- Logo & Description -->
                    <div class="lg:col-span-4">
                        <JetApplicationLogo class="h-10 w-auto" />
                        <p
                            class="mt-6 text-sm leading-6 text-gray-600 max-w-sm"
                        >
                            Open-source NMR data repository and analysis
                            platform for the chemistry research community.
                        </p>
                    </div>

                    <!-- Navigation Links -->
                    <div class="lg:col-span-8">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-8">
                            <!-- Quick Links -->
                            <div>
                                <h3
                                    class="text-sm font-semibold text-gray-900 tracking-tight"
                                >
                                    Quick Links
                                </h3>
                                <ul role="list" class="mt-4 space-y-3">
                                    <li
                                        v-for="item in navigation.quicklinks"
                                        :key="item.name"
                                    >
                                        <Link
                                            :href="item.href"
                                            class="text-sm text-gray-600 hover:text-gray-900 transition-colors duration-200"
                                        >
                                            {{ item.name }}
                                        </Link>
                                    </li>
                                    <li>
                                        <a
                                            target="_blank"
                                            href="https://docs.nmrxiv.org/FAQs.html"
                                            class="text-sm text-gray-600 hover:text-gray-900 transition-colors duration-200"
                                        >
                                            FAQs
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <!-- Support -->
                            <div>
                                <h3
                                    class="text-sm font-semibold text-gray-900 tracking-tight"
                                >
                                    Support
                                </h3>
                                <ul role="list" class="mt-4 space-y-3">
                                    <li
                                        v-for="item in navigation.support"
                                        :key="item.name"
                                    >
                                        <a
                                            target="_blank"
                                            :href="item.href"
                                            class="text-sm text-gray-600 hover:text-gray-900 transition-colors duration-200"
                                        >
                                            {{ item.name }}
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <!-- About -->
                            <div>
                                <h3
                                    class="text-sm font-semibold text-gray-900 tracking-tight"
                                >
                                    About
                                </h3>
                                <ul role="list" class="mt-4 space-y-3">
                                    <li
                                        v-for="item in navigation.about"
                                        :key="item.name"
                                    >
                                        <a
                                            target="_blank"
                                            :href="item.href"
                                            class="text-sm text-gray-600 hover:text-gray-900 transition-colors duration-200"
                                        >
                                            {{ item.name }}
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <!-- Legal -->
                            <div>
                                <h3
                                    class="text-sm font-semibold text-gray-900 tracking-tight"
                                >
                                    Legal
                                </h3>
                                <ul role="list" class="mt-4 space-y-3">
                                    <li
                                        v-for="item in navigation.legal"
                                        :key="item.name"
                                    >
                                        <Link
                                            :href="item.href"
                                            class="text-sm text-gray-600 hover:text-gray-900 transition-colors duration-200"
                                        >
                                            {{ item.name }}
                                        </Link>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-gray-200 py-6">
                <div
                    class="flex flex-col sm:flex-row items-center justify-between gap-4"
                >
                    <p class="text-sm text-gray-500">
                        &copy; {{ currentYear }} nmrXiv. All rights reserved.
                    </p>
                    <div class="flex items-center gap-6">
                        <a
                            href="https://github.com/nfdi4chem/nmrxiv"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
                        >
                            <span class="sr-only">GitHub</span>
                            <svg
                                class="h-5 w-5"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";
import JetApplicationLogo from "@/Jetstream/ApplicationLogo.vue";

const currentYear = computed(() => new Date().getFullYear());

const navigation = {
    quicklinks: [
        { name: "Projects", href: "/projects" },
        { name: "Compounds", href: "/compounds" },
    ],
    support: [
        { name: "Documentation", href: "https://docs.nmrxiv.org" },
        {
            name: "Guides",
            href: "https://docs.nmrxiv.org/submission-guides/data-lifecycle.html",
        },
        {
            name: "API",
            href: "https://docs.nmrxiv.org/developer-guides/api.html",
        },
        { name: "Status", href: "https://kuma.nfdi4chem.de/status/nmrxiv" },
    ],
    about: [
        {
            name: "Contributors",
            href: "https://docs.nmrxiv.org/contribution/contributors.html",
        },
    ],
    legal: [
        { name: "Privacy", href: "/privacy-policy" },
        { name: "Terms", href: "/terms-of-service" },
    ],
};
</script>
