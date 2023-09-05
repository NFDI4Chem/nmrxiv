<template>
    <div v-if="citationText" class="p-4">
        <div class="rounded-md bg-blue-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <InformationCircleIcon class="h-5 w-5 text-blue-400" />
                </div>
                <div class="ml-3 w-full">
                    <p class="text-md text-blue-700">
                        Cite this {{ model }}
                        <select
                            @change="queryDataCite"
                            v-model="selectedFormat"
                            class="-mt-2 -mr-2 block rounded-md border-gray-300 py-1 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm float-right"
                        >
                            <option name="citation" value="APA">APA</option>
                            <option name="citation" value="Harvard">
                                Harvard
                            </option>
                            <option name="citation" value="MLA">MLA</option>
                            <option name="citation" value="Vancouver">
                                Vancouver
                            </option>
                            <option name="citation" value="Chicago">
                                Chicago
                            </option>
                            <option name="citation" value="IEEE">IEEE</option>
                        </select>
                    </p>
                    <p
                        class="text-md font-bold mt-1 text-gray-900"
                        v-html="citationText"
                    ></p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { InformationCircleIcon } from "@heroicons/vue/24/solid";
export default {
    components: { InformationCircleIcon },
    props: ["doi", "model"],
    mounted() {
        if (this.doi) {
            this.queryDataCite();
        }
    },
    data() {
        return {
            formats: {
                APA: "apa",
                Harvard: "harvard-cite-them-right",
                MLA: "modern-language-association",
                Vancouver: "vancouver",
                Chicago: "chicago-fullnote-bibliography",
                IEEE: "ieee",
            },
            selectedFormat: "APA",
            citationText: null,
        };
    },
    computed: {
        dataciteURL() {
            return this.$page.props.dataciteURL
                ? String(this.$page.props.dataciteURL) + "/dois/"
                : "https://api.test.datacite.org/dois/";
        },
    },
    watch: {
        doi(newDOI, oldDOI) {
            if (newDOI != oldDOI) {
                this.queryDataCite();
            }
        },
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
                        this.citationText = response.data;
                    },
                    (error) => {
                        console.log(error);
                    }
                );
        },
    },
};
</script>
