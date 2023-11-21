<template>
    <app-layout title="Compounds">
        <div>
            <div class="mx-auto">
                <div class="space-y-6 sm:px-6 lg:col-span-9 lg:px-0">
                    <section class="mb-20 bg-white shadow sm:overflow-hidden">
                        <div>
                            <div class="relative border-b border-zinc-900/5">
                                <div
                                    class="relative pt-10 dark:border-white/5 mx-8 max-w-7xl py-12 sm:py-12"
                                >
                                    <div
                                        class="absolute inset-0 bg-gradient-to-r from-[#36b49f] to-[#DBFF75] opacity-40 [mask-image:radial-gradient(farthest-side_at_top,white,transparent)] dark:from-[#36b49f]/30 dark:to-[#DBFF75]/30 dark:opacity-100"
                                    >
                                        <svg
                                            aria-hidden="true"
                                            class="absolute inset-x-0 inset-y-[-50%] h-[200%] w-full skew-y-[-18deg] fill-black/40 stroke-black/50 mix-blend-overlay dark:fill-white/2.5 dark:stroke-white/5"
                                        >
                                            <defs>
                                                <pattern
                                                    id=":r99:"
                                                    width="72"
                                                    height="56"
                                                    patternUnits="userSpaceOnUse"
                                                    x="-12"
                                                    y="4"
                                                >
                                                    <path
                                                        d="M.5 56V.5H72"
                                                        fill="none"
                                                    ></path>
                                                </pattern>
                                            </defs>
                                            <rect
                                                width="100%"
                                                height="100%"
                                                stroke-width="0"
                                                fill="url(#:r99:)"
                                            ></rect>
                                            <svg
                                                x="-12"
                                                y="4"
                                                class="overflow-visible"
                                            >
                                                <rect
                                                    stroke-width="0"
                                                    width="73"
                                                    height="57"
                                                    x="288"
                                                    y="168"
                                                ></rect>
                                                <rect
                                                    stroke-width="0"
                                                    width="73"
                                                    height="57"
                                                    x="144"
                                                    y="56"
                                                ></rect>
                                                <rect
                                                    stroke-width="0"
                                                    width="73"
                                                    height="57"
                                                    x="504"
                                                    y="168"
                                                ></rect>
                                                <rect
                                                    stroke-width="0"
                                                    width="73"
                                                    height="57"
                                                    x="720"
                                                    y="336"
                                                ></rect>
                                            </svg>
                                        </svg>
                                    </div>
                                    <div
                                        class="text-4xl mb-3 font-bold tracking-tight text-gray-900"
                                    >
                                        Browse Compounds
                                    </div>
                                    <p>
                                        Search spectra by compound names,
                                        structure, sub-structures and
                                        smiliarity.
                                    </p>
                                </div>
                            </div>
                            <div>
                                <div class="bg-white">
                                    <section aria-labelledby="filter-heading">
                                        <div
                                            class="bg-gray-100 border-t border-b"
                                        >
                                            <div
                                                class="mx-auto py-3 px-4 sm:px-6 lg:px-8"
                                            >
                                                <div class="flex items-center">
                                                    <div
                                                        class="flex w-full bg-white shadow rounded-full"
                                                    >
                                                        <form
                                                            class="flex w-full md:ml-0"
                                                            action="#"
                                                            method="GET"
                                                        >
                                                            <label
                                                                for="mobile-search-field"
                                                                class="sr-only"
                                                                >Search</label
                                                            >
                                                            <label
                                                                for="desktop-search-field"
                                                                class="sr-only"
                                                                >Search</label
                                                            >
                                                            <div
                                                                class="relative w-full text-gray-400 focus-within:text-gray-600"
                                                            >
                                                                <div
                                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center"
                                                                >
                                                                    <svg
                                                                        class="h-5 w-5 flex-shrink-0"
                                                                        x-description="Heroicon name: mini/magnifying-glass"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        viewBox="0 0 20 20"
                                                                        fill="currentColor"
                                                                        aria-hidden="true"
                                                                    >
                                                                        <path
                                                                            fill-rule="evenodd"
                                                                            d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                                                            clip-rule="evenodd"
                                                                        ></path>
                                                                    </svg>
                                                                </div>
                                                                <input
                                                                    name="mobile-search-field"
                                                                    id="mobile-search-field"
                                                                    class="h-full w-full border-transparent py-2 pl-8 pr-3 text-base text-gray-900 placeholder-gray-500 focus:border-transparent focus:placeholder-gray-400 focus:outline-none focus:ring-0 sm:hidden"
                                                                    placeholder="Search"
                                                                    type="search"
                                                                    autofocus
                                                                />
                                                                <input
                                                                    name="desktop-search-field"
                                                                    id="desktop-search-field"
                                                                    class="relative w-full border-0 px-6 py-3 rounded-full focus:shadow-outline hidden h-full w-full border-transparent text-gray-900 placeholder-gray-500 focus:border-transparent focus:placeholder-gray-400 focus:outline-none focus:ring-0 sm:block"
                                                                    placeholder="Search by Compound name, SMILES, InChI, InChI Key"
                                                                    type="search"
                                                                    v-model="
                                                                        searchTerm
                                                                    "
                                                                    autofocus
                                                                    @change="
                                                                        search()
                                                                    "
                                                                    @search="
                                                                        search(
                                                                            ''
                                                                        )
                                                                    "
                                                                />
                                                            </div>
                                                        </form>
                                                    </div>

                                                    <div
                                                        class="flex items-center md:ml-6"
                                                    >
                                                        <StructureSearch
                                                            ref="structure-search"
                                                        ></StructureSearch>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>
                            </div>
                        </div>
                        <div v-if="recentQueries && recentQueries.length > 0">
                            <div class="bg-gray-100">
                                <div
                                    class="mx-auto border-y max-w-7xl py-3 px-4 sm:flex sm:items-center sm:px-6 lg:px-16"
                                >
                                    <h3
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Filters
                                    </h3>

                                    <div
                                        aria-hidden="true"
                                        class="hidden h-5 w-px bg-gray-300 sm:ml-4 sm:block"
                                    ></div>
                                    <div class="mt-2 sm:mt-0 sm:ml-4">
                                        <div
                                            class="-m-1 flex flex-wrap items-center"
                                        >
                                            <span
                                                v-if="type"
                                                class="m-1 inline-flex items-center rounded-full border border-gray-200 bg-white py-1.5 pl-3 pr-2 text-sm font-medium text-gray-900"
                                            >
                                                <a class="mr-1">{{ type }}</a>
                                            </span>
                                            <span
                                                v-if="limit"
                                                class="m-1 inline-flex items-center rounded-full border border-gray-200 bg-white py-1.5 pl-3 pr-2 text-sm font-medium text-gray-900"
                                            >
                                                <a class="cursor-pointer"
                                                    >LIMIT: {{ limit }}</a
                                                >
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="results">
                            <div
                                class="py-6 px-4 lg:px-12 sm:p-6 border-b border-gray-200"
                            >
                                <div
                                    v-if="results.data.length > 0"
                                    class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between"
                                >
                                    <div>
                                        <p class="text-sm text-gray-700">
                                            Showing
                                            <span class="font-medium">{{
                                                results.from
                                            }}</span>
                                            to
                                            <span class="font-medium">{{
                                                results.to
                                            }}</span>
                                            of
                                            <span class="font-medium">{{
                                                results.total
                                            }}</span>
                                            results
                                        </p>
                                    </div>
                                    <div>
                                        <nav
                                            class="isolate inline-flex -space-x-px shadow-sm"
                                            aria-label="Pagination"
                                        >
                                            <a
                                                @click="navigateTo(link)"
                                                v-for="link in results.links"
                                                :key="link.label"
                                                :class="[
                                                    link.active
                                                        ? 'bg-gray-200'
                                                        : '',
                                                    'first:rounded-l-lg last:rounded-r-lg relative cursor-pointer inline-flex items-center border border-gray-300 bg-white px-2 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-20',
                                                ]"
                                                v-html="link.label"
                                            >
                                            </a>
                                        </nav>
                                    </div>
                                </div>
                                <div
                                    v-if="results.data.length > 0"
                                    class="mx-auto grid mt-6 gap-5 lg:max-w-none md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6"
                                >
                                    <span
                                        class="rounded-lg hover:shadow-lg shadow border"
                                        v-for="result in results.data"
                                        :key="result.id"
                                    >
                                        <MoleculeCard
                                            :molecule="result"
                                        ></MoleculeCard>
                                    </span>
                                </div>
                                <div v-else>
                                    <div v-if="error">
                                        <div class="bg-white">
                                            <div
                                                class="mx-auto w-full max-w-7xl px-6 pb-16 sm:pb-24 lg:px-8"
                                            >
                                                <div
                                                    class="mx-auto mt-20 max-w-2xl text-center sm:mt-24"
                                                >
                                                    <p
                                                        class="text-base font-semibold leading-8 text-indigo-600"
                                                    >
                                                        {{ error.status }}
                                                    </p>
                                                    <h1
                                                        class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl"
                                                    >
                                                        {{ error.statusText }}
                                                    </h1>
                                                    <p
                                                        class="mt-4 text-base leading-7 text-gray-600 sm:mt-6 sm:text-lg sm:leading-8"
                                                    >
                                                        {{ error.data.message }}
                                                    </p>
                                                </div>
                                                <div
                                                    class="mx-auto mt-16 flow-root max-w-lg sm:mt-20"
                                                >
                                                    <!-- 
                <h2 class="sr-only">Popular pages</h2>
                <ul
                  role="list"
                  class="-mt-6 divide-y divide-gray-900/5 border-b border-gray-900/5"
                >
                  <li class="relative flex gap-x-6 py-6">
                    <div
                      class="flex h-10 w-10 flex-none items-center justify-center rounded-lg shadow-sm ring-1 ring-gray-900/10"
                    >
                      <svg
                        class="h-6 w-6 text-indigo-600"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          d="M11.25 4.533A9.707 9.707 0 006 3a9.735 9.735 0 00-3.25.555.75.75 0 00-.5.707v14.25a.75.75 0 001 .707A8.237 8.237 0 016 18.75c1.995 0 3.823.707 5.25 1.886V4.533zM12.75 20.636A8.214 8.214 0 0118 18.75c.966 0 1.89.166 2.75.47a.75.75 0 001-.708V4.262a.75.75 0 00-.5-.707A9.735 9.735 0 0018 3a9.707 9.707 0 00-5.25 1.533v16.103z"
                        />
                      </svg>
                    </div>
                    <div class="flex-auto">
                      <h3 class="text-sm font-semibold leading-6 text-gray-900">
                        <a href="#">
                          <span class="absolute inset-0" aria-hidden="true"></span>
                          Documentation
                        </a>
                      </h3>
                      <p class="mt-2 text-sm leading-6 text-gray-600">
                        Learn how to integrate our tools with your app.
                      </p>
                    </div>
                    <div class="flex-none self-center">
                      <svg
                        class="h-5 w-5 text-gray-400"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                  </li>
                  <li class="relative flex gap-x-6 py-6">
                    <div
                      class="flex h-10 w-10 flex-none items-center justify-center rounded-lg shadow-sm ring-1 ring-gray-900/10"
                    >
                      <svg
                        class="h-6 w-6 text-indigo-600"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          d="M5.625 3.75a2.625 2.625 0 100 5.25h12.75a2.625 2.625 0 000-5.25H5.625zM3.75 11.25a.75.75 0 000 1.5h16.5a.75.75 0 000-1.5H3.75zM3 15.75a.75.75 0 01.75-.75h16.5a.75.75 0 010 1.5H3.75a.75.75 0 01-.75-.75zM3.75 18.75a.75.75 0 000 1.5h16.5a.75.75 0 000-1.5H3.75z"
                        />
                      </svg>
                    </div>
                    <div class="flex-auto">
                      <h3 class="text-sm font-semibold leading-6 text-gray-900">
                        <a href="#">
                          <span class="absolute inset-0" aria-hidden="true"></span>
                          API Reference
                        </a>
                      </h3>
                      <p class="mt-2 text-sm leading-6 text-gray-600">
                        A complete API reference for our libraries.
                      </p>
                    </div>
                    <div class="flex-none self-center">
                      <svg
                        class="h-5 w-5 text-gray-400"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                  </li>
                  <li class="relative flex gap-x-6 py-6">
                    <div
                      class="flex h-10 w-10 flex-none items-center justify-center rounded-lg shadow-sm ring-1 ring-gray-900/10"
                    >
                      <svg
                        class="h-6 w-6 text-indigo-600"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M6 3a3 3 0 00-3 3v12a3 3 0 003 3h12a3 3 0 003-3V6a3 3 0 00-3-3H6zm1.5 1.5a.75.75 0 00-.75.75V16.5a.75.75 0 001.085.67L12 15.089l4.165 2.083a.75.75 0 001.085-.671V5.25a.75.75 0 00-.75-.75h-9z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                    <div class="flex-auto">
                      <h3 class="text-sm font-semibold leading-6 text-gray-900">
                        <a href="#">
                          <span class="absolute inset-0" aria-hidden="true"></span>
                          Guides
                        </a>
                      </h3>
                      <p class="mt-2 text-sm leading-6 text-gray-600">
                        Installation guides that cover popular setups.
                      </p>
                    </div>
                    <div class="flex-none self-center">
                      <svg
                        class="h-5 w-5 text-gray-400"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                  </li>
                  <li class="relative flex gap-x-6 py-6">
                    <div
                      class="flex h-10 w-10 flex-none items-center justify-center rounded-lg shadow-sm ring-1 ring-gray-900/10"
                    >
                      <svg
                        class="h-6 w-6 text-indigo-600"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M3.75 4.5a.75.75 0 01.75-.75h.75c8.284 0 15 6.716 15 15v.75a.75.75 0 01-.75.75h-.75a.75.75 0 01-.75-.75v-.75C18 11.708 12.292 6 5.25 6H4.5a.75.75 0 01-.75-.75V4.5zm0 6.75a.75.75 0 01.75-.75h.75a8.25 8.25 0 018.25 8.25v.75a.75.75 0 01-.75.75H12a.75.75 0 01-.75-.75v-.75a6 6 0 00-6-6H4.5a.75.75 0 01-.75-.75v-.75zm0 7.5a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                    <div class="flex-auto">
                      <h3 class="text-sm font-semibold leading-6 text-gray-900">
                        <a href="#">
                          <span class="absolute inset-0" aria-hidden="true"></span>
                          Blog
                        </a>
                      </h3>
                      <p class="mt-2 text-sm leading-6 text-gray-600">
                        Read our latest news and articles.
                      </p>
                    </div>
                    <div class="flex-none self-center">
                      <svg
                        class="h-5 w-5 text-gray-400"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                  </li>
                </ul>
                 -->
                                                    <div
                                                        class="mt-10 flex justify-center"
                                                    >
                                                        <a
                                                            href="/"
                                                            class="text-sm font-semibold leading-6 text-indigo-600"
                                                        >
                                                            <span
                                                                aria-hidden="true"
                                                                >&larr;</span
                                                            >
                                                            Back to home
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="bg-white px-4 py-12">
                                        <div class="text-center">
                                            <svg
                                                class="mx-auto h-12 w-12 text-gray-400"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    vector-effect="non-scaling-stroke"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"
                                                ></path>
                                            </svg>
                                            <h3
                                                class="mt-2 text-sm font-semibold text-gray-900"
                                            >
                                                No records were found
                                            </h3>
                                            <p
                                                class="mt-1 text-sm text-gray-500"
                                            >
                                                Try to refine your search query.
                                                If the problem persist please
                                                contact us.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                v-if="results.data.length > 0"
                                class="flex items-center justify-between bg-white px-12 py-3 rounded-md"
                            >
                                <div
                                    class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between"
                                >
                                    <div>
                                        <p class="text-sm text-gray-700">
                                            Showing
                                            <span class="font-medium">{{
                                                results.from
                                            }}</span>
                                            to
                                            <span class="font-medium">{{
                                                results.to
                                            }}</span>
                                            of
                                            <span class="font-medium">{{
                                                results.total
                                            }}</span>
                                            results
                                        </p>
                                    </div>
                                    <div>
                                        <nav
                                            class="isolate inline-flex -space-x-px shadow-sm"
                                            aria-label="Pagination"
                                        >
                                            <a
                                                @click="navigateTo(link)"
                                                v-for="link in results.links"
                                                :key="link.label"
                                                :class="[
                                                    link.active
                                                        ? 'bg-gray-200'
                                                        : '',
                                                    'first:rounded-l-lg last:rounded-r-lg relative cursor-pointer inline-flex items-center border border-gray-300 bg-white px-2 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-20',
                                                ]"
                                                v-html="link.label"
                                            >
                                            </a>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                            <div
                                v-if="recentQueries.length > 0"
                                class="bg-gray-100 border-t"
                            >
                                <div
                                    class="mx-auto max-w-7xl py-3 px-4 sm:flex sm:items-center sm:px-6 lg:px-16"
                                >
                                    <h3
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Recent Searches
                                    </h3>

                                    <div
                                        aria-hidden="true"
                                        class="hidden h-5 w-px bg-gray-300 sm:ml-4 sm:block"
                                    ></div>
                                    <div class="mt-2 sm:mt-0 sm:ml-4">
                                        <div
                                            class="-m-1 flex flex-wrap items-center"
                                        >
                                            <span
                                                :key="query"
                                                v-for="query in recentQueries"
                                                class="m-1 inline-flex items-center rounded-full border border-gray-200 bg-white py-1.5 pl-3 pr-2 text-sm font-medium text-gray-900"
                                            >
                                                <a
                                                    class="cursor-pointer"
                                                    @click="search(query)"
                                                    >{{ query }}</a
                                                >
                                                <button
                                                    type="button"
                                                    @click="
                                                        removeSearchQuery(query)
                                                    "
                                                    class="ml-1 inline-flex h-4 w-4 flex-shrink-0 rounded-full p-1 text-gray-400 hover:bg-gray-200 hover:text-gray-500"
                                                >
                                                    <span class="sr-only"
                                                        >Remove filter for
                                                        Objects</span
                                                    >
                                                    <svg
                                                        class="h-2 w-2"
                                                        stroke="currentColor"
                                                        fill="none"
                                                        viewBox="0 0 8 8"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-width="1.5"
                                                            d="M1 1l6 6m0-6L1 7"
                                                        ></path>
                                                    </svg>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="loading">
                            <div class="text-center py-20">
                                <svg
                                    class="animate-spin -ml-1 mr-3 h-5 w-5 inline"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>
                                Loading...
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </app-layout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import MoleculeCard from "@/App/MoleculeCard.vue";
import StructureSearch from "@/App/StructureSearch.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink.vue";
import { Menu, MenuItems, MenuButton } from "@headlessui/vue";

