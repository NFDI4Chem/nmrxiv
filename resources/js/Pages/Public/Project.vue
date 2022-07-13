<template>
  <app-layout :title="project.name">
    <template v-if="project" #header>
      <div class="bg-white border-b">
        <div class="border-b">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
            <img
              v-if="project && project.data.owner"
              class="inline h-7 w-7 rounded-full"
              :src="project.data.owner.profile_photo_url"
            />
            <p class="inline ml-3 text-xs font-bold text-gray-500 uppercase">
              <a class="text-gray-900">{{
                project.data.owner.first_name + " " + project.data.owner.last_name
              }}</a>
              updated on
              <time datetime="2020-08-25">{{ formatDate(project.data.updated_at) }}</time>
            </p>

            <div class="float-right">
              <a
                class="ml-4 cursor-pointer relative inline-flex items-center px-4 py-1 rounded-full border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
              >
                Download
              </a>
            </div>
            <div class="float-right">
              <div class="flex-shrink-0">
                <span
                  v-if="project.data.stats"
                  class="relative z-0 inline-flex shadow-sm rounded-lg"
                >
                  <button
                    type="button"
                    class="relative inline-flex items-center px-1 py-1 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-900 hover:bg-gray-50"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </button>
                  <a
                    class="-ml-px relative inline-flex items-center px-4 py-1 rounded-r-md border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    {{ project.data.stats.likes }}
                  </a>
                </span>
              </div>
            </div>
          </div>
        </div>
        <div>
          <div class="pb-5">
            <div>
              <div>
                <img
                  class="h-32 w-full object-cover lg:h-48"
                  src="https://images.unsplash.com/photo-1637625854255-d893202554f4?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80"
                  alt=""
                />
              </div>
              <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="-mt-12 sm:-mt-16 sm:flex sm:items-end sm:space-x-5">
                  <div class="flex mb-5">
                    <img
                      class="h-24 w-72 object-cover border rounded ring-4 ring-white sm:h-32 sm:w-96"
                      :src="project.data.photo_url"
                      alt=""
                    />
                  </div>
                  <div
                    class="mt-6 sm:flex-1 sm:min-w-0 sm:flex sm:items-center sm:justify-end sm:space-x-6 sm:pb-1"
                  >
                    <div class="sm:hidden 2xl:block min-w-0 flex-1">
                      <h1 class="text-2xl font-bold text-gray-900 break-words">
                        {{ project.data.name }}
                      </h1>
                    </div>
                  </div>
                </div>
                <div class="hidden sm:block 2xl:hidden min-w-0 flex-1">
                  <h1 class="text-2xl font-bold text-gray-900 break-words">
                    {{ project.data.name }}
                  </h1>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
    <div>
      <main class="flex-1 relative z-0 overflow-y-auto focus:outline-none xl:order-last">
        <div>
          <div class="mt-6 sm:mt-2 2xl:mt-5">
            <div class="border-b border-gray-200">
              <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                  <a
                    @click="selectedTab = tab.name"
                    :class="[
                      selectedTab == tab.name ? 'border-pink-500 text-gray-900' : '',
                      'cursor-pointer text-gray-900 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
                    ]"
                    :key="tab.name"
                    v-for="tab in tabs"
                    aria-current="page"
                  >
                    {{ tab.name }}
                  </a>
                </nav>
              </div>
            </div>
          </div>
          <div class="bg-white">
            <div v-if="selectedTab == 'Data'">
              <div class="pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <h3 class="text-xl font-extrabold text-blue-gray-900">About project</h3>
                <div class="mt-6 space-y-8 divide-y divide-y-blue-gray-200">
                  <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-6 sm:gap-x-6">
                    <div class="sm:col-span-6">
                      <h2 class="text-xl font-medium text-blue-gray-900">Description</h2>
                      <p class="mt-1 text-sm text-blue-gray-500">
                        {{ project.data.description }}
                      </p>
                    </div>
                    <div>
                      <a
                        :key="tag.id"
                        v-for="tag in project.data.tags"
                        target="_blank"
                        :href="'/projects?tag=' + tag.name.en"
                        class="mr-1 float rounded-full border border-gray-200 items-center py-1.5 pl-3 pr-3 text-sm font-medium bg-white text-gray-900 hover:text-white hover:bg-black"
                        ><span>{{ tag.name.en }}</span></a
                      >
                    </div>
                  </div>

                  <div class="pt-8 gap-y-6 sm:grid-cols-6 sm:gap-x-6">
                    <div class="sm:col-span-6">
                      <h2 class="text-xl font-medium text-blue-gray-900">Authors</h2>
                      <!-- <p class="mt-1 text-sm text-blue-gray-500">
                      This information will be displayed publicly so be careful what you
                      share.
                    </p> -->
                    </div>
                    <div class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2">
                      <div
                        :key="user.email"
                        v-for="user in project.data.users"
                        class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-pink-500"
                      >
                        <div class="flex-shrink-0">
                          <img
                            class="h-10 w-10 rounded-full"
                            :src="user.profile_photo_url"
                            alt=""
                          />
                        </div>
                        <div class="flex-1 min-w-0">
                          <a class="focus:outline-none">
                            <span class="absolute inset-0" aria-hidden="true"></span>
                            <p class="text-sm font-medium text-gray-900">
                              {{ user.first_name + " " + user.last_name }}
                            </p>
                            <!-- <p class="text-sm text-gray-500 truncate">Co-Founder / CEO</p> -->
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="pt-8 gap-y-6 sm:grid-cols-6 sm:gap-x-6">
                    <div class="sm:col-span-6">
                      <h2 class="text-xl font-medium text-blue-gray-900">
                        Studies ({{ project.data.studies.length }})
                      </h2>
                      <p class="mt-1 text-sm text-blue-gray-500 mb-4">
                        This information will be displayed publicly so be careful what you
                        share.
                      </p>
                    </div>
                    <div class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2">
                      <div
                        :key="study.id"
                        v-for="study in project.data.studies"
                        class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-pink-500"
                      >
                        <div class="flex-shrink-0"></div>
                        <div class="flex-1 min-w-0">
                          <a class="focus:outline-none">
                            <span class="absolute inset-0" aria-hidden="true"></span>
                            <p class="text-md font-bold text-gray-900">
                              {{ study.name }}
                            </p>
                            <div class="-m-1 mt-2 flex flex-wrap items-center">
                              <a
                                :key="dataset.id"
                                v-for="dataset in study.datasets"
                                target="_blank"
                                :href="'/datasets/' + dataset.slug"
                                class="mr-1 inline-flex rounded-full border border-gray-200 items-center py-1.5 pl-3 pr-3 text-sm font-medium bg-white text-gray-900 hover:text-white hover:bg-black"
                                ><span>{{ dataset.name }}</span></a
                              >
                            </div>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="selectedTab == 'Studies'">
              <div style="height: 80vh">
                <div class="flex-1 flex md:overflow-hidden">
                  <nav
                    aria-label="Sections"
                    class="hidden flex-shrink-0 w-64 bg-white border-r border-blue-gray-200 md:flex md:flex-col"
                  >
                    <div
                      class="border-gray-200 px-4 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider flex-shrink-0 border-b border-blue-gray-200 flex items-center"
                    >
                      STUDY
                    </div>
                    <div style="height: 74vh; overflow: scroll !important">
                      <a
                        v-for="study in project.data.studies"
                        :key="study.slug"
                        :class="[
                          selectedStudy.id == study.id
                            ? 'bg-gray-800 text-white'
                            : 'hover:bg-gray-200 hover:bg-opacity-50',
                          'cursor-pointer flex p-4 border-b border-blue-gray-200',
                        ]"
                        @click="selectStudy(study)"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="flex-shrink-0 -mt-0.5 h-6 w-6 text-blue-gray-400"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"
                          />
                        </svg>
                        <div class="ml-3 text-sm w-full">
                          <p class="font-medium text-blue-gray-900">
                            {{ study.name }}
                            <span
                              class="float-right"
                              v-if="study.sample.molecules.length > 0"
                            >
                              <img
                                class="flex-shrink-0 -mt-0.5 h-6 w-6 text-blue-gray-400"
                                src="https://upload.wikimedia.org/wikipedia/sco/3/35/ChEBI_logo.png"
                                alt=""
                              />
                            </span>
                          </p>
                          <!-- <p class="mt-1 text-blue-gray-500">{{ item.description }}</p> -->
                        </div>
                      </a>
                    </div>
                  </nav>
                  <div
                    style="height: 80vh; overflow: scroll !important"
                    class="flex-1 xl:overflow-y-auto p-2"
                  >
                    <div class="mx-auto flex flex-col md:px-4 xl:px-0">
                      <main class="flex-1">
                        <div class="relative mx-auto md:px-4 xl:px-0">
                          <div class="pt-5 pb-16">
                            <div class="px-4 sm:px-6">
                              <h1 class="text-3xl font-extrabold text-gray-900">
                                {{ selectedStudy.name }}
                              </h1>
                            </div>
                            <div class="lg:hidden px-4 sm:px-6">
                              <label for="selected-tab" class="sr-only"
                                >Select a tab</label
                              >
                              <select
                                id="selected-tab"
                                name="selected-tab"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm rounded-md"
                              >
                                <option
                                  v-for="tab in studyTabs"
                                  :key="tab.name"
                                  :selected="tab.current"
                                >
                                  {{ tab.name }}
                                </option>
                              </select>
                            </div>
                            <div class="hidden lg:block px-4 sm:px-6">
                              <div class="border-b border-gray-200">
                                <nav class="-mb-px flex space-x-8">
                                  <a
                                    v-for="tab in studyTabs"
                                    :key="tab.name"
                                    @click="selectTab(tab)"
                                    :class="[
                                      tab.current
                                        ? 'border-teal-500 text-teal-600'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                                      'cursor-pointer whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
                                    ]"
                                  >
                                    {{ tab.name }}
                                  </a>
                                </nav>
                              </div>
                            </div>
                            <div
                              v-if="currentStudyTab.name == 'Meta Data'"
                              class="px-4 sm:px-6"
                            >
                              <div class="pt-4 xl:col-span-2">
                                <div class="mb-3">
                                  <label
                                    for="study-name"
                                    class="block text-sm font-medium text-gray-700"
                                  >
                                    Study Name
                                  </label>
                                  <div class="mt-1">
                                    <input
                                      type="text"
                                      name="study-name"
                                      v-model="studyName"
                                      class="block w-full shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm border-gray-300 rounded-md"
                                    />
                                  </div>
                                </div>
                                <div class="mb-3">
                                  <label
                                    for="description"
                                    class="block text-sm font-medium text-gray-700"
                                  >
                                    Study Description
                                  </label>
                                  <div class="mt-1">
                                    <textarea
                                      id="study-description"
                                      name="study-description"
                                      v-model="studyDescription"
                                      rows="3"
                                      class="block w-full shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm border border-gray-300 rounded-md"
                                    ></textarea>
                                  </div>
                                </div>
                                <div class="mb-3">
                                  <label
                                    for="description"
                                    class="block text-sm font-medium text-gray-700"
                                  >
                                    Tags
                                  </label>
                                </div>
                                <div class="mb-6">
                                  <button
                                    @click="saveStudyDetails"
                                    type="button"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                  >
                                    SAVE
                                  </button>
                                </div>
                                <h1 class="text-xl font-bold text-gray-900">
                                  Sample Details
                                </h1>
                                <section
                                  aria-labelledby="activity-title"
                                  class="mt-2 xl:mt-2"
                                >
                                  <div>
                                    <div class="pt-3">
                                      <div>
                                        <label
                                          for="about"
                                          class="block text-sm font-medium text-gray-700"
                                        >
                                          Sample Collection Protocol
                                        </label>
                                        <div class="mt-1">
                                          <textarea
                                            readonly
                                            id="about"
                                            name="about"
                                            rows="3"
                                            class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                                          ></textarea>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-500">
                                          Describe your sample collection protocol.
                                          Protocol parameters can be provided below.
                                        </p>
                                      </div>
                                      <!-- <div class="mt-3">
                                        <h2 class="text-sm font-medium text-gray-500">
                                          Sample Collection Parameters
                                        </h2>
                                        <ul role="list" class="mt-2 leading-8">
                                          <li class="inline">
                                            <a
                                              href="#"
                                              class="relative inline-flex items-center rounded-full border border-gray-300 px-3 py-0.5"
                                            >
                                              <div
                                                class="absolute flex-shrink-0 flex items-center justify-center"
                                              >
                                                <span
                                                  class="h-1.5 w-1.5 rounded-full bg-rose-500"
                                                  aria-hidden="true"
                                                ></span>
                                              </div>
                                              <div
                                                class="ml-3.5 text-sm font-medium text-gray-900"
                                              >
                                                Bug
                                              </div>
                                            </a>
                                          </li>
                                          <li class="inline">
                                            <a
                                              href="#"
                                              class="relative inline-flex items-center rounded-full border border-gray-300 px-3 py-0.5"
                                            >
                                              <div
                                                class="absolute flex-shrink-0 flex items-center justify-center"
                                              >
                                                <span
                                                  class="h-1.5 w-1.5 rounded-full bg-teal-500"
                                                  aria-hidden="true"
                                                ></span>
                                              </div>
                                              <div
                                                class="ml-3.5 text-sm font-medium text-gray-900"
                                              >
                                                Accessibility
                                              </div>
                                            </a>
                                          </li>
                                        </ul>
                                      </div> -->
                                    </div>
                                  </div>
                                </section>
                              </div>
                            </div>
                            <div
                              v-if="currentStudyTab.name == 'Experiments (Spectra)'"
                              class="px-4 sm:px-6 md:px-0"
                            >
                              <div class="p-6">
                                <div>
                                  <label
                                    for="location"
                                    class="block text-sm font-medium text-gray-700"
                                    >Select Experiment
                                  </label>
                                  <select
                                    @change="loadSpectra"
                                    v-model="selectedDataset"
                                    id="location"
                                    name="location"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm rounded-md"
                                  >
                                    <option
                                      :value="dataset"
                                      v-for="dataset in selectedStudy.datasets"
                                      :key="dataset.slug"
                                    >
                                      {{ dataset.name }}
                                    </option>
                                  </select>
                                </div>
                                <div class="my-7">
                                  <iframe
                                    v-on:load="loadSpectra()"
                                    name="publicNMRiumIframe"
                                    frameborder="0"
                                    allowfullscreen
                                    class="rounded-md border"
                                    style="width: 100%; height: 75vh; max-height: 600px"
                                    :src="nmriumURL"
                                  ></iframe>
                                </div>
                                <div v-if="currentMolecules.length > 0">
                                  <ul
                                    role="list"
                                    class="grid grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-3 sm:gap-x-6 lg:grid-cols-4 xl:gap-x-8"
                                  >
                                    <li
                                      v-for="molecule in currentMolecules"
                                      :key="molecule.key"
                                      class="relative"
                                    >
                                      <div
                                        class="group block w-full aspect-w-10 aspect-h-7 rounded-lg bg-gray-100 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-offset-gray-100 focus-within:ring-teal-500 overflow-hidden"
                                      >
                                        <div
                                          class="object-cover pointer-events-none group-hover:opacity-75"
                                          v-html="molecule.svg"
                                        ></div>
                                        <button
                                          type="button"
                                          class="absolute inset-0 focus:outline-none"
                                        >
                                          <span class="sr-only"
                                            >View details for {{ file.title }}</span
                                          >
                                        </button>
                                      </div>
                                      <p
                                        class="mt-2 block text-sm font-medium text-gray-900 truncate pointer-events-none"
                                      >
                                        {{ file.title }}
                                      </p>
                                      <p
                                        class="block text-sm font-medium text-gray-500 pointer-events-none"
                                      >
                                        {{ file.size }}
                                      </p>
                                    </li>
                                  </ul>
                                </div>
                                <div class="grid grid-cols-2">
                                  <div class="p-1 pr-2">
                                    <label
                                      for="location"
                                      class="block text-sm font-medium text-gray-700"
                                      >Info</label
                                    >
                                    <div
                                      class="overflow-hidden ring-1 mt-3 ring-black ring-opacity-5 md:rounded-lg"
                                      v-if="selectedSpectraData"
                                    >
                                      <table
                                        class="min-w-full border divide-y divide-gray-300"
                                      >
                                        <thead class="bg-gray-50">
                                          <tr>
                                            <th
                                              scope="col"
                                              class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 lg:pl-8"
                                            >
                                              Field
                                            </th>
                                            <th
                                              scope="col"
                                              class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                            >
                                              Value
                                            </th>
                                          </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                          <tr
                                            v-for="key in Object.keys(
                                              selectedSpectraData.info
                                            )"
                                            :key="key"
                                          >
                                            <td
                                              class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 lg:pl-8"
                                            >
                                              {{ key }}
                                            </td>
                                            <td
                                              class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                                            >
                                              {{ selectedSpectraData.info[key] }}
                                            </td>
                                          </tr>
                                        </tbody>
                                      </table>
                                    </div>
                                  </div>
                                  <div class="p-1 pr-2">
                                    <div class="px-4 py-5 sm:p-6">
                                      <div>
                                        <div class="pt-3">
                                          <div>
                                            <label
                                              for="about"
                                              class="block text-sm font-medium text-gray-700"
                                            >
                                              Extraction
                                            </label>
                                            <div class="mt-1">
                                              <textarea
                                                readonly
                                                id="about"
                                                name="about"
                                                rows="3"
                                                class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                                              ></textarea>
                                            </div>
                                            <p class="mt-2 text-sm text-gray-500">
                                              Describe your extraction protocol. Protocol
                                              parameters can be provided below.
                                            </p>
                                          </div>
                                          <!-- <div class="mt-3">
                                            <h2 class="text-sm font-medium text-gray-500">
                                              Extraction Protocol Parameters
                                            </h2>
                                            <ul role="list" class="mt-2 leading-8">
                                              <li class="inline">
                                                <a
                                                  href="#"
                                                  class="relative inline-flex items-center rounded-full border border-gray-300 px-3 py-0.5"
                                                >
                                                  <div
                                                    class="absolute flex-shrink-0 flex items-center justify-center"
                                                  >
                                                    <span
                                                      class="h-1.5 w-1.5 rounded-full bg-rose-500"
                                                      aria-hidden="true"
                                                    ></span>
                                                  </div>
                                                  <div
                                                    class="ml-3.5 text-sm font-medium text-gray-900"
                                                  >
                                                    Bug
                                                  </div>
                                                </a>
                                                
                                              </li>
                                              <li class="inline">
                                                <a
                                                  href="#"
                                                  class="relative inline-flex items-center rounded-full border border-gray-300 px-3 py-0.5"
                                                >
                                                  <div
                                                    class="absolute flex-shrink-0 flex items-center justify-center"
                                                  >
                                                    <span
                                                      class="h-1.5 w-1.5 rounded-full bg-teal-500"
                                                      aria-hidden="true"
                                                    ></span>
                                                  </div>
                                                  <div
                                                    class="ml-3.5 text-sm font-medium text-gray-900"
                                                  >
                                                    Accessibility
                                                  </div>
                                                </a>
                                                
                                              </li>
                                            </ul>
                                          </div> -->
                                        </div>
                                        <div class="mt-3">
                                          <label
                                            for="about"
                                            class="block text-sm font-medium text-gray-700"
                                          >
                                            NMR Sample preparation
                                          </label>
                                          <div class="mt-1">
                                            <textarea
                                              readonly
                                              id="about"
                                              name="about"
                                              rows="3"
                                              class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                                            ></textarea>
                                          </div>
                                          <p class="mt-2 text-sm text-gray-500">
                                            Describe NMR sample preparation protocol
                                            details. Protocol parameters can be provided
                                            below.
                                          </p>
                                        </div>
                                        <!-- <div class="mt-3">
                                          <h2 class="text-sm font-medium text-gray-500">
                                            Extraction Protocol Parameters
                                          </h2>
                                          <ul role="list" class="mt-2 leading-8">
                                            <li class="inline">
                                              <a
                                                href="#"
                                                class="relative inline-flex items-center rounded-full border border-gray-300 px-3 py-0.5"
                                              >
                                                <div
                                                  class="absolute flex-shrink-0 flex items-center justify-center"
                                                >
                                                  <span
                                                    class="h-1.5 w-1.5 rounded-full bg-rose-500"
                                                    aria-hidden="true"
                                                  ></span>
                                                </div>
                                                <div
                                                  class="ml-3.5 text-sm font-medium text-gray-900"
                                                >
                                                  Bug
                                                </div>
                                              </a>
                                              
                                            </li>
                                            <li class="inline">
                                              <a
                                                href="#"
                                                class="relative inline-flex items-center rounded-full border border-gray-300 px-3 py-0.5"
                                              >
                                                <div
                                                  class="absolute flex-shrink-0 flex items-center justify-center"
                                                >
                                                  <span
                                                    class="h-1.5 w-1.5 rounded-full bg-teal-500"
                                                    aria-hidden="true"
                                                  ></span>
                                                </div>
                                                <div
                                                  class="ml-3.5 text-sm font-medium text-gray-900"
                                                >
                                                  Accessibility
                                                </div>
                                              </a>
                                              
                                            </li>
                                          </ul>
                                        </div> -->
                                      </div>
                                      <div class="mt-3">
                                        <div>
                                          <label
                                            for="about"
                                            class="block text-sm font-medium text-gray-700"
                                          >
                                            Data transformation
                                          </label>
                                          <div class="mt-1">
                                            <textarea
                                              readonly
                                              id="about"
                                              name="about"
                                              rows="3"
                                              class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                                            ></textarea>
                                          </div>
                                          <p class="mt-2 text-sm text-gray-500">
                                            Describe any data transformation protocol
                                            performed. Protocol parameters can be provided
                                            below.
                                          </p>
                                        </div>
                                        <!-- <div class="mt-3">
                                          <h2 class="text-sm font-medium text-gray-500">
                                            Data transformation Protocol Parameters
                                          </h2>
                                          <ul role="list" class="mt-2 leading-8">
                                            <li class="inline">
                                              <a
                                                href="#"
                                                class="relative inline-flex items-center rounded-full border border-gray-300 px-3 py-0.5"
                                              >
                                                <div
                                                  class="absolute flex-shrink-0 flex items-center justify-center"
                                                >
                                                  <span
                                                    class="h-1.5 w-1.5 rounded-full bg-rose-500"
                                                    aria-hidden="true"
                                                  ></span>
                                                </div>
                                                <div
                                                  class="ml-3.5 text-sm font-medium text-gray-900"
                                                >
                                                  Bug
                                                </div>
                                              </a>
                                              
                                            </li>
                                            <li class="inline">
                                              <a
                                                href="#"
                                                class="relative inline-flex items-center rounded-full border border-gray-300 px-3 py-0.5"
                                              >
                                                <div
                                                  class="absolute flex-shrink-0 flex items-center justify-center"
                                                >
                                                  <span
                                                    class="h-1.5 w-1.5 rounded-full bg-teal-500"
                                                    aria-hidden="true"
                                                  ></span>
                                                </div>
                                                <div
                                                  class="ml-3.5 text-sm font-medium text-gray-900"
                                                >
                                                  Accessibility
                                                </div>
                                              </a>
                                              
                                            </li>
                                          </ul>
                                        </div> -->
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div
                              v-if="currentStudyTab.name == 'Chemical Composition'"
                              class="px-4 sm:px-6"
                            >
                              <div class="py-4 max-w-7xl mx-auto">
                                <div
                                  class="pb-3 border-b border-gray-200 sm:flex sm:items-center sm:justify-between"
                                >
                                  <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Structures
                                  </h3>
                                  <!-- <div class="mt-3 sm:mt-0 sm:ml-4">
                                    <button
                                      type="button"
                                      class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                    >
                                      Add new
                                    </button>
                                  </div> -->
                                </div>
                              </div>

                              <div class="grid grid-cols-2 gap-2">
                                <div class="pr-2">
                                  <div
                                    v-if="selectedStudy.sample.molecules.length > 0"
                                    class="flow-root"
                                  >
                                    <ul role="list" class="-mb-8">
                                      <li
                                        v-for="molecule in selectedStudy.sample.molecules"
                                        :key="molecule.STANDARD_INCHI"
                                      >
                                        <div class="relative pb-8">
                                          <span
                                            class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200"
                                            aria-hidden="true"
                                          ></span>
                                          <div
                                            class="relative flex items-start space-x-3"
                                          >
                                            <div
                                              v-if="molecule && molecule.pivot"
                                              class="relative"
                                            >
                                              <img
                                                class="h-10 w-10 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white"
                                                :src="
                                                  'https://ui-avatars.com/api/?name=' +
                                                  (
                                                    '0' +
                                                    molecule.pivot.percentage_composition
                                                  ).slice(-2) +
                                                  '%2BUser&color=7F9CF5&background=EBF4FF'
                                                "
                                                alt=""
                                              />
                                              <!-- <span
                                                class="absolute -bottom-0.5 -right-1 bg-white rounded-tl px-0.5 py-px"
                                              >
                                                <svg
                                                  class="h-5 w-5 text-gray-400"
                                                  x-description="Heroicon name: solid/chat-alt"
                                                  xmlns="http://www.w3.org/2000/svg"
                                                  viewBox="0 0 20 20"
                                                  fill="currentColor"
                                                  aria-hidden="true"
                                                >
                                                  <path
                                                    fill-rule="evenodd"
                                                    d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z"
                                                    clip-rule="evenodd"
                                                  ></path>
                                                </svg>
                                              </span> -->
                                            </div>
                                            <div class="min-w-0 flex-1">
                                              <div>
                                                <div class="text-sm">
                                                  <a
                                                    href="#"
                                                    class="font-medium text-gray-900"
                                                    >{{ molecule.STANDARD_INCHI }}</a
                                                  >
                                                </div>
                                                <!-- <p class="mt-0.5 text-sm text-gray-500">
                                                  Commented 6d ago
                                                </p> -->
                                              </div>
                                              <div class="mt-2 text-sm text-gray-700">
                                                <div
                                                  class="rounded-md border my-3 flex justify-center items-center"
                                                >
                                                  <span
                                                    v-html="getSVGString(molecule)"
                                                  ></span>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </li>
                                    </ul>
                                  </div>
                                  <div v-else>
                                    <div class="text-center my-10 py-10">
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
                                        />
                                      </svg>
                                      <h3 class="mt-2 text-sm font-medium text-gray-900">
                                        No structures associated with the sample yet!
                                      </h3>
                                      <p class="mt-1 text-sm text-gray-500">
                                        Get started by adding a new molecule.
                                      </p>
                                    </div>
                                  </div>
                                </div>
                                <div class="pl-2">
                                  <div class="sm:col-span-4">
                                    <label
                                      for="email"
                                      class="block text-sm font-medium text-gray-700"
                                    >
                                      SMILES
                                    </label>
                                    <div class="mt-1 mb-2">
                                      <input
                                        @blur="loadSmiles"
                                        id="smiles"
                                        name="smiles"
                                        v-model="smiles"
                                        type="text"
                                        class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                      />
                                    </div>
                                  </div>
                                  <div class="relative">
                                    <div
                                      class="absolute inset-0 flex items-center"
                                      aria-hidden="true"
                                    >
                                      <div class="w-full border-t border-gray-300" />
                                    </div>
                                    <div class="relative flex justify-center">
                                      <span class="px-2 bg-white text-sm text-gray-500">
                                        Or
                                      </span>
                                    </div>
                                  </div>
                                  <div
                                    class="w-full border my-4 rounded-md"
                                    style="height: 400px"
                                    id="structureSearchEditor"
                                  />
                                  <div class="mt-1 mb-6">
                                    <label
                                      for="email"
                                      class="block text-sm font-medium text-gray-700"
                                    >
                                      Percentage composition ({{ percentage }}%)
                                    </label>
                                  </div>
                                  <button
                                    @click="saveMolecule"
                                    type="button"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                  >
                                    SAVE
                                  </button>
                                </div>
                              </div>
                              <div v-if="selectedStudy">
                                <div
                                  v-if="
                                    selectedStudy.molecules &&
                                    selectStudy.molecules.length == 0
                                  "
                                >
                                  <template>
                                    <button
                                      type="button"
                                      class="relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                    >
                                      <svg
                                        class="mx-auto h-12 w-12 text-gray-400"
                                        xmlns="http://www.w3.org/2000/svg"
                                        stroke="currentColor"
                                        fill="none"
                                        viewBox="0 0 48 48"
                                        aria-hidden="true"
                                      >
                                        <path
                                          stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M8 14v20c0 4.418 7.163 8 16 8 1.381 0 2.721-.087 4-.252M8 14c0 4.418 7.163 8 16 8s16-3.582 16-8M8 14c0-4.418 7.163-8 16-8s16 3.582 16 8m0 0v14m0-4c0 4.418-7.163 8-16 8S8 28.418 8 24m32 10v6m0 0v6m0-6h6m-6 0h-6"
                                        />
                                      </svg>
                                      <span
                                        class="mt-2 block text-sm font-medium text-gray-900"
                                      >
                                        Create a new database
                                      </span>
                                    </button>
                                  </template>
                                </div>
                                <div
                                  v-if="
                                    selectedStudy.molecules &&
                                    selectStudy.molecules.length > 0
                                  "
                                >
                                  Mols
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </main>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="selectedTab == 'Files'">
              <div
                class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
                v-if="project.data.files"
              >
                <nav
                  v-if="$page.props.selectedFolder"
                  class="flex p-3"
                  aria-label="Breadcrumb"
                >
                  <ol role="list" class="flex items-center space-x-2">
                    <li>
                      <div>
                        <a class="text-gray-400 hover:text-gray-900">
                          <HomeIcon class="flex-shrink-0 h-5 w-5" aria-hidden="true" />
                          <span class="sr-only">Home</span>
                        </a>
                      </div>
                    </li>
                    <li
                      v-for="page in $page.props.selectedFolder.split('/')"
                      :key="page.name"
                    >
                      <div v-if="page != ''" class="flex items-center">
                        <ChevronRightIcon
                          class="flex-shrink-0 h-5 w-5 text-gray-400"
                          aria-hidden="true"
                        />
                        <a
                          class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"
                          :aria-current="page ? 'page' : undefined"
                          >{{ page }}</a
                        >
                      </div>
                    </li>
                  </ol>
                </nav>
                <div class="min-w-0 flex-1 min-h-screen border-t border-gray-200 lg:flex">
                  <aside
                    class="hidden py-3 px-2 lg:block lg:flex-shrink-0 lg:order-first"
                  >
                    <div
                      v-if="project.data.files != null"
                      class="h-full relative flex flex-col w-64 border-r border-gray-200 overflow-y-auto"
                    >
                      <children :file="project.data.files" :project="project"></children>
                    </div>
                  </aside>
                  <section
                    class="min-w-0 p-6 flex-1 h-full flex flex-col overflow-y-auto lg:order-last"
                  >
                    <div
                      class="mb-3"
                      v-if="
                        $page.props.selectedFileSystemObject &&
                        $page.props.selectedFileSystemObject.has_children
                      "
                    >
                      <ul
                        role="list"
                        class="mb-3 grid grid-cols-1 gap-5 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4"
                      >
                        <li
                          v-for="file in $page.props.selectedFileSystemObject.children"
                          :key="file.key"
                          class="relative shadow rounded-lg"
                        >
                          <div
                            class="group block w-full aspect-w-10 aspect-h-7 py-4 bg-gray-100 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-offset-gray-100 focus-within:ring-indigo-500 overflow-hidden"
                          >
                            <span v-if="file.type == 'directory'">
                              <FolderIcon
                                @dblclick.stop="displaySelected(file)"
                                class="cursor-pointer h-28 w-28 text-gray-400 flex-shrink-0 mx-auto"
                                aria-hidden="true"
                              />
                            </span>
                            <span v-else>
                              <DocumentTextIcon
                                class="h-28 w-28 text-gray-400 flex-shrink-0 mx-auto"
                                aria-hidden="true"
                              />
                            </span>
                          </div>
                          <p
                            class="mt-2 px-2 py-1 block text-sm font-medium text-gray-900 pointer-events-none"
                          >
                            {{ file.name }}
                          </p>
                          <p
                            class="block text-sm font-medium text-gray-500 pointer-events-none"
                          >
                            {{ file.size }}
                          </p>
                        </li>
                      </ul>
                    </div>
                    <div v-else>
                      <div class="">
                        <span
                          v-if="
                            $page.props.selectedFileSystemObject &&
                            $page.props.selectedFileSystemObject.type == 'file'
                          "
                        >
                          <File-details
                            :file="$page.props.selectedFileSystemObject"
                          ></File-details>
                        </span>
                      </div>
                    </div>
                  </section>
                </div>
              </div>
            </div>
            <div v-if="selectedTab == 'License'">
              <div class="pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <h3 class="text-xl font-extrabold text-blue-gray-900">
                  {{ project.data.license.title }}
                </h3>

                <div class="mt-6 space-y-8 divide-y divide-y-blue-gray-200">
                  <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-6 sm:gap-x-6">
                    <div class="sm:col-span-6">
                      <p class="mt-1 text-md text-blue-gray-500">
                        {{ project.data.license.description }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import FileDetails from "@/Shared/FileDetails.vue";
import {
  FolderIcon,
  DocumentTextIcon,
  ChevronRightIcon,
  HomeIcon,
} from "@heroicons/vue/solid";

export default {
  components: {
    AppLayout,
    FileDetails,
    FolderIcon,
    DocumentTextIcon,
    ChevronRightIcon,
    HomeIcon,
  },
  props: ["project"],
  data() {
    return {
      selectedTab: "Data",
      selectedStudy: null,
      selectedDataset: null,

      selectedSpectraData: null,

      currentMolecules: [],

      studyTabs: [
        { name: "Experiments (Spectra)", current: true },
        { name: "Chemical Composition", current: false },
        { name: "Meta Data", current: false },
      ],

      currentTab: "",
      tabs: [
        {
          name: "Data",
          description: "",
          icon: "",
        },
        {
          name: "Studies",
          description: "",
          icon: "",
        },
        {
          name: "Files",
          description: "",
          icon: "",
        },
        {
          name: "License",
          description: "",
          icon: "",
        },
      ],
    };
  },
  mounted() {
    if (this.project) {
      this.$page.props.selectedFileSystemObject = this.project.data.files;
      this.selectStudy(this.project.data.studies[0]);
    }
  },
  methods: {
    selectTab(tab) {
      this.tabs.forEach((t) => {
        if (t.name == tab.name) {
          t.current = true;
        } else {
          t.current = false;
        }
      });
    },
    selectStudy(study) {
      this.selectedStudy = study;
      this.studyName = this.selectedStudy.name;
      this.studyDescription = this.selectedStudy.description;
      let tags = [];
      this.selectedStudy.tags.forEach((t) => {
        tags.push({
          text: t.name["en"],
        });
      });
      this.studyTags = tags;
      if(this.selectedStudy.datasets && this.selectedStudy.datasets.length > 0){
        this.selectDataset(this.selectedStudy.datasets[0]);
      }
    },
    selectDataset(dataset) {
      this.selectedDataset = dataset;
      this.loadSpectra();
    },
    loadSpectra() {
      const iframe = window.frames.publicNMRiumIframe;
      if (iframe) {
        if (!this.selectedDataset.nmrium_info) {
          let data = {
            data: 
              this.url +
                "/download/asc/datasets/" +
                this.selectedDataset.id +
                "/" +
                this.selectedDataset.name +
                ".zip"
            
            ,
            type: "url",
          };
          iframe.postMessage({ type: `nmr-wrapper:load`, data }, "*");
        } else {
          let data = { data: {
            spectra:[JSON.parse(this.selectedDataset.nmrium_info)]
          }, type: "nmrium" };

          iframe.postMessage({ type: `nmr-wrapper:load`, data }, "*");
        }
      }
    },
  },
  computed: {
    currentStudyTab() {
      return this.studyTabs.find((t) => t.current);
    },
    nmriumURL() {
      return this.$page.props.nmriumURL
        ? String(this.$page.props.nmriumURL + "?workspace=embedded&id=" + Math.random())
        : "http://nmriumdev.nmrxiv.org?workspace=embedded&id=" + Math.random();
    },
    url() {
      return String(this.$page.props.url);
    },
  },
};
</script>
