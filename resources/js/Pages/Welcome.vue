<template>
    <div class="bg-white">
        <Head title="Welcome to nmrXiv"></Head>
        <FlashMessages />
        <main>
            <!-- Header and Hero with Animated Gradient -->
            <div class="relative overflow-hidden">
                <!-- Animated mesh gradient background -->
                <div
                    class="absolute inset-0 bg-gradient-to-br from-blue-50/30 via-indigo-50/30 to-purple-50/30"
                ></div>
                <div class="absolute inset-0 opacity-20">
                    <div
                        class="absolute top-0 left-1/4 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl animate-blob"
                    ></div>
                    <div
                        class="absolute top-0 right-1/4 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-2000"
                    ></div>
                    <div
                        class="absolute -bottom-32 left-1/3 w-96 h-96 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-4000"
                    ></div>
                </div>

                <!-- Gradient fade to white at bottom -->
                <div
                    class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-b from-transparent to-white"
                ></div>

                <PublicSiteHeader variant="hero">
                    <template #mobile-extra>
                        <nav class="grid grid-cols-1 gap-4">
                            <Link
                                v-for="item in Search"
                                :key="item.name"
                                :href="item.href"
                                class="flex items-center gap-3 rounded-lg p-3 hover:bg-gray-50 transition-colors"
                            >
                                <div
                                    class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary-500 text-white"
                                >
                                    <component
                                        :is="item.icon"
                                        class="size-5"
                                        aria-hidden="true"
                                    />
                                </div>
                                <span
                                    class="text-[0.9375rem] font-medium text-gray-900"
                                >
                                    {{ item.name }}
                                </span>
                            </Link>
                        </nav>
                    </template>
                </PublicSiteHeader>
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="relative py-20 sm:py-24 lg:py-32">
                        <div class="text-center">
                            <h1 class="sr-only">
                                nmrXiv — Open NMR data repository
                            </h1>
                            <UnifiedSearch variant="hero" />

                            <!-- Quick Links -->
                            <div
                                class="mt-28 flex flex-wrap items-center justify-center gap-6 text-sm sm:mt-32"
                            >
                                <a
                                    href="https://docs.nmrxiv.org"
                                    target="_blank"
                                    class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-900 transition-colors"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        fill="currentColor"
                                        viewBox="0 0 16 16"
                                    >
                                        <path
                                            d="M7 3.41a1 1 0 0 0-.668-.943L2.275 1.039a.987.987 0 0 0-.877.166c-.25.192-.398.493-.398.812V12.2c0 .454.296.853.725.977l3.948 1.365A1 1 0 0 0 7 13.596V3.41ZM9 13.596a1 1 0 0 0 1.327.946l3.948-1.365c.429-.124.725-.523.725-.977V2.017c0-.32-.147-.62-.398-.812a.987.987 0 0 0-.877-.166L9.668 2.467A1 1 0 0 0 9 3.41v10.186Z"
                                        />
                                    </svg>
                                    Documentation
                                </a>
                                <a
                                    href="https://github.com/NFDI4Chem/nmrxiv"
                                    target="_blank"
                                    class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-900 transition-colors"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        fill="currentColor"
                                        viewBox="0 0 16 16"
                                    >
                                        <path
                                            d="M8 .198a8 8 0 0 0-8 8 7.999 7.999 0 0 0 5.47 7.59c.4.076.547-.172.547-.384 0-.19-.007-.694-.01-1.36-2.226.482-2.695-1.074-2.695-1.074-.364-.923-.89-1.17-.89-1.17-.725-.496.056-.486.056-.486.803.056 1.225.824 1.225.824.714 1.224 1.873.87 2.33.666.072-.518.278-.87.507-1.07-1.777-.2-3.644-.888-3.644-3.954 0-.873.31-1.586.823-2.146-.09-.202-.36-1.016.07-2.118 0 0 .67-.214 2.2.82a7.67 7.67 0 0 1 2-.27 7.67 7.67 0 0 1 2 .27c1.52-1.034 2.19-.82 2.19-.82.43 1.102.16 1.916.08 2.118.51.56.82 1.273.82 2.146 0 3.074-1.87 3.75-3.65 3.947.28.24.54.73.54 1.48 0 1.07-.01 1.93-.01 2.19 0 .21.14.46.55.38A7.972 7.972 0 0 0 16 8.199a8 8 0 0 0-8-8Z"
                                        />
                                    </svg>
                                    GitHub
                                </a>
                                <a
                                    href="https://nmrxiv.org/api/documentation"
                                    target="_blank"
                                    class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-900 transition-colors"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5"
                                        />
                                    </svg>
                                    API
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metrics Section - Apple iPad Style -->
            <div class="relative">
                <!-- Gradient background: white -> gray -> white -->
                <div
                    class="absolute inset-0 bg-gradient-to-b from-white via-gray-50 to-white"
                ></div>

                <div
                    class="relative max-w-6xl mx-auto px-6 pb-20 sm:px-8 sm:pb-24 pt-4 lg:px-12"
                >
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Projects Card -->
                        <a
                            href="/projects"
                            class="group relative overflow-hidden rounded-3xl bg-white border border-gray-200 p-8 min-h-[400px] flex flex-col justify-between transition-all duration-300 shadow-sm hover:shadow-md"
                        >
                            <div>
                                <p
                                    class="text-sm font-semibold text-gray-500 mb-2"
                                >
                                    Publications & Studies
                                </p>
                                <h3
                                    class="text-3xl font-bold text-gray-900 leading-tight"
                                >
                                    Explore<br />dataset<br />collections.
                                </h3>
                                <p class="text-sm text-gray-600 mt-4">
                                    Comprehensive collections of NMR data and
                                    spectra organized by publication or research
                                    study.
                                </p>
                            </div>

                            <div class="mt-auto">
                                <div class="text-gray-900 mb-6">
                                    <div class="text-5xl font-bold">
                                        {{ projects }}
                                    </div>
                                    <div class="text-lg mt-1 text-gray-600">
                                        Projects
                                    </div>
                                    <small
                                        v-if="
                                            embargoed_projects &&
                                            embargoed_projects > 0
                                        "
                                        class="text-sm text-gray-500"
                                        >{{ embargoed_projects }} in
                                        embargo</small
                                    >
                                </div>
                            </div>
                        </a>

                        <!-- Compounds Card -->
                        <a
                            href="/compounds"
                            class="group relative overflow-hidden rounded-3xl bg-white border border-gray-200 p-8 min-h-[400px] flex flex-col justify-between transition-all duration-300 shadow-sm hover:shadow-md"
                        >
                            <div>
                                <p
                                    class="text-sm font-semibold text-gray-500 mb-2"
                                >
                                    Chemical Structures
                                </p>
                                <h3
                                    class="text-3xl font-bold text-gray-900 leading-tight"
                                >
                                    Discover<br />compounds<br />spectra.
                                </h3>
                                <p class="text-sm text-gray-600 mt-4">
                                    Search and analyze chemical structures with
                                    their associated experimental data and
                                    measurements.
                                </p>
                            </div>

                            <div class="mt-auto">
                                <div class="text-gray-900 mb-6">
                                    <div class="text-5xl font-bold">
                                        {{ compounds }}
                                    </div>
                                    <div class="text-lg mt-1 text-gray-600">
                                        Compounds
                                    </div>
                                </div>
                            </div>
                        </a>

                        <!-- Spectra Card -->
                        <a
                            href="/projects"
                            class="group relative overflow-hidden rounded-3xl bg-white border border-gray-200 p-8 min-h-[400px] flex flex-col justify-between transition-all duration-300 shadow-sm hover:shadow-md"
                        >
                            <div>
                                <p
                                    class="text-sm font-semibold text-gray-500 mb-2"
                                >
                                    Experimental data
                                </p>
                                <h3
                                    class="text-3xl font-bold text-gray-900 leading-tight"
                                >
                                    Access and<br />compare<br />spectra.
                                </h3>
                                <p class="text-sm text-gray-600 mt-4">
                                    Access raw NMR spectroscopy data with
                                    complete experimental details and metadata.
                                </p>
                            </div>

                            <div class="mt-auto">
                                <div class="text-gray-900 mb-6">
                                    <div class="text-5xl font-bold">
                                        {{ spectra }}
                                    </div>
                                    <div class="text-lg mt-1 text-gray-600">
                                        Spectra
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </main>

        <Footer />
    </div>
    <component :is="'script'" type="application/ld+json">{{
        schema
    }}</component>
