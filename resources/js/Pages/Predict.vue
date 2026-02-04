<template>
    <div class="bg-white">
        <Head title="NMR Prediction - nmrXiv"></Head>
        <FlashMessages />
        <main>
            <div class="relative">
                <header class="relative">
                    <Popover class="relative">
                        <div
                            class="flex justify-between items-center mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8"
                        >
                            <div class="flex justify-start items-center gap-10">
                                <Link :href="'/'" class="flex-shrink-0">
                                    <jet-application-logo
                                        class="block h-9 w-auto"
                                    />
                                </Link>
                                
                                <!-- Desktop Navigation -->
                                <PopoverGroup
                                    as="nav"
                                    class="hidden md:flex items-center gap-8"
                                >
                                    <Link
                                        href="/projects"
                                        class="text-sm font-medium text-gray-900 hover:text-gray-600 transition-colors"
                                    >
                                        Projects
                                    </Link>
                                    <Link
                                        href="/spectra"
                                        class="text-sm font-medium text-gray-900 hover:text-gray-600 transition-colors"
                                    >
                                        Spectra
                                    </Link>
                                    <Link
                                        href="/compounds"
                                        class="text-sm font-medium text-gray-900 hover:text-gray-600 transition-colors"
                                    >
                                        Compounds
                                    </Link>
                                    <Link
                                        href="/about-us"
                                        class="text-sm font-medium text-gray-900 hover:text-gray-600 transition-colors"
                                    >
                                        About
                                    </Link>
                                </PopoverGroup>
                            </div>
                            
                            <!-- Mobile menu button -->
                            <div class="md:hidden">
                                <PopoverButton
                                    class="rounded-lg p-2 inline-flex items-center justify-center text-gray-900 hover:text-gray-600 hover:bg-gray-50 focus:outline-none focus:ring-2 ring-brand transition-colors"
                                >
                                    <span class="sr-only">Open menu</span>
                                    <Bars3Icon
                                        class="h-6 w-6"
                                        aria-hidden="true"
                                    />
                                </PopoverButton>
                            </div>
                            
                            <!-- Auth Buttons -->
                            <div
                                v-if="
                                    $page.props.auth.user &&
                                    $page.props.auth.user?.first_name != null
                                "
                                class="hidden md:flex items-center"
                            >
                                <Link
                                    href="/dashboard"
                                    class="whitespace-nowrap inline-flex items-center justify-center px-5 py-2.5 border border-transparent rounded-full text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 transition-colors shadow-sm"
                                >
                                    Dashboard
                                </Link>
                            </div>
                            <div
                                v-else
                                class="hidden md:flex items-center gap-4"
                            >
                                <Link
                                    href="/login"
                                    class="whitespace-nowrap text-sm font-medium text-gray-900 hover:text-gray-600 transition-colors"
                                >
                                    Login
                                </Link>
                                <Link
                                    href="/register"
                                    class="whitespace-nowrap inline-flex items-center justify-center px-5 py-2.5 border border-transparent rounded-full text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 transition-colors shadow-sm"
                                >
                                    Register
                                </Link>
                            </div>
                        </div>

                        <transition
                            enter-active-class="duration-200 ease-out"
                            enter-from-class="opacity-0 scale-95"
                            enter-to-class="opacity-100 scale-100"
                            leave-active-class="duration-100 ease-in"
                            leave-from-class="opacity-100 scale-100"
                            leave-to-class="opacity-0 scale-95"
                        >
                            <PopoverPanel
                                focus
                                class="absolute z-30 top-0 inset-x-0 p-2 transition transform origin-top-right md:hidden"
                            >
                                <div
                                    class="rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 bg-white divide-y-2 divide-gray-50"
                                >
                                    <div class="pt-5 pb-6 px-5">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <jet-application-logo
                                                    class="block h-10 p-0.5 ml-1.5 w-auto"
                                                />
                                            </div>
                                            <div class="-mr-2">
                                                <PopoverButton
                                                    class="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset ring-brand"
                                                >
                                                    <span class="sr-only">Close menu</span>
                                                    <XMarkIcon
                                                        class="h-6 w-6"
                                                        aria-hidden="true"
                                                    />
                                                </PopoverButton>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="py-6 px-5">
                                        <div class="grid grid-cols-2 gap-4">
                                            <Link
                                                href="/projects"
                                                class="text-sm font-medium text-gray-900 hover:text-gray-700"
                                            >
                                                Projects
                                            </Link>
                                            <Link
                                                href="/spectra"
                                                class="text-sm font-medium text-gray-900 hover:text-gray-700"
                                            >
                                                Spectra
                                            </Link>
                                            <Link
                                                href="/compounds"
                                                class="text-sm font-medium text-gray-900 hover:text-gray-700"
                                            >
                                                Compounds
                                            </Link>
                                            <Link
                                                href="/about-us"
                                                class="text-sm font-medium text-gray-900 hover:text-gray-700"
                                            >
                                                About
                                            </Link>
                                        </div>
                                        <div class="mt-6">
                                            <Link
                                                v-if="!$page.props.auth.user"
                                                href="/register"
                                                class="w-full flex items-center justify-center bg-gray-900 px-4 py-2 border border-transparent rounded-full shadow-sm text-sm font-medium text-white hover:bg-gray-800"
                                            >
                                                Register
                                            </Link>
                                            <Link
                                                v-else
                                                href="/dashboard"
                                                class="w-full flex items-center justify-center bg-gray-900 px-4 py-2 border border-transparent rounded-full shadow-sm text-sm font-medium text-white hover:bg-gray-800"
                                            >
                                                Dashboard
                                            </Link>
                                            <p class="mt-6 text-center text-sm font-medium text-gray-500">
                                                <Link
                                                    v-if="!$page.props.auth.user"
                                                    href="/login"
                                                    class="text-gray-900"
                                                >
                                                    Login
                                                </Link>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </PopoverPanel>
                        </transition>
                    </Popover>
                </header>

                <!-- Main Content -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    <!-- Hero Section -->
                    <div class="text-center mb-12">
                        <h1 class="text-4xl font-bold text-gray-900 sm:text-5xl md:text-6xl">
                            NMR Spectrum Prediction
                        </h1>
                        <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                            Fast and accurate NMR spectra predictions from chemical structures
                        </p>
                    </div>

                    <!-- Prediction Tool Container -->
                    <div class="max-w-4xl mx-auto">
                        <div class="bg-white shadow-xl rounded-3xl overflow-hidden border border-gray-200">
                            <div class="px-6 py-8 sm:p-10">
                                <!-- Input Section -->
                                <div class="mb-8">
                                    <label for="structure-input" class="block text-sm font-medium text-gray-700 mb-2">
                                        Enter Chemical Structure (SMILES, InChI, or Molecular Formula)
                                    </label>
                                    <textarea
                                        id="structure-input"
                                        rows="4"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-lg"
                                        placeholder="e.g., CCO (Ethanol) or InChI=1S/C2H6O/c1-2-3/h3H,2H2,1H3"
                                    ></textarea>
                                </div>

                                <!-- Prediction Type Selection -->
                                <div class="mb-8">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Prediction Type
                                    </label>
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                        <div class="relative flex items-start">
                                            <div class="flex items-center h-5">
                                                <input
                                                    id="nmr-1h"
                                                    name="prediction-type"
                                                    type="radio"
                                                    checked
                                                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300"
                                                />
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="nmr-1h" class="font-medium text-gray-700">
                                                    <sup>1</sup>H NMR
                                                </label>
                                            </div>
                                        </div>
                                        <div class="relative flex items-start">
                                            <div class="flex items-center h-5">
                                                <input
                                                    id="nmr-13c"
                                                    name="prediction-type"
                                                    type="radio"
                                                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300"
                                                />
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="nmr-13c" class="font-medium text-gray-700">
                                                    <sup>13</sup>C NMR
                                                </label>
                                            </div>
                                        </div>
                                        <div class="relative flex items-start">
                                            <div class="flex items-center h-5">
                                                <input
                                                    id="nmr-both"
                                                    name="prediction-type"
                                                    type="radio"
                                                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300"
                                                />
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="nmr-both" class="font-medium text-gray-700">
                                                    Both
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Predict Button -->
                                <div class="flex justify-center">
                                    <button
                                        type="button"
                                        class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-full shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                    >
                                        <svg class="-ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd" />
                                        </svg>
                                        Predict Spectrum
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Results Section (Placeholder) -->
                        <div class="mt-8 bg-gray-50 rounded-3xl border border-gray-200 p-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No predictions yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Enter a chemical structure and click predict to see results</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <Footer />
    </div>
</template>

<script>
import { Head, Link } from "@inertiajs/vue3";
import { Popover, PopoverButton, PopoverGroup, PopoverPanel } from "@headlessui/vue";
import { Bars3Icon, XMarkIcon } from "@heroicons/vue/24/outline";
import JetApplicationLogo from "@/Jetstream/ApplicationLogo.vue";
import FlashMessages from "@/Shared/FlashMessages.vue";
import Footer from "@/Shared/Footer.vue";

export default {
    components: {
        Head,
        Link,
        Popover,
        PopoverButton,
        PopoverGroup,
        PopoverPanel,
        Bars3Icon,
        XMarkIcon,
        JetApplicationLogo,
        FlashMessages,
        Footer,
    },
};
</script>
