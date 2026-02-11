<template>
    <div class="bg-white">
        <Head title="Privacy Policy" />

        <!-- Header -->
        <header class="border-b border-gray-200">
            <div class="max-w-6xl mx-auto px-6 sm:px-8 lg:px-12 py-6">
                <Link href="/" class="inline-block">
                    <JetApplicationLogo class="h-10 w-auto" />
                </Link>
            </div>
        </header>

        <!-- Content -->
        <main class="min-h-screen">
            <div class="max-w-6xl mx-auto px-6 sm:px-8 lg:px-12 py-12 sm:py-16">
                <!-- Page Header -->
                <div class="mb-12">
                    <h1
                        class="text-3xl sm:text-4xl font-bold text-gray-900 mb-3"
                    >
                        Privacy Policy
                    </h1>
                    <p class="text-gray-500">
                        Last updated:
                        {{
                            new Date().toLocaleDateString("en-US", {
                                year: "numeric",
                                month: "long",
                                day: "numeric",
                            })
                        }}
                    </p>
                </div>

                <!-- Policy Content -->
                <div
                    class="prose prose-gray max-w-none prose-headings:font-semibold prose-headings:text-gray-900 prose-p:text-gray-600 prose-a:text-teal-600 hover:prose-a:text-teal-700 prose-strong:text-gray-900"
                    v-html="sanitizeHtml(policy)"
                ></div>

                <!-- Back Link -->
                <div class="mt-12 pt-8 border-t border-gray-200">
                    <Link
                        href="/"
                        class="inline-flex items-center gap-2 text-teal-600 hover:text-teal-700 font-medium transition-colors"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"
                            />
                        </svg>
                        Back to Home
                    </Link>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <Footer />
    </div>
</template>

<script>
import { Head, Link } from "@inertiajs/vue3";
import JetApplicationLogo from "@/Jetstream/ApplicationLogo.vue";
import Footer from "@/Shared/Footer.vue";

export default {
    components: {
        Head,
        Link,
        JetApplicationLogo,
        Footer,
    },
    props: ["policy"],
};
</script>
