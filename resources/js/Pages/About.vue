<template>
    <div class="bg-white">
        <Head title="Welcome to nmrXiv"></Head>
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
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="relative py-20 sm:py-24 lg:py-32">
                        <div class="overflow-hidden">
                            <div class="mx-auto max-w-6xl pb-8">
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
                    class="mx-auto -mt-12 max-w-6xl px-6 sm:mt-0 lg:px-8 xl:-mt-8"
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

                <!-- Image section -->
                <div class="mt-32 sm:mt-40 xl:mx-auto xl:max-w-6xl xl:px-8">
                    <img
                        src="img/journals.png"
                        class="w-full object-cover xl:rounded-xl border shadow-lg"
                    />
                </div>

                <div class="mx-auto mt-32 max-w-5xl px-6 sm:mt-40 lg:px-8">
                    <dl
                        class="mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 text-base leading-7 sm:grid-cols-2 lg:mx-0 lg:max-w-none lg:grid-cols-3"
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

                <div class="mx-auto mt-32 max-w-5xl px-6 sm:mt-48 lg:px-8">
                    <div class="mx-auto max-w-2xl lg:mx-0">
                        <h2
                            class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl"
                        >
                            Steering Committee
                        </h2>
                    </div>
                    <ul
                        role="list"
                        class="mx-auto mt-20 grid max-w-2xl grid-cols-2 gap-x-8 gap-y-16 text-center sm:grid-cols-3 md:grid-cols-4 lg:mx-0 lg:max-w-none lg:grid-cols-5 xl:grid-cols-6"
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
                <div class="relative isolate -z-10 mt-32 sm:mt-48">
                    <div
                        class="absolute inset-x-0 top-1/2 -z-10 flex -translate-y-1/2 justify-center overflow-hidden [mask-image:radial-gradient(50%_45%_at_50%_55%,white,transparent)]"
                    >
                        <svg
                            class="h-[40rem] w-[80rem] flex-none stroke-gray-200"
                            aria-hidden="true"
                        >
                            <defs>
                                <pattern
                                    id="e9033f3e-f665-41a6-84ef-756f6778e6fe"
                                    width="200"
                                    height="200"
                                    x="50%"
                                    y="50%"
                                    patternUnits="userSpaceOnUse"
                                    patternTransform="translate(-100 0)"
                                >
                                    <path d="M.5 200V.5H200" fill="none" />
                                </pattern>
                            </defs>
                            <svg
                                x="50%"
                                y="50%"
                                class="overflow-visible fill-gray-50"
                            >
                                <path
                                    d="M-300 0h201v201h-201Z M300 200h201v201h-201Z"
                                    stroke-width="0"
                                />
                            </svg>
                            <rect
                                width="100%"
                                height="100%"
                                stroke-width="0"
                                fill="url(#e9033f3e-f665-41a6-84ef-756f6778e6fe)"
                            />
                        </svg>
                    </div>
                    <div class="mx-auto max-w-6xl px-6 lg:px-8">
                        <h2
                            class="text-center text-3xl tracking-tight font-bold leading-8 text-gray-900"
                        >
                            Academic & Other Partners
                        </h2>
                        <div
                            class="mx-auto mt-16 grid max-w-lg grid-cols-2 md:grid-cols-5 items-center gap-x-8 gap-y-10 sm:max-w-xl sm:grid-cols-6 sm:gap-x-10 lg:mx-0 lg:max-w-none lg:grid-cols-5"
                        >
                            <a href="https://cheminf.uni-jena.de/">
                                <img
                                    class="col-span-1 md:col-span-2 max-h-12 w-full object-contain lg:col-span-1"
                                    src="/img/FSU-Jena-logo.jpg"
                                    alt="FSU"
                                    width="158"
                                    height="48"
                                />
                            </a>
                            <a href="https://www.nfdi4chem.de/">
                                <img
                                    class="col-span-2 max-h-12 w-full object-contain lg:col-span-1"
                                    src="/img/nfdi4chem-logo.png"
                                    alt="NFDI4Chem"
                                    width="158"
                                    height="48"
                            /></a>
                            <a href="https://pharmacy.uic.edu/">
                                <img
                                    class="col-span-2 max-h-12 w-full object-contain lg:col-span-1"
                                    src="/img/uic.png"
                                    alt="UIC"
                                    width="158"
                                    height="48"
                            /></a>
                            <a href="https://www.nmrium.org/">
                                <img
                                    class="col-span-2 max-h-12 w-full object-contain sm:col-start-2 lg:col-span-1"
                                    src="/img/nmrium-logo.png"
                                    alt="NMRium"
                                    width="158"
                                    height="48"
                            /></a>
                            <a
                                href="https://ctb.nmrsolutions.fi/login?returnUrl=~dashboard"
                            >
                                <img
                                    class="col-span-2 max-h-12 w-full object-contain sm:col-start-2 lg:col-span-1"
                                    src="/img/ct.png"
                                    alt="CT"
                                    width="158"
                                    height="48"
                            /></a>
                        </div>
                    </div>
                </div>

                <div class="mx-auto my-16 max-w-6xl">&nbsp;</div>
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
import {
    Popover,
    PopoverButton,
    PopoverGroup,
    PopoverPanel,
} from "@headlessui/vue";
import {
    InboxIcon,
    Bars3Icon,
    MagnifyingGlassIcon,
    XMarkIcon,
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

export default {
    components: {
        Head,
        Link,
        JetApplicationLogo,
        InboxIcon,
        Bars3Icon,
        MagnifyingGlassIcon,
        XMarkIcon,
        Popover,
        PopoverButton,
        PopoverGroup,
        PopoverPanel,
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
        };
    },

    data() {
        return {
            schema: null,
        };
    },

    methods: {
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
.ring-brand {
    --tw-ring-color: #fd0039;
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
