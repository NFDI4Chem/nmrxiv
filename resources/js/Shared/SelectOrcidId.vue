<template>
    <jet-dialog-modal
        :show="show"
        @close="show = false"
        v-model="this.selectedOrcidId"
        :value="item"
        as="div"
    >
        <template #title> Select ORCID iD </template>

        <template #content>
            <div v-if="loading" class="sm:col-span-9 mt-4 align-centre">
                <loading-button :loading="loading" />
            </div>
            <div
                v-if="orcidIdSearchResults.length == 0 && !loading"
                class="sm:col-span-9 mt-4 align-centre text-red-500"
            >
                <p>Something went wrong. Please enter the id manually.</p>
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
                        @click="selectOrcidId(item)"
                        class="cursor-pointer flex-1 border rounded-md p-2 bg-white-200 hover:bg-gray-200"
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
            <jet-secondary-button @click="show = false">
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
    emits: ["update:orcidId", "update:affiliation"],
    data() {
        return {
            show: false,
            selectedOrcidId: this.orcidId,
            selectedAffiliation: this.affiliation,
            loading: false,
            orcidIdSearchResults: [],
        };
    },
    methods: {
        findOrcidID(first_name, last_name) {
            this.orcidIdSearchResults = [];
            if (first_name && last_name) {
                this.loading = true;
                this.show = true;
                axios
                    .get(this.$page.props.orcidSearchApi, {
                        headers: {
                            accept: "application/json",
                        },
                        params: {
                            q:
                                "given-names:" +
                                first_name +
                                " AND family-name:" +
                                last_name,
                            start: 0,
                            row: 100,
                        },
                    })
                    .then((res) => {
                        if (res.data && res.data.result.length > 0) {
                            this.getPersonData(res.data.result);
                        } else {
                            this.loading = false;
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    })
                    .finally(() => {});
            }
        },
        getPersonData(results) {
            if (results) {
                var personData = {};
                var employmentdata = {};
                var element = {};
                var orcidId = "";
                this.orcidIdSearchResults = [];
                results.forEach((item) => {
                    orcidId = item["orcid-identifier"]
                        ? item["orcid-identifier"].path
                        : null;
                    if (orcidId) {
                        let personDataAPI =
                            this.$page.props.orcidPersonApi.replace(
                                "{orcid_id}",
                                orcidId
                            );
                        let employmentDataAPI =
                            this.$page.props.orcidEmploymentApi.replace(
                                "{orcid_id}",
                                item["orcid-identifier"].path
                            );

                        const requestPersonData = axios.get(personDataAPI, {
                            headers: {
                                accept: "application/json",
                            },
                        });
                        const requestEmploymentData = axios.get(
                            employmentDataAPI,
                            {
                                headers: {
                                    accept: "application/json",
                                },
                            }
                        );
                        axios
                            .all([requestPersonData, requestEmploymentData])
                            .then(
                                axios.spread((...responses) => {
                                    personData = responses[0].data;
                                    employmentdata = responses[1].data;
                                })
                            )
                            .catch((error) => {
                                console.log(error);
                            })
                            .finally(() => {
                                if (personData) {
                                    element.firstName = personData["name"]
                                        ? personData["name"]["given-names"]
                                              .value
                                        : "";
                                    element.lastName = personData["name"]
                                        ? personData["name"]["family-name"]
                                              .value
                                        : "";
                                    if (personData["emails"]) {
                                        element.email = personData["emails"][
                                            "email"
                                        ][0]
                                            ? personData["emails"]["email"][0]
                                                  .email
                                            : "";
                                    }
                                }
                                if (employmentdata) {
                                    if (employmentdata["affiliation-group"]) {
                                        if (
                                            employmentdata[
                                                "affiliation-group"
                                            ][0]
                                        ) {
                                            if (
                                                employmentdata[
                                                    "affiliation-group"
                                                ][0].summaries
                                            ) {
                                                element.employer =
                                                    employmentdata[
                                                        "affiliation-group"
                                                    ][0].summaries[0]
                                                        ? employmentdata[
                                                              "affiliation-group"
                                                          ][0].summaries[0][
                                                              "employment-summary"
                                                          ].organization.name
                                                        : "";
                                            }
                                        }
                                    }
                                }
                                element.orcidId = item["orcid-identifier"]
                                    ? item["orcid-identifier"].uri
                                    : "";
                                this.orcidIdSearchResults.push(element);
                                element = {};
                                this.loading = false;
                            });
                    }
                });
            }
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
