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
                  <jet-application-logo class="block h-10 p-0.5 ml-1.5 w-auto" />
                </Link>
              </div>
              <div class="-mr-2 -my-2 md:hidden">
                <PopoverButton
                  class="rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-teal-500"
                >
                  <span class="sr-only">Open menu</span>
                  <Bars3Icon class="h-6 w-6" aria-hidden="true" />
                </PopoverButton>
              </div>
              <PopoverGroup as="nav" class="hidden md:flex space-x-10">
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
                <Link
                  href="/about-us"
                  class="text-base font-medium text-gray-500 hover:text-gray-900"
                >
                  About
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
                      <div class="flex items-center justify-between">
                        <div>
                          <jet-application-logo class="block h-10 p-0.5 ml-1.5 w-auto" />
                        </div>
                        <div class="-mr-2">
                          <PopoverButton
                            class="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-teal-500"
                          >
                            <span class="sr-only">Close menu</span>
                            <XMarkIcon class="h-6 w-6" aria-hidden="true" />
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
        <div class="min-h-screen pb-16 isolate">
          <div class="relative isolate -z-10">
            <svg
              class="absolute inset-x-0 top-0 -z-10 h-[64rem] w-full stroke-gray-200 [mask-image:radial-gradient(32rem_32rem_at_center,white,transparent)]"
              aria-hidden="true"
            >
              <defs>
                <pattern
                  id="1f932ae7-37de-4c0a-a8b0-a6e3b4d44b84"
                  width="200"
                  height="200"
                  x="50%"
                  y="-1"
                  patternUnits="userSpaceOnUse"
                >
                  <path d="M.5 200V.5H200" fill="none" />
                </pattern>
              </defs>
              <svg x="50%" y="-1" class="overflow-visible fill-gray-50">
                <path
                  d="M-200 0h201v201h-201Z M600 0h201v201h-201Z M-400 600h201v201h-201Z M200 800h201v201h-201Z"
                  stroke-width="0"
                />
              </svg>
              <rect
                width="100%"
                height="100%"
                stroke-width="0"
                fill="url(#1f932ae7-37de-4c0a-a8b0-a6e3b4d44b84)"
              />
            </svg>
            <div
              class="absolute left-1/2 right-0 top-0 -z-10 -ml-24 transform-gpu overflow-hidden blur-3xl lg:ml-24 xl:ml-48"
              aria-hidden="true"
            >
              <div
                class="aspect-[801/1036] w-[50.0625rem] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30"
                style="
                  clip-path: polygon(
                    63.1% 29.5%,
                    100% 17.1%,
                    76.6% 3%,
                    48.4% 0%,
                    44.6% 4.7%,
                    54.5% 25.3%,
                    59.8% 49%,
                    55.2% 57.8%,
                    44.4% 57.2%,
                    27.8% 47.9%,
                    35.1% 81.5%,
                    0% 97.7%,
                    39.2% 100%,
                    35.2% 81.4%,
                    97.2% 52.8%,
                    63.1% 29.5%
                  );
                "
              ></div>
            </div>
            <div class="overflow-hidden">
              <div class="mx-auto max-w-6xl pb-32 px-8">
                <div
                  class="mx-auto max-w-2xl gap-x-14 lg:mx-0 lg:flex lg:max-w-none lg:items-center"
                >
                  <div class="w-full max-w-xl lg:shrink-0 xl:max-w-2xl">
                    <h1
                      class="text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl"
                    >
                      We’re changing the way chemists publish NMR data.
                    </h1>
                    <p
                      class="relative mt-6 text-lg leading-7 text-gray-600 sm:max-w-md lg:max-w-none"
                    >
                      While several attempts have been made recently to create NMR
                      databases from literature, they fall short of meeting the needs of
                      modern chemical and related scientific communities. nmrXiv is the
                      first open archive to preserve NMR data in its original instrument
                      format, while offering tools for their analysis and promoting open
                      data and standards for long-term sustainability and accessibility.
                      <br /><br />
                      nmrXiv revolutionizes NMR data management and archival by providing
                      an open-access, standardized, and user-friendly platform. Our
                      platform is public and interoperable to ensure that NMR data is
                      freely accessible, transparent, and reproducible. We support
                      standardized data formats to enhance consistency and facilitate data
                      comparison and integration. Our platform includes processing and
                      quality control tools to verify the integrity and accuracy of NMR
                      data before and after publication. With advanced search and
                      retrieval features powered by comprehensive metadata, nmrXiv allows
                      researchers to efficiently locate and utilize specific datasets,
                      fostering greater collaboration and accelerating scientific
                      discovery. nmrXiv serves as core platform for the nmrXiv Project
                      Group, an international collaborative that seeks to advance the
                      utility of structural and quantitative NMR analysis for chemical,
                      pharmaceutical, and natural research and applications.
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
                    <div class="w-44 flex-none space-y-8 pt-32 sm:pt-0">
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

          <div class="mx-auto -mt-12 max-w-6xl px-6 sm:mt-0 lg:px-8 xl:-mt-8">
            <div class="mx-auto max-w-2xl lg:mx-0 lg:max-w-none">
              <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                Our mission
              </h2>
              <div class="mt-6 flex flex-col gap-x-8 gap-y-20 lg:flex-row">
                <div class="lg:w-full lg:max-w-2xl lg:flex-auto">
                  <p class="text-xl leading-8 text-gray-600">
                    <b>All chemists publish FAIR NMR data.</b>
                    To get there, we will support chemists and scientists from related
                    disciplines in their efforts to collect, store, process, analyse,
                    publish, and re-use NMR data.<br />
                  </p>
                  <p class="italic text-md leading-7 text-gray-600 mt-4">
                    Developed at
                    <a class="text-blue-900" href="https://www.uni-jena.de/en"
                      >Friedrich Schiller University, Jena</a
                    >
                    in Germany, under the leadership of
                    <a class="text-blue-900" href="https://cheminf.uni-jena.de/"
                      >Professor Christoph Steinbeck</a
                    >. Funded by the
                    <a class="text-blue-900 underline" href="https://www.dfg.de/"
                      >Deutsche Forschungsgemeinschaft (DFG, German Research
                      Foundation)</a
                    >
                    under the
                    <a class="text-blue-900 underline" href="https://nfdi4chem.de/"
                      >National Research Data Infrastructure – NFDI4Chem</a
                    >
                    – Projektnummer
                    <strong class="text-blue-900">441958208</strong>.
                  </p>
                </div>
                <div class="lg:flex lg:flex-auto lg:justify-center">
                  <dl class="w-64 space-y-8 xl:w-80 -mt-10">
                    <div class="flex flex-col-reverse gap-y-4">
                      <dt class="text-base leading-7 text-gray-600">
                        Projects published so far
                      </dt>
                      <dd class="text-5xl font-semibold tracking-tight text-gray-900">
                        {{ projects }}
                      </dd>
                    </div>
                    <div class="flex flex-col-reverse gap-y-4">
                      <dt class="text-base leading-7 text-gray-600">
                        Compounds experimental spectra
                      </dt>
                      <dd class="text-5xl font-semibold tracking-tight text-gray-900">
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
                  >. Every dataset on nmrXiv comes with its own specific license. It is
                  essential to review the license details for each dataset before using
                  it.
                </dd>
                <br />
                <dt class="font-semibold text-gray-900">Help Desk</dt>
                <dd class="mt-1 text-gray-600">
                  Any issues or support requests can be raised at our
                  <a class="text-blue-900 underline" href="https://helpdesk.nfdi4chem.de/"
                    >Help Desk</a
                  >
                  or write to us at
                  <a class="text-blue-900 underline" href="mailto:info.nmrxiv@uni-jena.de"
                    >info.nmrxiv@uni-jena.de</a
                  >.
                </dd>
              </div>
              <div>
                <dt class="font-semibold text-gray-900">
                  Contributors and Steering Committee
                </dt>
                <dd class="mt-1 text-gray-600">
                  At nmrXiv, our global contributors bring a wealth of diverse expertise
                  to our platform. Guided by a seasoned Steering Committee, we ensure
                  strategic oversight, foster innovation, and drive continuous improvement
                  in all our endeavors.
                  <br /><a
                    class="text-blue-900 underline"
                    href="https://docs.nmrxiv.org/contribution/contributors.html"
                    >View →</a
                  >
                </dd>
              </div>
              <div>
                <dt class="font-semibold text-gray-900">Documentation</dt>
                <dd class="mt-1 text-gray-600">
                  Our documentation-first approach enables our developers and chemists to
                  collaborate on proposed features, ensuring implementations accurately
                  address real-world use cases.
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
              <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                Steering Committee
              </h2>
            </div>
            <ul
              role="list"
              class="mx-auto mt-20 grid max-w-2xl grid-cols-2 gap-x-8 gap-y-16 text-center sm:grid-cols-3 md:grid-cols-4 lg:mx-0 lg:max-w-none lg:grid-cols-5 xl:grid-cols-6"
            >
              <li class="flex flex-col justify-center items-center">
                <div
                  class="h-24 w-24 overflow-hidden rounded-full flex justify-center items-center"
                >
                  <img class="mx-auto" src="/img/gpauli.jpg" alt="Guido Pauli" />
                </div>
                <h3
                  class="mt-6 text-base font-semibold leading-7 tracking-tight text-gray-900"
                >
                  <a href="https://pharmacy.uic.edu/profiles/gfp/" target="_blank"
                    >Guido Pauli</a
                  >
                </h3>
              </li>
              <li class="flex flex-col justify-center items-center">
                <div
                  class="bg-gray-300 h-24 w-24 overflow-hidden rounded-full flex justify-center items-center"
                >
                  <img class="h-24 w-24" src="/img/ph.jpg" alt="Nils Schlörer" />
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
                  class="h-24 w-24 overflow-hidden rounded-full flex justify-center items-center"
                >
                  <img class="mx-auto" src="/img/jw.jpeg" alt="Julien Wist" />
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
                  class="h-24 w-24 overflow-hidden rounded-full flex justify-center items-center"
                >
                  <img class="mx-auto" src="/img/lp.jpg" alt="Luc Patiny" />
                </div>
                <h3
                  class="mt-6 text-base font-semibold leading-7 tracking-tight text-gray-900"
                >
                  <a href="https://www.linkedin.com/in/lpatiny/" target="_blank"
                    >Luc Patiny</a
                  >
                </h3>
              </li>
              <li class="flex flex-col justify-center items-center">
                <div
                  class="bg-gray-300 h-24 w-24 overflow-hidden rounded-full flex justify-center items-center"
                >
                  <img class="h-24 w-24" src="/img/ph.jpg" alt="Stephan Kuhn" />
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
                  class="h-24 w-24 overflow-hidden rounded-full flex justify-center items-center"
                >
                  <img class="mx-auto" src="/img/jl.jpeg" alt="Johannes Liermann" />
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
                <svg x="50%" y="50%" class="overflow-visible fill-gray-50">
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
                nmrXiv Project Group
              </h2>
              <div
                class="mx-auto mt-16 grid max-w-lg grid-cols-2 md:grid-cols-5 items-center gap-x-8 gap-y-10 sm:max-w-xl sm:grid-cols-6 sm:gap-x-10 lg:mx-0 lg:max-w-none lg:grid-cols-5"
              >
                <a href="https://cheminf.uni-jena.de/">
                  <img
                    class="col-span-1 md:col-span-2 max-h-12 w-full object-contain lg:col-span-1"
                    src="https://www.uni-jena.de/unijenamedia/universitaet/abteilung-hochschulkommunikation/marketing/wort-bildmarke-universitaet-jena.jpg?height=335&width=1000"
                    alt="FSU"
                    width="158"
                    height="48"
                  />
                </a>
                <a href="https://www.nfdi4chem.de/">
                  <img
                    class="col-span-2 max-h-12 w-full object-contain lg:col-span-1"
                    src="/img/nmrxiv-logo.png"
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
                <a href="https://ctb.nmrsolutions.fi/login?returnUrl=~dashboard">
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
      </div>
    </main>

    <footer class="bg-white border-t border-gray-100" aria-labelledby="footer-heading">
      <h2 id="footer-heading" class="sr-only">Footer</h2>
      <div class="max-w-6xl mx-auto pt-16 pb-8 px-4 sm:px-6 lg:pt-24 lg:px-8">
        <div class="md:grid md:grid-cols-3 md:gap-8">
          <div class="mt-12 xl:mt-0 items center content-center">
            <jet-application-logo class="p-0.5 ml-1.5" />
          </div>
          <div class="grid grid-cols-2 gap-8 md:col-span-2">
            <div class="md:grid md:grid-cols-2 md:gap-8">
              <div>
                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                  Quick links
                </h3>
                <ul role="list" class="mt-4 space-y-4">
                  <li v-for="item in footerNavigation.quicklinks" :key="item.name">
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
                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                  Support
                </h3>
                <ul role="list" class="mt-4 space-y-4">
                  <li v-for="item in footerNavigation.support" :key="item.name">
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
                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                  About
                </h3>
                <ul role="list" class="mt-4 space-y-4">
                  <li v-for="item in footerNavigation.About" :key="item.name">
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
              <component :is="item.icon" class="h-6 w-6" aria-hidden="true" />
            </a>
          </div>
          <p class="mt-8 text-base text-gray-400 md:mt-0 md:order-1">
            &copy; 2024 nmrXiv. All rights reserved.
          </p>
        </div>
      </div>
    </footer>
  </div>
  <component :is="'script'" type="application/ld+json">{{ schema }}</component>
</template>

<script>
import { Head, Link } from "@inertiajs/vue3";
import JetApplicationLogo from "@/Jetstream/ApplicationLogo.vue";
import Projects from "@/Shared/Projects.vue";
import { defineComponent, h } from "vue";
import StructureSearch from "@/App/StructureSearch.vue";
import { Popover, PopoverButton, PopoverGroup, PopoverPanel } from "@headlessui/vue";
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
      href: "https://docs.nmrxiv.org/developer-guides/api.html",
    },
  ],
  About: [
    {
      name: "Adivsory Board",
      href:
        "https://docs.nmrxiv.org/docs/contributing/contributors-and-steering-committee",
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
              d:
                "M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z",
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
    StructureSearch,
  },

  props: {
    projects: Number,
    compounds: Number,
  },

  setup() {
    return {
      footerNavigation,
    };
  },

  data() {
    return {};
  },
};
</script>
