<template>
    <div class="bg-white">
        <Head title="Welcome to nmrXiv"></Head>
        <FlashMessages />
        <main>
            <div class="relative index_beams">
                <header>
                    <Popover class="relative">
                        <div
                            class="flex justify-between items-center mx-auto px-4 py-6 sm:px-6 md:justify-start md:space-x-10 lg:px-8"
                        >
                            <div class="flex justify-start lg:w-0 lg:flex-1">
                                <Link :href="'/'">
                                    <jet-application-logo
                                        class="block h-10 p-0.5 ml-1.5 w-auto"
                                    />
                                </Link>
                            </div>
                            <div class="-mr-2 -my-2 md:hidden">
                                <PopoverButton
                                    class="rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-teal-500"
                                >
                                    <span class="sr-only">Open menu</span>
                                    <Bars3Icon
                                        class="h-6 w-6"
                                        aria-hidden="true"
                                    />
                                </PopoverButton>
                            </div>
                            <PopoverGroup
                                as="nav"
                                class="hidden md:flex space-x-10"
                            >
                                <Link
                                    href="/projects"
                                    class="text-base font-medium text-gray-500 hover:text-gray-900"
                                >
                                    Projects
                                </Link>
                                <Link
                                    href="/spectra"
                                    class="text-base font-medium text-gray-500 hover:text-gray-900"
                                >
                                    Spectra
                                </Link>
                                <Link
                                    href="/compounds"
                                    class="text-base font-medium text-gray-500 hover:text-gray-900"
                                >
                                    Compounds
                                </Link>
                            </PopoverGroup>
                            <!-- <div
                            
                            class="ml-5 tooltip"
                        >
                            
                        </div> -->
                            <div
                                v-if="$page.props.user.first_name != null"
                                class="hidden md:flex items-center justify-end md:flex-1 lg:w-0"
                            >
                                <Link
                                    href="/dashboard"
                                    class="ml-8 whitespace-nowrap inline-flex items-center justify-center bg-gradient-to-r from-indigo-600 to-teal-600 bg-origin-border px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white hover:from-indigo-700 hover:to-teal-700"
                                >
                                    Dashboard
                                </Link>
                            </div>
                            <div
                                v-else
                                class="hidden md:flex items-center justify-end md:flex-1 lg:w-0"
                            >
                                <Link
                                    href="/login"
                                    class="whitespace-nowrap text-base font-medium text-gray-500 hover:text-gray-900"
                                >
                                    Login
                                </Link>
                                <Link
                                    href="/register"
                                    class="ml-8 whitespace-nowrap inline-flex items-center justify-center bg-gradient-to-r from-indigo-600 to-teal-600 bg-origin-border px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white hover:from-indigo-700 hover:to-teal-700"
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
                            <div>
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
                                                        class="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-teal-500"
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
                                                            class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-md bg-gradient-to-r from-indigo-600 to-teal-600 text-white"
                                                        >
                                                            <component
                                                                :is="item.icon"
                                                                class="h-6 w-6"
                                                                aria-hidden="true"
                                                            />
                                                        </div>
                                                        <div
                                                            class="ml-4 text-base font-medium text-gray-900"
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
                                                    class="text-base font-medium text-gray-900 hover:text-gray-700"
                                                >
                                                    Projects
                                                </Link>
                                                <Link
                                                    href="/datasets"
                                                    class="text-base font-medium text-gray-900 hover:text-gray-700"
                                                >
                                                    Datasets
                                                </Link>
                                                <Link
                                                    href="/compounds"
                                                    class="text-base font-medium text-gray-900 hover:text-gray-700"
                                                >
                                                    Compounds
                                                </Link>
                                            </div>
                                            <div class="mt-6">
                                                <Link
                                                    href="/login"
                                                    class="w-full flex items-center justify-center bg-gradient-to-r from-indigo-600 to-teal-600 bg-origin-border px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white hover:from-indigo-700 hover:to-teal-700"
                                                >
                                                    Login
                                                </Link>
                                                <p
                                                    class="mt-6 text-center text-base font-medium text-gray-500"
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
                <div class="mx-auto sm:px-6 lg:px-8">
                    <div class="relative sm:rounded-2xl">
                        <div
                            class="relative px-4 py-8 sm:px-6 sm:py-12 lg:py-12 lg:px-8"
                        >
                            <h1
                                class="text-center text-2xl font-extrabold tracking-tight sm:text-5xl lg:text-4xl"
                            >
                                <span class="block text-teal-900"
                                    >Open, FAIR and Consensus-Driven</span
                                >
                                <span class="block text-teal-200"
                                    >NMR spectroscopy data repository and
                                    analysis platform.</span
                                >
                            </h1>
                            <p
                                class="mt-6 max-w-lg mx-auto text-center text-xl text-dark-200 sm:max-w-3xl"
                            >
                                We archive raw and processed NMR data, providing
                                support for browsing, search, analysis, and
                                dissemination of NMR data worldwide.
                            </p>
                            <div
                                class="mt-10 max-w-sm mx-auto sm:max-w-none sm:flex sm:justify-center"
                            >
                                <div
                                    class="space-y-4 sm:space-y-0 sm:mx-auto sm:inline-grid sm:grid-cols-2 sm:gap-5"
                                >
                                    <Link
                                        href="/register"
                                        class="flex items-center justify-center px-4 py-3 border border-1 text-base font-medium rounded-md shadow-sm text-teal-700 bg-white hover:bg-teal-50 sm:px-8"
                                    >
                                        Submit data
                                        <ToolTip
                                            text="To submit data you will need an account with nmrXiv, so you will be redirected to our register page and once registered you can then go ahead and submit data. For more information please checkout our <a target='_blank' href='//docs.nmrxiv.org' class='text-gray-400' target='_blank'>documentation</a>."
                                        ></ToolTip>
                                    </Link>
                                    <Link
                                        href="/projects"
                                        class="flex items-center justify-center px-4 py-3 border border-1 text-base font-medium rounded-md shadow-sm text-white bg-teal-500 sm:px-8"
                                    >
                                        Browse data
                                    </Link>
                                </div>
                            </div>
                            <div class="flex justify-center">
                                <div
                                    class="mt-8 flex flex-wrap justify-center gap-x-1 gap-y-3 sm:gap-x-2 lg:justify-start"
                                >
                                    <a
                                        class="flex-none group relative isolate flex items-center rounded-lg px-2 py-0.5 text-[0.8125rem]/6 font-medium text-dark/30 transition-colors hover:text-sky-300 gap-x-3"
                                        href="https://docs.nmrxiv.org"
                                        target="_blank"
                                        ><span
                                            class="absolute inset-0 -z-10 scale-75 rounded-lg bg-white/5 opacity-0 transition group-hover:scale-100 group-hover:opacity-100"
                                        ></span
                                        ><svg
                                            viewBox="0 0 16 16"
                                            aria-hidden="true"
                                            fill="currentColor"
                                            class="h-4 w-4 flex-none"
                                        >
                                            <path
                                                d="M7 3.41a1 1 0 0 0-.668-.943L2.275 1.039a.987.987 0 0 0-.877.166c-.25.192-.398.493-.398.812V12.2c0 .454.296.853.725.977l3.948 1.365A1 1 0 0 0 7 13.596V3.41ZM9 13.596a1 1 0 0 0 1.327.946l3.948-1.365c.429-.124.725-.523.725-.977V2.017c0-.32-.147-.62-.398-.812a.987.987 0 0 0-.877-.166L9.668 2.467A1 1 0 0 0 9 3.41v10.186Z"
                                            ></path></svg
                                        ><span class="self-baseline text-dark"
                                            >Documentation</span
                                        ></a
                                    ><a
                                        class="flex-none group relative isolate flex items-center rounded-lg px-2 py-0.5 text-[0.8125rem]/6 font-medium text-dark/30 transition-colors hover:text-sky-300 gap-x-3"
                                        href="https://github.com/NFDI4Chem/nmrxiv"
                                        ><span
                                            class="absolute inset-0 -z-10 scale-75 rounded-lg bg-white/5 opacity-0 transition group-hover:scale-100 group-hover:opacity-100"
                                        ></span
                                        ><svg
                                            viewBox="0 0 16 16"
                                            aria-hidden="true"
                                            fill="currentColor"
                                            class="h-4 w-4 flex-none"
                                        >
                                            <path
                                                d="M8 .198a8 8 0 0 0-8 8 7.999 7.999 0 0 0 5.47 7.59c.4.076.547-.172.547-.384 0-.19-.007-.694-.01-1.36-2.226.482-2.695-1.074-2.695-1.074-.364-.923-.89-1.17-.89-1.17-.725-.496.056-.486.056-.486.803.056 1.225.824 1.225.824.714 1.224 1.873.87 2.33.666.072-.518.278-.87.507-1.07-1.777-.2-3.644-.888-3.644-3.954 0-.873.31-1.586.823-2.146-.09-.202-.36-1.016.07-2.118 0 0 .67-.214 2.2.82a7.67 7.67 0 0 1 2-.27 7.67 7.67 0 0 1 2 .27c1.52-1.034 2.19-.82 2.19-.82.43 1.102.16 1.916.08 2.118.51.56.82 1.273.82 2.146 0 3.074-1.87 3.75-3.65 3.947.28.24.54.73.54 1.48 0 1.07-.01 1.93-.01 2.19 0 .21.14.46.55.38A7.972 7.972 0 0 0 16 8.199a8 8 0 0 0-8-8Z"
                                            ></path></svg
                                        ><span class="self-baseline text-dark"
                                            >GitHub</span
                                        ></a
                                    ><a
                                        class="flex-none group relative isolate flex items-center rounded-lg px-2 py-0.5 text-[0.8125rem]/6 font-medium text-dark/30 transition-colors hover:text-sky-300 gap-x-3"
                                        href="https://nmrxiv.org/api/documentation"
                                        target="_blank"
                                        ><span
                                            class="absolute inset-0 -z-10 scale-75 rounded-lg bg-white/5 opacity-0 transition group-hover:scale-100 group-hover:opacity-100"
                                        ></span>

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="2"
                                            stroke="currentColor"
                                            class="w-6 h-6"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5"
                                            />
                                        </svg>
                                        <span class="self-baseline text-dark"
                                            >API</span
                                        ></a
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative bg-transparent">
                <div class="absolute inset-x-0 top-0 overflow-hidden pl-[50%]">
                    <img
                        src="/img/beams-basic.png"
                        alt=""
                        class="-ml-[39rem] w-[113.125rem] max-w-none"
                    />
                </div>
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div
                        class="relative pt-12 sm:p-24 xl:col-start-1 border-b border-slate-100"
                    >
                        <h2
                            class="text-sm font-semibold tracking-wide uppercase"
                        >
                            <span class="bg-clip-text text-gray-600"
                                >Metrics</span
                            >
                        </h2>
                        <p class="mt-3 text-xl font-bold">
                            Validate your spectral findings and develop
                            new/accurate tools using state of the art AI/ML
                            models.
                        </p>
                        <div
                            class="mt-4 grid grid-cols-1 gap-y-12 gap-x-6 sm:grid-cols-2"
                        >
                            <p>
                                <span class="block text-2xl font-bold"
                                    >{{ projects }} Projects</span
                                >
                            </p>
                            <p>
                                <span class="block text-2xl font-bold"
                                    >{{ compounds }} Compounds</span
                                >
                            </p>
                            <p>
                                <span class="block text-2xl font-bold"
                                    >{{ spectra }} Spectra</span
                                >
                            </p>
                            <!-- <p>
                                <span class="block text-2xl font-bold"
                                    >{{ techniques }} Techniques</span
                                >
                            </p> -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-b">
                <div class="bg-white pb-24 pt-24">
                    <div class="mx-auto max-w-7xl px-6 lg:px-8">
                        <h2
                            class="text-center text-lg font-semibold leading-8 text-gray-900"
                        >
                            Recommended by leading journals as the
                            community-trusted repository for NMR data deposition
                        </h2>
                        <div
                            class="mx-auto mt-10 grid max-w-lg grid-cols-2 md:grid-cols-4 items-center gap-x-8 gap-y-10"
                        >
                            <div
                                class="col-span-1 w-full object-contain lg:col-span-2"
                            >
                                <a
                                    href="https://pubs.acs.org/doi/10.1021/acs.jnatprod.3c00281"
                                    target="_blank"
                                    ><img
                                        src="/img/jnp.png"
                                        alt="Journal of Natural Products"
                                /></a>
                            </div>
                            <div
                                class="col-span-1 w-full object-contain lg:col-span-2"
                            >
                                <a
                                    href="https://onlinelibrary.wiley.com/page/journal/15213773/homepage/notice-to-authors"
                                    target="_blank"
                                    ><img
                                        src="/img/angewandte-chemie.png"
                                        alt="Angewandte Chemie"
                                /></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative pt-16">
                <div>
                    <div
                        class="lg:mx-auto lg:max-w-7xl lg:px-8 lg:grid lg:grid-cols-2 lg:grid-flow-col-dense lg:gap-24 overflow-x-hidden py-10 border-r"
                    >
                        <div
                            class="px-4 max-w-xl mx-auto sm:px-6 lg:py-16 lg:max-w-none lg:mx-0 lg:px-0"
                        >
                            <div>
                                <div>
                                    <span
                                        class="h-12 w-12 rounded-md flex items-center justify-center bg-gradient-to-r from-indigo-600 to-teal-600"
                                    >
                                        <InboxIcon
                                            class="h-6 w-6 text-white"
                                            aria-hidden="true"
                                        />
                                    </span>
                                </div>
                                <div class="mt-6">
                                    <h2
                                        class="text-3xl font-extrabold tracking-tight text-gray-900"
                                    >
                                        Submit spectral data to us
                                    </h2>
                                    <p class="mt-4 text-lg text-gray-500">
                                        nmrXiv offers a ready-to-use NMR data
                                        management platform to host your raw
                                        instrument data and processed files for
                                        free and provides you with tools and
                                        services to analyse data. Additionally,
                                        get community support for any of your
                                        NMR spectra-related queries or provide
                                        one yourself.
                                    </p>
                                    <div class="mt-6">
                                        <Link
                                            href="/login"
                                            class="inline-flex bg-gradient-to-r from-indigo-600 to-teal-600 bg-origin-border px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white hover:from-indigo-700 hover:to-teal-700"
                                        >
                                            Get started
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
                                    src="/img/welcome1.jpg"
                                    alt=""
                                />
                            </div>
                        </div>
                    </div>
                    <div class="my-5 py-10">
                        <div
                            class="lg:mx-auto lg:max-w-7xl px-8 lg:grid lg:grid-cols-1 lg:grid-flow-col-dense lg:gap-24"
                        >
                            <div>
                                <div
                                    class="md:flex md:items-center md:justify-between"
                                >
                                    <h2
                                        class="text-2xl font-extrabold tracking-tight text-gray-900"
                                    >
                                        Trending projects
                                    </h2>
                                    <Link
                                        href="/projects"
                                        class="hidden text-sm font-medium text-indigo-600 hover:text-indigo-500 md:block"
                                        >Explore more<span aria-hidden="true">
                                            &rarr;</span
                                        ></Link
                                    >
                                </div>
                            </div>
                        </div>
                        <div
                            class="lg:mx-auto lg:max-w-7xl px-8 lg:grid lg:grid-cols-1 lg:grid-flow-col-dense lg:gap-24"
                        >
                            <div>
                                <div
                                    class="max-w-7xl mx-auto py-3 px-0 sm:flex sm:items-center"
                                >
                                    <div class="mt-2 sm:mt-0">
                                        <div
                                            class="-m-1 flex flex-wrap items-center"
                                        >
                                            <Link
                                                href="/projects?filter=recent"
                                                class="m-1 inline-flex rounded-full border border-gray-200 items-center py-1.5 pl-3 pr-3 text-sm font-medium bg-white text-gray-900 hover:text-white hover:bg-black"
                                                ><span>Recent</span></Link
                                            >
                                            <Link
                                                href="/projects?filter=trending"
                                                class="m-1 inline-flex rounded-full border border-gray-200 items-center py-1.5 pl-3 pr-3 text-sm font-medium bg-white text-gray-900 hover:text-white hover:bg-black"
                                                ><span>Trending</span></Link
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="lg:mx-auto lg:max-w-7xl lg:px-8 md:grid grid-cols-1 grid-flow-col-dense lg:gap-24 px-4"
                        >
                            <Projects :limit="8"></Projects>
                        </div>
                    </div>
                </div>
                <div class="mt-24">
                    <div
                        class="lg:mx-auto lg:max-w-7xl lg:px-8 lg:grid lg:grid-cols-2 lg:grid-flow-col-dense lg:gap-24 overflow-x-hidden py-10 border-l"
                    >
                        <div
                            class="px-4 max-w-xl mx-auto sm:px-6 lg:py-32 lg:max-w-none lg:mx-0 lg:px-0 lg:col-start-2"
                        >
                            <div>
                                <div>
                                    <span
                                        class="h-12 w-12 rounded-md flex items-center justify-center bg-gradient-to-r from-indigo-600 to-teal-600"
                                    >
                                        <SparklesIcon
                                            class="h-6 w-6 text-white"
                                            aria-hidden="true"
                                        />
                                    </span>
                                </div>
                                <div class="mt-6">
                                    <h2
                                        class="text-3xl font-extrabold tracking-tight text-gray-900"
                                    >
                                        Develop tools with our API
                                    </h2>
                                    <p class="mt-4 text-lg text-gray-500">
                                        Software programmers and data scientists
                                        can leverage nmrXiv API and its rich
                                        data/metadata descriptions to develop
                                        advanced toolsets/software.
                                    </p>
                                    <div class="mt-6">
                                        <a
                                            target="_blank"
                                            href="https://docs.nmrxiv.org/docs/developer-guides/API"
                                            class="inline-flex bg-gradient-to-r from-indigo-600 to-teal-600 bg-origin-border px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white hover:from-indigo-700 hover:to-teal-700"
                                        >
                                            API Documentation
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-12 sm:mt-16 lg:mt-0 lg:col-start-1">
                            <div
                                class="pr-4 -ml-48 sm:pr-6 md:-ml-16 lg:px-0 lg:m-0 lg:relative lg:h-full"
                            >
                                <img
                                    class="w-full rounded-xl shadow-xl ring-1 ring-black ring-opacity-5 lg:absolute lg:right-0 lg:h-full lg:w-auto lg:max-w-none"
                                    src="https://docs.nmrxiv.org/img/postman.png"
                                    alt=""
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-24">
                    <div
                        class="lg:mx-auto lg:max-w-7xl lg:px-8 lg:grid lg:grid-cols-2 lg:grid-flow-col-dense lg:gap-24 overflow-x-hidden py-10 border-r"
                    >
                        <div
                            class="px-4 max-w-xl mx-auto sm:px-6 lg:py-16 lg:max-w-none lg:mx-0 lg:px-0"
                        >
                            <div>
                                <div>
                                    <span
                                        class="h-12 w-12 rounded-md flex items-center justify-center bg-gradient-to-r from-indigo-600 to-teal-600"
                                    >
                                        <InboxIcon
                                            class="h-6 w-6 text-white"
                                            aria-hidden="true"
                                        />
                                    </span>
                                </div>
                                <div class="mt-6">
                                    <h2
                                        class="text-3xl font-extrabold tracking-tight text-gray-900"
                                    >
                                        Review and analyse the spectral
                                        assignments
                                    </h2>
                                    <p class="mt-4 text-lg text-gray-500">
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
                                            class="inline-flex bg-gradient-to-r from-indigo-600 to-teal-600 bg-origin-border px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white hover:from-indigo-700 hover:to-teal-700"
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
                                            class="inline-flex bg-gradient-to-r from-indigo-600 to-teal-600 bg-origin-border px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white hover:from-indigo-700 hover:to-teal-700 mt-4"
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
                </div>
            </div>

            <div>
                <FAQs></FAQs>
            </div>

            <!-- Gradient Feature Section -->
            <div class="bg-gradient-to-r from-indigo-800 to-teal-700">
                <div
                    class="max-w-4xl mx-auto px-4 py-16 sm:px-6 sm:pt-20 sm:pb-24 lg:max-w-7xl lg:pt-24 lg:px-8"
                >
                    <h2
                        class="text-3xl font-extrabold text-white tracking-tight"
                    >
                        Built to be FAIR from ground up
                    </h2>

                    <div
                        class="mt-12 grid grid-cols-1 gap-x-6 gap-y-12 sm:grid-cols-2 lg:mt-16 lg:grid-cols-4 lg:gap-x-8 lg:gap-y-16"
                    >
                        <div v-for="feature in features" :key="feature.name">
                            <div>
                                <span
                                    class="flex items-center justify-center h-12 w-12 rounded-md bg-white bg-opacity-10"
                                >
                                    <component
                                        :is="feature.icon"
                                        class="h-6 w-6 text-white"
                                        aria-hidden="true"
                                    />
                                </span>
                            </div>
                            <div class="mt-6">
                                <h3 class="text-lg font-medium text-white">
                                    {{ feature.name }}
                                </h3>
                                <p class="mt-2 text-base text-indigo-200">
                                    {{ feature.description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 border-t border-b border-gray-100">
                <div
                    class="max-w-7xl mx-auto content-center py-16 px-4 sm:px-6 lg:px-8"
                >
                    <p
                        class="text-center text-sm font-semibold uppercase text-gray-600 tracking-wide"
                    ></p>
                    <div class="mt-6 grid grid-cols-3 gap-8">
                        <div
                            class="col-span-3 md:col-span-1 flex justify-center"
                        >
                            <a
                                target="_blank"
                                href="https://cheminf.uni-jena.de/"
                            >
                                <img
                                    class="h-12"
                                    src="https://www.uni-jena.de/unijenamedia/universitaet/abteilung-hochschulkommunikation/marketing/wort-bildmarke-universitaet-jena.jpg?height=335&width=1000"
                                    alt="FSU Jena"
                                />
                            </a>
                        </div>
                        <div
                            class="col-span-3 md:col-span-1 flex justify-center"
                        >
                            <a target="_blank" href="https://www.nfdi4chem.de/">
                                <img
                                    class="h-12"
                                    src="/img/nmrxiv-logo.png"
                                    alt="NFDI4Chem"
                                />
                            </a>
                        </div>
                        <div
                            class="col-span-3 md:col-span-1 flex justify-center"
                        >
                            <a target="_blank" href="https://www.nmrium.org/">
                                <img
                                    class="h-12"
                                    src="/img/nmrium-logo.png"
                                    alt="NMRium"
                                />
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white">
                <div
                    class="max-w-4xl mx-auto py-16 px-4 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8 lg:flex lg:items-center lg:justify-between"
                >
                    <h2
                        class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-4xl"
                    >
                        <span class="block">Want to submit data?</span>
                        <span
                            class="-mb-1 pb-1 block bg-gradient-to-r from-indigo-600 to-teal-600 bg-clip-text text-transparent"
                            >Get in touch or create an account.</span
                        >
                    </h2>
                    <div
                        class="mt-6 space-y-4 sm:space-y-0 sm:flex sm:space-x-5"
                    >
                        <Link
                            href="/login"
                            class="flex items-center justify-center bg-gradient-to-r from-indigo-600 to-teal-600 bg-origin-border px-4 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white hover:from-indigo-700 hover:to-teal-700"
                        >
                            Get started
                        </Link>
                        <a
                            target="_blank"
                            href="//docs.nmrxiv.org"
                            class="flex items-center justify-center px-4 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-teal-800 bg-teal-50 hover:text-white hover:bg-teal-100"
                        >
                            Learn more
                        </a>
                    </div>
                </div>
            </div>
        </main>

        <footer class="bg-gray-50" aria-labelledby="footer-heading">
            <h2 id="footer-heading" class="sr-only">Footer</h2>
            <div
                class="max-w-7xl mx-auto pt-16 pb-8 px-4 sm:px-6 lg:pt-24 lg:px-8"
            >
                <div class="xl:grid xl:grid-cols-3 xl:gap-8">
                    <div class="mt-12 xl:mt-0 items center content-center">
                        <jet-application-logo class="p-0.5 ml-1.5" />
                    </div>
                    <div class="grid grid-cols-2 gap-8 xl:col-span-2">
                        <div class="md:grid md:grid-cols-2 md:gap-8">
                            <div>
                                <h3
                                    class="text-sm font-semibold text-gray-400 tracking-wider uppercase"
                                >
                                    Quick links
                                </h3>
                                <ul role="list" class="mt-4 space-y-4">
                                    <li
                                        v-for="item in footerNavigation.quicklinks"
                                        :key="item.name"
                                    >
                                        <Link
                                            :href="item.href"
                                            class="text-base text-gray-500 hover:text-gray-900"
                                        >
                                            {{ item.name }}
                                        </Link>
                                    </li>
                                    <li>
                                        <a
                                            target="_blank"
                                            href="https://docs.nmrxiv.org/FAQs.html"
                                            class="text-base text-gray-500 hover:text-gray-900"
                                        >
                                            FAQs
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="mt-12 md:mt-0">
                                <h3
                                    class="text-sm font-semibold text-gray-400 tracking-wider uppercase"
                                >
                                    Support
                                </h3>
                                <ul role="list" class="mt-4 space-y-4">
                                    <li
                                        v-for="item in footerNavigation.support"
                                        :key="item.name"
                                    >
                                        <a
                                            target="_blank"
                                            :href="item.href"
                                            class="text-base text-gray-500 hover:text-gray-900"
                                        >
                                            {{ item.name }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="md:grid md:grid-cols-2 md:gap-8">
                            <div>
                                <h3
                                    class="text-sm font-semibold text-gray-400 tracking-wider uppercase"
                                >
                                    About
                                </h3>
                                <ul role="list" class="mt-4 space-y-4">
                                    <li
                                        v-for="item in footerNavigation.About"
                                        :key="item.name"
                                    >
                                        <a
                                            target="_blank"
                                            :href="item.href"
                                            class="text-base text-gray-500 hover:text-gray-900"
                                        >
                                            {{ item.name }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="mt-12 md:mt-0">
                                <h3
                                    class="text-sm font-semibold text-gray-400 tracking-wider uppercase"
                                >
                                    Legal
                                </h3>
                                <ul role="list" class="mt-4 space-y-4">
                                    <li
                                        v-for="item in footerNavigation.legal"
                                        :key="item.name"
                                    >
                                        <Link
                                            :href="item.href"
                                            class="text-base text-gray-500 hover:text-gray-900"
                                        >
                                            {{ item.name }}
                                        </Link>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="my-12 px-12 border-t border-gray-200 pt-8 md:flex md:items-center md:justify-between lg:mt-16"
                >
                    <div class="flex space-x-6 md:order-2">
                        <a
                            v-for="item in footerNavigation.social"
                            :key="item.name"
                            :href="item.href"
                            class="text-gray-400 hover:text-gray-500"
                        >
                            <span class="sr-only">{{ item.name }}</span>
                            <component
                                :is="item.icon"
                                class="h-6 w-6"
                                aria-hidden="true"
                            />
                        </a>
                    </div>
                    <p class="mt-8 text-base text-gray-400 md:mt-0 md:order-1">
                        &copy; 2023 nmrXiv. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>
    <component :is="'script'" type="application/ld+json">{{
        schema
    }}</component>
</template>

<script>
import { Head, Link } from "@inertiajs/vue3";
import JetApplicationLogo from "@/Jetstream/ApplicationLogo.vue";
import Projects from "@/Shared/Projects.vue";
import { defineComponent, h } from "vue";
import {
    Popover,
    PopoverButton,
    PopoverGroup,
    PopoverPanel,
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
        name: "Advanced search",
        description: "",
        icon: MagnifyingGlassIcon,
    },
    {
        name: "Open Source",
        description: "",
        icon: ScaleIcon,
    },
    {
        name: "Auto Assignments",
        description: "",
        icon: ShareIcon,
    },
    {
        name: "Prediction",
        description: "",
        icon: PencilSquareIcon,
    },
    {
        name: "Schemas and MIChI",
        description: "",
        icon: DocumentTextIcon,
    },
    {
        name: "Community challenges",
        description: "",
        icon: ChatBubbleLeftIcon,
    },
    {
        name: "Docs & API",
        description: "",
        icon: DocumentTextIcon,
    },
    {
        name: "Backups",
        description: "",
        icon: CircleStackIcon,
    },
];
const footerNavigation = {
    // Search: [
    //   { name: "Browse", href: "/projects" },
    //   { name: "Advanced Search", href: "/projects" },
    // ],
    quicklinks: [
        { name: "Projects", href: "/projects" },
        { name: "Spectra", href: "/spectra" },
    ],
    support: [
        { name: "Documentation", href: "https://docs.nmrxiv.org" },
        {
            name: "Guides",
            href: "https://docs.nmrxiv.org/docs/submission-guides/overview",
        },
        {
            name: "API Status",
            href: "https://docs.nmrxiv.org/docs/developer-guides/API",
        },
    ],
    About: [
        {
            name: "Adivsory Board",
            href: "https://docs.nmrxiv.org/docs/contributing/contributors-and-steering-committee",
        },
    ],
    legal: [
        { name: "Privacy", href: "/privacy-policy" },
        { name: "Terms", href: "/terms-of-service" },
    ],
    social: [
        {
            name: "GitHub",
            href: "https://github.com/nfdi4chem/nmrxiv",
            icon: defineComponent({
                render: () =>
                    h("svg", { fill: "currentColor", viewBox: "0 0 24 24" }, [
                        h("path", {
                            "fill-rule": "evenodd",
                            d: "M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z",
                            "clip-rule": "evenodd",
                        }),
                    ]),
            }),
        },
    ],
};

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
        ChevronDownIcon,
        CircleStackIcon,
        ToolTip,
        Projects,
        FAQs,
        FlashMessages,
    },

    props: {
        spectra: Number,
        projects: Number,
        compounds: Number,
        techniques: Number,
    },

    setup() {
        return {
            Search,
            features,
            footerNavigation,
        };
    },

    data() {
        return {
            schema: {},
        };
    },

    mounted() {
        axios.get(route("bioschemas.datacatalog")).then((response) => {
            this.schema = response.data;
        });
    },
};
</script>
