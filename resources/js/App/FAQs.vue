<template>
    <div class="bg-white">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
            <!-- Section Header -->
            <div class="text-center mb-10">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">
                    Frequently asked questions
                </h2>
                <p class="mt-3 text-gray-500">
                    Quick answers to common questions
                </p>
            </div>

            <!-- FAQ Accordion -->
            <div class="divide-y divide-gray-100">
                <Disclosure
                    v-for="faq in faqs.slice(0, limit)"
                    :key="faq.question"
                    v-slot="{ open }"
                    as="div"
                >
                    <DisclosureButton
                        class="flex w-full items-center justify-between py-5 text-left group"
                    >
                        <span
                            class="text-base font-medium pr-4"
                            :class="
                                open
                                    ? 'text-teal-700'
                                    : 'text-gray-900 group-hover:text-gray-700'
                            "
                        >
                            {{ faq.question }}
                        </span>
                        <ChevronDownIcon
                            class="h-5 w-5 flex-shrink-0 transition-transform duration-200"
                            :class="
                                open
                                    ? 'rotate-180 text-teal-600'
                                    : 'text-gray-400'
                            "
                        />
                    </DisclosureButton>
                    <DisclosurePanel class="pb-5 pr-12">
                        <p class="text-gray-600 leading-relaxed">
                            {{ faq.answer }}
                        </p>
                    </DisclosurePanel>
                </Disclosure>
            </div>

            <!-- Help Link -->
            <div class="mt-10 pt-8 border-t border-gray-100 text-center">
                <p class="text-gray-500 text-sm">
                    Can't find what you're looking for?
                    <a
                        href="https://docs.nmrxiv.org/FAQs.html"
                        target="_blank"
                        class="text-teal-600 font-medium hover:text-teal-700"
                    >
                        View all FAQs
                    </a>
                    or
                    <a
                        href="https://www.nfdi4chem.de/helpdesk/"
                        target="_blank"
                        class="text-teal-600 font-medium hover:text-teal-700"
                    >
                        contact support
                    </a>
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import { ChevronDownIcon } from "@heroicons/vue/24/outline";

const { limit } = defineProps({
    limit: {
        type: Number,
        default: undefined,
    },
});

const faqs = [
    {
        question: "Why should I submit data to nmrXiv?",
        answer: "Submitting to nmrXiv helps make your research more findable, accessible, and reusable (FAIR). It enhances reproducibility, increases visibility of your work, supports open science initiatives, provides DOIs for data citation, and contributes to the global NMR research community.",
    },
    {
        question: "How nmrXiv is different from Zenodo?",
        answer: "nmrXiv is a specialized repository designed specifically for NMR data with domain-specific validation, visualization tools, and metadata standards. While Zenodo is a general-purpose repository, nmrXiv provides tailored features for the NMR community including format validation, spectral viewers, and standardized NMR-specific metadata.",
    },
    {
        question:
            "What is the minimum requirements for submitting my NMR data?",
        answer: "The minimum requirements include: organized data structure (project, samples, and datasets), at least one NMR spectrum in a readable format (JCAMP-DX, JEOL, Bruker, NMReData, or NMRium), and basic metadata describing your sample and experiment. At least one format must be validated for successful submission.",
    },
    {
        question: "Can I submit data before I publish my research?",
        answer: "Yes, you can submit data at any time. Your data remains private until you choose to publish it. This allows you to prepare your submission in advance and publish it when you're ready, such as when your research paper is accepted or published.",
    },
    {
        question: "Who owns the rights to the data I submit?",
        answer: "You retain full ownership of your data. By submitting to nmrXiv, you grant us the right to preserve, distribute, and provide access to your data according to the license you select. The license you choose determines how others can use your data.",
    },
];
</script>
