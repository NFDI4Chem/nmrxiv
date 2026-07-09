<template>
    <div
        v-if="doi"
        class="w-full min-w-0 rounded-lg border border-gray-200 bg-gray-50/60 p-3.5 dark:border-gray-700 dark:bg-gray-800/40"
    >
        <div class="flex items-center justify-between gap-2">
            <h3
                id="citation-heading"
                class="text-sm font-bold text-gray-900 dark:text-gray-100"
            >
                {{ headingTitle }}
            </h3>
            <button
                type="button"
                :class="[
                    'inline-flex shrink-0 items-center gap-1 rounded-md px-2 py-1 text-xs font-medium transition-colors',
                    copied
                        ? 'text-emerald-600 dark:text-emerald-400'
                        : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200',
                ]"
                :title="copied ? 'Copied!' : 'Copy citation'"
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
                <span>{{ copied ? "Copied" : "Copy" }}</span>
            </button>
        </div>

        <select
            id="citation-style-select"
            v-model="selectedFormat"
            class="mt-2 block w-full min-w-0 rounded-md border-0 bg-white py-1.5 pl-2 pr-7 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-teal-500 dark:bg-gray-900/60 dark:text-gray-300 dark:ring-gray-600 dark:focus:ring-teal-400"
            :disabled="loading && !citationText"
            aria-label="Citation format"
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

        <div
            class="mt-3"
            role="region"
            aria-labelledby="citation-heading"
            :aria-busy="loading ? 'true' : 'false'"
            aria-live="polite"
        >
            <div
                v-if="loading && !citationText"
                class="space-y-2"
                aria-hidden="true"
            >
                <div
                    class="h-2 w-full animate-pulse rounded bg-gray-200 dark:bg-gray-700"
                ></div>
                <div
                    class="h-2 w-11/12 animate-pulse rounded bg-gray-200 dark:bg-gray-700"
                ></div>
                <div
                    class="h-2 w-4/5 animate-pulse rounded bg-gray-200 dark:bg-gray-700"
                ></div>
            </div>

            <p
                v-else-if="loadError"
                class="text-xs leading-relaxed text-gray-500 dark:text-gray-400"
            >
                Could not load citation.
                <a
                    :href="doiResolverUrl"
                    class="font-medium text-teal-700 underline underline-offset-2 hover:text-teal-900 dark:text-teal-400 dark:hover:text-teal-300"
                    target="_blank"
                    rel="noopener noreferrer"
                    >View DOI</a
                >
            </p>

            <p
                v-else-if="citationText"
                class="max-h-[min(18rem,45vh)] overflow-y-auto text-xs leading-relaxed text-gray-600 dark:text-gray-400"
                v-html="sanitizeHtml(citationText)"
            ></p>
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
            if (this.model === "dataset") {
                return "Cite this dataset";
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
                            this.processedResponse.substring(
                                0,
                                matchIndex - 2
                            ) +
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
                            this.processedResponse.replace(" [Data set]", "");
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