export default {
    components: {
        AppLayout,
        MoleculeCard,
        StructureSearch,
        JetDropdownLink,
        Menu,
        MenuItems,
        MenuButton,
    },
    props: ["page", "query", "limit", "tagType"],
    data() {
        return {
            results: null,
            loading: false,
            searchTerm: "",
            recentQueries: [],
            type: null,
            error: null,
        };
    },
    mounted() {
        this.searchTerm = "";
        if (this.query) {
            this.searchTerm = this.query;
            this.fetchCompounds();
        } else {
            this.searchTerm = "";
            this.fetchCompounds();
        }

        if (typeof window !== "undefined") {
            let recentQueries = localStorage.getItem("recentQueries");
            if (recentQueries) {
                this.recentQueries = JSON.parse(recentQueries);
            }
        }

        if (window.location.hash) {
            if (window.location.hash == "#structure") {
                this.$refs["structure-search"].openDialog(true);
            } else if (window.location.hash == "#advanced") {
                this.$refs["advanced-search"].openDialog(true);
            }
        }
    },
    methods: {
        fetchCompounds(queryT) {
            this.error = null;
            this.results = null;
            this.loading = true;
            let queryTerm = null;
            if (queryT) {
                queryTerm = queryT;
            } else {
                queryTerm = this.searchTerm ? this.searchTerm : "";
            }

            if (queryTerm && queryTerm != "") {
                let queryType = this.getParameterByName("type");
                this.type =
                    queryType && queryType != ""
                        ? this.getParameterByName("type")
                        : this.tagQuery(queryTerm);
            }

            let sort = this.getParameterByName("sort");

            if (typeof window !== "undefined") {
                let recentQueries = localStorage.getItem("recentQueries");
                recentQueries = recentQueries ? JSON.parse(recentQueries) : [];
                recentQueries.push(queryTerm);
                recentQueries = [...new Set(recentQueries)].filter((n) => n);
                localStorage.setItem(
                    "recentQueries",
                    JSON.stringify(recentQueries)
                );
            }

            let url = new URL(window.location);
            url.searchParams.set("query", queryTerm);
            url.searchParams.set("page", 1);
            url = this.sanitiseURL(url);
            window.history.pushState(
                null,
                "",
                queryTerm == ""
                    ? window.location.href.split("?")[0]
                    : url.toString()
            );
            let page = this.page ? this.page : 1;
            let urlEndpoint =
                "/api/v1/search/?limit=" + this.limit + "&page=" + page;
            if (sort) {
                urlEndpoint = urlEndpoint + "&sort=" + sort;
            }

            if (queryTerm == "") {
                this.type = "";
            }
            axios
                .post(urlEndpoint, {
                    query: queryTerm,
                    type: this.type,
                    tagType: this.tagType ? this.tagType : null,
                })
                .then((response) => {
                    this.results = response.data;
                    this.loading = false;
                })
                .catch((err) => {
                    this.loading = false;
                    this.results = {};
                    this.results["data"] = [];
                    this.error = err.response;
                });
        },
        sanitiseURL(url) {
            for (const [key, value] of url.searchParams.entries()) {
                if (
                    value == "null" ||
                    value == null ||
                    value == "" ||
                    value == "undefined"
                ) {
                    url.searchParams.delete(key);
                }
            }
            return url;
        },
        search(query) {
            if (query) {
                this.searchTerm = query;
                this.type = "text";
            }
            this.fetchCompounds(query);
        },
        removeSearchQuery(query) {
            if (typeof window !== "undefined") {
                let recentQueries = localStorage.getItem("recentQueries");
                recentQueries = recentQueries ? JSON.parse(recentQueries) : [];
                recentQueries = recentQueries.filter(function (item) {
                    return item !== query;
                });
                recentQueries = [...new Set(recentQueries)].filter((n) => n);
                this.recentQueries = recentQueries;
                localStorage.setItem(
                    "recentQueries",
                    JSON.stringify(recentQueries)
                );
            }
        },
        navigateTo(link) {
            let queryType = this.getParameterByName("type");
            let searchTerm = this.searchTerm;
            let tagType = this.tagType;
            let location = "/compounds" + link.url;

            let query = queryType ? "&type=" + queryType : "";
            query += searchTerm ? "&query=" + searchTerm : "";
            tagType = tagType ? "&tagType=" + tagType : "";
            window.location = location + query + tagType;
        },
        getParameterByName(name, url = window.location.href) {
            name = name.replace(/[\[\]]/g, "\\$&");
            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return "";
            return decodeURIComponent(results[2].replace(/\+/g, " "));
        },
        downloadSmilesData(downloadType) {
            console.log("Download Data");
            console.log(this.$page.props.queryType);
            axios
                .post("/downloadSmiles", {
                    query: this.$page.props.query,
                    type: this.$page.props.queryType,
                    downloadType: downloadType,
                })
                .then((response) => {
                    const { url, filename, mimeType } = response.data;

                    // Create a temporary link element
                    const link = document.createElement("a");
                    link.href = url;
                    link.download = filename;
                    link.type = mimeType;

                    // Programmatically click the link to trigger the file download
                    link.click();
                })
                .catch((error) => {
                    console.error(error);
                });
        },
    },
};
</script>
