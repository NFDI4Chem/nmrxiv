<template>
    <div class="bg-white min-h-screen flex flex-col">
        <Head title="Privacy Policy - nmrXiv" />
        <FlashMessages />
        <main class="flex-1">
            <PublicSiteHeader :bordered="false" />

            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
                <div class="mb-10 text-center sm:text-left">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                        Privacy Policy
                    </h1>
                    <p class="mt-3 text-gray-500">
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

                <div
                    class="prose prose-gray max-w-none prose-headings:font-semibold prose-headings:text-gray-900 prose-p:text-gray-600 prose-a:text-blue-900 prose-a:no-underline hover:prose-a:underline prose-strong:text-gray-900"
                    v-html="sanitizeHtml(policy)"
                />

                <div class="mt-12 pt-8 border-t border-gray-100">
                    <Link
                        href="/"
                        class="inline-flex items-center gap-2 text-[0.9375rem] font-medium text-gray-600 hover:text-gray-900 transition-colors"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"
                            />
                        </svg>
                        Back to home
                    </Link>
                </div>
            </div>
        </main>

        <Footer />
    </div>
</template>

<script>
import { Head, Link } from "@inertiajs/vue3";
import FlashMessages from "@/Shared/FlashMessages.vue";
import Footer from "@/Shared/Footer.vue";
import PublicSiteHeader from "@/Shared/PublicSiteHeader.vue";

export default {
    components: {
        Head,
        Link,
        FlashMessages,
        Footer,
        PublicSiteHeader,
    },
    props: {
        policy: {
            type: String,
            required: true,
        },
    },
};
</script>