</template>

<script>
import { Head, Link } from "@inertiajs/vue3";
import Projects from "@/Shared/Projects.vue";
import Footer from "@/Shared/Footer.vue";
import PublicSiteHeader from "@/Shared/PublicSiteHeader.vue";
import StructureSearch from "@/App/StructureSearch.vue";
import UnifiedSearch from "@/Shared/UnifiedSearch.vue";
import {
    ChatBubbleLeftRightIcon,
    HeartIcon,
    InboxIcon,
    MagnifyingGlassIcon,
    ArrowUturnLeftIcon,
    TrashIcon,
    UsersIcon,
    CloudArrowUpIcon,
    BeakerIcon,
    UserGroupIcon,
    ShieldCheckIcon,
    LockClosedIcon,
    GlobeAltIcon,
    DocumentCheckIcon,
} from "@heroicons/vue/24/outline";
import { ChevronDownIcon } from "@heroicons/vue/24/solid";
import ToolTip from "@/Shared/ToolTip.vue";
import FlashMessages from "@/Shared/FlashMessages.vue";

const Search = [
    {
        name: "Browse",
        description:
            "Know more about the data deposited in nmrXiv by browsing projects and datasets. You can also learn more about our data schema on our documentation site.",
        href: "#",
        icon: InboxIcon,
    },
    {
        name: "Advanced search",
        description:
            "Search similar spectra by simple drag and drop of your machine output files or search spectra by structures. Need further guidance or found any missing information. Reach out to us or check out our documentation site.",
        href: "#",
        icon: MagnifyingGlassIcon,
    },
];

