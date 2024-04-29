<template>
    <a>
        <div>
            <Link :href="'/spectra?compound=' + molecule.identifier">
                <div class="rounded overflow-hidden">
                    <Depictor2D
                        class="p-4 border-b"
                        :molecule="molecule.canonical_smiles"
                    ></Depictor2D>
                </div>
                <div class="py-4 px-4">
                    <div class="flex items-center">
                        <div class="mb-1 flex items-center">
                            <svg
                                :key="index"
                                v-for="index in molecule.annotation_level"
                                class="inline text-yellow-400 h-4 w-4 flex-shrink-0"
                                x-state:on="Active"
                                x-state:off="Inactive"
                                x-state-description='Active: "text-yellow-400", Inactive: "text-gray-200"'
                                x-description="Heroicon name: mini/star"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                                    clip-rule="evenodd"
                                ></path>
                            </svg>
                            <svg
                                :key="index"
                                v-for="index in 5 - molecule.annotation_level"
                                class="inline text-gray-200 h-4 w-4 flex-shrink-0"
                                x-state-description='undefined: "text-yellow-400", undefined: "text-gray-200"'
                                x-description="Heroicon name: mini/star"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                                    clip-rule="evenodd"
                                ></path>
                            </svg>
                        </div>
                    </div>
                    <div class="font-bold text-md mb-2">
                        <a class="font-semibold text-gray-600 hover:underline"
                            >#NMRXIV:M{{ molecule.identifier }}</a
                        >
                    </div>
                    <div
                        class="text-gray-700 text-base break-all text-sm capitalize"
                        v-if="molecule.iupac_name && molecule.iupac_name != ''"
                    >
                        {{ molecule.iupac_name }}
                    </div>
                    <div
                        class="text-gray-700 text-base break-all text-sm"
                        v-else
                    >
                        {{ molecule.canonical_smiles }}
                    </div>
                </div>

                <!-- <div class="px-6 pb-2">
        <span
          class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2"
          >#photography</span
        >
        <span
          class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2"
          >#travel</span
        >
        <span
          class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2"
          >#winter</span
        >
      </div> -->
                <!-- </div> -->
            </Link>
        </div>
    </a>
</template>
<script>
import Depictor2D from "@/Shared/Depictor2D.vue";
import { Link } from "@inertiajs/vue3";
export default {
    components: {
        Depictor2D,
        Link,
    },
    props: ["molecule"],
    computed: {},
    data() {
        return {
            results: [],
        };
    },
    mounted() {},
    methods: {},
};
</script>
