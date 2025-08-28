<template>
    <div v-if="citationText" class="p-3 sm:p-4">
        <div class="rounded-lg bg-blue-50 p-3 sm:p-4 border border-blue-200">
            <div class="flex flex-col sm:flex-row sm:items-start">
                <!-- Header section with icon and title -->
                <div class="flex items-start mb-3 sm:mb-0 sm:flex-shrink-0">
                    <div class="flex-shrink-0">
                        <svg
                            class="h-5 w-5 text-blue-400"
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
                    <div class="ml-3 sm:ml-2">
                        <h3 class="text-sm sm:text-base font-medium text-blue-700">
                            <span v-if="model == 'study'">Cite this sample</span>
                            <span v-else>Cite this {{ model }}</span>
                        </h3>
                    </div>
                </div>
                
                <!-- Format selector -->
                <div class="w-full sm:ml-3 sm:flex-1">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <label class="block text-xs font-medium text-blue-600 mb-1 sm:hidden">
                            Citation Format:
                        </label>
                        <select
                            v-model="selectedFormat"
                            class="w-full sm:w-auto sm:ml-auto block rounded-md border-gray-300 py-2 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white shadow-sm"
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
            </div>
            
            <!-- Citation text -->
            <div class="mt-4 pt-3 border-t border-blue-200">
                <p
                    class="text-sm sm:text-base font-medium text-gray-900 leading-relaxed break-words"
                    v-html="citationText"
                ></p>
                
                <!-- Copy button for mobile -->
                <div class="mt-3 sm:mt-4">
                    <button
                        @click="copyCitation"
                        class="inline-flex items-center px-3 py-2 border border-blue-300 rounded-md text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                    >
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Copy Citation
                    </button>
                </div>
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
                
                // Show success feedback (you could add a toast notification here)
                console.log('Citation copied to clipboard');
            } catch (err) {
                console.error('Failed to copy citation: ', err);
            }
        },
    },
};
</script>