export default {
    components: {
        Head,
        Link,
        PublicSiteHeader,
        ChatBubbleLeftRightIcon,
        HeartIcon,
        InboxIcon,
        MagnifyingGlassIcon,
        ArrowUturnLeftIcon,
        TrashIcon,
        UsersIcon,
        ChevronDownIcon,
        CloudArrowUpIcon,
        BeakerIcon,
        UserGroupIcon,
        ShieldCheckIcon,
        LockClosedIcon,
        GlobeAltIcon,
        DocumentCheckIcon,
        ToolTip,
        Projects,
        FlashMessages,
        StructureSearch,
        UnifiedSearch,
        Footer,
    },

    props: {
        spectra: String,
        projects: String,
        compounds: String,
        embargoed_projects: String,
        techniques: String,
    },

    setup() {
        return {
            Search,
        };
    },

    data() {
        return {
            schema: {},
        };
    },

    mounted() {
        axios.get(route("bioschemas.datacatalog")).then((response) => {
            this.schema = response.data;
        });
    },
};
</script>

<style scoped>
/* Retailstack-inspired blob animations */
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

/* Apple-inspired gradient animations */
@keyframes gradient {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0.6;
    }
    100% {
        opacity: 1;
    }
}

@keyframes gradient-reverse {
    0% {
        opacity: 0.5;
    }
    50% {
        opacity: 0.8;
    }
    100% {
        opacity: 0.5;
    }
}

@keyframes fade-in {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-gradient {
    animation: gradient 8s ease-in-out infinite;
}

.animate-gradient-reverse {
    animation: gradient-reverse 8s ease-in-out infinite;
}

.animate-fade-in {
    animation: fade-in 1s ease-out;
}

.animate-fade-in-up {
    animation: fade-in-up 1s ease-out 0.3s both;
}
</style>
