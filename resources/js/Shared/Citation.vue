<template>
    <div v-if="citationText" class="p-2 sm:p-3">
        <div class="rounded-md bg-blue-50 border border-blue-200">
            <!-- Compact header section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-3 px-2 sm:px-2 pt-2">
                <!-- Title and icon section with copy button -->
                <div class="flex items-center gap-2">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg
                                class="h-4 w-4 text-blue-400"
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
                        </div>
                        <div class="ml-2">
                            <h3 class="text-sm font-medium text-blue-700">
                                <span v-if="model == 'study'">Cite this sample</span>
                                <span v-else>Cite this {{ model }}</span>
                            </h3>
                        </div>
                    </div>
                    
                    <!-- Copy button next to title -->
                    <button
                        @click="copyCitation"
                        :class="[
                            'inline-flex items-center justify-center p-1.5 rounded-md border transition-all duration-200',
                            copied 
                                ? 'border-green-300 bg-green-50 text-green-600' 
                                : 'bg-blue-50 text-blue-600 hover:bg-blue-100 hover:border-blue-400'
                        ]"
                        :title="copied ? 'Copied!' : 'Copy citation to clipboard'"
                    >
                        <!-- Copy icon -->
                        <svg 
                            v-if="!copied"
                            class="h-3.5 w-3.5" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path 
                                stroke-linecap="round" 
                                stroke-linejoin="round" 
                                stroke-width="2" 
                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" 
                            />
                        </svg>
                        <!-- Check icon when copied -->
                        <svg 
                            v-else
                            class="h-3.5 w-3.5" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
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
                
                <!-- Compact format selector -->
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-blue-600 hidden sm:block">
                        Format:
                    </label>
                    <select
                        v-model="selectedFormat"
                        class="block rounded-md border-gray-300 py-1.5 px-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white shadow-sm min-w-[120px]"
                        @change="queryDataCite"
                    >
                        <option name="citation" value="APA">APA</option>
                        <option name="citation" value="Harvard">Harvard</option>
                        <option name="citation" value="MLA">MLA</option>
                        <option name="citation" value="Vancouver">Vancouver</option>
                        <option name="citation" value="Chicago">Chicago</option>
                        <option name="citation" value="IEEE">IEEE</option>
                        <option name="citation" value="ACS">ACS</option>
                        <option name="citation" value="RSC">RSC</option>
                        <option name="citation" value="Wiley">Wiley</option>
                        <option name="citation" value="Springer">Springer Nature</option>
                    </select>
                </div>
            </div>
            
            <!-- Citation text section -->
            <div class="mt-3 pt-3 border-t border-blue-200 p-3 sm:p-4">
                <p
                    class="text-sm font-medium text-gray-900 leading-relaxed break-words"
                    v-html="citationText"
                ></p>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
export default {
    components: {},
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
        };
    },
    computed: {
        dataciteURL() {
            return this.$page.props.dataciteURL
                ? String(this.$page.props.dataciteURL) + "/dois/"
                : "https://api.datacite.org/dois/";
        },
    },
    watch: {
        doi(newDOI, oldDOI) {
            if (newDOI != oldDOI) {
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
            let config = {
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
                .then(
                    (response) => {
                        this.processedResponse = response.data;
                        if (this.selectedFormat == "ACS") {
                            var pattern = /20(\d{2})(?=\.)/;
                            var matchIndex =
                                this.processedResponse.search(pattern);
                            this.processedResponse =
                                this.processedResponse.substring(
                                    0,
                                    matchIndex - 2
                                ) +
                                ". nmrXiv. " +
                                this.processedResponse.substring(matchIndex);
                        } else if (this.selectedFormat == "RSC") {
                            var id = this.doi.substring(
                                this.doi.lastIndexOf(".") + 1
                            );
                            this.processedResponse =
                                this.processedResponse.slice(0, -1) +
                                ", nmrXiv Data set:" +
                                id +
                                ", DOI:" +
                                this.doi;
                        } else if (this.selectedFormat == "Wiley") {
                            this.processedResponse =
                                "[dataset] " +
                                this.processedResponse.replace(
                                    " [Data set]",
                                    ""
                                );
                        } else if (this.selectedFormat == "Springer") {
                            var pattern = /\(20(\d{2})\)/;
                            var match = this.processedResponse.match(pattern);
                            var matchIndex =
                                this.processedResponse.search(pattern);
                            this.processedResponse =
                                this.processedResponse.substring(
                                    0,
                                    matchIndex
                                ) +
                                this.processedResponse
                                    .replace(" [Data set]", "")
                                    .substring(matchIndex + 8) +
                                " " +
                                match[0];
                        }
                        this.citationText = this.processedResponse;
                    },
                    (error) => {
                        console.log(error);
                    }
                );
        },
        async copyCitation() {
            try {
                // Create a temporary element to extract plain text from HTML
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = this.citationText;
                const plainText = tempDiv.textContent || tempDiv.innerText || '';
                
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(plainText);
                } else {
                    // Fallback for older browsers
                    const textArea = document.createElement('textarea');
                    textArea.value = plainText;
                    textArea.style.position = 'fixed';
                    textArea.style.left = '-999999px';
                    textArea.style.top = '-999999px';
                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();
                    document.execCommand('copy');
                    textArea.remove();
                }
                
                // Show visual feedback
                this.copied = true;
                
                // Reset the copied state after 2 seconds
                setTimeout(() => {
                    this.copied = false;
                }, 2000);
                
            } catch (err) {
                console.error('Failed to copy citation: ', err);
            }
        },
    },
};
</script>
