<template>
    <jet-dialog-modal :show="show" @close="closeModal">
        <template #title> Select ORCID iD </template>

        <template #content>
            <div v-if="loading" class="sm:col-span-9 mt-4 align-centre">
                <loading-button :loading="loading" />
            </div>
            <div
                v-if="
                    !loading && !hasError && orcidIdSearchResults.length === 0
                "
                class="sm:col-span-9 mt-4 align-centre text-gray-500"
            >
                <p>
                    No results found. Please try again or enter the ID manually.
                </p>
            </div>
            <div
                v-if="!loading && hasError"
                class="sm:col-span-9 mt-4 align-centre text-red-500"
            >
                <p>{{ errorMessage }}</p>
            </div>
            <div
                v-if="!loading && orcidIdSearchResults.length > 0"
                style="min-height: 10vh; max-height: 60vh"
                class="overflow-auto p-1"
            >
                <div
                    v-for="item in orcidIdSearchResults"
                    :key="item.orcidId"
                    class="relative flex items-start mt-2"
                >
                    <div
                        class="cursor-pointer flex-1 border rounded-md p-2 bg-white-200 hover:bg-gray-200"
                        @click="selectOrcidId(item)"
                    >
                        <div class="text-gray-900">
                            <p class="text-sm font-medium text-teal-900">
                                {{ item.firstName }} {{ item.lastName }}
                            </p>
                            <p
                                v-if="item.employer"
                                class="text-xs text-gray-500 mt-1"
                            >
                                <b class="text-gray-500">Employer:</b>
                                {{ item.employer }}
                            </p>
                            <p
                                v-if="item.email"
                                class="text-xs text-gray-500 mt-1"
                            >
                                <b class="text-gray-500">Email:</b>
                                {{ item.email }}
                            </p>
                            <div
                                class="flex items-center text-xs text-gray-600 mt-1 underline"
                            >
                                <img
                                    alt="ORCID logo"
                                    src="https://orcid.org/assets/vectors/orcid.logo.icon.svg"
                                    width="15"
                                    height="15"
                                />
                                <p class="ml-1">{{ item.orcidId }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
            <jet-secondary-button @click="closeModal">
                Cancel
            </jet-secondary-button>
        </template>
    </jet-dialog-modal>
</template>
<script>
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import LoadingButton from "@/Shared/LoadingButton.vue";

export default {
    components: {
        JetSecondaryButton,
        JetDialogModal,
        LoadingButton,
    },
    props: ["orcidId", "affiliation"],
    emits: ["update:orcidId", "update:affiliation", "loadingComplete"],
    data() {
        return {
            show: false,
            selectedOrcidId: this.orcidId,
            selectedAffiliation: this.affiliation,
            loading: false,
            hasError: false,
            errorMessage: "",
            orcidIdSearchResults: [],
            pendingRequests: 0,
            isSearching: false,
        };
    },
    methods: {
        /**
         * Search for ORCID IDs by first and last name
         * Prevents duplicate searches while one is in progress
         * @param {string} first_name - User's first name
         * @param {string} last_name - User's last name
         */
        findOrcidID(first_name, last_name) {
            // Prevent duplicate requests while search is in progress
            if (this.isSearching) {
                return;
            }

            // Validate input
            if (!first_name || !last_name) {
                return;
            }

            // Reset state for new search
            this.resetSearchState();
            this.isSearching = true;
            this.loading = true;

            axios
                .get("/orcid/search", {
                    headers: {
                        accept: "application/json",
                    },
                    params: {
                        q: `given-names:${first_name} AND family-name:${last_name}`,
                    },
                })
                .then((res) => {
                    if (
                        res.data &&
                        res.data.result &&
                        res.data.result.length > 0
                    ) {
                        this.show = true;
                        this.$emit("loadingComplete");
                        this.getPersonData(res.data.result);
                    } else {
                        this.handleNoResults();
                    }
                })
                .catch((error) => {
                    this.handleSearchError(error);
                });
        },
        /**
         * Fetch detailed person data for each ORCID ID from search results
         * Tracks pending requests to maintain loading state until all complete
         * @param {Array} results - Array of ORCID search results
         */
        getPersonData(results) {
            if (!results || results.length === 0) {
                this.finalizeSearch();
                return;
            }

            this.orcidIdSearchResults = [];
            this.pendingRequests = results.length;

            results.forEach((item) => {
                const orcidId = item["orcid-identifier"]?.path;

                if (!orcidId) {
                    this.decrementPendingRequests();
                    return;
                }

                this.fetchOrcidDetails(orcidId, item);
            });
        },

        /**
         * Fetch person and employment details for a single ORCID ID
         * @param {string} orcidId - The ORCID identifier
         * @param {Object} item - Original search result item
         */
        fetchOrcidDetails(orcidId, item) {
            const requestPersonData = axios.get(`/orcid/${orcidId}/person`, {
                headers: { accept: "application/json" },
            });

            const requestEmploymentData = axios.get(
                `/orcid/${orcidId}/employment`,
                {
                    headers: { accept: "application/json" },
                }
            );

            axios
                .all([requestPersonData, requestEmploymentData])
                .then(
                    axios.spread((personResponse, employmentResponse) => {
                        const personData = personResponse.data;
                        const employmentData = employmentResponse.data;

                        const element = this.formatOrcidResult(
                            personData,
                            employmentData,
                            item
                        );

                        if (element) {
                            this.orcidIdSearchResults.push(element);
                        }
                    })
                )
                .catch((error) => {
                    console.error(
                        `Error fetching ORCID details for ${orcidId}:`,
                        error
                    );
                })
                .finally(() => {
                    this.decrementPendingRequests();
                });
        },

        /**
         * Format raw ORCID API data into display-ready format
         * @param {Object} personData - Person data from ORCID API
         * @param {Object} employmentData - Employment data from ORCID API
         * @param {Object} item - Original search result item
         * @returns {Object|null} Formatted result object or null if data is invalid
         */
        formatOrcidResult(personData, employmentData, item) {
            const element = {
                firstName: personData?.name?.["given-names"]?.value || "",
                lastName: personData?.name?.["family-name"]?.value || "",
                email: personData?.emails?.email?.[0]?.email || "",
                employer: this.extractEmployerName(employmentData),
                orcidId: item["orcid-identifier"]?.uri || "",
            };

            return element.orcidId ? element : null;
        },

        /**
         * Extract employer name from employment data
         * @param {Object} employmentData - Employment data from ORCID API
         * @returns {string} Employer name or empty string
         */
        extractEmployerName(employmentData) {
            const affiliationGroup = employmentData?.["affiliation-group"]?.[0];
            const summary = affiliationGroup?.summaries?.[0];
            return summary?.["employment-summary"]?.organization?.name || "";
        },

        /**
         * Decrement pending requests counter and finalize when all complete
         */
        decrementPendingRequests() {
            this.pendingRequests--;
            if (this.pendingRequests <= 0) {
                this.finalizeSearch();
            }
        },

        /**
         * Complete the search process and update UI state
         */
        finalizeSearch() {
            this.loading = false;
            this.isSearching = false;
        },

        /**
         * Handle case when no results are found
         */
        handleNoResults() {
            this.finalizeSearch();
            this.show = true;
            this.$emit("loadingComplete");
        },

        /**
         * Handle search errors with user-friendly messaging
         * @param {Error} error - Error object from API call
         */
        handleSearchError(error) {
            console.error("ORCID search error:", error);
            this.hasError = true;
            this.errorMessage =
                "An error occurred while searching. Please try again or enter the ID manually.";
            this.finalizeSearch();
            this.show = true;
            this.$emit("loadingComplete");
        },

        /**
         * Reset search state for new search
         */
        resetSearchState() {
            this.orcidIdSearchResults = [];
            this.hasError = false;
            this.errorMessage = "";
            this.pendingRequests = 0;
        },

        /**
         * Close modal and reset state
         */
        closeModal() {
            this.show = false;
            this.resetSearchState();
        },
        selectOrcidId(item) {
            this.selectedOrcidId = item.orcidId
                ? item.orcidId.substr(18, item.orcidId.length)
                : "";
            this.selectedAffiliation = item.employer ? item.employer : "";
            this.$emit("update:orcidId", this.selectedOrcidId);
            this.$emit("update:affiliation", this.selectedAffiliation);

            this.show = false;
        },
    },
};
</script>
