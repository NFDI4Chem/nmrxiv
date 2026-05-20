<template>
    <article
        class="primer-root mx-auto max-w-4xl text-gray-900"
        aria-labelledby="primer-heading"
    >
        <header class="border-b border-gray-200/80 pb-3 mb-4">
            <p
                class="text-xs font-semibold uppercase tracking-[0.2em] text-teal-600"
            >
                Introduction
            </p>
            <h1
                id="primer-heading"
                class="mt-2 text-2xl sm:text-3xl font-semibold text-gray-900 tracking-tight"
            >
                Basic concepts
            </h1>
            <p class="mt-3 max-w-3xl text-sm text-gray-600 leading-relaxed">
                A short overview of how submission works on nmrXiv and how your
                NMR data is structured before you upload files.
            </p>
        </header>

        <section class="mb-10" aria-labelledby="primer-steps-heading">
            <h2
                id="primer-steps-heading"
                class="text-sm font-semibold text-gray-900 mb-4"
            >
                Submission in three steps
            </h2>
            <ol class="grid gap-4 sm:grid-cols-3" role="list">
                <li
                    v-for="(step, index) in steps"
                    :key="step.title"
                    class="relative flex flex-col rounded-xl border border-gray-200 bg-white p-5 shadow-sm ring-1 ring-gray-900/5 transition hover:border-teal-200/80 hover:ring-teal-900/5"
                >
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-teal-700"
                        aria-hidden="true"
                    >
                        <component :is="step.icon" class="h-5 w-5" />
                    </div>
                    <p
                        class="mt-3 text-xs font-semibold uppercase tracking-wide text-gray-500"
                    >
                        Step {{ index + 1 }}
                    </p>
                    <p class="mt-1 font-semibold text-gray-900">
                        {{ step.title }}
                    </p>
                    <p class="mt-2 text-sm text-gray-600 leading-relaxed">
                        {{ step.description }}
                    </p>
                </li>
            </ol>
        </section>

        <section class="mb-5" aria-labelledby="primer-model-heading">
            <h2
                id="primer-model-heading"
                class="text-sm font-semibold text-gray-900 mb-2"
            >
                How your data is organised
            </h2>
            <p class="mb-5 max-w-3xl text-sm text-gray-600 leading-relaxed">
                nmrXiv groups uploads into a clear hierarchy so datasets stay
                traceable from raw files through to publication.
            </p>
            <div class="grid gap-4 lg:grid-cols-3">
                <div
                    v-for="card in conceptCards"
                    :key="card.term"
                    class="rounded-xl border border-gray-200 bg-gradient-to-b from-white to-gray-50/80 p-5 shadow-sm"
                >
                    <p
                        class="text-xs font-semibold uppercase tracking-wide text-teal-600"
                    >
                        {{ card.term }}
                    </p>
                    <p class="mt-2 text-sm text-gray-700 leading-relaxed">
                        {{ card.body }}
                    </p>
                </div>
            </div>
            <p class="mt-4 text-sm text-gray-600 leading-relaxed max-w-3xl">
                You can also publish
                <span class="font-medium text-gray-900">samples</span>
                on their own without creating a project.
            </p>
        </section>

        <section class="mb-8" aria-labelledby="primer-diagram-heading">
            <h2 id="primer-diagram-heading" class="sr-only">Visual overview</h2>
            <figure
                class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm ring-1 ring-gray-900/5"
            >
                <img
                    src="/img/primer.png"
                    alt="Diagram showing the relationship between projects, studies, and datasets in nmrXiv"
                    class="mx-auto max-w-full h-auto rounded-md"
                    width="800"
                    height="450"
                    loading="lazy"
                    decoding="async"
                />
            </figure>
        </section>

        <footer
            class="rounded-xl border border-gray-200 bg-gray-50/40 p-5 sm:p-6"
        >
            <div
                class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4"
            >
                <div class="flex gap-3 min-w-0">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-teal-800"
                        aria-hidden="true"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="h-6 w-6 shrink-0"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"
                            />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">
                            Formats &amp; automatic structure
                        </p>
                        <p class="mt-2 text-sm text-gray-700 leading-relaxed">
                            nmrXiv accepts NMR raw data from common instruments.
                            We currently auto-detect Bruker, Varian, JEOL
                            formats, and JCAMP files, with more formats planned.
                            After upload, nmrXiv derives
                            <span class="font-medium text-gray-900"
                                >studies</span
                            >
                            and
                            <span class="font-medium text-gray-900"
                                >datasets</span
                            >
                            from your folder layout.
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 border-t border-gray-200/60 pt-4"
            >
                <a
                    class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-700 hover:text-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2 rounded"
                    href="https://docs.nmrxiv.org/submission-guides/submission-process.html"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Read the full submission guide
                    <ArrowTopRightOnSquareIcon class="h-4 w-4 shrink-0" />
                </a>
                <span class="hidden sm:inline text-gray-300" aria-hidden="true"
                    >|</span
                >
                <a
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-700 hover:text-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2 rounded"
                    href="https://docs.nmrxiv.org/submission-guides/submission/folder-structure"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Folder structure reference
                    <ArrowTopRightOnSquareIcon
                        class="h-4 w-4 shrink-0 opacity-70"
                    />
                </a>
            </div>
        </footer>

        <div
            v-if="showActions"
            class="mt-8 mb-10 flex flex-col-reverse gap-3 border-t border-gray-200/80 pt-6 sm:flex-row sm:items-center sm:justify-end"
        >
            <div class="flex items-center gap-2">
                <input
                    id="primer-dont-show-again"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                    @change="$emit('dont-show-again')"
                />
                <label
                    for="primer-dont-show-again"
                    class="text-sm font-medium text-gray-700"
                >
                    Don't show this again
                </label>
            </div>
            <JetButton
                :class="{ 'opacity-25': processing }"
                :disabled="processing"
                @click="$emit('proceed')"
            >
                Proceed
            </JetButton>
        </div>
    </article>
</template>

<script setup>
import {
    ArrowUpTrayIcon,
    Cog6ToothIcon,
    RocketLaunchIcon,
    ArrowTopRightOnSquareIcon,
} from "@heroicons/vue/24/outline";
import JetButton from "@/Jetstream/Button.vue";

defineProps({
    showActions: {
        type: Boolean,
        default: false,
    },
    processing: {
        type: Boolean,
        default: false,
    },
});

defineEmits(["proceed", "dont-show-again"]);

const steps = [
    {
        title: "File upload",
        description:
            "Upload your raw or processed NMR data either by dragging and dropping files or by using the file browser.",
        icon: ArrowUpTrayIcon,
    },
    {
        title: "Processing & validation",
        description:
            "nmrXiv workflows archives your data, processes and extracts metadata from the files. Validation is performed to ensure the data is reusable and meets the recommended MiChI standards.",
        icon: Cog6ToothIcon,
    },
    {
        title: "Publish",
        description:
            "Publish your data or move it to embargo status to keep it private until you are ready to share it.",
        icon: RocketLaunchIcon,
    },
];

const conceptCards = [
    {
        term: "Project",
        body: "Think of a project like your publication container: the title, description, and all related NMR data you want to share on nmrXiv in one place. A project includes multiple samples.",
    },
    {
        term: "Study",
        body: "A study is the set of NMR experiments for one sample—for example 1H, 13C, COSY, HSQC, HMBC, and NOESY together.",
    },
    {
        term: "Dataset",
        body: "Each individual measurement inside a study is a dataset—for instance one 1H spectrum or one 2D experiment such as COSY.",
    },
];
</script>
