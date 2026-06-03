<template>
    <div class="bg-white">
        <Head title="About - nmrXiv"></Head>
        <FlashMessages />
        <main>
            <!-- Header and Hero with Animated Gradient -->
            <div class="relative overflow-hidden">
                <!-- Animated mesh gradient background -->
                <div
                    class="absolute inset-0 bg-gradient-to-br from-blue-50/30 via-indigo-50/30 to-purple-50/30"
                ></div>
                <div class="absolute inset-0 opacity-20">
                    <div
                        class="absolute top-0 left-1/4 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl animate-blob"
                    ></div>
                    <div
                        class="absolute top-0 right-1/4 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-2000"
                    ></div>
                    <div
                        class="absolute -bottom-32 left-1/3 w-96 h-96 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-4000"
                    ></div>
                </div>

                <!-- Gradient fade to white at bottom -->
                <div
                    class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-b from-transparent to-white"
                ></div>

                <PublicSiteHeader variant="hero">
                    <template #mobile-extra>
                        <nav class="grid grid-cols-1 gap-4">
                            <Link
                                v-for="item in Search"
                                :key="item.name"
                                :href="item.href"
                                class="flex items-center gap-3 rounded-lg p-3 hover:bg-gray-50 transition-colors"
                            >
                                <div
                                    class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-gradient-to-r from-indigo-600 to-primary-500 text-white"
                                >
                                    <component
                                        :is="item.icon"
                                        class="size-5"
                                        aria-hidden="true"
                                    />
                                </div>
                                <span
                                    class="text-[0.9375rem] font-medium text-gray-900"
                                >
                                    {{ item.name }}
                                </span>
                            </Link>
                        </nav>
                    </template>
                </PublicSiteHeader>
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="relative py-20 sm:py-24 lg:py-32">
                        <div class="overflow-hidden">
                            <div class="pb-8">
                                <div
                                    class="mx-auto max-w-2xl gap-x-14 lg:mx-0 lg:flex lg:max-w-none lg:items-center"
                                >
                                    <div
                                        class="w-full max-w-xl lg:shrink-0 xl:max-w-2xl"
                                    >
                                        <h1
                                            class="text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl"
                                        >
                                            We’re changing the way chemists
                                            publish NMR data.
                                        </h1>
                                        <p
                                            class="relative mt-6 text-lg leading-7 text-gray-600 sm:max-w-md lg:max-w-none"
                                        >
                                            While several attempts have been
                                            made recently to create NMR
                                            databases from literature, they fall
                                            short of meeting the needs of modern
                                            chemical and related scientific
                                            communities. nmrXiv is the first
                                            open archive to preserve NMR data in
                                            its original instrument format,
                                            while offering tools for their
                                            analysis and promoting open data and
                                            standards for long-term
                                            sustainability and accessibility.
                                            <br /><br />
                                            nmrXiv revolutionizes NMR data
                                            management and archival by providing
                                            an open-access, standardized, and
                                            user-friendly platform. Our platform
                                            is public and interoperable to
                                            ensure that NMR data is freely
                                            accessible, transparent, and
                                            reproducible. We support
                                            standardized data formats to enhance
                                            consistency and facilitate data
                                            comparison and integration. Our
                                            platform includes processing and
                                            quality control tools to verify the
                                            integrity and accuracy of NMR data
                                            before and after publication. With
                                            advanced search and retrieval
                                            features powered by comprehensive
                                            metadata, nmrXiv allows researchers
                                            to efficiently locate and utilize
                                            specific datasets, fostering greater
                                            collaboration and accelerating
                                            scientific discovery. nmrXiv serves
                                            as core platform for the nmrXiv
                                            Project Group, an international
                                            collaborative that seeks to advance
                                            the utility of structural and
                                            quantitative NMR analysis for
                                            chemical, pharmaceutical, and
                                            natural research and applications.
                                        </p>
                                    </div>
                                    <div
                                        class="mt-14 flex justify-end gap-8 sm:-mt-44 sm:justify-start sm:pl-20 lg:mt-0 lg:pl-0"
                                    >
                                        <div
                                            class="ml-auto w-44 flex-none space-y-8 pt-32 sm:ml-0 sm:pt-80 lg:order-last lg:pt-36 xl:order-none xl:pt-80"
                                        >
                                            <div class="relative">
                                                <img
                                                    src="img/about1.jpeg"
                                                    alt=""
                                                    class="aspect-[2/3] w-full rounded-xl bg-gray-900/5 object-cover shadow-lg"
                                                />
                                                <div
                                                    class="pointer-events-none absolute inset-0 rounded-xl ring-1 ring-inset ring-gray-900/10"
                                                ></div>
                                            </div>
                                        </div>
                                        <div
                                            class="mr-auto w-44 flex-none space-y-8 sm:mr-0 sm:pt-52 lg:pt-36"
                                        >
                                            <div class="relative">
                                                <img
                                                    src="img/about5.jpeg"
                                                    alt=""
                                                    class="aspect-[2/3] w-full rounded-xl bg-gray-900/5 object-cover shadow-lg"
                                                />
                                                <div
                                                    class="pointer-events-none absolute inset-0 rounded-xl ring-1 ring-inset ring-gray-900/10"
                                                ></div>
                                            </div>
                                            <div class="relative">
                                                <img
                                                    src="img/about3.jpeg"
                                                    alt=""
                                                    class="aspect-[2/3] w-full rounded-xl bg-gray-900/5 object-cover shadow-lg"
                                                />
                                                <div
                                                    class="pointer-events-none absolute inset-0 rounded-xl ring-1 ring-inset ring-gray-900/10"
                                                ></div>
                                            </div>
                                        </div>
                                        <div
                                            class="w-44 flex-none space-y-8 pt-32 sm:pt-0"
                                        >
                                            <div class="relative">
                                                <img
                                                    src="img/about4.jpeg"
                                                    alt=""
                                                    class="aspect-[2/3] w-full rounded-xl bg-gray-900/5 object-cover shadow-lg"
                                                />
                                                <div
                                                    class="pointer-events-none absolute inset-0 rounded-xl ring-1 ring-inset ring-gray-900/10"
                                                ></div>
                                            </div>
                                            <div class="relative">
                                                <img
                                                    src="img/about2.jpeg"
                                                    alt=""
                                                    class="aspect-[2/3] w-full rounded-xl bg-gray-900/5 object-cover shadow-lg"
                                                />
                                                <div
                                                    class="pointer-events-none absolute inset-0 rounded-xl ring-1 ring-inset ring-gray-900/10"
                                                ></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Our Mission Section (on white background) -->
            <div class="relative bg-white z-10">
                <div
                    class="mx-auto -mt-12 max-w-7xl px-4 sm:mt-0 sm:px-6 lg:px-8 xl:-mt-8"
                >
                    <div class="mx-auto max-w-2xl lg:mx-0 lg:max-w-none">
                        <h2
                            class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl"
                        >
                            Our mission
                        </h2>
                        <div
                            class="mt-6 flex flex-col gap-x-8 gap-y-20 lg:flex-row"
                        >
                            <div class="lg:w-full lg:max-w-2xl lg:flex-auto">
                                <p class="text-xl leading-8 text-gray-600">
                                    <b>All chemists publish FAIR NMR data.</b>
                                    To get there, we will support chemists and
                                    scientists from related disciplines in their
                                    efforts to collect, store, process, analyse,
                                    publish, and re-use NMR data.<br />
                                </p>
                                <p
                                    class="italic text-md leading-7 text-gray-600 mt-4"
                                >
                                    Developed at
                                    <a
                                        class="text-blue-900"
                                        href="https://www.uni-jena.de/en"
                                        >Friedrich Schiller University, Jena</a
                                    >
                                    in Germany, under the leadership of
                                    <a
                                        class="text-blue-900"
                                        href="https://cheminf.uni-jena.de/"
                                        >Professor Christoph Steinbeck</a
                                    >. Funded by the
                                    <a
                                        class="text-blue-900 underline"
                                        href="https://www.dfg.de/"
                                        >Deutsche Forschungsgemeinschaft (DFG,
                                        German Research Foundation)</a
                                    >
                                    under the
                                    <a
                                        class="text-blue-900 underline"
                                        href="https://nfdi4chem.de/"
                                        >National Research Data Infrastructure –
                                        NFDI4Chem</a
                                    >
                                    – Projektnummer
                                    <strong class="text-blue-900"
                                        >441958208</strong
                                    >.
                                </p>
                            </div>
                            <div class="lg:flex lg:flex-auto lg:justify-center">
                                <dl class="w-64 space-y-8 xl:w-80 -mt-10">
                                    <div class="flex flex-col-reverse gap-y-4">
                                        <dt
                                            class="text-base leading-7 text-gray-600"
                                        >
                                            Projects published so far
                                        </dt>
                                        <dd
                                            class="text-5xl font-semibold tracking-tight text-gray-900"
                                        >
                                            {{ projects }}
                                        </dd>
                                    </div>
                                    <div class="flex flex-col-reverse gap-y-4">
                                        <dt
                                            class="text-base leading-7 text-gray-600"
                                        >
                                            Compounds experimental spectra
                                        </dt>
                                        <dd
                                            class="text-5xl font-semibold tracking-tight text-gray-900"
                                        >
                                            {{ compounds }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAIR Features Section -->
                <div class="bg-gray-50 mt-32 sm:mt-40">
                    <div class="py-20 sm:py-24">
                        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                            <div class="flex items-end justify-between mb-12">
                                <div class="max-w-3xl">
                                    <h2
                                        class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight"
                                    >
                                        Built to be FAIR from ground up
                                    </h2>
                                </div>
                                <a
                                    href="https://docs.nmrxiv.org"
                                    target="_blank"
                                    class="hidden sm:inline-flex items-center gap-1 text-brand font-medium text-brand-hover transition-colors text-sm"
                                >
                                    Learn more
                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="m8.25 4.5 7.5 7.5-7.5 7.5"
                                        />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <div class="relative">
                            <div
                                ref="scrollContainer"
                                class="overflow-x-auto scrollbar-hide px-4 sm:px-6 lg:px-8"
                            >
                                <div
                                    class="flex gap-3 pb-4"
                                    style="width: max-content"
                                >
                                    <div
                                        v-for="(feature, index) in features"
                                        :key="feature.name"
                                        class="relative bg-white rounded-3xl p-8 border border-gray-200 transition-all duration-300 flex-shrink-0 w-[320px] h-[320px] flex flex-col"
                                    >
                                        <div class="flex-1 flex flex-col">
                                            <span
                                                class="flex items-center justify-center h-12 w-12"
                                            >
                                                <component
                                                    :is="feature.icon"
                                                    class="h-12 w-12 text-gray-900"
                                                    aria-hidden="true"
                                                />
                                            </span>
                                            <div class="mt-6 flex-1">
                                                <h3
                                                    class="text-2xl font-bold text-gray-900 leading-tight mb-3"
                                                >
                                                    {{ feature.name }}
                                                </h3>
                                                <p
                                                    v-if="
                                                        feature.shortDescription
                                                    "
                                                    class="text-base text-gray-600 leading-relaxed"
                                                >
                                                    {{
                                                        feature.shortDescription
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex justify-end">
                                            <button
                                                class="flex items-center justify-center h-11 w-11 rounded-3xl bg-gray-900 text-white hover:bg-gray-700 transition-colors flex-shrink-0"
                                                :aria-label="`Learn more about ${feature.name}`"
                                                @click="openFeatureModal(index)"
                                            >
                                                <svg
                                                    class="h-5 w-5"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="2.5"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M12 4.5v15m7.5-7.5h-15"
                                                    />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                            <div class="flex justify-end gap-2 mt-6">
                                <button
                                    class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    :disabled="!canScrollLeft"
                                    @click="scrollLeft"
                                >
                                    <svg
                                        class="h-5 w-5 text-gray-900"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M15.75 19.5 8.25 12l7.5-7.5"
                                        />
                                    </svg>
                                </button>
                                <button
                                    class="h-10 w-10 rounded-full bg-gray-900 flex items-center justify-center hover:bg-gray-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    :disabled="!canScrollRight"
                                    @click="scrollRight"
                                >
                                    <svg
                                        class="h-5 w-5 text-white"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="m8.25 4.5 7.5 7.5-7.5 7.5"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feature Modal -->
                <TransitionRoot appear :show="isFeatureModalOpen" as="template">
                    <Dialog
                        as="div"
                        class="relative z-50"
                        @close="closeFeatureModal"
                    >
                        <TransitionChild
                            as="template"
                            enter="duration-300 ease-out"
                            enter-from="opacity-0"
                            enter-to="opacity-100"
                            leave="duration-200 ease-in"
                            leave-from="opacity-100"
                            leave-to="opacity-0"
                        >
                            <div
                                class="fixed inset-0 bg-black/50 backdrop-blur-sm"
                            />
                        </TransitionChild>

                        <div class="fixed inset-0 overflow-y-auto">
                            <div
                                class="flex min-h-full items-center justify-center p-4"
                            >
                                <TransitionChild
                                    as="template"
                                    enter="duration-300 ease-out"
                                    enter-from="opacity-0 scale-95"
                                    enter-to="opacity-100 scale-100"
                                    leave="duration-200 ease-in"
                                    leave-from="opacity-100 scale-100"
                                    leave-to="opacity-0 scale-95"
                                >
                                    <DialogPanel
                                        class="w-full max-w-2xl transform overflow-hidden rounded-3xl bg-white p-8 sm:p-12 text-left align-middle shadow-2xl transition-all"
                                    >
                                        <div class="absolute right-6 top-6">
                                            <button
                                                class="flex items-center justify-center h-10 w-10 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors"
                                                @click="closeFeatureModal"
                                            >
                                                <XMarkIcon
                                                    class="h-5 w-5 text-gray-900"
                                                />
                                            </button>
                                        </div>

                                        <div v-if="selectedFeature">
                                            <p
                                                class="text-sm font-medium text-gray-600 mb-2"
                                            >
                                                {{
                                                    selectedFeature.category ||
                                                    "Feature"
                                                }}
                                            </p>
                                            <DialogTitle
                                                as="h2"
                                                class="text-3xl sm:text-4xl font-bold text-gray-900 leading-tight"
                                            >
                                                {{
                                                    selectedFeature.modalTitle ||
                                                    selectedFeature.name
                                                }}
                                            </DialogTitle>
                                            <div
                                                class="mt-6 text-base text-gray-700 space-y-4"
                                            >
                                                <p>
                                                    {{
                                                        selectedFeature.modalDescription
                                                    }}
                                                </p>
                                            </div>

                                            <div
                                                v-if="
                                                    selectedFeature.learnMoreUrl
                                                "
                                                class="mt-8"
                                            >
                                                <a
                                                    :href="
                                                        selectedFeature.learnMoreUrl
                                                    "
                                                    target="_blank"
                                                    class="inline-flex items-center gap-2 text-brand font-medium text-brand-hover transition-colors"
                                                >
                                                    Learn more
                                                    <ArrowRightIcon
                                                        class="h-4 w-4"
                                                    />
                                                </a>
                                            </div>
                                        </div>
                                    </DialogPanel>
                                </TransitionChild>
                            </div>
                        </div>
                    </Dialog>
                </TransitionRoot>

                <!-- Image section -->
                <div
                    class="mx-auto mt-32 max-w-7xl px-4 sm:mt-40 sm:px-6 lg:px-8"
                >
                    <img
                        src="img/journals.png"
                        class="w-full object-cover xl:rounded-xl border shadow-lg"
                    />
                </div>

                <div
                    class="mx-auto mt-32 max-w-7xl px-4 sm:mt-40 sm:px-6 lg:px-8"
                >
                    <dl
                        class="mt-16 grid grid-cols-1 gap-x-8 gap-y-16 text-base leading-7 sm:grid-cols-2 lg:grid-cols-3"
                    >
                        <div>
                            <dt class="font-semibold text-gray-900">License</dt>
                            <dd class="mt-1 text-gray-600">
                                nmrXiv infrastucture
                                <a
                                    class="text-blue-900 underline"
                                    href="https://github.com/NFDI4Chem/nmrxiv"
                                    >code</a
                                >
                                is licensed under the
                                <a
                                    class="text-blue-900 underline"
                                    href="https://opensource.org/licenses/MIT"
                                    >MIT license</a
                                >. Every dataset on nmrXiv comes with its own
                                specific license. It is essential to review the
                                license details for each dataset before using
                                it.
                            </dd>
                            <br />
                            <dt class="font-semibold text-gray-900">
                                Help Desk
                            </dt>
                            <dd class="mt-1 text-gray-600">
                                Any issues or support requests can be raised at
                                our
                                <a
                                    class="text-blue-900 underline"
                                    href="https://helpdesk.nfdi4chem.de/"
                                    >Help Desk</a
                                >
                                or write to us at
                                <a
                                    class="text-blue-900 underline"
                                    href="mailto:info.nmrxiv@uni-jena.de"
                                    >info.nmrxiv@uni-jena.de</a
                                >.
                            </dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-900">
                                Contributors and Steering Committee
                            </dt>
                            <dd class="mt-1 text-gray-600">
                                At nmrXiv, our global contributors bring a
                                wealth of diverse expertise to our platform.
                                Guided by a seasoned Steering Committee, we
                                ensure strategic oversight, foster innovation,
                                and drive continuous improvement in all our
                                endeavors.
                                <br /><a
                                    class="text-blue-900 underline"
                                    href="https://docs.nmrxiv.org/contribution/contributors.html"
                                    >View →</a
                                >
                            </dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-900">
                                Documentation
                            </dt>
                            <dd class="mt-1 text-gray-600">
                                Our documentation-first approach enables our
                                developers and chemists to collaborate on
                                proposed features, ensuring implementations
                                accurately address real-world use cases.
                                <br /><a
                                    class="text-blue-900 underline"
                                    href="https://docs.nmrxiv.org/introduction/intro.html"
                                    >More details →</a
                                >
                            </dd>
                        </div>
                    </dl>
                </div>

                <div
                    class="mx-auto mt-32 max-w-7xl px-4 sm:mt-48 sm:px-6 lg:px-8"
                >
                    <h2
                        class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl"
                    >
                        Steering Committee
                    </h2>
                    <ul
                        role="list"
                        class="mt-20 grid grid-cols-2 gap-x-8 gap-y-16 text-center sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6"
                    >
                        <li class="flex flex-col justify-center items-center">
                            <div
                                class="bg-gray-200 h-24 w-24 overflow-hidden rounded-full flex justify-center items-center"
                            >
                                <img
                                    class="mx-auto h-24 w-24 object-cover"
                                    src="/img/gpauli.jpg"
                                    alt="Guido Pauli"
                                    @error="handleImageError"
                                />
                            </div>
                            <h3
                                class="mt-6 text-base font-semibold leading-7 tracking-tight text-gray-900"
                            >
                                <a
                                    href="https://pharmacy.uic.edu/profiles/gfp/"
                                    target="_blank"
                                    >Guido Pauli</a
                                >
                            </h3>
                        </li>
                        <li class="flex flex-col justify-center items-center">
                            <div
                                class="bg-gray-200 h-24 w-24 overflow-hidden rounded-full flex justify-center items-center"
                            >
                                <img
                                    class="h-24 w-24 object-cover"
                                    src="/img/nschloerer.jpg"
                                    alt="Nils Schlörer"
                                    @error="handleImageError"
                                />
                            </div>
                            <h3
                                class="mt-6 text-base font-semibold leading-7 tracking-tight text-gray-900"
                            >
                                <a
                                    href="https://friedolin.uni-jena.de/qisserver/rds?state=verpublish&status=init&vmfile=no&moduleCall=webInfo&publishConfFile=webInfoPerson&publishSubDir=personal&keep=y&personal.pid=16242"
                                    target="_blank"
                                    >Nils Schlörer</a
                                >
                            </h3>
                        </li>
                        <li class="flex flex-col justify-center items-center">
                            <div
                                class="bg-gray-200 h-24 w-24 overflow-hidden rounded-full flex justify-center items-center"
                            >
                                <img
                                    class="mx-auto h-24 w-24 object-cover"
                                    src="/img/jw.jpeg"
                                    alt="Julien Wist"
                                    @error="handleImageError"
                                />
                            </div>
                            <h3
                                class="mt-6 text-base font-semibold leading-7 tracking-tight text-gray-900"
                            >
                                <a
                                    href="https://researchportal.murdoch.edu.au/esploro/profile/julien_wist/overview"
                                    target="_blank"
                                    >Julien Wist</a
                                >
                            </h3>
                        </li>
                        <li class="flex flex-col justify-center items-center">
                            <div
                                class="bg-gray-200 h-24 w-24 overflow-hidden rounded-full flex justify-center items-center"
                            >
                                <img
                                    class="mx-auto h-24 w-24 object-cover"
                                    src="/img/lp.jpg"
                                    alt="Luc Patiny"
                                    @error="handleImageError"
                                />
                            </div>
                            <h3
                                class="mt-6 text-base font-semibold leading-7 tracking-tight text-gray-900"
                            >
                                <a
                                    href="https://www.linkedin.com/in/lpatiny/"
                                    target="_blank"
                                    >Luc Patiny</a
                                >
                            </h3>
                        </li>
                        <li class="flex flex-col justify-center items-center">
                            <div
                                class="bg-gray-200 h-24 w-24 overflow-hidden rounded-full flex justify-center items-center"
                            >
                                <img
                                    class="h-24 w-24 object-cover"
                                    src="/img/skuhn.jpg"
                                    alt="Stephan Kuhn"
                                    @error="handleImageError"
                                />
                            </div>
                            <h3
                                class="mt-6 text-base font-semibold leading-7 tracking-tight text-gray-900"
                            >
                                <a
                                    href="https://www.linkedin.com/in/stefan-kuhn-756bb74/?originalSubdomain=ee"
                                    target="_blank"
                                    >Stephan Kuhn</a
                                >
                            </h3>
                        </li>
                        <li class="flex flex-col justify-center items-center">
                            <div
                                class="bg-gray-200 h-24 w-24 overflow-hidden rounded-full flex justify-center items-center"
                            >
                                <img
                                    class="mx-auto h-24 w-24 object-cover"
                                    src="/img/jl.jpeg"
                                    alt="Johannes Liermann"
                                    @error="handleImageError"
                                />
                            </div>
                            <h3
                                class="mt-6 text-base font-semibold leading-7 tracking-tight text-gray-900"
                            >
                                <a
                                    href="https://personen.uni-mainz.de/public/person/1737"
                                    target="_blank"
                                    >Johannes&nbsp;Liermann</a
                                >
                            </h3>
                        </li>
                    </ul>
                </div>
                <!-- Academic and public/private partners Section -->
                <div class="mt-32 sm:mt-48 border-t border-gray-100">
                    <div class="py-20">
                        <div
                            class="mx-auto mb-12 max-w-7xl px-4 sm:px-6 lg:px-8"
                        >
                            <div class="max-w-3xl">
                                <h2
                                    class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight mb-4"
                                >
                                    Academic & Other Partners
                                </h2>
                                <p
                                    class="text-lg text-gray-600 leading-relaxed"
                                >
                                    Our initiative consists of academic and
                                    public/private partners that contribute
                                    unique expertise and resources towards our
                                    joint goal
                                </p>
                            </div>
                        </div>

                        <div class="relative">
                            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                                <div
                                    id="partners-carousel"
                                    class="flex gap-5 overflow-x-auto snap-x snap-mandatory pb-4 scroll-smooth"
                                    style="
                                        scrollbar-width: none;
                                        -ms-overflow-style: none;
                                        -webkit-overflow-scrolling: touch;
                                    "
                                >
                                    <div
                                        class="flex-none w-[320px] sm:w-[350px] snap-start"
                                    >
                                        <a
                                            href="https://nmrxiv.org"
                                            target="_blank"
                                            class="group relative bg-white rounded-[28px] overflow-hidden transition-transform duration-300 aspect-[3/4] block border border-gray-200"
                                        >
                                            <div
                                                class="relative h-full flex flex-col p-8"
                                            >
                                                <div>
                                                    <p
                                                        class="text-xs font-medium text-gray-500 mb-2"
                                                    >
                                                        NFDI4Chem, Friedrich
                                                        Schiller University Jena
                                                    </p>
                                                    <h3
                                                        class="text-2xl font-semibold text-gray-900 leading-tight mb-2"
                                                    >
                                                        Share FAIRly
                                                    </h3>
                                                    <p
                                                        class="text-sm text-gray-600"
                                                    >
                                                        Research data
                                                        infrastructure for
                                                        chemistry, enabling FAIR
                                                        sharing of raw and
                                                        processed NMR data
                                                        worldwide through
                                                        nmrXiv.org
                                                    </p>
                                                </div>
                                                <div class="mt-auto pt-8">
                                                    <div
                                                        class="flex flex-col gap-4 w-full max-w-[180px]"
                                                    >
                                                        <div class="w-full">
                                                            <img
                                                                class="w-full h-auto object-contain"
                                                                src="/img/nfdi4chem-logo.png"
                                                                alt="NFDI4Chem"
                                                            />
                                                        </div>
                                                        <div class="w-full">
                                                            <img
                                                                class="w-full h-auto object-contain"
                                                                src="/img/FSU-Jena-logo.jpg"
                                                                alt="Friedrich Schiller University Jena"
                                                            />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>

                                    <div
                                        class="flex-none w-[320px] sm:w-[350px] snap-start"
                                    >
                                        <a
                                            href="https://ctb.nmrsolutions.fi/login?returnUrl=~dashboard"
                                            target="_blank"
                                            class="group relative bg-white rounded-[28px] overflow-hidden transition-transform duration-300 aspect-[3/4] block border border-gray-200"
                                        >
                                            <div
                                                class="relative h-full flex flex-col p-8"
                                            >
                                                <div>
                                                    <p
                                                        class="text-xs font-medium text-gray-500 mb-2"
                                                    >
                                                        CT.nmrsolutions.io
                                                    </p>
                                                    <h3
                                                        class="text-2xl font-semibold text-gray-900 leading-tight mb-2"
                                                    >
                                                        Analyze & Automate
                                                    </h3>
                                                    <p
                                                        class="text-sm text-gray-600"
                                                    >
                                                        Analyze experimental NMR
                                                        spectra fully with
                                                        CT.nmrsolutions.io and
                                                        determine NMR parameters
                                                        automatically
                                                    </p>
                                                </div>
                                                <div class="mt-auto pt-8">
                                                    <div
                                                        class="w-full max-w-[200px]"
                                                    >
                                                        <img
                                                            class="w-full h-auto object-contain"
                                                            src="/img/ct.png"
                                                            alt="CT NMR Solutions"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>

                                    <div
                                        class="flex-none w-[320px] sm:w-[350px] snap-start"
                                    >
                                        <a
                                            href="https://www.nmrium.org/"
                                            target="_blank"
                                            class="group relative bg-white rounded-[28px] overflow-hidden transition-transform duration-300 aspect-[3/4] block border border-gray-200"
                                        >
                                            <div
                                                class="relative h-full flex flex-col p-8"
                                            >
                                                <div>
                                                    <p
                                                        class="text-xs font-medium text-gray-500 mb-2"
                                                    >
                                                        NMRium
                                                    </p>
                                                    <h3
                                                        class="text-2xl font-semibold text-gray-900 leading-tight mb-2"
                                                    >
                                                        View & Process
                                                    </h3>
                                                    <p
                                                        class="text-sm text-gray-600"
                                                    >
                                                        Enhance productivity
                                                        with NMRium, offering
                                                        intuitive, secure,
                                                        browser-based processing
                                                        of NMR spectra
                                                    </p>
                                                </div>
                                                <div class="mt-auto pt-8">
                                                    <div
                                                        class="w-full max-w-[200px]"
                                                    >
                                                        <img
                                                            class="w-full h-auto object-contain"
                                                            src="/img/nmrium-logo.png"
                                                            alt="NMRium"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>

                                    <div
                                        class="flex-none w-[320px] sm:w-[350px] snap-start"
                                    >
                                        <a
                                            href="https://qnmr.org"
                                            target="_blank"
                                            class="group relative bg-white rounded-[28px] overflow-hidden transition-transform duration-300 aspect-[3/4] block border border-gray-200"
                                        >
                                            <div
                                                class="relative h-full flex flex-col p-8"
                                            >
                                                <div>
                                                    <p
                                                        class="text-xs font-medium text-gray-500 mb-2"
                                                    >
                                                        University of Illinois
                                                        Chicago
                                                    </p>
                                                    <h3
                                                        class="text-2xl font-semibold text-gray-900 leading-tight mb-2"
                                                    >
                                                        Quantify
                                                    </h3>
                                                    <p
                                                        class="text-sm text-gray-600"
                                                    >
                                                        Metrologically quantify
                                                        (qnmr.org) biomedical
                                                        material, products, and
                                                        samples
                                                    </p>
                                                </div>
                                                <div class="mt-auto pt-8">
                                                    <div
                                                        class="w-full max-w-[200px]"
                                                    >
                                                        <img
                                                            class="w-full h-auto object-contain"
                                                            src="/img/uic.png"
                                                            alt="University of Illinois Chicago"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>

                                    <div
                                        class="flex-none w-[320px] sm:w-[350px] snap-start"
                                    >
                                        <a
                                            href="https://nobs.naturalproducts.net/"
                                            target="_blank"
                                            class="group relative bg-white rounded-[28px] overflow-hidden transition-transform duration-300 aspect-[3/4] block border border-gray-200"
                                        >
                                            <div
                                                class="relative h-full flex flex-col p-8"
                                            >
                                                <div>
                                                    <p
                                                        class="text-xs font-medium text-gray-500 mb-2"
                                                    >
                                                        Natural Products
                                                        Community
                                                    </p>
                                                    <h3
                                                        class="text-2xl font-semibold text-gray-900 leading-tight mb-2"
                                                    >
                                                        Identify & Qualify
                                                    </h3>
                                                    <p
                                                        class="text-sm text-gray-600"
                                                    >
                                                        Separate, identify, and
                                                        qualify complex natural,
                                                        pharmaceutical, and
                                                        related health products
                                                    </p>
                                                </div>
                                                <div class="mt-auto pt-8">
                                                    <div
                                                        class="flex flex-col gap-3 w-full max-w-[180px]"
                                                    >
                                                        <div class="w-full">
                                                            <img
                                                                class="w-full h-auto object-contain"
                                                                src="/img/UniversiteDeGeneve.png"
                                                                alt="University of Geneva"
                                                            />
                                                        </div>
                                                        <div class="w-full">
                                                            <img
                                                                class="w-full h-auto object-contain"
                                                                src="/img/UniversiteParisSaclay.png"
                                                                alt="Université Paris-Saclay"
                                                            />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                                <div
                                    class="mt-8 flex items-center justify-end gap-3"
                                >
                                    <button
                                        type="button"
                                        class="w-10 h-10 rounded-full bg-gray-200/80 hover:bg-gray-300/80 flex items-center justify-center transition-colors backdrop-blur-sm"
                                        aria-label="Previous partners"
                                        @click="scrollPartnersCarousel(-370)"
                                    >
                                        <svg
                                            class="w-5 h-5 text-gray-700"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 19l-7-7 7-7"
                                            />
                                        </svg>
                                    </button>
                                    <button
                                        type="button"
                                        class="w-10 h-10 rounded-full bg-gray-200/80 hover:bg-gray-300/80 flex items-center justify-center transition-colors backdrop-blur-sm"
                                        aria-label="Next partners"
                                        @click="scrollPartnersCarousel(370)"
                                    >
                                        <svg
                                            class="w-5 h-5 text-gray-700"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 5l7 7-7 7"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="my-16" aria-hidden="true"></div>
            </div>
        </main>

        <Footer />
    </div>
    <component :is="'script'" type="application/ld+json">{{
        schema
    }}</component>
</template>

<script>
import { Head, Link } from "@inertiajs/vue3";
import PublicSiteHeader from "@/Shared/PublicSiteHeader.vue";
import {
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionRoot,
    TransitionChild,
} from "@headlessui/vue";
import {
    ShareIcon,
    ScaleIcon,
    ChatBubbleLeftIcon,
    DocumentTextIcon,
    InboxIcon,
    PencilSquareIcon,
    MagnifyingGlassIcon,
    XMarkIcon,
    CircleStackIcon,
    ArrowRightIcon,
} from "@heroicons/vue/24/outline";
import FlashMessages from "@/Shared/FlashMessages.vue";
import Footer from "@/Shared/Footer.vue";

const Search = [
    {
        name: "Browse",
        description:
            "Know more about the data deposited in nmrXiv by browsing projects and datasets. You can also learn more about our data schema on our documentation site.",
        href: "#",
        icon: InboxIcon,
    },
    {
        name: "Advanced search",
        description:
            "Search similar spectra by simple drag and drop of your machine output files or search spectra by structures. Need further guidance or found any missing information. Reach out to us or check out our documentation site.",
        href: "#",
        icon: MagnifyingGlassIcon,
    },
];

const features = [
    {
        name: "Advanced Search",
        shortDescription: "Find data with powerful search tools.",
        category: "Discovery",
        modalTitle: "Advanced search capabilities",
        modalDescription:
            "Search nmrXiv using multiple methods: browse datasets, search by structure similarity, or upload your own spectra for comparison. Our advanced search tools help you find exactly what you need from our comprehensive NMR database.",
        learnMoreUrl: "https://docs.nmrxiv.org",
        icon: MagnifyingGlassIcon,
    },
    {
        name: "Open Source",
        shortDescription: "Transparent and community-driven.",
        category: "Philosophy",
        modalTitle: "Built on open source principles",
        modalDescription:
            "nmrXiv is built entirely on open-source technologies and follows open science principles. Our codebase is publicly available, allowing the community to contribute, audit, and improve the platform. We believe in transparency and collaboration to advance scientific research.",
        learnMoreUrl: "https://github.com/NFDI4Chem/nmrxiv",
        icon: ScaleIcon,
    },
    {
        name: "Auto Assignments",
        shortDescription: "Automated spectral annotations.",
        category: "Analysis",
        modalTitle: "Automated spectral assignments",
        modalDescription:
            "Leverage machine learning and AI to automatically assign NMR peaks and annotations. Our system can help identify compounds and suggest assignments based on spectral patterns, saving researchers valuable time in data analysis.",
        learnMoreUrl: "https://docs.nmrxiv.org",
        icon: ShareIcon,
    },
    {
        name: "Prediction",
        shortDescription: "AI-powered spectrum prediction.",
        category: "Analysis",
        modalTitle: "Predict NMR spectra",
        modalDescription:
            "Use advanced computational methods to predict NMR spectra for your compounds. Compare predicted vs. experimental data to validate structures and identify discrepancies, accelerating your research workflow.",
        learnMoreUrl: "https://docs.nmrxiv.org",
        icon: PencilSquareIcon,
    },
    {
        name: "Schemas & MIChI",
        shortDescription: "Standardized data formats.",
        category: "Standards",
        modalTitle: "Standardized schemas and identifiers",
        modalDescription:
            "nmrXiv implements standardized data schemas and MIChI (Molecular Identifier for Chemical Information) to ensure data consistency and interoperability. This makes your data findable, accessible, and reusable across different platforms and tools.",
        learnMoreUrl: "https://docs.nmrxiv.org",
        icon: DocumentTextIcon,
    },
    {
        name: "Community Challenges",
        shortDescription: "Collaborative problem solving.",
        category: "Engagement",
        modalTitle: "Join community challenges",
        modalDescription:
            "Participate in structure elucidation challenges and collaborative research projects. Work with researchers worldwide to solve complex spectroscopy problems and contribute to advancing the field of NMR spectroscopy.",
        learnMoreUrl: "https://docs.nmrxiv.org",
        icon: ChatBubbleLeftIcon,
    },
    {
        name: "Docs & API",
        shortDescription: "Developer-friendly tools.",
        category: "Integration",
        modalTitle: "Comprehensive API and documentation",
        modalDescription:
            "Access our rich dataset through a well-documented RESTful API. Build custom tools, integrate nmrXiv data into your workflows, or create new applications. Our comprehensive documentation helps developers get started quickly.",
        learnMoreUrl: "https://docs.nmrxiv.org/developer-guides/api.html",
        icon: DocumentTextIcon,
    },
    {
        name: "Backups",
        shortDescription: "Secure and reliable storage.",
        category: "Reliability",
        modalTitle: "Enterprise-grade data backups",
        modalDescription:
            "Your data is protected with automated daily backups and redundant storage systems. We ensure your research data is safe, secure, and always accessible when you need it.",
        learnMoreUrl: "https://docs.nmrxiv.org",
        icon: CircleStackIcon,
    },
];

export default {
    components: {
        Head,
        Link,
        PublicSiteHeader,
        InboxIcon,
        PencilSquareIcon,
        MagnifyingGlassIcon,
        ShareIcon,
        ScaleIcon,
        ChatBubbleLeftIcon,
        DocumentTextIcon,
        CircleStackIcon,
        ArrowRightIcon,
        XMarkIcon,
        Dialog,
        DialogPanel,
        DialogTitle,
        TransitionRoot,
        TransitionChild,
        FlashMessages,
        Footer,
    },

    props: {
        projects: String,
        compounds: String,
    },

    setup() {
        return {
            Search,
            features,
        };
    },

    data() {
        return {
            schema: null,
            isFeatureModalOpen: false,
            selectedFeatureIndex: null,
            canScrollLeft: false,
            canScrollRight: true,
        };
    },

    computed: {
        selectedFeature() {
            if (this.selectedFeatureIndex !== null) {
                return features[this.selectedFeatureIndex];
            }

            return null;
        },
    },

    mounted() {
        this.$nextTick(() => {
            const container = this.$refs.scrollContainer;
            if (container) {
                container.addEventListener("scroll", this.updateScrollButtons);
                this.updateScrollButtons();
            }
        });
    },

    beforeUnmount() {
        const container = this.$refs.scrollContainer;
        if (container) {
            container.removeEventListener("scroll", this.updateScrollButtons);
        }
    },

    methods: {
        openFeatureModal(index) {
            this.selectedFeatureIndex = index;
            this.isFeatureModalOpen = true;
        },
        closeFeatureModal() {
            this.isFeatureModalOpen = false;
            setTimeout(() => {
                this.selectedFeatureIndex = null;
            }, 300);
        },
        scrollLeft() {
            const container = this.$refs.scrollContainer;
            if (container) {
                container.scrollBy({ left: -330, behavior: "smooth" });
                setTimeout(() => this.updateScrollButtons(), 300);
            }
        },
        scrollRight() {
            const container = this.$refs.scrollContainer;
            if (container) {
                container.scrollBy({ left: 330, behavior: "smooth" });
                setTimeout(() => this.updateScrollButtons(), 300);
            }
        },
        updateScrollButtons() {
            const container = this.$refs.scrollContainer;
            if (container) {
                this.canScrollLeft = container.scrollLeft > 0;
                this.canScrollRight =
                    container.scrollLeft <
                    container.scrollWidth - container.clientWidth - 10;
            }
        },
        scrollPartnersCarousel(offset) {
            const carousel = document.getElementById("partners-carousel");
            if (carousel) {
                carousel.scrollBy({ left: offset, behavior: "smooth" });
            }
        },
        handleImageError(event) {
            const img = event.target;
            const parent = img.parentElement;

            // Hide the broken image
            img.style.display = "none";

            // Create placeholder if it doesn't exist
            if (!parent.querySelector(".avatar-placeholder")) {
                const placeholder = document.createElement("div");
                placeholder.className =
                    "avatar-placeholder flex items-center justify-center w-full h-full";

                // Get initials from alt text
                const altText = img.alt || "User";
                const initials = altText
                    .split(" ")
                    .map((word) => word.charAt(0))
                    .join("")
                    .toUpperCase()
                    .slice(0, 2);

                placeholder.innerHTML = `<span class="text-2xl font-semibold text-gray-500">${initials}</span>`;
                parent.appendChild(placeholder);
            }
        },
    },
};
</script>

<style scoped>
/* Custom brand color */
.text-brand {
    color: #fd0039;
}

.text-brand-hover:hover {
    color: #d4002f;
}

.ring-brand {
    --tw-ring-color: #fd0039;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Blob animations */
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}
</style>
