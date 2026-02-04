<template>
    <div class="bg-white">
        <Head title="Welcome to nmrXiv"></Head>
        <FlashMessages />
        <main>
            <!-- Header and Hero with Animated Gradient -->
            <div class="relative overflow-hidden">
                <!-- Animated mesh gradient background -->
                <div class="absolute inset-0 bg-gradient-to-br from-blue-50/30 via-indigo-50/30 to-purple-50/30"></div>
                <div class="absolute inset-0 opacity-20">
                    <div class="absolute top-0 left-1/4 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl animate-blob"></div>
                    <div class="absolute top-0 right-1/4 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-2000"></div>
                    <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-4000"></div>
                </div>
                
                <!-- Gradient fade to white at bottom -->
                <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-b from-transparent to-white"></div>
                
                <header class="relative">
                    <Popover class="relative border-b border-white/20">
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
                                    class="rounded-lg p-2 inline-flex items-center justify-center text-gray-900 hover:text-gray-600 hover:bg-white/50 focus:outline-none focus:ring-2 ring-brand transition-colors"
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
                            <div v-if="true">
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
                                            <div class="mt-6">
                                                <nav
                                                    class="grid grid-cols-1 gap-7"
                                                >
                                                    <Link
                                                        v-for="item in Search"
                                                        :key="item.name"
                                                        :href="item.href"
                                                        class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-50"
                                                    >
                                                        <div
                                                            class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-md bg-blue-600 text-white"
                                                        >
                                                            <component
                                                                :is="item.icon"
                                                                class="h-6 w-6"
                                                                aria-hidden="true"
                                                            />
                                                        </div>
                                                        <div
                                                            class="ml-4 text-sm font-medium text-gray-900"
                                                        >
                                                            {{ item.name }}
                                                        </div>
                                                    </Link>
                                                </nav>
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
                                                    href="/datasets"
                                                    class="text-sm font-medium text-gray-900 hover:text-gray-700"
                                                >
                                                    Datasets
                                                </Link>
                                                <Link
                                                    href="/compounds"
                                                    class="text-sm font-medium text-gray-900 hover:text-gray-700"
                                                >
                                                    Compounds
                                                </Link>
                                            </div>
                                            <div class="mt-6">
                                                <Link
                                                    href="/login"
                                                    class="w-full flex items-center justify-center bg-blue-600 bg-origin-border px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700"
                                                >
                                                    Login
                                                </Link>
                                                <p
                                                    class="mt-6 text-center text-sm font-medium text-gray-500"
                                                >
                                                    <Link
                                                        href="/register"
                                                        class="text-gray-900"
                                                    >
                                                        Register
                                                    </Link>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </PopoverPanel>
                            </div>
                        </transition>
                    </Popover>
                </header>
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="relative py-20 sm:py-24 lg:py-32">
                        <div class="text-center">
                            <!-- Main Heading -->
                            <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl">
                                Open, <span class="highlight-word">FAIR</span> and Consensus-Driven
                                <span class="block mt-2 text-gray-900"><span class="highlight-word highlight-word-delay">NMR Data Repository</span></span>
                            </h1>
                            
                            <!-- Description -->
                            <p class="mt-6 max-w-3xl mx-auto text-xl text-gray-600 leading-relaxed">
                                Archive, browse, and analyze NMR spectroscopy data with comprehensive tools for research and collaboration worldwide.
                            </p>
                            
                            <!-- Action Buttons -->
                            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                                <Link
                                    href="/projects"
                                    class="inline-flex items-center justify-center px-6 py-3 text-base font-medium rounded-full text-white bg-gray-900 hover:bg-gray-800 transition-all duration-200"
                                >
                                    Browse Data
                                </Link>
                                
                                <UnifiedSearch></UnifiedSearch>
                                
                                <Link
                                    href="/register"
                                    class="inline-flex items-center justify-center px-6 py-3 text-base font-medium rounded-full text-gray-900 bg-white border border-gray-900 hover:bg-gray-50 transition-all duration-200"
                                >
                                    Submit Data
                                </Link>
                            </div>
                            
                            <!-- Quick Links -->
                            <div class="mt-12 flex flex-wrap items-center justify-center gap-6 text-sm">
                                <a
                                    href="https://docs.nmrxiv.org"
                                    target="_blank"
                                    class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-900 transition-colors"
                                >
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M7 3.41a1 1 0 0 0-.668-.943L2.275 1.039a.987.987 0 0 0-.877.166c-.25.192-.398.493-.398.812V12.2c0 .454.296.853.725.977l3.948 1.365A1 1 0 0 0 7 13.596V3.41ZM9 13.596a1 1 0 0 0 1.327.946l3.948-1.365c.429-.124.725-.523.725-.977V2.017c0-.32-.147-.62-.398-.812a.987.987 0 0 0-.877-.166L9.668 2.467A1 1 0 0 0 9 3.41v10.186Z" />
                                    </svg>
                                    Documentation
                                </a>
                                <a
                                    href="https://github.com/NFDI4Chem/nmrxiv"
                                    target="_blank"
                                    class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-900 transition-colors"
                                >
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M8 .198a8 8 0 0 0-8 8 7.999 7.999 0 0 0 5.47 7.59c.4.076.547-.172.547-.384 0-.19-.007-.694-.01-1.36-2.226.482-2.695-1.074-2.695-1.074-.364-.923-.89-1.17-.89-1.17-.725-.496.056-.486.056-.486.803.056 1.225.824 1.225.824.714 1.224 1.873.87 2.33.666.072-.518.278-.87.507-1.07-1.777-.2-3.644-.888-3.644-3.954 0-.873.31-1.586.823-2.146-.09-.202-.36-1.016.07-2.118 0 0 .67-.214 2.2.82a7.67 7.67 0 0 1 2-.27 7.67 7.67 0 0 1 2 .27c1.52-1.034 2.19-.82 2.19-.82.43 1.102.16 1.916.08 2.118.51.56.82 1.273.82 2.146 0 3.074-1.87 3.75-3.65 3.947.28.24.54.73.54 1.48 0 1.07-.01 1.93-.01 2.19 0 .21.14.46.55.38A7.972 7.972 0 0 0 16 8.199a8 8 0 0 0-8-8Z" />
                                    </svg>
                                    GitHub
                                </a>
                                <a
                                    href="https://nmrxiv.org/api/documentation"
                                    target="_blank"
                                    class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-900 transition-colors"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
                                    </svg>
                                    API
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Journals Section with Transparent Background -->
            <div class="relative overflow-hidden bg-white">
                <!-- Content -->
                <div class="relative py-2">
                    <div class="mx-auto max-w-6xl px-6 sm:px-8 lg:px-12">
                        <h2
                            class="text-center text-lg sm:text-xl font-semibold leading-8 text-gray-900"
                        >
                            Recommended by leading journals as the
                            community-trusted repository for NMR data
                            deposition.
                        </h2>
                        <div
                            class="mx-auto mt-12 grid max-w-lg grid-cols-2 md:grid-cols-4 items-center gap-x-8 gap-y-10"
                        >
                            <div
                                class="col-span-1 w-full object-contain lg:col-span-2 transition-transform duration-300 hover:scale-105"
                            >
                                <a
                                    href="https://pubs.acs.org/doi/10.1021/acs.jnatprod.3c00281"
                                    target="_blank"
                                    ><img
                                        src="/img/jnp.png"
                                        alt="Journal of Natural Products"
                                        class="w-full h-auto"
                                /></a>
                            </div>
                            <div
                                class="col-span-1 w-full object-contain lg:col-span-2 transition-transform duration-300 hover:scale-105"
                            >
                                <a
                                    href="https://onlinelibrary.wiley.com/page/journal/15213773/homepage/notice-to-authors"
                                    target="_blank"
                                    ><img
                                        src="/img/angewandte-chemie.png"
                                        alt="Angewandte Chemie"
                                        class="w-full h-auto"
                                /></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Promotional Section - Apple Music Style -->
            <div class="relative">
                <!-- Gradient background matching metrics section -->
                <div class="absolute inset-0 bg-gradient-to-b from-white via-gray-50 to-white"></div>
                
                <div class="relative max-w-6xl mx-auto px-6 pt-20 sm:px-8 sm:pt-24 lg:px-12">
                    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-50 to-white border border-gray-200 shadow-sm">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 p-8 sm:p-12 items-center">
                            <!-- Left Side: Content -->
                            <div>
                                <!-- Brand Text -->
                                <div class="mb-6">
                                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">NMR PREDICTION</p>
                                </div>
                                
                                <!-- Main Heading -->
                                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-semibold text-gray-900 leading-tight tracking-tight">
                                    Fast and accurate NMR spectra predictions from chemical structures
                                </h2>
                                
                                <!-- Call to Action Button -->
                                <div class="mt-8">
                                    <a href="/predict" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white text-base font-semibold rounded-full hover:bg-blue-700 transition-colors duration-200 shadow-md hover:shadow-lg">
                                        Try now
                                        <svg class="ml-2 w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Right Side: Visual Element -->
                            <div class="relative h-full flex items-center justify-center">
                                <!-- Spectra Card Cascade -->
                                <div class="relative w-full max-w-lg aspect-[4/3]">
                                    <!-- Card 1 - Back layer -->
                                    <div class="absolute inset-0 rounded-2xl overflow-hidden shadow-lg bg-white animate-cascade-rotate-1">
                                        <img src="/img/spectra/1.png" alt="NMR Spectrum" class="w-full h-full object-cover" />
                                    </div>
                                    
                                    <!-- Card 2 - Middle back -->
                                    <div class="absolute inset-0 rounded-2xl overflow-hidden shadow-lg bg-white animate-cascade-rotate-2">
                                        <img src="/img/spectra/2.png" alt="NMR Spectrum" class="w-full h-full object-cover" />
                                    </div>
                                    
                                    <!-- Card 3 - Middle front -->
                                    <div class="absolute inset-0 rounded-2xl overflow-hidden shadow-xl bg-white animate-cascade-rotate-3">
                                        <img src="/img/spectra/3.png" alt="NMR Spectrum" class="w-full h-full object-cover" />
                                    </div>
                                    
                                    <!-- Card 4 - Front layer -->
                                    <div class="absolute inset-0 rounded-2xl overflow-hidden shadow-2xl bg-white animate-cascade-rotate-4 hover:scale-[1.02] transition-transform duration-300">
                                        <img src="/img/spectra/4.png" alt="NMR Spectrum" class="w-full h-full object-cover" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metrics Section - Apple iPad Style -->
            <div class="relative">
                <!-- Gradient background: white -> gray -> white -->
                <div class="absolute inset-0 bg-gradient-to-b from-white via-gray-50 to-white"></div>
                
                <div class="relative max-w-6xl mx-auto px-6 pb-20 sm:px-8 sm:pb-24 pt-4 lg:px-12">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Projects Card -->
                        <a
                            href="/projects"
                            class="group relative overflow-hidden rounded-3xl bg-white border border-gray-200 p-8 min-h-[400px] flex flex-col justify-between transition-all duration-300 shadow-sm hover:shadow-md"
                        >
                            <div>
                                <p class="text-sm font-semibold text-gray-500 mb-2">Publications & Studies</p>
                                <h3 class="text-3xl font-bold text-gray-900 leading-tight">
                                    Explore<br>dataset<br>collections.
                                </h3>
                                <p class="text-sm text-gray-600 mt-4">
                                    Comprehensive collections of NMR data and spectra organized by publication or research study.
                                </p>
                            </div>
                            
                            <div class="mt-auto">
                                <div class="text-gray-900 mb-6">
                                    <div class="text-5xl font-bold">{{ projects }}</div>
                                    <div class="text-lg mt-1 text-gray-600">Projects</div>
                                    <small
                                        v-if="embargoed_projects && embargoed_projects > 0"
                                        class="text-sm text-gray-500"
                                    >{{ embargoed_projects }} in embargo</small>
                                </div>
                            </div>
                        </a>

                        <!-- Compounds Card -->
                        <a
                            href="/compounds"
                            class="group relative overflow-hidden rounded-3xl bg-white border border-gray-200 p-8 min-h-[400px] flex flex-col justify-between transition-all duration-300 shadow-sm hover:shadow-md"
                        >
                            <div>
                                <p class="text-sm font-semibold text-gray-500 mb-2">Chemical Structures</p>
                                <h3 class="text-3xl font-bold text-gray-900 leading-tight">
                                    Discover<br>compounds<br>spectra.
                                </h3>
                                <p class="text-sm text-gray-600 mt-4">
                                    Search and analyze chemical structures with their associated experimental data and measurements.
                                </p>
                            </div>
                            
                            <div class="mt-auto">
                                <div class="text-gray-900 mb-6">
                                    <div class="text-5xl font-bold">{{ compounds }}</div>
                                    <div class="text-lg mt-1 text-gray-600">Compounds</div>
                                </div>
                            </div>
                        </a>

                        <!-- Spectra Card -->
                        <a
                            href="/spectra"
                            class="group relative overflow-hidden rounded-3xl bg-white border border-gray-200 p-8 min-h-[400px] flex flex-col justify-between transition-all duration-300 shadow-sm hover:shadow-md"
                        >
                            <div>
                                <p class="text-sm font-semibold text-gray-500 mb-2">Experimental data</p>
                                <h3 class="text-3xl font-bold text-gray-900 leading-tight">
                                    Access and<br>compare<br>spectra.
                                </h3>
                                <p class="text-sm text-gray-600 mt-4">
                                    Access raw NMR spectroscopy data with complete experimental details and metadata.
                                </p>
                            </div>
                            
                            <div class="mt-auto">
                                <div class="text-gray-900 mb-6">
                                    <div class="text-5xl font-bold">{{ spectra }}</div>
                                    <div class="text-lg mt-1 text-gray-600">Spectra</div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="relative">
        
                <div class="mt-12">
                    <div
                        class="lg:mx-auto lg:max-w-6xl lg:px-12 lg:grid lg:grid-cols-2 lg:grid-flow-col-dense lg:gap-24 overflow-x-hidden"
                    >
                        <div
                            class="px-4 z-20 max-w-xl mx-auto sm:px-6 lg:py-32 lg:max-w-none lg:mx-0 lg:px-0 lg:col-start-2"
                        >
                            <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm">
                                <div>
                                    <span
                                        class="h-12 w-12 rounded-md flex items-center justify-center bg-blue-600"
                                    >
                                        <SparklesIcon
                                            class="h-6 w-6 text-white"
                                            aria-hidden="true"
                                        />
                                    </span>
                                </div>
                                <div class="mt-6">
                                <h2
                                    class="text-2xl font-bold tracking-tight text-gray-900"
                                >
                                    Build with Our API
                                </h2>
                                <p class="mt-4 text-base text-gray-500">
                                    Leverage our comprehensive API to build innovative tools and applications. Access rich data and metadata to power your research workflows and analytical solutions.
                                </p>
                                    <div class="mt-6">
                                        <a
                                            target="_blank"
                                            href="https://docs.nmrxiv.org/developer-guides/api.html"
                                            class="inline-flex items-center justify-center bg-blue-600 bg-origin-border px-6 py-3 border border-transparent text-base font-medium rounded-full shadow-sm text-white hover:bg-blue-700 transition-colors duration-200"
                                        >
                                            View API Docs
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-12 sm:mt-16 lg:mt-0">
                            <div class="lg:relative lg:h-full">
                                <img
                                    class="w-full lg:absolute lg:left-0 lg:h-full lg:w-auto lg:max-w-none"
                                    src="/img/api.png"
                                    alt=""
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="mt-24 border-y pt-10">
                    <div
                        class="lg:mx-auto lg:max-w-6xl lg:px-12 lg:grid lg:grid-cols-2 lg:grid-flow-col-dense lg:gap-24 overflow-x-hidden py-10 pb-20 border-r"
                    >
                        <div
                            class="px-4 max-w-xl mx-auto sm:px-6 lg:py-16 lg:max-w-none lg:mx-0 lg:px-0"
                        >
                            <div>
                                <div>
                                    <span
                                        class="h-12 w-12 rounded-md flex items-center justify-center bg-blue-600"
                                    >
                                        <InboxIcon
                                            class="h-6 w-6 text-white"
                                            aria-hidden="true"
                                        />
                                    </span>
                                </div>
                                <div class="mt-6">
                                <h2
                                    class="text-2xl font-bold tracking-tight text-gray-900"
                                >
                                    Review and analyse the spectral
                                    assignments
                                </h2>
                                <p class="mt-4 text-base text-gray-500">
                                        Data deposited in nmrXiv requires
                                        original machine output files or
                                        processed raw data. With this data,
                                        researchers can annotate the missing
                                        assignments in the spectra, reanalyse
                                        previous work and offer additional help
                                        there by sharing their knowledge and
                                        expertise.
                                    </p>
                                    <div class="mt-6">
                                        <Link
                                            href="#"
                                            class="inline-flex bg-blue-600 bg-origin-border px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white hover:bg-blue-700"
                                        >
                                            Need help with structure
                                            ellucidation?&emsp;<i
                                                ><small>
                                                    (coming soon)</small
                                                ></i
                                            ></Link
                                        ><br />
                                        <Link
                                            href="#"
                                            class="inline-flex bg-blue-600 bg-origin-border px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white hover:bg-blue-700 mt-4"
                                        >
                                            Check out active challenges&emsp;<i
                                                ><small>
                                                    (coming soon)</small
                                                ></i
                                            >
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-12 sm:mt-16 lg:mt-0">
                            <div
                                class="pl-4 -mr-48 sm:pl-6 md:-mr-16 lg:px-0 lg:m-0 lg:relative lg:h-full"
                            >
                                <img
                                    class="w-full rounded-xl shadow-xl ring-1 ring-black ring-opacity-5 lg:absolute lg:left-0 lg:h-full lg:w-auto lg:max-w-none"
                                    src="/img/welcome2.jpg"
                                    alt=""
                                />
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>

            <!-- FAIR Features Section - Apple Style -->
            <div class="bg-gray-50">
                <div class="py-20 sm:py-24">
                    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
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
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Scrollable Container - Full Width -->
                    <div class="relative">
                        <div class="overflow-x-auto scrollbar-hide px-6 sm:px-8 lg:px-12" ref="scrollContainer">
                            <div class="flex gap-3 pb-4" style="width: max-content;">
                                <div v-for="(feature, index) in features" :key="feature.name"
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
                                            <h3 class="text-2xl font-bold text-gray-900 leading-tight mb-3">
                                                {{ feature.name }}
                                            </h3>
                                            <p class="text-base text-gray-600 leading-relaxed" v-if="feature.shortDescription">
                                                {{ feature.shortDescription }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex justify-end">
                                        <button
                                            @click="openFeatureModal(index)"
                                            class="flex items-center justify-center h-11 w-11 rounded-3xl bg-gray-900 text-white hover:bg-gray-700 transition-colors flex-shrink-0"
                                            :aria-label="`Learn more about ${feature.name}`"
                                        >
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        
                    <!-- Navigation Arrows -->
                    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
                        <div class="flex justify-end gap-2 mt-6">
                            <button 
                                @click="scrollLeft"
                                class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="!canScrollLeft"
                            >
                                <svg class="h-5 w-5 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                                </svg>
                            </button>
                            <button 
                                @click="scrollRight"
                                class="h-10 w-10 rounded-full bg-gray-900 flex items-center justify-center hover:bg-gray-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="!canScrollRight"
                            >
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feature Modal -->
            <TransitionRoot appear :show="isFeatureModalOpen" as="template">
                <Dialog as="div" @close="closeFeatureModal" class="relative z-50">
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0"
                        enter-to="opacity-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100"
                        leave-to="opacity-0"
                    >
                        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" />
                    </TransitionChild>

                    <div class="fixed inset-0 overflow-y-auto">
                        <div class="flex min-h-full items-center justify-center p-4">
                            <TransitionChild
                                as="template"
                                enter="duration-300 ease-out"
                                enter-from="opacity-0 scale-95"
                                enter-to="opacity-100 scale-100"
                                leave="duration-200 ease-in"
                                leave-from="opacity-100 scale-100"
                                leave-to="opacity-0 scale-95"
                            >
                                <DialogPanel class="w-full max-w-2xl transform overflow-hidden rounded-3xl bg-white p-8 sm:p-12 text-left align-middle shadow-2xl transition-all">
                                    <div class="absolute right-6 top-6">
                                        <button
                                            @click="closeFeatureModal"
                                            class="flex items-center justify-center h-10 w-10 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors"
                                        >
                                            <XMarkIcon class="h-5 w-5 text-gray-900" />
                                        </button>
                                    </div>
                                    
                                    <div v-if="selectedFeature">
                                        <p class="text-sm font-medium text-gray-600 mb-2">{{ selectedFeature.category || 'Feature' }}</p>
                                        <DialogTitle
                                            as="h2"
                                            class="text-3xl sm:text-4xl font-bold text-gray-900 leading-tight"
                                        >
                                            {{ selectedFeature.modalTitle || selectedFeature.name }}
                                        </DialogTitle>
                                        <div class="mt-6 text-base text-gray-700 space-y-4">
                                            <p>{{ selectedFeature.modalDescription }}</p>
                                        </div>
                                        
                                        <div class="mt-8" v-if="selectedFeature.learnMoreUrl">
                                            <a
                                                :href="selectedFeature.learnMoreUrl"
                                                target="_blank"
                                                class="inline-flex items-center gap-2 text-brand font-medium text-brand-hover transition-colors"
                                            >
                                                Learn more
                                                <ArrowRightIcon class="h-4 w-4" />
                                            </a>
                                        </div>
                                    </div>
                                </DialogPanel>
                            </TransitionChild>
                        </div>
                    </div>
                </Dialog>
            </TransitionRoot>
            <!-- Academic and public/private partners Section - iPad Style -->
            <div class="bg-white border-t border-gray-100">
                <div class="py-20">
                    <!-- Section Header with max-width -->
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-12">
                        <div class="max-w-3xl">
                            <p class="text-lg text-gray-600 leading-relaxed">
                                Our initiative consists of academic and public/private partners that contribute unique expertise and resources towards our joint goal
                            </p>
                        </div>
                    </div>

                    <!-- Horizontal Scrollable Cards with Controls - Full Width -->
                    <div class="relative">
                        <!-- Cards Container -->
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div 
                                id="partners-carousel" 
                                class="flex gap-5 overflow-x-auto snap-x snap-mandatory pb-4 scroll-smooth"
                                style="scrollbar-width: none; -ms-overflow-style: none; -webkit-overflow-scrolling: touch;"
                            >
                            <!-- Share FAIRly Card (nmrXiv) -->
                            <div class="flex-none w-[320px] sm:w-[350px] snap-start">
                                <a 
                                    href="https://nmrxiv.org" 
                                    target="_blank"
                                    class="group relative bg-white rounded-[28px] overflow-hidden transition-transform duration-300 aspect-[3/4] block border border-gray-200"
                                >
                                    <!-- Content -->
                                    <div class="relative h-full flex flex-col p-8">
                                        <!-- Top Section - Labels & Title -->
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 mb-2">NFDI4Chem, Friedrich Schiller University Jena</p>
                                            <h3 class="text-2xl font-semibold text-gray-900 leading-tight mb-2">
                                                Share FAIRly
                                            </h3>
                                            <p class="text-sm text-gray-600">
                                                Research data infrastructure for chemistry, enabling FAIR sharing of raw and processed NMR data worldwide through nmrXiv.org
                                            </p>
                                        </div>
                                        
                                        <!-- Bottom - Logos (sticky to bottom, stacked) -->
                                        <div class="mt-auto pt-8">
                                            <div class="flex flex-col gap-4 w-full max-w-[180px]">
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

                            <!-- Analyze & Automate Card -->
                            <div class="flex-none w-[320px] sm:w-[350px] snap-start">
                                <a 
                                    href="https://ctb.nmrsolutions.fi/login?returnUrl=~dashboard" 
                                    target="_blank"
                                    class="group relative bg-white rounded-[28px] overflow-hidden transition-transform duration-300 aspect-[3/4] block border border-gray-200"
                                >
                                    <!-- Content -->
                                    <div class="relative h-full flex flex-col p-8">
                                        <!-- Top Section - Labels & Title -->
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 mb-2">CT.nmrsolutions.io</p>
                                            <h3 class="text-2xl font-semibold text-gray-900 leading-tight mb-2">
                                                Analyze & Automate
                                            </h3>
                                            <p class="text-sm text-gray-600">
                                                Analyze experimental NMR spectra fully with CT.nmrsolutions.io and determine NMR parameters automatically
                                            </p>
                                        </div>
                                        
                                        <!-- Bottom - Large Image/Content (sticky to bottom) -->
                                        <div class="mt-auto pt-8">
                                            <div class="w-full max-w-[200px]">
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

                            <!-- View & Process Card -->
                            <div class="flex-none w-[320px] sm:w-[350px] snap-start">
                                <a 
                                    href="https://www.nmrium.org/" 
                                    target="_blank"
                                    class="group relative bg-white rounded-[28px] overflow-hidden transition-transform duration-300 aspect-[3/4] block border border-gray-200"
                                >
                                    <!-- Content -->
                                    <div class="relative h-full flex flex-col p-8">
                                        <!-- Top Section - Labels & Title -->
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 mb-2">NMRium</p>
                                            <h3 class="text-2xl font-semibold text-gray-900 leading-tight mb-2">
                                                View & Process
                                            </h3>
                                            <p class="text-sm text-gray-600">
                                                Enhance productivity with NMRium, offering intuitive, secure, browser-based processing of NMR spectra
                                            </p>
                                        </div>
                                        
                                        <!-- Bottom - Large Image/Content (sticky to bottom) -->
                                        <div class="mt-auto pt-8">
                                            <div class="w-full max-w-[200px]">
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

                            <!-- Quantify Card -->
                            <div class="flex-none w-[320px] sm:w-[350px] snap-start">
                                <a 
                                    href="https://qnmr.org" 
                                    target="_blank"
                                    class="group relative bg-white rounded-[28px] overflow-hidden transition-transform duration-300 aspect-[3/4] block border border-gray-200"
                                >
                                    <!-- Content -->
                                    <div class="relative h-full flex flex-col p-8">
                                        <!-- Top Section - Labels & Title -->
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 mb-2">University of Illinois Chicago</p>
                                            <h3 class="text-2xl font-semibold text-gray-900 leading-tight mb-2">
                                                Quantify
                                            </h3>
                                            <p class="text-sm text-gray-600">
                                                Metrologically quantify (qnmr.org) biomedical material, products, and samples
                                            </p>
                                        </div>
                                        
                                        <!-- Bottom - Large Image/Content (sticky to bottom) -->
                                        <div class="mt-auto pt-8">
                                            <div class="w-full max-w-[200px]">
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

                            <!-- Identify & Qualify Card -->
                            <div class="flex-none w-[320px] sm:w-[350px] snap-start">
                                <a 
                                    href="https://nobs.naturalproducts.net/" 
                                    target="_blank"
                                    class="group relative bg-white rounded-[28px] overflow-hidden transition-transform duration-300 aspect-[3/4] block border border-gray-200"
                                >
                                    <!-- Content -->
                                    <div class="relative h-full flex flex-col p-8">
                                        <!-- Top Section - Labels & Title -->
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 mb-2">Natural Products Community</p>
                                            <h3 class="text-2xl font-semibold text-gray-900 leading-tight mb-2">
                                                Identify & Qualify
                                            </h3>
                                            <p class="text-sm text-gray-600">
                                                Separate, identify, and qualify complex natural, pharmaceutical, and related health products
                                            </p>
                                        </div>
                                        
                                        <!-- Bottom - Logos (sticky to bottom, stacked) -->
                                        <div class="mt-auto pt-8">
                                            <div class="flex flex-col gap-3 w-full max-w-[180px]">
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

                        <!-- Navigation Controls -->
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-end gap-3 mt-8">
                                <button 
                                    onclick="document.getElementById('partners-carousel').scrollBy({ left: -370, behavior: 'smooth' })"
                                    class="w-10 h-10 rounded-full bg-gray-200/80 hover:bg-gray-300/80 flex items-center justify-center transition-colors backdrop-blur-sm"
                                    aria-label="Previous"
                                >
                                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </button>
                                <button 
                                    onclick="document.getElementById('partners-carousel').scrollBy({ left: 370, behavior: 'smooth' })"
                                    class="w-10 h-10 rounded-full bg-gray-200/80 hover:bg-gray-300/80 flex items-center justify-center transition-colors backdrop-blur-sm"
                                    aria-label="Next"
                                >
                                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQs Section -->
            <div>
                <FAQs></FAQs>
            </div>

            <div class="bg-white">
                <div
                    class="mx-auto py-8 px-4 sm:px-6 lg:px-8 lg:flex lg:items-center lg:justify-between"
                >
                    <!-- CTA Section -->
                    <div
                        class="w-full relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 p-8 sm:p-12"
                    >
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 opacity-10">
                            <svg
                                class="absolute inset-0 h-full w-full"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <defs>
                                    <pattern
                                        id="grid-pattern"
                                        width="32"
                                        height="32"
                                        patternUnits="userSpaceOnUse"
                                    >
                                        <path
                                            d="M0 32V0h32"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="0.5"
                                        />
                                    </pattern>
                                </defs>
                                <rect
                                    width="100%"
                                    height="100%"
                                    fill="url(#grid-pattern)"
                                />
                            </svg>
                        </div>
                        <!-- Gradient Orbs -->
                        <div
                            class="absolute -top-24 -right-24 w-96 h-96 bg-gradient-to-br from-teal-500/20 to-indigo-500/20 rounded-full blur-3xl"
                        ></div>
                        <div
                            class="absolute -bottom-24 -left-24 w-96 h-96 bg-gradient-to-br from-indigo-500/20 to-purple-500/20 rounded-full blur-3xl"
                        ></div>

                        <div
                            class="relative flex flex-col sm:flex-row items-center justify-between gap-6"
                        >
                            <div class="text-center sm:text-left">
                                <h3
                                    class="text-xl sm:text-2xl font-bold text-white mb-2"
                                >
                                    Ready to share your data?
                                </h3>
                                <p class="text-slate-300 text-sm">
                                    Join hundreds of researchers contributing to
                                    open science.
                                </p>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <Link
                                    href="/register"
                                    class="group inline-flex items-center justify-center gap-2 px-6 py-3 bg-white text-slate-900 font-semibold rounded-full hover:bg-blue-50 transition-colors duration-200 shadow-sm"
                                >
                                    Get started
                                    <ArrowRightIcon
                                        class="h-4 w-4 group-hover:translate-x-1 transition-transform duration-200"
                                    />
                                </Link>
                                <a
                                    href="https://docs.nmrxiv.org/submission-guides/data-lifecycle.html"
                                    target="_blank"
                                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-transparent text-white font-semibold rounded-full border border-slate-600 hover:bg-slate-800 hover:border-slate-500 transition-colors duration-200"
                                >
                                    Learn more
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
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
import JetApplicationLogo from "@/Jetstream/ApplicationLogo.vue";
import Projects from "@/Shared/Projects.vue";
import Footer from "@/Shared/Footer.vue";
import StructureSearch from "@/App/StructureSearch.vue";
import UnifiedSearch from "@/Shared/UnifiedSearch.vue";
import {
    Popover,
    PopoverButton,
    PopoverGroup,
    PopoverPanel,
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
    ChatBubbleLeftRightIcon,
    DocumentTextIcon,
    HeartIcon,
    InboxIcon,
    Bars3Icon,
    PencilSquareIcon,
    MagnifyingGlassIcon,
    ArrowUturnLeftIcon,
    SparklesIcon,
    TrashIcon,
    UsersIcon,
    XMarkIcon,
    CircleStackIcon,
    CloudArrowUpIcon,
    BeakerIcon,
    UserGroupIcon,
    ArrowRightIcon,
    ShieldCheckIcon,
    LockClosedIcon,
    GlobeAltIcon,
    DocumentCheckIcon,
} from "@heroicons/vue/24/outline";
import { ChevronDownIcon } from "@heroicons/vue/24/solid";
import ToolTip from "@/Shared/ToolTip.vue";
import FAQs from "@/App/FAQs.vue";
import FlashMessages from "@/Shared/FlashMessages.vue";

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
        modalDescription: "Search nmrXiv using multiple methods: browse datasets, search by structure similarity, or upload your own spectra for comparison. Our advanced search tools help you find exactly what you need from our comprehensive NMR database.",
        learnMoreUrl: "https://docs.nmrxiv.org",
        icon: MagnifyingGlassIcon,
    },
    {
        name: "Open Source",
        shortDescription: "Transparent and community-driven.",
        category: "Philosophy",
        modalTitle: "Built on open source principles",
        modalDescription: "nmrXiv is built entirely on open-source technologies and follows open science principles. Our codebase is publicly available, allowing the community to contribute, audit, and improve the platform. We believe in transparency and collaboration to advance scientific research.",
        learnMoreUrl: "https://github.com/NFDI4Chem/nmrxiv",
        icon: ScaleIcon,
    },
    {
        name: "Auto Assignments",
        shortDescription: "Automated spectral annotations.",
        category: "Analysis",
        modalTitle: "Automated spectral assignments",
        modalDescription: "Leverage machine learning and AI to automatically assign NMR peaks and annotations. Our system can help identify compounds and suggest assignments based on spectral patterns, saving researchers valuable time in data analysis.",
        learnMoreUrl: "https://docs.nmrxiv.org",
        icon: ShareIcon,
    },
    {
        name: "Prediction",
        shortDescription: "AI-powered spectrum prediction.",
        category: "Analysis",
        modalTitle: "Predict NMR spectra",
        modalDescription: "Use advanced computational methods to predict NMR spectra for your compounds. Compare predicted vs. experimental data to validate structures and identify discrepancies, accelerating your research workflow.",
        learnMoreUrl: "https://docs.nmrxiv.org",
        icon: PencilSquareIcon,
    },
    {
        name: "Schemas & MIChI",
        shortDescription: "Standardized data formats.",
        category: "Standards",
        modalTitle: "Standardized schemas and identifiers",
        modalDescription: "nmrXiv implements standardized data schemas and MIChI (Molecular Identifier for Chemical Information) to ensure data consistency and interoperability. This makes your data findable, accessible, and reusable across different platforms and tools.",
        learnMoreUrl: "https://docs.nmrxiv.org",
        icon: DocumentTextIcon,
    },
    {
        name: "Community Challenges",
        shortDescription: "Collaborative problem solving.",
        category: "Engagement",
        modalTitle: "Join community challenges",
        modalDescription: "Participate in structure elucidation challenges and collaborative research projects. Work with researchers worldwide to solve complex spectroscopy problems and contribute to advancing the field of NMR spectroscopy.",
        learnMoreUrl: "https://docs.nmrxiv.org",
        icon: ChatBubbleLeftIcon,
    },
    {
        name: "Docs & API",
        shortDescription: "Developer-friendly tools.",
        category: "Integration",
        modalTitle: "Comprehensive API and documentation",
        modalDescription: "Access our rich dataset through a well-documented RESTful API. Build custom tools, integrate nmrXiv data into your workflows, or create new applications. Our comprehensive documentation helps developers get started quickly.",
        learnMoreUrl: "https://docs.nmrxiv.org/developer-guides/api.html",
        icon: DocumentTextIcon,
    },
    {
        name: "Backups",
        shortDescription: "Secure and reliable storage.",
        category: "Reliability",
        modalTitle: "Enterprise-grade data backups",
        modalDescription: "Your data is protected with automated daily backups and redundant storage systems. We ensure your research data is safe, secure, and always accessible when you need it.",
        learnMoreUrl: "https://docs.nmrxiv.org",
        icon: CircleStackIcon,
    },
];

