<template>
  <div class="bg-white">
    <main>
      <div class="relative index_beams">
        <header>
          <Popover class="relative">
            <div
              class="flex justify-between items-center mx-auto px-4 py-6 sm:px-6 md:justify-start md:space-x-10 lg:px-8"
            >
              <div class="flex justify-start lg:w-0 lg:flex-1">
                <Link :href="'/'">
                  <jet-application-logo class="block h-10 p-0.5 ml-1.5 w-auto" />
                </Link>
              </div>
              <div class="-mr-2 -my-2 md:hidden">
                <PopoverButton
                  class="rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-teal-500"
                >
                  <span class="sr-only">Open menu</span>
                  <MenuIcon class="h-6 w-6" aria-hidden="true" />
                </PopoverButton>
              </div>
              <PopoverGroup as="nav" class="hidden md:flex space-x-10">
                <!-- <Popover class="relative" v-slot="{ open }">
                  <PopoverButton
                    :class="[
                      open ? 'text-gray-900' : 'text-gray-500',
                      'group rounded-md inline-flex items-center text-base font-medium hover:text-gray-900 focus:outline-none',
                    ]"
                  >
                    <span>Search</span>
                    <ChevronDownIcon
                      :class="[
                        open ? 'text-gray-600' : 'text-gray-400',
                        'ml-2 h-5 w-5 group-hover:text-gray-500',
                      ]"
                      aria-hidden="true"
                    />
                  </PopoverButton>

                  <transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 translate-y-1"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 translate-y-1"
                  >
                    <div>
                      <PopoverPanel
                        class="absolute z-10 -ml-4 mt-3 transform w-screen max-w-md lg:max-w-2xl lg:ml-0 lg:left-1/2 lg:-translate-x-1/2"
                      >
                        <div
                          class="rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 overflow-hidden"
                        >
                          <div
                            class="relative grid gap-6 bg-white px-5 py-6 sm:gap-8 sm:p-8 lg:grid-cols-2"
                          >
                            <Link
                              v-for="item in Search"
                              :key="item.name"
                              :href="item.href"
                              class="-m-3 p-3 flex items-start rounded-lg hover:bg-gray-50"
                            >
                              <div
                                class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-md bg-gradient-to-r from-indigo-600 to-teal-600 text-white sm:h-12 sm:w-12"
                              >
                                <component
                                  :is="item.icon"
                                  class="h-6 w-6"
                                  aria-hidden="true"
                                />
                              </div>
                              <div class="ml-4">
                                <p class="text-base font-medium text-gray-900">
                                  {{ item.name }}
                                </p>
                                <p class="mt-1 text-sm text-gray-500">
                                  {{ item.description }}
                                </p>
                              </div>
                            </Link>
                          </div>
                        </div>
                      </PopoverPanel>
                    </div>
                  </transition>
                </Popover> -->
                <Link
                  href="/projects"
                  class="text-base font-medium text-gray-500 hover:text-gray-900"
                >
                  Projects
                </Link>
                <Link
                  href="/datasets"
                  class="text-base font-medium text-gray-500 hover:text-gray-900"
                >
                  Datasets
                </Link>
                <a
                  target="_blank"
                  href="https://docs.nmrxiv.org"
                  class="text-base font-medium text-gray-500 hover:text-gray-900"
                >
                  Docs
                </a>
              </PopoverGroup>
              <div class="hidden md:flex items-center justify-end md:flex-1 lg:w-0">
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
                      <div class="flex items-center justify-between">
                        <div>
                          <jet-application-logo class="block h-10 p-0.5 ml-1.5 w-auto" />
                        </div>
                        <div class="-mr-2">
                          <PopoverButton
                            class="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-teal-500"
                          >
                            <span class="sr-only">Close menu</span>
                            <XIcon class="h-6 w-6" aria-hidden="true" />
                          </PopoverButton>
                        </div>
                      </div>
                      <div class="mt-6">
                        <nav class="grid grid-cols-1 gap-7">
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
                            <div class="ml-4 text-base font-medium text-gray-900">
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
                        <a
                          target="_blank"
                          href="https://docs.nmrxiv.org"
                          class="text-base font-medium text-gray-900 hover:text-gray-700"
                        >
                          Docs
                        </a>
                      </div>
                      <div class="mt-6">
                        <Link
                          href="/login"
                          class="w-full flex items-center justify-center bg-gradient-to-r from-indigo-600 to-teal-600 bg-origin-border px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white hover:from-indigo-700 hover:to-teal-700"
                        >
                          Login
                        </Link>
                        <p class="mt-6 text-center text-base font-medium text-gray-500">
                          <Link href="/register" class="text-gray-900"> Register </Link>
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
            <div class="relative px-4 py-8 sm:px-6 sm:py-12 lg:py-12 lg:px-8">
              <h1
                class="text-center text-2xl font-extrabold tracking-tight sm:text-5xl lg:text-4xl"
              >
                <span class="block text-dark">Open, FAIR and Consensus-Driven</span>
                <span class="block text-teal-200"
                  >NMR spectroscopy data repository and analysis platform.</span
                >
              </h1>
              <p
                class="mt-6 max-w-lg mx-auto text-center text-xl text-dark-200 sm:max-w-3xl"
              >
                We archive raw and processed NMR data, providing support for browsing,
                search, analysis, and dissemination of NMR data worldwide.
              </p>
              <div class="mt-10 max-w-sm mx-auto sm:max-w-none sm:flex sm:justify-center">
                <div
                  class="space-y-4 sm:space-y-0 sm:mx-auto sm:inline-grid sm:grid-cols-2 sm:gap-5"
                >
                  <Link
                    href="/register"
                    class="flex items-center justify-center px-4 py-3 border border-1 text-base font-medium rounded-md shadow-sm text-teal-700 bg-white hover:bg-teal-50 sm:px-8"
                  >
                    Submit data
                    <ToolTip
                      text="To submit data you will need an account with nmrXiv, so you will be redirected to our register page and once registered you can then go ahead and submit data. For more information please checkout our <a href='//docs.nmrxiv.org' class='text-gray-400' target='_blank'>documentation</a>."
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
            </div>
          </div>
        </div>
      </div>

      <!-- Logo Cloud -->
      <div class="bg-gray-50 border-t border-b border-gray-100">
        <div class="max-w-7xl mx-auto content-center py-16 px-4 sm:px-6 lg:px-8">
          <p
            class="text-center text-sm font-semibold uppercase text-gray-600 tracking-wide"
          >
            <!-- Trusted by over 5 very average small businesses -->
          </p>
          <div class="mt-6 grid grid-cols-3 gap-8">
            <div class="col-span-3 md:col-span-1 flex justify-center">
              <a target="_blank" href="https://cheminf.uni-jena.de/">
                <img
                  class="h-12"
                  src="https://www.uni-jena.de/unijenamedia/universitaet/abteilung-hochschulkommunikation/marketing/wort-bildmarke-universitaet-jena.jpg?height=335&width=1000"
                  alt="FSU Jena"
                />
              </a>
            </div>
            <div class="col-span-3 md:col-span-1 flex justify-center">
              <a target="_blank" href="https://www.nfdi4chem.de/">
                <img
                  class="h-12"
                  src="https://www.nfdi4chem.de/wp-content/uploads/2021/11/cropped-NFDI4Chem-Logo-Claim_mehrfarbig_schwarz-e1636478409489.png"
                  alt="NFDI4Chem"
                />
              </a>
            </div>
            <div class="col-span-3 md:col-span-1 flex justify-center">
              <a target="_blank" href="https://www.nmrium.org/">
                <img
                  class="h-12"
                  src="https://www.nmrium.org/brand/nmrium-logo.svg"
                  alt="NMRium"
                />
              </a>
            </div>
          </div>
        </div>
      </div>
      <div class="relative pt-16 pb-32">
        <div>
          <div
            class="lg:mx-auto lg:max-w-7xl lg:px-8 lg:grid lg:grid-cols-2 lg:grid-flow-col-dense lg:gap-24"
          >
            <div
              class="px-4 max-w-xl mx-auto sm:px-6 lg:py-16 lg:max-w-none lg:mx-0 lg:px-0"
            >
              <div>
                <div>
                  <span
                    class="h-12 w-12 rounded-md flex items-center justify-center bg-gradient-to-r from-indigo-600 to-teal-600"
                  >
                    <InboxIcon class="h-6 w-6 text-white" aria-hidden="true" />
                  </span>
                </div>
                <div class="mt-6">
                  <h2 class="text-3xl font-extrabold tracking-tight text-gray-900">
                    Submit spectral data to us
                  </h2>
                  <p class="mt-4 text-lg text-gray-500">
                    nmrXiv offers you ready to use NMR data management platform to host
                    your raw instrument data and processed files for free and also
                    provides you with tools and services to analyse data. Added to that
                    get community support for any of your NMR spectra related queries or
                    provide one yourself.
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
                  src="/img/s1.jpg"
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
                <div class="md:flex md:items-center md:justify-between">
                  <h2 class="text-2xl font-extrabold tracking-tight text-gray-900">
                    Trending projects
                  </h2>
                  <Link
                    href="/projects"
                    class="hidden text-sm font-medium text-indigo-600 hover:text-indigo-500 md:block"
                    >Explore more<span aria-hidden="true"> &rarr;</span></Link>
                </div>
              </div>
            </div>
            <div
              class="lg:mx-auto lg:max-w-7xl px-8 lg:grid lg:grid-cols-1 lg:grid-flow-col-dense lg:gap-24"
            >
              <div>
                <div class="max-w-7xl mx-auto py-3 px-0 sm:flex sm:items-center">
                  <div class="mt-2 sm:mt-0">
                    <div class="-m-1 flex flex-wrap items-center">
                      <Link
                        href="/projects?filter=recent"
                        class="m-1 inline-flex rounded-full border border-gray-200 items-center py-1.5 pl-3 pr-3 text-sm font-medium bg-white text-gray-900 hover:text-white hover:bg-black"
                        ><span>Recent</span></Link>
                      <Link
                        href="/projects?filter=trending"
                        class="m-1 inline-flex rounded-full border border-gray-200 items-center py-1.5 pl-3 pr-3 text-sm font-medium bg-white text-gray-900 hover:text-white hover:bg-black"
                        ><span>Trending</span></Link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div
              class="lg:mx-auto lg:max-w-7xl lg:px-8 md:grid grid-cols-1 grid-flow-col-dense lg:gap-24 px-4"
            >
              <Projects></Projects>
            </div>
          </div>
        </div>
        <div class="mt-24">
          <div
            class="lg:mx-auto lg:max-w-7xl lg:px-8 lg:grid lg:grid-cols-2 lg:grid-flow-col-dense lg:gap-24"
          >
            <div
              class="px-4 max-w-xl mx-auto sm:px-6 lg:py-32 lg:max-w-none lg:mx-0 lg:px-0 lg:col-start-2"
            >
              <div>
                <div>
                  <span
                    class="h-12 w-12 rounded-md flex items-center justify-center bg-gradient-to-r from-indigo-600 to-teal-600"
                  >
                    <SparklesIcon class="h-6 w-6 text-white" aria-hidden="true" />
                  </span>
                </div>
                <div class="mt-6">
                  <h2 class="text-3xl font-extrabold tracking-tight text-gray-900">
                    Develop tools with our API
                  </h2>
                  <p class="mt-4 text-lg text-gray-500">
                    Software programmers and data scientists can leverage nmrXiv API and
                    its rich data / metadata descriptions to develop advanced tool
                    set/software.
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
            class="lg:mx-auto lg:max-w-7xl lg:px-8 lg:grid lg:grid-cols-2 lg:grid-flow-col-dense lg:gap-24"
          >
            <div
              class="px-4 max-w-xl mx-auto sm:px-6 lg:py-16 lg:max-w-none lg:mx-0 lg:px-0"
            >
              <div>
                <div>
                  <span
                    class="h-12 w-12 rounded-md flex items-center justify-center bg-gradient-to-r from-indigo-600 to-teal-600"
                  >
                    <InboxIcon class="h-6 w-6 text-white" aria-hidden="true" />
                  </span>
                </div>
                <div class="mt-6">
                  <h2 class="text-3xl font-extrabold tracking-tight text-gray-900">
                    Review and analyse the spectral assignments
                  </h2>
                  <p class="mt-4 text-lg text-gray-500">
                    Data deposited in nmrXiv requires original machine output files. With
                    this original data, researchers can annotate missing assignments in
                    the spectra and also reanalyse previous work to offer additional help.
                    In a way sharing the knowledge and expertise to the next generation of
                    spectroscopic analysts.
                  </p>
                  <div class="mt-6">
                    <Link
                      href="#"
                      class="inline-flex bg-gradient-to-r from-indigo-600 to-teal-600 bg-origin-border px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white hover:from-indigo-700 hover:to-teal-700"
                    >
                      Need help with structure ellucidation?&emsp;<i><small> (coming soon)</small></i></Link><br />
                    <Link
                      href="#"
                      class="inline-flex bg-gradient-to-r from-indigo-600 to-teal-600 bg-origin-border px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white hover:from-indigo-700 hover:to-teal-700 mt-4"
                    >
                      Check out active challenges&emsp;<i><small> (coming soon)</small></i> 
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
                  src="/img/s1.jpg"
                  alt=""
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Gradient Feature Section -->
      <div class="bg-gradient-to-r from-indigo-800 to-teal-700">
        <div
          class="max-w-4xl mx-auto px-4 py-16 sm:px-6 sm:pt-20 sm:pb-24 lg:max-w-7xl lg:pt-24 lg:px-8"
        >
          <h2 class="text-3xl font-extrabold text-white tracking-tight">
            Built to be FAIR from ground up
          </h2>
          <!-- <p class="mt-4 max-w-3xl text-lg text-indigo-200">
            Ac tincidunt sapien vehicula erat auctor pellentesque rhoncus. Et magna sit
            morbi lobortis. Blandit aliquam sit nisl euismod mattis in.
          </p> -->
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
                <h3 class="text-lg font-medium text-white">{{ feature.name }}</h3>
                <p class="mt-2 text-base text-indigo-200">
                  {{ feature.description }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats section -->
      <div class="relative bg-gray-900">
        <div class="h-80 absolute inset-x-0 bottom-0 xl:top-0 xl:h-full">
          <div class="h-full w-full xl:grid xl:grid-cols-2">
            <div class="h-full xl:relative xl:col-start-2">
              <img
                class="h-full w-full object-cover opacity-25 xl:absolute xl:inset-0"
                src="https://images.unsplash.com/photo-1521737852567-6949f3f9f2b5?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=2830&q=80&sat=-100"
                alt="People working on laptops"
              />
              <div
                aria-hidden="true"
                class="absolute inset-x-0 top-0 h-32 bg-gradient-to-b from-gray-900 xl:inset-y-0 xl:left-0 xl:h-full xl:w-32 xl:bg-gradient-to-r"
              />
            </div>
          </div>
        </div>
        <div
          class="max-w-4xl mx-auto px-4 sm:px-6 lg:max-w-7xl lg:px-8 xl:grid xl:grid-cols-2 xl:grid-flow-col-dense xl:gap-x-8"
        >
          <div class="relative pt-12 pb-64 sm:pt-24 sm:pb-64 xl:col-start-1 xl:pb-24">
            <h2 class="text-sm font-semibold tracking-wide uppercase">
              <span
                class="bg-gradient-to-r from-indigo-300 to-teal-300 bg-clip-text text-transparent"
                >Valuable Metrics</span
              >
            </h2>
            <p class="mt-3 text-3xl font-extrabold text-white">
              Get actionable data that will help develop new and accurate tools using
              state of the art AI/ML models.
            </p>
            <!-- <p class="mt-5 text-lg text-gray-300">
              Rhoncus sagittis risus arcu erat lectus bibendum. Ut in adipiscing quis in
              viverra tristique sem. Ornare feugiat viverra eleifend fusce orci in quis
              amet. Sit in et vitae tortor, massa. Dapibus laoreet amet lacus nibh integer
              quis. Eu vulputate diam sit tellus quis at.
            </p> -->
            <div class="mt-12 grid grid-cols-1 gap-y-12 gap-x-6 sm:grid-cols-2">
              <p v-for="item in metrics" :key="item.id">
                <span class="block text-2xl font-bold text-white">{{ item.stat }}</span>
                <span class="mt-1 block text-base text-gray-300"
                  ><span class="font-medium text-white">{{ item.emphasis }}</span>
                  {{ item.rest }}</span
                >
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- CTA Section -->
      <div class="bg-white">
        <div
          class="max-w-4xl mx-auto py-16 px-4 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8 lg:flex lg:items-center lg:justify-between"
        >
          <h2 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">
            <span class="block">Want to submit data?</span>
            <span
              class="-mb-1 pb-1 block bg-gradient-to-r from-indigo-600 to-teal-600 bg-clip-text text-transparent"
              >Get in touch or create an account.</span
            >
          </h2>
          <div class="mt-6 space-y-4 sm:space-y-0 sm:flex sm:space-x-5">
            <Link
              href="/login"
              class="flex items-center justify-center bg-gradient-to-r from-indigo-600 to-teal-600 bg-origin-border px-4 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white hover:from-indigo-700 hover:to-teal-700"
            >
              Get started
            </Link>
            <a
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
      <div class="max-w-7xl mx-auto pt-16 pb-8 px-4 sm:px-6 lg:pt-24 lg:px-8">
        <div class="xl:grid xl:grid-cols-3 xl:gap-8">
          <div class="grid grid-cols-2 gap-8 xl:col-span-2">
            <div class="md:grid md:grid-cols-2 md:gap-8">
              <!-- <div>
                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                  Search
                </h3>
                <ul role="list" class="mt-4 space-y-4">
                  <li v-for="item in footerNavigation.Search" :key="item.name">
                    <Link
                      :href="item.href"
                      class="text-base text-gray-500 hover:text-gray-900"
                    >
                      {{ item.name }}
                    </Link>
                  </li>
                </ul>
              </div> -->
              <div>
                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                  Quick links
                </h3>
                <ul role="list" class="mt-4 space-y-4">
                  <li>
                    <Link
                      href="/projects"
                      class="text-base text-gray-500 hover:text-gray-900"
                    >
                      Projects
                    </Link>
                  </li>
                  <li>
                    <Link
                      href="/datasets"
                      class="text-base text-gray-500 hover:text-gray-900"
                    >
                      Datasets
                    </Link>
                  </li>
                </ul>
              </div>
              <div class="mt-12 md:mt-0">
                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                  Support
                </h3>
                <ul role="list" class="mt-4 space-y-4">
                  <li v-for="item in footerNavigation.support" :key="item.name">
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
            <div class="md:grid md:grid-cols-2 md:gap-8">
              <div>
                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                  About
                </h3>
                <ul role="list" class="mt-4 space-y-4">
                  <li v-for="item in footerNavigation.About" :key="item.name">
                    <Link
                      :href="item.href"
                      class="text-base text-gray-500 hover:text-gray-900"
                    >
                      {{ item.name }}
                    </Link>
                  </li>
                </ul>
              </div>
              <div class="mt-12 md:mt-0">
                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                  Legal
                </h3>
                <ul role="list" class="mt-4 space-y-4">
                  <li v-for="item in footerNavigation.legal" :key="item.name">
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
          <div class="mt-12 xl:mt-0">
            <jet-application-logo class="block h-10 p-0.5 ml-1.5 w-auto" />
            <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase"></h3>
            <!-- <p class="mt-4 text-base text-gray-500">
              The latest news, articles, and resources, sent to your inbox weekly.
            </p> -->
          </div>
          <!-- <div class="mt-12 xl:mt-0">
            <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
              Subscribe to our newsletter
            </h3>
            <p class="mt-4 text-base text-gray-500">
              The latest news, articles, and resources, sent to your inbox weekly.
            </p>
            <form class="mt-4 sm:flex sm:max-w-md">
              <label for="email-address" class="sr-only">Email address</label>
              <input
                type="email"
                name="email-address"
                id="email-address"
                autocomplete="email"
                required=""
                class="appearance-none min-w-0 w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-4 text-base text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-teal-500 focus:border-teal-500 focus:placeholder-gray-400"
                placeholder="Enter your email"
              />
              <div class="mt-3 rounded-md sm:mt-0 sm:ml-3 sm:flex-shrink-0">
                <button
                  type="submit"
                  class="w-full flex items-center justify-center bg-gradient-to-r from-indigo-600 to-teal-600 bg-origin-border px-4 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white hover:from-indigo-700 hover:to-teal-700"
                >
                  Subscribe
                </button>
              </div>
            </form>
          </div> -->
        </div>
        <div
          class="mt-12 border-t border-gray-200 pt-8 md:flex md:items-center md:justify-between lg:mt-16"
        >
          <div class="flex space-x-6 md:order-2">
            <Link
              v-for="item in footerNavigation.social"
              :key="item.name"
              :href="item.href"
              class="text-gray-400 hover:text-gray-500"
            >
              <span class="sr-only">{{ item.name }}</span>
              <component :is="item.icon" class="h-6 w-6" aria-hidden="true" />
            </Link>
          </div>
          <p class="mt-8 text-base text-gray-400 md:mt-0 md:order-1">
            &copy; 2022 nmrXiv, Inc. All rights reserved.
          </p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import { Head, Link } from "@inertiajs/inertia-vue3";
import JetApplicationLogo from "@/Jetstream/ApplicationLogo.vue";
import Projects from "@/Shared/Projects.vue";
import { defineComponent, h } from "vue";
import { Popover, PopoverButton, PopoverGroup, PopoverPanel } from "@headlessui/vue";
import {
  ShareIcon,
  ScaleIcon,
  AnnotationIcon,
  ChatAlt2Icon,
  ChatAltIcon,
  DocumentReportIcon,
  HeartIcon,
  InboxIcon,
  MenuIcon,
  PencilAltIcon,
  SearchIcon,
  ReplyIcon,
  SparklesIcon,
  TrashIcon,
  UsersIcon,
  XIcon,
  DatabaseIcon,
  DocumentTextIcon,
} from "@heroicons/vue/outline";
import { ChevronDownIcon } from "@heroicons/vue/solid";
import ToolTip from "@/Shared/ToolTip.vue";

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
    icon: SearchIcon,
  },
];
const features = [
  {
    name: "Advanced search",
    description: "",
    icon: SearchIcon,
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
    icon: PencilAltIcon,
  },
  {
    name: "Schemas and MIChI",
    description: "",
    icon: DocumentReportIcon,
  },
  {
    name: "Community challenges",
    description: "",
    icon: ChatAltIcon,
  },
  {
    name: "Docs & API",
    description: "",
    icon: DocumentTextIcon,
  },
  {
    name: "Backups",
    description: "",
    icon: DatabaseIcon,
  },
];
const metrics = [
  {
    id: 1,
    stat: "XXXXX Spectra Datasets",
    emphasis: "",
    rest: "",
  },
  {
    id: 2,
    stat: "XX Techniques Covered",
    emphasis: "",
    rest: "",
  },
  {
    id: 3,
    stat: "XX Active Challenges",
    emphasis: "",
    rest: "",
  },
  {
    id: 4,
    stat: "XXXX Users",
    emphasis: "",
    rest: "",
  },
];
const footerNavigation = {
  // Search: [
  //   { name: "Browse", href: "/projects" },
  //   { name: "Advanced Search", href: "/projects" },
  // ],
  support: [
    { name: "Documentation", href: "https://docs.nmrxiv.org" },
    { name: "Guides", href: "https://docs.nmrxiv.org/docs/submission-guides/overview" },
    { name: "API Status", href: "https://docs.nmrxiv.org/docs/developer-guides/API" },
  ],
  About: [
    {
      name: "Adivsory Board",
      href:
        "https://docs.nmrxiv.org/docs/contributing/contributors-and-steering-committee",
    },
    // { name: "Metrics", href: "#" },
    // { name: "Blog", href: "#" },
    // { name: "Press", href: "#" },
  ],
  legal: [
    { name: "Privacy", href: "/privacy-policy" },
    { name: "Terms", href: "/terms-of-service" },
  ],
  social: [
    // {
    //   name: "Facebook",
    //   href: "#",
    //   icon: defineComponent({
    //     render: () =>
    //       h("svg", { fill: "currentColor", viewBox: "0 0 24 24" }, [
    //         h("path", {
    //           "fill-rule": "evenodd",
    //           d:
    //             "M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z",
    //           "clip-rule": "evenodd",
    //         }),
    //       ]),
    //   }),
    // },
    // {
    //   name: "Instagram",
    //   href: "#",
    //   icon: defineComponent({
    //     render: () =>
    //       h("svg", { fill: "currentColor", viewBox: "0 0 24 24" }, [
    //         h("path", {
    //           "fill-rule": "evenodd",
    //           d:
    //             "M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z",
    //           "clip-rule": "evenodd",
    //         }),
    //       ]),
    //   }),
    // },
    // {
    //   name: "Twitter",
    //   href: "#",
    //   icon: defineComponent({
    //     render: () =>
    //       h("svg", { fill: "currentColor", viewBox: "0 0 24 24" }, [
    //         h("path", {
    //           d:
    //             "M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84",
    //         }),
    //       ]),
    //   }),
    // },
    {
      name: "GitHub",
      href: "https://github.com/nfdi4chem/nmrxiv",
      icon: defineComponent({
        render: () =>
          h("svg", { fill: "currentColor", viewBox: "0 0 24 24" }, [
            h("path", {
              "fill-rule": "evenodd",
              d:
                "M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z",
              "clip-rule": "evenodd",
            }),
          ]),
      }),
    },
    // {
    //   name: "Dribbble",
    //   href: "#",
    //   icon: defineComponent({
    //     render: () =>
    //       h("svg", { fill: "currentColor", viewBox: "0 0 24 24" }, [
    //         h("path", {
    //           "fill-rule": "evenodd",
    //           d:
    //             "M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10c5.51 0 10-4.48 10-10S17.51 2 12 2zm6.605 4.61a8.502 8.502 0 011.93 5.314c-.281-.054-3.101-.629-5.943-.271-.065-.141-.12-.293-.184-.445a25.416 25.416 0 00-.564-1.236c3.145-1.28 4.577-3.124 4.761-3.362zM12 3.475c2.17 0 4.154.813 5.662 2.148-.152.216-1.443 1.941-4.48 3.08-1.399-2.57-2.95-4.675-3.189-5A8.687 8.687 0 0112 3.475zm-3.633.803a53.896 53.896 0 013.167 4.935c-3.992 1.063-7.517 1.04-7.896 1.04a8.581 8.581 0 014.729-5.975zM3.453 12.01v-.26c.37.01 4.512.065 8.775-1.215.25.477.477.965.694 1.453-.109.033-.228.065-.336.098-4.404 1.42-6.747 5.303-6.942 5.629a8.522 8.522 0 01-2.19-5.705zM12 20.547a8.482 8.482 0 01-5.239-1.8c.152-.315 1.888-3.656 6.703-5.337.022-.01.033-.01.054-.022a35.318 35.318 0 011.823 6.475 8.4 8.4 0 01-3.341.684zm4.761-1.465c-.086-.52-.542-3.015-1.659-6.084 2.679-.423 5.022.271 5.314.369a8.468 8.468 0 01-3.655 5.715z",
    //           "clip-rule": "evenodd",
    //         }),
    //       ]),
    //   }),
    // },
  ],
};

export default {
  components: {
    Head,
    Link,
    JetApplicationLogo,
    AnnotationIcon,
    ChatAlt2Icon,
    ChatAltIcon,
    DocumentReportIcon,
    HeartIcon,
    InboxIcon,
    MenuIcon,
    PencilAltIcon,
    SearchIcon,
    ReplyIcon,
    SparklesIcon,
    TrashIcon,
    ShareIcon,
    UsersIcon,
    XIcon,
    ScaleIcon,
    Popover,
    PopoverButton,
    PopoverGroup,
    PopoverPanel,
    ChevronDownIcon,
    DatabaseIcon,
    ToolTip,
    Projects,
  },

  setup() {
    return {
      Search,
      features,
      metrics,
      footerNavigation,
    };
  },

  props: {
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
  },
};
</script>
