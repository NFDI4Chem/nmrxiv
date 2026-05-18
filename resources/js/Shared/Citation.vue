<template>
    <div v-if="doi" class="w-full min-w-0">
        <div
            class="rounded-md border border-blue-200 bg-blue-50 dark:border-blue-900/50 dark:bg-blue-950/30"
        >
            <div class="flex flex-col gap-3 p-3">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex min-w-0 items-start gap-2">
                        <svg
                            class="mt-0.5 h-5 w-5 shrink-0 text-blue-500 dark:text-blue-400"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M19 10.5a8.5 8.5 0 11-17 0 8.5 8.5 0 0117 0zM8.25 9.75A.75.75 0 019 9h.253a1.75 1.75 0 011.709 2.13l-.46 2.066a.25.25 0 00.245.304H11a.75.75 0 010 1.5h-.253a1.75 1.75 0 01-1.709-2.13l.46-2.066a.25.25 0 00-.245-.304H9a.75.75 0 01-.75-.75zM10 7a1 1 0 100-2 1 1 0 000 2z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <h3
                            id="citation-heading"
                            class="min-w-0 text-sm font-semibold leading-snug text-blue-900 dark:text-blue-100"
                        >
                            {{ headingTitle }}
                        </h3>
                    </div>
                    <button
                        type="button"
                        :class="[
                            'inline-flex shrink-0 items-center justify-center rounded-md border p-1.5 transition-all duration-200',
                            copied
                                ? 'border-green-300 bg-green-50 text-green-600 dark:border-green-800 dark:bg-green-950/50 dark:text-green-400'
                                : 'border-transparent bg-blue-100/80 text-blue-600 hover:border-blue-300 hover:bg-blue-100 dark:bg-blue-900/40 dark:text-blue-300 dark:hover:border-blue-600 dark:hover:bg-blue-900/70',
                        ]"
                        :title="
                            copied ? 'Copied!' : 'Copy citation to clipboard'
                        "
                        :aria-label="
                            copied
                                ? 'Citation copied to clipboard'
                                : 'Copy citation to clipboard'
                        "
                        :disabled="!citationText"
                        @click="copyCitation"
                    >
                        <svg
                            v-if="!copied"
                            class="h-3.5 w-3.5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
                            />
                        </svg>
                        <svg
                            v-else
                            class="h-3.5 w-3.5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>
                    </button>
                </div>

                <div class="flex min-w-0 flex-col gap-1.5">
                    <label
                        class="text-xs font-semibold uppercase tracking-wide text-blue-700 dark:text-blue-300"
                        for="citation-style-select"
                    >
                        Format
                    </label>
                    <select
                        id="citation-style-select"
                        v-model="selectedFormat"
                        class="block w-full min-w-0 rounded-md border-gray-300 bg-white py-1.5 pl-2 pr-8 text-sm font-medium text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-400 dark:focus:ring-blue-400"
                        :disabled="loading && !citationText"
                        @change="queryDataCite"
                    >
                        <option value="APA">APA</option>
                        <option value="Harvard">Harvard</option>
                        <option value="MLA">MLA</option>
                        <option value="Vancouver">Vancouver</option>
                        <option value="Chicago">Chicago</option>
                        <option value="IEEE">IEEE</option>
                        <option value="ACS">ACS</option>
                        <option value="RSC">RSC</option>
                        <option value="Wiley">Wiley</option>
                        <option value="Springer">Springer Nature</option>
                    </select>
                </div>
            </div>

            <div
                class="border-t border-blue-200 px-3 pb-3 pt-3 dark:border-blue-900/50"
                role="region"
                :aria-busy="loading ? 'true' : 'false'"
                aria-live="polite"
            >
                <div
                    v-if="loading && !citationText"
                    class="space-y-2"
                    aria-hidden="true"
                >
                    <div class="h-2.5 w-full animate-pulse rounded bg-blue-200/80 dark:bg-blue-900/60"></div>
                    <div
                        class="h-2.5 w-5/6 animate-pulse rounded bg-blue-200/80 dark:bg-blue-900/60"
                    ></div>
                    <div
                        class="h-2.5 w-4/6 animate-pulse rounded bg-blue-200/80 dark:bg-blue-900/60"
                    ></div>
                </div>

                <div
                    v-else-if="loadError"
                    class="rounded border border-amber-200 bg-amber-50 p-2 text-xs leading-relaxed text-amber-900 dark:border-amber-900/60 dark:bg-amber-950/40 dark:text-amber-100"
                >
                    <p class="text-sm font-semibold">Could not load citation.</p>
                    <p class="mt-0.5 font-normal opacity-90">
                        Try another format or open
                        <a
                            :href="doiResolverUrl"
                            class="font-semibold text-amber-800 underline dark:text-amber-200"
                            target="_blank"
                            rel="noopener noreferrer"
                            >the DOI page</a
                        >.
                    </p>
                </div>

                <p
                    v-else-if="citationText"
                    class="max-h-[min(22rem,50vh)] overflow-y-auto break-words text-sm font-normal leading-relaxed text-gray-900 antialiased dark:text-gray-100"
                    v-html="sanitizeHtml(citationText)"
                ></p>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    props: ["doi", "model"],
    data() {
        return {
            formats: {
                APA: "apa",
                Harvard: "harvard-cite-them-right",
                MLA: "modern-language-association",
                Vancouver: "vancouver",
                Chicago: "chicago-fullnote-bibliography",
                IEEE: "ieee",
                ACS: "american-chemical-society",
                RSC: "royal-society-of-chemistry",
                Wiley: "wiley-was",
                Springer: "apa",
            },
            selectedFormat: "APA",
            citationText: null,
            processedResponse: null,
            copied: false,
            loading: false,
            loadError: false,
        };
    },
    computed: {
        dataciteURL() {
            return this.$page.props.dataciteURL
                ? String(this.$page.props.dataciteURL) + "/dois/"
                : "https://api.datacite.org/dois/";
        },
        doiResolverUrl() {
            return "https://doi.org/" + this.doi;
        },
        headingTitle() {
            if (this.model === "study") {
                return "Cite this study";
            }
            if (this.model === "sample") {
                return "Cite this sample";
            }
            if (this.model === "project") {
                return "Cite this project";
            }
            return "Cite this " + this.model;
        },
    },
    watch: {
        doi(newDOI, oldDOI) {
            if (newDOI !== oldDOI) {
                this.queryDataCite();
            }
        },
    },
    mounted() {
        if (this.doi) {
            this.queryDataCite();
        }
    },
    methods: {
        queryDataCite() {
            if (!this.doi) {
                return;
            }

            this.loading = true;
            this.loadError = false;

            const config = {
                headers: {
                    Accept: "text/x-bibliography; charset=utf-8",
                },
            };

            axios
                .get(
                    this.dataciteURL +
                        this.doi +
                        "?style=" +
                        this.formats[this.selectedFormat],
                    config
                )
                .then((response) => {
                    this.processedResponse = response.data;
                    if (this.selectedFormat === "ACS") {
                        const pattern = /20(\d{2})(?=\.)/;
                        const matchIndex =
                            this.processedResponse.search(pattern);
                        this.processedResponse =
                            this.processedResponse.substring(0, matchIndex - 2) +
                            ". nmrXiv. " +
                            this.processedResponse.substring(matchIndex);
                    } else if (this.selectedFormat === "RSC") {
                        const id = this.doi.substring(
                            this.doi.lastIndexOf(".") + 1
                        );
                        this.processedResponse =
                            this.processedResponse.slice(0, -1) +
                            ", nmrXiv Data set:" +
                            id +
                            ", DOI:" +
                            this.doi;
                    } else if (this.selectedFormat === "Wiley") {
                        this.processedResponse =
                            "[dataset] " +
                            this.processedResponse.replace(
                                " [Data set]",
                                ""
                            );
                    } else if (this.selectedFormat === "Springer") {
                        const springerPattern = /\(20(\d{2})\)/;
                        const match =
                            this.processedResponse.match(springerPattern);
                        const springerMatchIndex =
                            this.processedResponse.search(springerPattern);
                        if (match) {
                            this.processedResponse =
                                this.processedResponse.substring(
                                    0,
                                    springerMatchIndex
                                ) +
                                this.processedResponse
                                    .replace(" [Data set]", "")
                                    .substring(springerMatchIndex + 8) +
                                " " +
                                match[0];
                        }
                    }
                    this.citationText = this.processedResponse;
                    this.loadError = false;
                })
                .catch(() => {
                    this.loadError = true;
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        async copyCitation() {
            try {
                const tempDiv = document.createElement("div");
                tempDiv.innerHTML = this.citationText;
                const plainText =
                    tempDiv.textContent || tempDiv.innerText || "";

                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(plainText);
                } else {
                    const textArea = document.createElement("textarea");
                    textArea.value = plainText;
                    textArea.style.position = "fixed";
                    textArea.style.left = "-999999px";
                    textArea.style.top = "-999999px";
                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();
                    document.execCommand("copy");
                    textArea.remove();
                }

                this.copied = true;

                setTimeout(() => {
                    this.copied = false;
                }, 2000);
            } catch (err) {
                console.error("Failed to copy citation: ", err);
            }
        },
    },
};
</script>