export default {
    components: {
        Head,
        Link,
        JetApplicationLogo,
        ChatBubbleLeftIcon,
        ChatBubbleLeftRightIcon,
        DocumentTextIcon,
        HeartIcon,
        InboxIcon,
        Bars3Icon,
        PencilSquareIcon,
        MagnifyingGlassIcon,
        ArrowUturnLeftIcon,
        SparklesIcon,
        TrashIcon,
        ShareIcon,
        UsersIcon,
        XMarkIcon,
        ScaleIcon,
        Popover,
        PopoverButton,
        PopoverGroup,
        PopoverPanel,
        Dialog,
        DialogPanel,
        DialogTitle,
        TransitionRoot,
        TransitionChild,
        ChevronDownIcon,
        CircleStackIcon,
        CloudArrowUpIcon,
        BeakerIcon,
        UserGroupIcon,
        ArrowRightIcon,
        ShieldCheckIcon,
        LockClosedIcon,
        GlobeAltIcon,
        DocumentCheckIcon,
        ToolTip,
        Projects,
        FAQs,
        FlashMessages,
        StructureSearch,
        UnifiedSearch,
        Footer,
    },

    props: {
        spectra: String,
        projects: String,
        compounds: String,
        embargoed_projects: String,
        techniques: String,
    },

    setup() {
        return {
            Search,
            features,
        };
    },

    data() {
        return {
            schema: {},
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
                container.scrollBy({ left: -330, behavior: 'smooth' });
                setTimeout(() => this.updateScrollButtons(), 300);
            }
        },
        scrollRight() {
            const container = this.$refs.scrollContainer;
            if (container) {
                container.scrollBy({ left: 330, behavior: 'smooth' });
                setTimeout(() => this.updateScrollButtons(), 300);
            }
        },
        updateScrollButtons() {
            const container = this.$refs.scrollContainer;
            if (container) {
                this.canScrollLeft = container.scrollLeft > 0;
                this.canScrollRight = 
                    container.scrollLeft < (container.scrollWidth - container.clientWidth - 10);
            }
        },
    },

    mounted() {
        axios.get(route("bioschemas.datacatalog")).then((response) => {
            this.schema = response.data;
        });
        
        // Setup scroll listener
        this.$nextTick(() => {
            const container = this.$refs.scrollContainer;
            if (container) {
                container.addEventListener('scroll', this.updateScrollButtons);
                this.updateScrollButtons();
            }
        });
    },

    beforeUnmount() {
        const container = this.$refs.scrollContainer;
        if (container) {
            container.removeEventListener('scroll', this.updateScrollButtons);
        }
    },
};
</script>

