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
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <div>
                                                <jet-application-logo
                                                    class="block h-10 p-0.5 ml-1.5 w-auto"
                                                />
                                            </div>
                                            <div class="-mr-2">
                                                <PopoverButton
                                                    class="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset ring-brand"
                                                >
                                                    <span class="sr-only"
                                                        >Close menu</span
                                                    >
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
                                            <p
                                                class="mt-6 text-center text-sm font-medium text-gray-500"
                                            >
                                                <Link
                                                    v-if="
                                                        !$page.props.auth.user
                                                    "
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
                        <h1
                            class="text-4xl font-bold text-gray-900 sm:text-5xl md:text-6xl"
                        >
                            NMR Spectrum Prediction
                        </h1>
                        <p
                            class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl"
                        >
                            Fast and accurate NMR spectra predictions from
                            chemical structures
                        </p>
                    </div>

                    <!-- Prediction Tool Container -->
                    <div class="max-w-6xl mx-auto">
                        <div
                            class="bg-white shadow-xl rounded-3xl overflow-hidden border border-gray-200"
                        >
                            <div class="px-6 py-8 sm:p-10">
                                <!-- Title Section -->
                                <div class="mb-6">
                                    <h2
                                        class="text-2xl font-bold text-gray-900"
                                    >
                                        Draw or Import Structure
                                    </h2>
                                    <p class="mt-2 text-gray-600">
                                        Draw, paste, or import a chemical
                                        structure for NMR prediction
                                    </p>
                                </div>

                                <!-- Input Options -->
                                <div
                                    class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-3"
                                >
                                    <!-- File Upload -->
                                    <label
                                        class="relative flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-gray-400 hover:bg-gray-50 transition-colors"
                                        :class="
                                            isDragging
                                                ? 'border-gray-900 bg-gray-50'
                                                : ''
                                        "
                                        @dragover.prevent="isDragging = true"
                                        @dragleave.prevent="isDragging = false"
                                        @drop.prevent="handleDrop"
                                    >
                                        <svg
                                            class="w-4 h-4 mr-2"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                                            />
                                        </svg>
                                        Drop or select MOL/SDF
                                        <input
                                            ref="fileInput"
                                            type="file"
                                            accept=".mol,.sdf,.sd"
                                            class="sr-only"
                                            @change="handleFileSelect"
                                        />
                                    </label>

                                    <!-- Clipboard Paste -->
                                    <button
                                        class="flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                                        @click="pasteFromClipboard"
                                    >
                                        <svg
                                            class="w-4 h-4 mr-2"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                            />
                                        </svg>
                                        Paste from Clipboard
                                    </button>
                                </div>

                                <!-- Structure Editor Card -->
                                <div
                                    id="predictionEditor"
                                    class="w-full bg-white rounded-xl border border-gray-200 shadow-sm mb-6"
                                    style="height: 450px"
                                />

                                <!-- Prediction Type Selection -->
                                <div class="mb-8">
                                    <h3
                                        class="text-lg font-semibold text-gray-900 mb-4"
                                    >
                                        Prediction Type
                                    </h3>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-3 gap-4"
                                    >
                                        <!-- 1H NMR -->
                                        <label
                                            for="prediction-type-1h"
                                            class="relative flex items-start p-4 bg-white border-2 border-gray-200 rounded-xl cursor-pointer hover:border-gray-300 transition-all"
                                            :class="
                                                predictionType === '1h'
                                                    ? 'border-gray-900 bg-gray-50'
                                                    : ''
                                            "
                                        >
                                            <input
                                                id="prediction-type-1h"
                                                v-model="predictionType"
                                                name="prediction-type"
                                                value="1h"
                                                type="radio"
                                                class="sr-only"
                                            />
                                            <div class="flex-1">
                                                <span
                                                    class="block text-sm font-semibold text-gray-900"
                                                >
                                                    <sup>1</sup>H NMR
                                                </span>
                                                <span
                                                    class="block text-xs text-gray-500 mt-1"
                                                >
                                                    Proton NMR prediction
                                                </span>
                                            </div>
                                        </label>

                                        <!-- 13C NMR -->
                                        <label
                                            for="prediction-type-13c"
                                            class="relative flex items-start p-4 bg-white border-2 border-gray-200 rounded-xl cursor-pointer hover:border-gray-300 transition-all"
                                            :class="
                                                predictionType === '13c'
                                                    ? 'border-gray-900 bg-gray-50'
                                                    : ''
                                            "
                                        >
                                            <input
                                                id="prediction-type-13c"
                                                v-model="predictionType"
                                                name="prediction-type"
                                                value="13c"
                                                type="radio"
                                                class="sr-only"
                                            />
                                            <div class="flex-1">
                                                <span
                                                    class="block text-sm font-semibold text-gray-900"
                                                >
                                                    <sup>13</sup>C NMR
                                                </span>
                                                <span
                                                    class="block text-xs text-gray-500 mt-1"
                                                >
                                                    Carbon-13 NMR prediction
                                                </span>
                                            </div>
                                        </label>

                                        <!-- Both -->
                                        <label
                                            for="prediction-type-both"
                                            class="relative flex items-start p-4 bg-white border-2 border-gray-200 rounded-xl cursor-pointer hover:border-gray-300 transition-all"
                                            :class="
                                                predictionType === 'both'
                                                    ? 'border-gray-900 bg-gray-50'
                                                    : ''
                                            "
                                        >
                                            <input
                                                id="prediction-type-both"
                                                v-model="predictionType"
                                                name="prediction-type"
                                                value="both"
                                                type="radio"
                                                class="sr-only"
                                            />
                                            <div class="flex-1">
                                                <span
                                                    class="block text-sm font-semibold text-gray-900"
                                                >
                                                    Both
                                                </span>
                                                <span
                                                    class="block text-xs text-gray-500 mt-1"
                                                >
                                                    <sup>1</sup>H and
                                                    <sup>13</sup>C NMR
                                                </span>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Predict Button -->
                                <div class="flex justify-center">
                                    <button
                                        type="button"
                                        class="inline-flex items-center px-8 py-3 text-sm font-semibold text-white bg-gray-900 rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 shadow-sm transition-colors"
                                        @click="predictSpectrum"
                                    >
                                        Predict Spectrum
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Results Section (Placeholder) -->
                        <!-- <div class="mt-8 bg-gray-50 rounded-3xl border border-gray-200 p-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No predictions yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Draw a chemical structure and click predict to see results</p>
                        </div> -->
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
import {
    Popover,
    PopoverButton,
    PopoverGroup,
    PopoverPanel,
} from "@headlessui/vue";
import { Bars3Icon, XMarkIcon } from "@heroicons/vue/24/outline";
import JetApplicationLogo from "@/Jetstream/ApplicationLogo.vue";
import FlashMessages from "@/Shared/FlashMessages.vue";
import Footer from "@/Shared/Footer.vue";
import OCL from "openchemlib";
import { createStructureEditor } from "@/Utils/structureEditor";

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
    data() {
        return {
            editor: null,
            predictionType: "1h",
            isDragging: false,
        };
    },
    mounted() {
        this.$nextTick(() => {
            this.editor = createStructureEditor("predictionEditor");
        });
    },
    methods: {
        async handleFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;
            await this.loadFile(file);
        },
        async handleDrop(event) {
            this.isDragging = false;
            const file = event.dataTransfer.files[0];
            if (!file) return;
            await this.loadFile(file);
        },
        async loadFile(file) {
            if (!this.editor) return;

            const validExtensions = [".mol", ".sdf", ".sd"];
            const fileName = file.name.toLowerCase();
            const isValid = validExtensions.some((ext) =>
                fileName.endsWith(ext)
            );

            if (!isValid) {
                alert("Please upload a MOL or SDF file");
                return;
            }

            try {
                const text = await file.text();
                this.editor.setMolFile(text);
            } catch (error) {
                console.error("Error loading file:", error);
                alert("Error loading file");
            }
        },
        async pasteFromClipboard() {
            if (!this.editor) return;

            try {
                const text = await navigator.clipboard.readText();

                // Try as SMILES first
                try {
                    const mol = OCL.Molecule.fromSmiles(text.trim());
                    this.editor.setMolFile(mol.toMolfile());
                    return;
                } catch {
                    // If not SMILES, try as MOL file
                    if (text.includes("M  END") || text.includes("$$$$")) {
                        this.editor.setMolFile(text);
                    } else {
                        alert(
                            "Clipboard content is not a valid SMILES or MOL format"
                        );
                    }
                }
            } catch (error) {
                console.error("Error reading clipboard:", error);
                alert(
                    "Unable to read clipboard. Please allow clipboard access."
                );
            }
        },
        predictSpectrum() {
            if (!this.editor) {
                alert("Structure editor not initialized");
                return;
            }

            try {
                const smiles = this.editor.getSmiles();
                if (!smiles || smiles.trim() === "") {
                    alert("Please draw or import a chemical structure first");
                    return;
                }

                // TODO: Implement actual prediction API call
                console.log(
                    "Predicting spectrum for:",
                    smiles,
                    "Type:",
                    this.predictionType
                );
                alert(
                    `Prediction requested for:\nSMILES: ${smiles}\nType: ${this.predictionType}\n\nAPI integration coming soon!`
                );
            } catch (error) {
                console.error("Error getting structure:", error);
                alert("Error reading structure from editor");
            }
        },
    },
};
</script>
