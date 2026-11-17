<template>
    <Popover v-slot="{ open }" class="relative">
        <PopoverButton
            :class="[
                open
                    ? 'bg-indigo-50 text-indigo-700 ring-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-300 dark:ring-indigo-800'
                    : 'bg-white text-gray-700 ring-gray-200 hover:bg-gray-50 hover:text-gray-900 dark:bg-gray-900 dark:text-gray-200 dark:ring-gray-700 dark:hover:bg-gray-800',
                'inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-medium shadow-sm ring-1 ring-inset transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500',
            ]"
        >
            <LinkIcon class="h-4 w-4" aria-hidden="true" />
            Share library
        </PopoverButton>

        <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="opacity-0 translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-1"
        >
            <PopoverPanel
                class="absolute right-0 z-20 mt-2 w-[min(24rem,calc(100vw-2rem))] rounded-xl bg-white p-4 shadow-xl ring-1 ring-black/5 dark:bg-gray-900 dark:ring-white/10"
            >
                <p
                    class="text-sm font-semibold text-gray-900 dark:text-gray-100"
                >
                    Share your compound library
                </p>
                <p class="mt-1 text-xs leading-relaxed text-gray-500">
                    Anyone with this link can browse the compounds in this
                    workspace. Published compounds link to their public records;
                    unpublished compounds are listed with their structure but
                    cannot be opened.
                </p>

                <div class="mt-3 flex items-center gap-2">
                    <label :for="inputId" class="sr-only"
                        >Compound library link</label
                    >
                    <input
                        :id="inputId"
                        type="text"
                        readonly
                        :value="url"
                        class="block w-full min-w-0 rounded-lg border-gray-300 bg-gray-50 py-1.5 font-mono text-xs text-gray-700 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-300"
                        @focus="$event.target.select()"
                    />
                    <button
                        type="button"
                        class="inline-flex shrink-0 items-center gap-1 rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition-colors hover:bg-indigo-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                        @click="copyLink"
                    >
                        <CheckIcon
                            v-if="copied"
                            class="h-4 w-4"
                            aria-hidden="true"
                        />
                        <ClipboardDocumentIcon
                            v-else
                            class="h-4 w-4"
                            aria-hidden="true"
                        />
                        {{ copied ? "Copied" : "Copy" }}
                    </button>
                </div>
                <p class="sr-only" role="status" aria-live="polite">
                    {{ copied ? "Link copied to clipboard" : "" }}
                </p>

                <div
                    class="mt-3 flex items-center justify-between gap-3 border-t border-gray-100 pt-3 dark:border-gray-800"
                >
                    <p class="text-xs text-gray-500">
                        <template v-if="publicCount === null">
                            Unpublished compounds are shown as locked.
                        </template>
                        <template v-else-if="publicCount > 0">
                            <span
                                class="font-semibold tabular-nums text-gray-700 dark:text-gray-300"
                                >{{ publicCount }}</span
                            >
                            published
                            {{ publicCount === 1 ? "compound" : "compounds" }}
                        </template>
                        <template v-else>
                            No published compounds yet.
                        </template>
                    </p>
                    <a
                        :href="url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex shrink-0 items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400"
                    >
                        Open page
                        <ArrowTopRightOnSquareIcon
                            class="h-3.5 w-3.5"
                            aria-hidden="true"
                        />
                    </a>
                </div>
            </PopoverPanel>
        </transition>
    </Popover>
</template>

<script>
import { Popover, PopoverButton, PopoverPanel } from "@headlessui/vue";
import {
    ArrowTopRightOnSquareIcon,
    CheckIcon,
    ClipboardDocumentIcon,
    LinkIcon,
} from "@heroicons/vue/24/outline";

export default {
    components: {
        Popover,
        PopoverButton,
        PopoverPanel,
        ArrowTopRightOnSquareIcon,
        CheckIcon,
        ClipboardDocumentIcon,
        LinkIcon,
    },
    props: {
        url: {
            type: String,
            required: true,
        },
        publicCount: {
            type: Number,
            default: null,
        },
    },
    data() {
        return {
            copied: false,
            copiedTimer: null,
            inputId: "share-compound-library-url",
        };
    },
    beforeUnmount() {
        clearTimeout(this.copiedTimer);
    },
    methods: {
        async copyLink() {
            try {
                await navigator.clipboard.writeText(this.url);
            } catch {
                const el = document.createElement("textarea");
                el.value = this.url;
                el.setAttribute("readonly", "");
                el.style.position = "fixed";
                el.style.left = "-9999px";
                document.body.appendChild(el);
                el.select();
                document.execCommand("copy");
                document.body.removeChild(el);
            }

            this.copied = true;
            clearTimeout(this.copiedTimer);
            this.copiedTimer = setTimeout(() => {
                this.copied = false;
            }, 2000);
        },
    },
};
</script>