<style scoped>
/* Custom brand color */
.text-brand {
    color: #FD0039;
}

.text-brand-hover:hover {
    color: #D4002F;
}

.ring-brand {
    --tw-ring-color: #FD0039;
}

/* Hide scrollbar for Chrome, Safari and Opera */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

/* Hide scrollbar for IE, Edge and Firefox */
.scrollbar-hide {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}

/* Retailstack-inspired blob animations */
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

/* Apple-inspired gradient animations */
@keyframes gradient {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0.6;
    }
    100% {
        opacity: 1;
    }
}

@keyframes gradient-reverse {
    0% {
        opacity: 0.5;
    }
    50% {
        opacity: 0.8;
    }
    100% {
        opacity: 0.5;
    }
}

@keyframes fade-in {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-gradient {
    animation: gradient 8s ease-in-out infinite;
}

.animate-gradient-reverse {
    animation: gradient-reverse 8s ease-in-out infinite;
}

.animate-fade-in {
    animation: fade-in 1s ease-out;
}

.animate-fade-in-up {
    animation: fade-in-up 1s ease-out 0.3s both;
}

/* Highlight word animation with subtle color changes */
.highlight-word {
    position: relative;
    display: inline-block;
    z-index: 1;
}

.highlight-word::before {
    content: '';
    position: absolute;
    left: -6px;
    right: -6px;
    top: 15%;
    bottom: 15%;
    background: linear-gradient(90deg, 
        rgba(147, 197, 253, 0) 0%,
        rgba(147, 197, 253, 0.3) 10%,
        rgba(147, 197, 253, 0.3) 90%,
        rgba(147, 197, 253, 0) 100%
    );
    border-radius: 4px;
    z-index: -1;
    transform-origin: left center;
    animation: highlight-sweep 3s ease-in-out infinite, color-change 20s ease-in-out infinite;
}

.highlight-word-delay::before {
    animation-delay: 1.5s, 0s;
}

@keyframes highlight-sweep {
    0% {
        opacity: 0;
        transform: scaleX(0);
    }
    15% {
        opacity: 1;
        transform: scaleX(1);
    }
    85% {
        opacity: 1;
        transform: scaleX(1);
    }
    100% {
        opacity: 0;
        transform: scaleX(0);
    }
}

@keyframes color-change {
    0%, 100% {
        background: linear-gradient(90deg, 
            rgba(147, 197, 253, 0) 0%,
            rgba(147, 197, 253, 0.3) 10%,
            rgba(147, 197, 253, 0.3) 90%,
            rgba(147, 197, 253, 0) 100%
        ); /* Soft Blue */
    }
    25% {
        background: linear-gradient(90deg, 
            rgba(196, 181, 253, 0) 0%,
            rgba(196, 181, 253, 0.3) 10%,
            rgba(196, 181, 253, 0.3) 90%,
            rgba(196, 181, 253, 0) 100%
        ); /* Soft Lavender */
    }
    50% {
        background: linear-gradient(90deg, 
            rgba(167, 243, 208, 0) 0%,
            rgba(167, 243, 208, 0.3) 10%,
            rgba(167, 243, 208, 0.3) 90%,
            rgba(167, 243, 208, 0) 100%
        ); /* Soft Mint */
    }
    75% {
        background: linear-gradient(90deg, 
            rgba(253, 230, 138, 0) 0%,
            rgba(253, 230, 138, 0.3) 10%,
            rgba(253, 230, 138, 0.3) 90%,
            rgba(253, 230, 138, 0) 100%
        ); /* Soft Yellow */
    }
}

/* Cascade rotation animations - cards cycle through positions */
@keyframes cascade-cycle {
    0% {
        transform: translate(-24px, -24px) rotate(-3deg) scale(0.95);
        opacity: 0.6;
        z-index: 10;
    }
    25% {
        transform: translate(-12px, -12px) rotate(-1deg) scale(0.97);
        opacity: 0.75;
        z-index: 20;
    }
    50% {
        transform: translate(8px, 8px) rotate(1deg) scale(0.99);
        opacity: 0.9;
        z-index: 30;
    }
    75% {
        transform: translate(16px, 16px) rotate(0deg) scale(1);
        opacity: 1;
        z-index: 40;
    }
    100% {
        transform: translate(-24px, -24px) rotate(-3deg) scale(0.95);
        opacity: 0.6;
        z-index: 10;
    }
}

.animate-cascade-rotate-1 {
    animation: cascade-cycle 12s ease-in-out infinite;
}

.animate-cascade-rotate-2 {
    animation: cascade-cycle 12s ease-in-out infinite 3s;
}

.animate-cascade-rotate-3 {
    animation: cascade-cycle 12s ease-in-out infinite 6s;
}

.animate-cascade-rotate-4 {
    animation: cascade-cycle 12s ease-in-out infinite 9s;
}
</style>
