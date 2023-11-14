<template>
    <div class="bg-white">
        <div class="mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:px-8 lg:py-40">
            <div class="mx-auto max-w-4xl divide-y divide-gray-900/10">
                <h2
                    class="text-2xl font-bold leading-10 tracking-tight text-gray-900"
                >
                    Frequently asked questions
                </h2>
                <dl class="mt-10 space-y-6 divide-y divide-gray-900/10">
                    <Disclosure
                        as="div"
                        v-for="faq in faqs.slice(0, limit)"
                        :key="faq.question"
                        class="pt-6"
                        v-slot="{ open }"
                    >
                        <dt>
                            <DisclosureButton
                                class="flex w-full items-start justify-between text-left text-gray-900"
                            >
                                <span
                                    class="text-base font-semibold leading-7"
                                    >{{ faq.question }}</span
                                >
                                <span class="ml-6 flex h-7 items-center">
                                    <PlusSmallIcon
                                        v-if="!open"
                                        class="h-6 w-6"
                                        aria-hidden="true"
                                    />
                                    <MinusSmallIcon
                                        v-else
                                        class="h-6 w-6"
                                        aria-hidden="true"
                                    />
                                </span>
                            </DisclosureButton>
                        </dt>
                        <DisclosurePanel as="dd" class="mt-2 pr-12">
                            <p class="text-base leading-7 text-gray-600">
                                {{ faq.answer }}
                            </p>
                        </DisclosurePanel>
                    </Disclosure>
                </dl>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import { MinusSmallIcon, PlusSmallIcon } from "@heroicons/vue/24/outline";

const props = defineProps(["limit"]);

const faqs = [
    {
        question: "How can I submit my data to nmrXiv?",
        answer: " Register to nmrXiv. Structure your data in folders similar to nmrXiv structuring of projects, studies, and datasets. This step might not be intuitive so we recommend checking its docummentation. Upload your data, edit it, and provide its metadata via the submission pipeline.",
    },
    {
        question: "What should I do if I think I've found a bug?",
        answer: "If you believe you've found a bug in nmrXiv, please write to us at info.nmrxiv@uni-jena.de or reach out to our Helpdesk https://www.nfdi4chem.de/helpdesk/.",
    },
    {
        question: "Who can use my public/published resources?",
        answer: "If you make your resources public (projects, studies, datasets), you are making them open for access to everyone (even to the non-registered user of nmrXiv), but you can specify rights by choosing licenses for your projects and studies (study license propagate to its datasets). Once your project is made public, you cannot edit, delete or make it private again.",
    },
    {
        question: "What are supported files format in nmrXiv?",
        answer: "nmrXiv accepts all NMR formats uploaded. However, not all of them are readable at the moment. So far, only NMRium-supported formats can be translated into spectra in nmrXiv. Those formats are jcamp-dx, jeol, Bruker folders, NMReData, and nmrium. For validation purposes, the uploaded data should have at least one readable format.",
    },
    // More questions...
];
</script>
