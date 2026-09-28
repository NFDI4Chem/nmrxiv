<template>
    <div class="flex min-h-screen flex-col bg-white">
        <Head title="BagIt Viewer - nmrXiv" />
        <FlashMessages />
        <main class="flex-1">
            <div class="relative">
                <PublicSiteHeader />

                <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-3xl text-center">
                        <p
                            class="text-sm font-semibold uppercase tracking-wide text-teal-700"
                        >
                            Long-term access
                        </p>
                        <h1
                            class="mt-2 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl"
                        >
                            Offline BagIt Viewer
                        </h1>
                        <p class="mt-4 text-lg text-gray-600">
                            Download a single HTML file that opens nmrXiv BagIt
                            archives on your computer — validates checksums and
                            shows spectra without an internet connection.
                        </p>
                        <div class="mt-8 flex flex-wrap justify-center gap-3">
                            <a
                                :href="route('bagit-viewer.download')"
                                class="inline-flex items-center rounded-lg bg-teal-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-teal-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500"
                            >
                                Download viewer (zip)
                            </a>
                            <a
                                href="https://datatracker.ietf.org/doc/html/rfc8493"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50"
                            >
                                About BagIt
                            </a>
                        </div>
                        <p
                            v-if="viewerVersion"
                            class="mt-3 text-xs text-gray-500"
                        >
                            Viewer version {{ viewerVersion }}
                            <span v-if="viewerBuildDate">
                                · built {{ viewerBuildDate }}
                            </span>
                        </p>
                    </div>

                    <ol
                        class="mx-auto mt-14 grid max-w-4xl gap-8 sm:grid-cols-3"
                    >
                        <li class="text-center sm:text-left">
                            <span
                                class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-teal-100 text-sm font-bold text-teal-800"
                            >
                                1
                            </span>
                            <h2
                                class="mt-3 text-base font-semibold text-gray-900"
                            >
                                Download and unzip
                            </h2>
                            <p class="mt-2 text-sm text-gray-600">
                                Get the zip above. Inside you will find
                                <code class="rounded bg-gray-100 px-1"
                                    >nmrxiv-bagit-viewer.html</code
                                >
                                and a short README.
                            </p>
                        </li>
                        <li class="text-center sm:text-left">
                            <span
                                class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-teal-100 text-sm font-bold text-teal-800"
                            >
                                2
                            </span>
                            <h2
                                class="mt-3 text-base font-semibold text-gray-900"
                            >
                                Open the HTML file
                            </h2>
                            <p class="mt-2 text-sm text-gray-600">
                                Double-click the HTML file in Chrome, Firefox,
                                or Safari. No installation is required.
                            </p>
                        </li>
                        <li class="text-center sm:text-left">
                            <span
                                class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-teal-100 text-sm font-bold text-teal-800"
                            >
                                3
                            </span>
                            <h2
                                class="mt-3 text-base font-semibold text-gray-900"
                            >
                                Open a BagIt archive
                            </h2>
                            <p class="mt-2 text-sm text-gray-600">
                                Click “Open folder” or drop a bag folder /
                                <code class="rounded bg-gray-100 px-1"
                                    >.zip</code
                                >
                                onto the page. Checksums are checked, then the
                                sample is shown with interactive spectra.
                            </p>
                        </li>
                    </ol>

                    <div
                        class="mx-auto mt-14 max-w-3xl rounded-2xl border border-gray-200 bg-gray-50 p-6 text-sm text-gray-700"
                    >
                        <h2 class="text-base font-semibold text-gray-900">
                            Supported browsers
                        </h2>
                        <p class="mt-2">
                            Current Chrome, Edge, Firefox, and Safari. The
                            viewer needs the browser’s folder picker or
                            drag-and-drop file access; Internet Explorer is not
                            supported.
                        </p>
                        <h2 class="mt-6 text-base font-semibold text-gray-900">
                            Privacy and sustainability
                        </h2>
                        <p class="mt-2">
                            The viewer runs entirely in your browser. Bag
                            contents never leave your computer. Keep the HTML
                            file with your archives so the data remains readable
                            even if nmrXiv is offline.
                        </p>
                    </div>
                </div>
            </div>
        </main>
        <Footer />
    </div>
</template>

<script>
import { Head } from "@inertiajs/vue3";
import FlashMessages from "@/Shared/FlashMessages.vue";
import PublicSiteHeader from "@/Shared/PublicSiteHeader.vue";
import Footer from "@/Shared/Footer.vue";

export default {
    components: {
        Head,
        FlashMessages,
        PublicSiteHeader,
        Footer,
    },
    props: {
        viewerVersion: { type: String, default: null },
        viewerBuildDate: { type: String, default: null },
        viewerAvailable: { type: Boolean, default: false },
    },
};
</script>
