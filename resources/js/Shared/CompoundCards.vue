<template>
    <div>
        <div v-if="loading" class="text-gray-400">
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
        <div v-else-if="isMoleculeMode && molecules.length > 0">
            <div
                class="mx-auto grid max-w-none gap-5 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6"
            >
                <span
                    v-for="molecule in molecules"
                    :key="moleculeKey(molecule)"
                    class="rounded-lg border shadow hover:shadow-lg"
                >
                    <MoleculeCard
                        :molecule="molecule"
                        :href="publicCompoundHref(molecule)"
                        :show-annotation-stars="false"
                    />
                </span>
            </div>
        </div>
        <div v-else-if="!isMoleculeMode && studies.length > 0">
            <div
                class="mx-auto grid max-w-none gap-5 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6"
            >
                <span
                    v-for="study in studies"
                    :key="study.uuid"
                    class="rounded-lg border shadow hover:shadow-lg"
                >
                    <MoleculeCard
                        v-if="primaryMolecule(study)"
                        :molecule="primaryMolecule(study)"
                        :href="route('dashboard.studies', [study.id])"
                        :show-annotation-stars="false"
                    />
                    <Link
                        v-else
                        :href="route('dashboard.studies', [study.id])"
                        class="block bg-white"
                    >
                        <div
                            class="flex min-h-[180px] items-center justify-center border-b bg-gray-50 p-4"
                        >
                            <span
                                class="text-center text-sm text-gray-500 dark:text-gray-400"
                                >No compound structure linked yet</span
                            >
                        </div>
                        <div class="px-4 py-4">
                            <p
                                v-if="study.identifier"
                                class="font-semibold text-gray-600 dark:text-gray-300"
                            >
                                {{ study.identifier }}
                            </p>
                            <p
                                class="mt-1 truncate text-sm font-medium text-gray-900 dark:text-gray-100"
                            >
                                {{ study.name }}
                            </p>
                            <p
                                class="mt-2 text-xs text-gray-500 dark:text-gray-400"
                            >
                                Open to add structure or spectra
                            </p>
                        </div>
                    </Link>
                </span>
            </div>
        </div>
        <div v-else-if="!isMoleculeMode" class="mt-4">
            <button
                type="button"
                class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                <span class="mt-2 block text-sm font-semibold text-gray-900"
                    >No compounds in library</span
                >
            </button>
        </div>
    </div>
</template>

<script>
import MoleculeCard from "@/App/MoleculeCard.vue";
import { Link } from "@inertiajs/vue3";

export default {
    name: "CompoundCards",
    components: {
        Link,
        MoleculeCard,
    },
    props: {
        studies: {
            default: () => [],
            type: Array,
        },
        molecules: {
            default: null,
            type: Array,
        },
        loading: {
            default: false,
            type: Boolean,
        },
        role: {
            default: () => ({}),
            type: Object,
        },
        teamRole: {
            default: null,
            type: Object,
        },
    },
    computed: {
        isMoleculeMode() {
            return this.molecules !== null;
        },
    },
    methods: {
        moleculeKey(molecule) {
            return molecule.id ?? molecule.identifier;
        },
        compoundNumericId(molecule) {
            const raw = molecule?.identifier;
            if (raw === null || raw === undefined) {
                return "";
            }
            const s = String(raw).replace(/^NMRXIV:M/i, "");
            const lead = s.match(/^(\d+)/);

            return lead ? lead[1] : s.replace(/\D/g, "") || "";
        },
        publicCompoundHref(molecule) {
            const numericId = this.compoundNumericId(molecule);
            if (numericId === "") {
                return "/search?scope=compounds";
            }

            return this.route("public.compound", { id: `M${numericId}` });
        },
        primaryMolecule(study) {
            if (!study) {
                return null;
            }
            const top = study.molecules;
            if (Array.isArray(top) && top.length > 0) {
                return top[0];
            }
            const nested = study.sample && study.sample.molecules;
            if (Array.isArray(nested) && nested.length > 0) {
                return nested[0];
            }

            return null;
        },
    },
};
</script>
