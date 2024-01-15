<template>
    <div>
        <div v-if="!loading && studies">
            <div v-if="studies.length == 0">
                <div
                    class="mt-8 mx-auto max-w-md grid gap-8 sm:max-w-lg lg:grid-cols-3 lg:max-w-7xl"
                >
                    <div v-for="study in studies" :key="study.uuid">
                        <study-card :study="study" />
                    </div>
                </div>
            </div>
            <div class="mt-4" v-else>
                <button
                    type="button"
                    class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    <span class="mt-2 block text-sm font-semibold text-gray-900"
                        >No samples</span
                    >
                </button>
            </div>
        </div>
        <div v-else class="text-gray-400">
            <svg
                class="animate-spin inline -ml-1 mr-2 h-5 w-5 text-dark"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                ></circle>
                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
            </svg>
            Loading...
        </div>
        <!-- <study-create :project="project"></study-create> -->
    </div>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import StudyCreate from "@/Pages/Study/Partials/Create.vue";
import StudyCard from "@/Shared/StudyCardPublic.vue";
import JetButton from "@/Jetstream/Button.vue";
import { ArrowLongLeftIcon, ArrowLongRightIcon } from "@heroicons/vue/24/solid";

export default {
    components: {
        Link,
        StudyCreate,
        StudyCard,
        JetButton,
        ArrowLongLeftIcon,
        ArrowLongRightIcon,
    },
    props: {
        studies: {
            default: [],
            type: Object,
        },
        role: {
            default: {},
            type: Object,
        },
        teamRole: {
            default: null,
            type: Object,
        },
    },
    data() {
        return {
            loading: false,
            query: "",
        };
    },
    mounted() {
        if (this.studies.length > 0) {
            this.loading = false;
        }
    },
    methods: {
        fetchStudies(url) {
            axios.get(url).then((response) => {
                this.loading = false;
                this.studies = response.data;
            });
        },
    },
};
</script>
