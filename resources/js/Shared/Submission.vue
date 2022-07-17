<template>
  <jet-dialog-modal
    :max-width="'7xl'"
    :show="openCreateDatasetDialog"
    @close="openCreateDatasetDialog = false"
  >
    <template #title>
      <div
        v-if="!loading && currentDraft"
        class="lg:border-b lg:border-gray-200 pb-3 pr-5 -mx-6"
      >
        <div class="flex items-left justify-start pl-5 float-left">
          <b>{{ steps.find((step) => step.status === "current").name }}</b>
        </div>
        <nav class="flex items-right justify-end" aria-label="Progress">
          <p class="text-sm font-medium">
            Step {{ steps.findIndex((step) => step.status === "current") + 1 }} of
            {{ steps.length }}
          </p>
          <ol role="list" class="ml-8 flex items-center space-x-5">
            <li v-for="step in steps" :key="step.name">
              <a
                v-if="step.status === 'complete'"
                :href="step.href"
                class="block w-2.5 h-2.5 bg-teal-600 rounded-full hover:bg-teal-900"
              >
              </a>
              <a
                v-else-if="step.status === 'current'"
                :href="step.href"
                class="relative flex items-center justify-center"
                aria-current="step"
              >
                <span class="absolute w-5 h-5 p-px flex" aria-hidden="true">
                  <span class="w-full h-full rounded-full bg-blue-100" />
                </span>
                <span
                  class="relative block w-2.5 h-2.5 bg-teal-600 rounded-full"
                  aria-hidden="true"
                />
              </a>
              <a
                v-else
                :href="step.href"
                class="block w-2.5 h-2.5 bg-gray-200 rounded-full hover:bg-gray-400"
              >
              </a>
            </li>
          </ol>
        </nav>
      </div>
      <div class="lg:border-b lg:border-gray-200 pb-3 pr-5 -mx-6" v-else>
        <div
          v-if="!currentDraft"
          class="flex text-md font-bold items-left justify-start pl-5"
        >
          Drafts
        </div>
      </div>
    </template>
    <template #content>
      <div
        class="absolute inset-0 flex justify-center items-center z-10 bg-white rounded bg-opacity-70"
        v-if="loadingStep"
      >
        <svg
          class="animate-spin -ml-1 mr-3 h-5 w-5 text-dark flex-inline inline"
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
      </div>
      <div v-if="!loading">
        <div v-if="drafts.length > 0 && !currentDraft">
          <div>
            <ul role="list" class="-my-5 divide-y divide-gray-200">
              <li v-for="draft in drafts" :key="draft.id" class="py-4">
                <div class="flex items-center space-x-4">
                  <div class="flex-1 min-w-0">
                    <p class="text-lg font-large text-black truncate">
                      <b>{{ draft.name }}</b>
                    </p>
                    <p class="text-sm font-medium text-gray-600 truncate pr-10">
                      {{ draft.description }}
                    </p>
                    <p class="text-sm font-medium text-gray-500 truncate">
                      ID: {{ draft.key }}
                    </p>
                    <p class="text-sm text-gray-500 truncate">
                      Created at: {{ formatDateTime(draft.created_at) }}
                    </p>
                  </div>
                  <div>
                    <button
                      @click="selectDraft(draft)"
                      class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ml-2"
                    >
                      Select
                    </button>
                  </div>
                </div>
              </li>
            </ul>
          </div>
          <div class="relative mt-6">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
              <div class="w-full border-t border-gray-300" />
            </div>
            <div class="relative flex justify-center">
              <span class="px-2 bg-white text-sm text-gray-500"> Or </span>
            </div>
          </div>
          <div class="my-6 justify-center items-center flex">
            <button
              @click="createNewDraft()"
              class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
            >
              Create New
            </button>
          </div>
        </div>
        <div v-else>
          <div v-if="currentStep">
            <div id="submission-dropzone" v-if="currentStep.id == '01'">
              <div v-if="currentDraft">
                <div class="mb-3">
                  <label
                    for="project-name"
                    class="block text-sm font-medium text-gray-700"
                  >
                    Project Name
                  </label>
                  <div class="mt-1">
                    <input
                      type="text"
                      name="project-name"
                      v-model="draftName"
                      class="block w-full shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm border-gray-300 rounded-md"
                    />
                  </div>
                </div>
                <div class="mb-3">
                  <label
                    for="description"
                    class="block text-sm font-medium text-gray-700"
                  >
                    <span
                      @click="
                        draftDescription =
                          'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore'
                      "
                      >Project Description</span
                    >
                  </label>
                  <div class="mt-1">
                    <textarea
                      id="description"
                      name="description"
                      v-model="draftDescription"
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
                    Keywords
                  </label>
                  <div>
                    <vue-tags-input
                      placeholder="Type and press enter"
                      :separators="[';', ',']"
                      v-model="draftTag"
                      max-width="100%"
                      :tags="draftTags"
                      @tags-changed="(newTags) => (draftTags = newTags)"
                    />
                  </div>
                </div>
                <div>
                  <div>
                    <small class="cursor-pointer">Draft ID: {{ currentDraft.key }}</small>
                    <div>
                      <file-system-browser
                        @loading="updateLoadingStatus"
                        :readonly="false"
                        ref="fsbRef"
                        :draft="currentDraft"
                      ></file-system-browser>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="-mx-6 -my-4" v-if="currentStep.id == '02'">
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
                        v-for="study in studies"
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
                                class="flex-shrink-0 -mt-0.5 mr-2 h-6 w-6 text-blue-gray-400"
                                src="https://upload.wikimedia.org/wikipedia/sco/3/35/ChEBI_logo.png"
                                alt=""
                              />
                            </span>
                          </p>
                          <p class="mt-1 text-blue-gray-500">
                            <span
                              :class="[
                                ds.nmrium_info
                                  ? 'bg-green-100 text-gray-800'
                                  : 'bg-gray-100 text-gray-800',
                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mr-1',
                              ]"
                              v-for="ds in study.datasets"
                              :key="ds.id"
                            >
                              {{ ds.name }}
                            </span>
                          </p>
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
                                  v-for="tab in tabs"
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
                                    v-for="tab in tabs"
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
                              v-if="currentTab.name == 'Meta Data'"
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
                                  <div>
                                    <vue-tags-input
                                      v-model="studyTag"
                                      max-width="100%"
                                      :tags="studyTags"
                                      @tags-changed="(newTags) => (studyTags = newTags)"
                                    />
                                  </div>
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
                              </div>
                            </div>
                            <div
                              v-if="currentTab.name == 'Experiments (Spectra)'"
                              class="px-4 sm:px-6 md:px-0"
                            >
                              <div class="p-6">
                                <div>
                                  <label
                                    for="location"
                                    class="block text-sm font-medium text-gray-700"
                                    >Select Experiment
                                    <small
                                      ><span class="float-right" v-if="autoSaving"
                                        >Saving...</span
                                      ></small
                                    >
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
                                    name="submissionNMRiumIframe"
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
                                    class="flex gap-x-4 gap-y-8 sm:gap-x-6 xl:gap-x-8"
                                  >
                                    <li
                                      v-for="molecule in currentMolecules"
                                      :key="molecule.key"
                                      class="relative"
                                    >
                                      <div
                                        class="group flex justify-center block w-full aspect-w-10 aspect-h-7 rounded-lg bg-gray-100 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-offset-gray-100 focus-within:ring-teal-500 overflow-hidden"
                                      >
                                        <div
                                          class="p-4 object-cover pointer-events-none group-hover:opacity-75"
                                          v-html="molecule.svg"
                                        ></div>
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
                              v-if="currentTab.name == 'Sample Info'"
                              class="px-4 sm:px-6 pt-4"
                            >
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
                                        Describe your sample collection protocol. Protocol
                                        parameters can be provided below.
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
                              <div class="py-4 max-w-7xl mx-auto">
                                <div class="sm:flex sm:items-center sm:justify-between">
                                  <h3 class="text-xl font-bold text-gray-900">
                                    Chemical composition (optional)
                                  </h3>
                                </div>
                                <p
                                  class="mt-2 pb-3 border-b border-gray-200 text-sm text-gray-500"
                                >
                                  Residual Complexity (RC) refers to the subtle but
                                  significant convolution of major and minor ("hidden")
                                  chemical species in materials that originate from
                                  biochemical or synthetic reaction mixtures. Certain
                                  levels of these chemical species (molecules) usually
                                  remain present even after a number of purification
                                  steps, and this RC is to some degree conserved even in
                                  highly purified materials. In principle, RC affects all
                                  "pure" materials, including synthetic compound, whenever
                                  chromatographic or other purification steps are required
                                  prior to their biological evaluation. Please report the
                                  chemical composition (if known) of your sample. If
                                  unknown please report the molecules that you expect to
                                  be present the sample with our the composition.
                                </p>
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
                                    <slider
                                      :min="0"
                                      :max="getMax"
                                      :height="10"
                                      v-model="percentage"
                                      color="#000"
                                      track-color="#999"
                                    />
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
            <div v-if="currentStep.id == '03'">
              <div v-if="project.status == 'complete'">
                <div
                  class="relative grid grid-cols-1 gap-x-16 max-w-7xl mx-auto lg:grid-cols-2"
                >
                  <section
                    aria-labelledby="summary-heading"
                    class="bg-gray-50 pb-10 px-4 sm:px-6 lg:px-0 lg:pb-16 lg:bg-transparent lg:row-start-1 lg:col-start-2"
                  >
                    <div class="p-4 px-6 bg-gray-100 rounded-lg">
                      <div class="max-w-lg mx-auto lg:max-w-none">
                        <h2
                          id="summary-heading"
                          class="text-lg font-medium text-gray-900"
                        >
                          Summary
                        </h2>
                      </div>
                      <div class="mt-2">
                        <h3 class="text-sm leading-6 font-medium text-gray-700">
                          Citation
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                          Author, 1., & Author, 2.. (2022). FAIR, consensus-driven NMR
                          data repository and computational platform. The ultimate goal is
                          to accelerate broader coordination and data sharing among
                          natural product (NP) researchers by enabling storage,
                          management, sharing and analysis of NMR data.
                        </p>
                      </div>
                      <div class="mt-5 overflow-auto">
                        <table class="min-w-full rounded border divide-y divide-gray-300 ">
                          <thead class="bg-gray-50">
                            <tr>
                              <th
                                scope="col"
                                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 lg:pl-8"
                              >
                                Study
                              </th>
                              <th
                                scope="col"
                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                              >
                                Datasets
                              </th>
                            </tr>
                          </thead>
                          <tbody class="divide-y divide-gray-200 bg-white">
                            <tr
                              v-for="study in this.project.studies"
                              :key="study.id"
                              :value="study.name"
                            >
                              <td
                                class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 lg:pl-8"
                              >
                                {{ study.name }}
                              </td>
                              <td
                                class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                              >
                                <div v-for="dataset in study.datasets" :key="dataset.id">
                                  <span class="break-normal">
                                    {{ dataset.name }}
                                  </span>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      <div class="my-5">
                        <h3 class="text-sm leading-6 mb-2 font-medium text-gray-700">
                          Quick links
                        </h3>
                        <jet-button
                          type="submit"
                          class="mr-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                        >
                          Download Zip
                        </jet-button>
                        <jet-button
                          type="submit"
                          class="mr-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                        >
                          MD5 hashmap
                        </jet-button>
                        <jet-button
                          type="submit"
                          class="mr-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                        >
                          Embed
                        </jet-button>
                        <jet-button
                          type="submit"
                          class="mr-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                        >
                          Share
                        </jet-button>
                      </div>
                    </div>
                  </section>
                  <div
                    class="pb-36 px-4 sm:px-6 lg:pb-16 lg:px-0 lg:row-start-1 lg:col-start-1"
                  >
                    <div class="max-w-lg mx-auto lg:max-w-none">
                      <section>
                        <div>
                          <div>
                            <label
                              for="project-name"
                              class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                            >
                              Project name
                            </label>
                            <div class="mt-1">
                              <input
                                v-model="this.project.name"
                                type="text"
                                autocomplete="given-name"
                                class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                              />
                            </div>
                            <jet-input-error
                              :message="updateProjectForm.errors.name"
                              class="mt-2"
                            />
                          </div>
                          <div class="mt-4">
                            <label
                              for="description"
                              class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                            >
                              Description
                            </label>
                            <div class="mt-1">
                              <textarea
                                v-model="this.project.description"
                                rows="4"
                                class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                              />
                            </div>
                            <jet-input-error
                              :message="updateProjectForm.errors.description"
                              class="mt-2"
                            />
                          </div>
                        </div>
                      </section>

                      <section class="mt-5">
                        <h2
                          id="contact-info-heading"
                          class="text-lg font-medium text-gray-900"
                        >
                          Share and Release
                        </h2>
                        <div>
                          <fieldset>
                            <legend class="text-sm font-medium text-gray-900">
                              Privacy
                            </legend>
                            <div class="mt-2 space-y-5">
                              <div class="relative flex items-start">
                                <div class="absolute flex items-center h-5">
                                  <input
                                    :checked="this.updateProjectForm.is_public === true"
                                    v-model="this.updateProjectForm.is_public"
                                    id="privacy-public"
                                    name="privacy"
                                    value="true"
                                    aria-describedby="privacy-public-description"
                                    type="radio"
                                    class="focus:ring-gray-500 h-4 w-4 text-gray-600 border-gray-300"
                                  />
                                </div>
                                <div class="pl-7 text-sm">
                                  <label
                                    for="privacy-public"
                                    class="font-medium text-gray-900"
                                  >
                                    Public access
                                  </label>
                                  <p
                                    id="privacy-public-description"
                                    class="text-gray-500"
                                  >
                                    Everyone with the link will see this project.
                                  </p>
                                </div>
                              </div>
                              <div>
                                <div class="relative flex items-start">
                                  <div class="absolute flex items-center h-5">
                                    <input
                                      :checked="
                                        this.updateProjectForm.is_public === false
                                      "
                                      v-model="this.updateProjectForm.is_public"
                                      id="privacy-private-to-project"
                                      name="privacy"
                                      value="false"
                                      aria-describedby="privacy-private-to-project-description"
                                      type="radio"
                                      class="focus:ring-gray-500 h-4 w-4 text-gray-600 border-gray-300"
                                    />
                                  </div>
                                  <div class="pl-7 text-sm">
                                    <label
                                      for="privacy-private-to-project"
                                      class="font-medium text-gray-900"
                                    >
                                      Private to project members
                                    </label>
                                    <p
                                      id="privacy-private-to-project-description"
                                      class="text-gray-500"
                                    >
                                      Only members of this project would be able to
                                      access.
                                    </p>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </fieldset>
                        </div>
                        <div
                          v-if="
                            this.updateProjectForm.is_public == 'false' ||
                            !this.updateProjectForm.is_public
                          "
                          class="mt-5"
                        >
                          <div>
                            <label
                              class="block text-sm font-medium text-gray-700, block text-sm font-medium text-gray-700"
                            >
                              Choose Release Date
                            </label>
                            <Datepicker
                              v-model="updateProjectForm.releaseDate"
                            ></Datepicker>
                            <p class="mt-1 text-sm text-gray-500">
                              Choose release date to auto publish your project to public.
                            </p>
                          </div>
                        </div>

                        <div class="mt-5">
                          <label
                            for="share-url"
                            class="block text-sm font-medium text-gray-700"
                          >
                            Share URL
                          </label>
                          <div class="mt-1">
                            <input
                              readonly
                              v-if="
                                this.updateProjectForm.is_public == 'false' ||
                                !this.updateProjectForm.is_public
                              "
                              v-model="this.project.private_url"
                              type="text"
                              autocomplete="given-name"
                              class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            />
                            <input
                              readonly
                              v-else
                              v-model="this.project.public_url"
                              type="text"
                              autocomplete="given-name"
                              class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            />
                          </div>
                          <div class="mt-1"></div>
                          <p class="mt-1 text-sm text-gray-500">
                            The project and the underlying studies and datasets can be
                            shared with others using this URL.
                          </p>
                        </div>
                        <div class="mt-5">
                          <div>
                            <select-rich
                              label="License"
                              v-model:selected="updateProjectForm.license"
                              class="mt-0"
                              :items="licenses"
                            />
                            <p class="mt-1 text-sm text-gray-500">
                              Choosing license is recommended before releasing the project
                              to public.
                            </p>
                          </div>
                        </div>
                      </section>

                      <div
                        class="mt-10 pt-6 border-t border-gray-200 sm:flex sm:items-center sm:justify-between"
                      >
                        <button
                          @click="updateProject"
                          class="w-full bg-teal-600 border border-transparent rounded-md shadow-sm py-2 px-4 text-sm font-medium text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-50 focus:ring-teal-500 sm:ml-6 sm:order-last sm:w-auto"
                        >
                          Save
                        </button>
                        <p
                          class="mt-4 text-center text-sm text-gray-500 sm:mt-0 sm:text-left"
                        >
                          <!-- lorem ipsum -->
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else>
                <div class="py-16">
                  <div class="text-center">
                    <p
                      class="text-sm font-semibold text-indigo-600 uppercase tracking-wide"
                    >
                      {{ project.name }}
                    </p>
                    <span v-if="project.status == 'queued'">
                      <span
                        class="m-3 relative inline-flex border-dotted border-2 border-gray-300 rounded-lg"
                        ><span
                          class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm rounded-md text-sky-500 bg-white dark:bg-slate-800 transition ease-in-out duration-150 cursor-not-allowed dark:ring-slate-200/20"
                          disabled=""
                          ><h1
                            class="capitalize text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl"
                          >
                            {{ project.status }}
                          </h1></span
                        ></span
                      >
                    </span>
                    <span v-if="project.status == 'processing'">
                      <span
                        class="m-3 relative inline-flex border-dotted border-2 border-gray-300 rounded-lg"
                        ><span
                          class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm rounded-md text-sky-500 bg-white dark:bg-slate-800 transition ease-in-out duration-150 cursor-not-allowed dark:ring-slate-200/20"
                          disabled=""
                          ><h1
                            class="capitalize text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl"
                          >
                            {{ project.status }}
                          </h1></span
                        ><span class="flex absolute h-3 w-3 top-0 right-0 -mt-1 -mr-1"
                          ><span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"
                          ></span
                          ><span
                            class="relative inline-flex rounded-full h-3 w-3 bg-sky-500"
                          ></span></span
                      ></span>
                    </span>
                    <p class="mt-2 text-base text-gray-500">
                      Please allow some time for us to process your submission. You will
                      also recieve an email once your submission is processed
                    </p>
                    <div class="mt-6">
                      <a
                        class="text-base font-medium text-indigo-600 hover:text-indigo-500"
                        >Go back home<span aria-hidden="true"> →</span></a
                      >
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-else>
        <svg
          class="animate-spin -ml-1 mr-3 h-5 w-5 text-dark flex-inline inline"
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
    </template>
    <template #footer>
      <span v-if="!currentStep">
        <jet-secondary-button @click="toggleOpenCreateDatasetDialog">
          Cancel
        </jet-secondary-button>
      </span>
      <span v-else>
        <span v-if="currentStep.id == '01'">
          <jet-secondary-button
            class="ml-2 float-left"
            @click="openSelectDraftsView"
            :class="{ 'opacity-25': createDatasetForm.processing }"
            :disabled="createDatasetForm.processing"
          >
            Drafts
          </jet-secondary-button>
          <jet-secondary-button @click="toggleOpenCreateDatasetDialog">
            Cancel
          </jet-secondary-button>
          <jet-button
            class="ml-2"
            @click="process"
            :class="{ 'opacity-25': createDatasetForm.processing }"
            :disabled="createDatasetForm.processing || loading || loadingStep"
          >
            <span v-if="loadingStep">
              <svg
                class="animate-spin -ml-1 mr-3 h-2 w-2 text-white"
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
            </span>
            Proceed
          </jet-button>
        </span>
        <span v-else-if="currentStep.id == '02'">
          <jet-button
            class="ml-2 float-left"
            @click="selectStep(1)"
            :class="{ 'opacity-25': createDatasetForm.processing }"
            :disabled="createDatasetForm.processing"
          >
            Back
          </jet-button>
          <jet-secondary-button @click="toggleOpenCreateDatasetDialog">
            Cancel
          </jet-secondary-button>
          <jet-button
            class="ml-2"
            @click="closeDraft"
            :class="{ 'opacity-25': createDatasetForm.processing }"
            :disabled="createDatasetForm.processing || loading || loadingStep"
          >
            <span v-if="loadingStep">
              <svg
                class="animate-spin -ml-1 mr-3 h-2 w-2 text-white"
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
            </span>
            Proceed
          </jet-button>
        </span>
        <span v-else-if="currentStep.id == '03'">
          <jet-button @click="toggleOpenCreateDatasetDialog">
            Save and Finish
          </jet-button>
        </span>
      </span>
    </template>
  </jet-dialog-modal>
</template>
<script>
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import VueTagsInput from "@sipec/vue3-tags-input";
import { inject, ref } from "vue";
import slider from "vue3-slider";
import OCL from "openchemlib/full";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import SelectRich from "@/Shared/SelectRich.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import FileSystemBrowser from "./FileSystemBrowser.vue";

export default {
  components: {
    JetDialogModal,
    JetSecondaryButton,
    JetButton,
    VueTagsInput,
    slider,
    SelectRich,
    Datepicker,
    JetInputError,
    FileSystemBrowser,
  },
  props: [],
  data() {
    return {
      openCreateDatasetDialog: false,
      loading: false,
      loadingStep: false,
      currentDraft: null,

      draftTags: [],
      draftName: "",
      draftDescription: "",

      steps: [
        {
          id: "01",
          name: "Files Upload",
          description: "Vitae sed mi luctus laoreet.",
          href: "#",
          status: "upcoming",
        },
        {
          id: "02",
          name: "Assignments & Meta data",
          description: "Cursus semper viverra.",
          href: "#",
          status: "upcoming",
        },
        {
          id: "03",
          name: "Complete",
          description: "Penatibus eu quis ante.",
          href: "#",
          status: "upcoming",
        },
      ],

      tabs: [
        { name: "Experiments (Spectra)", current: true },
        { name: "Sample Info", current: false },
        { name: "Meta Data", current: false },
      ],

      createDatasetForm: this.$inertia.form({
        _method: "POST",
        name: "",
        description: "",
        error_message: null,
        team_id: null,
        owner_id: null,
        color: null,
        starred: null,
        project_id: this.project ? this.project.id : null,
        is_public: ref(false),
      }),

      file: null,

      draftTags: [],
      draftTag: "",

      status: null,

      selectedStudy: null,
      studyName: "",
      studyDescription: "",
      studyTags: [],
      studyTag: "",

      selectedDataset: null,
      selectedSpectraData: null,
      autoSaving: false,
      currentMolecules: [],

      smiles: "",
      percentage: 1,
      editor: null,

      updateProjectForm: this.$inertia.form({
        _method: "PUT",
        name: null,
        description: null,
        error_message: null,
        is_public: false,
        license: null,
        owner_id: null,
        team_id: null,
        license_id: null,
        releaseDate: this.setReleaseDate(),
      }),

      licenses: [],

      project: null,
    };
  },
  mounted() {
    const emitter = inject("emitter");
    emitter.on("openDatasetCreateDialog", (data) => {
      this.currentDraft = null;
      this.fetchDrafts().then((response) => {
        this.drafts = response.data.drafts;
        if (data.draft_id) {
          let selectedDraft = this.drafts.find((d) => d.id == data.draft_id);
          this.selectDraft(selectedDraft);
          this.loading = false;
          this.toggleOpenCreateDatasetDialog();
        } else {
          this.defaultDraft = response.data.default;
          this.toggleOpenCreateDatasetDialog();
          if (this.drafts.length == 0) {
            this.currentDraft = this.defaultDraft;
            this.selectStep(1);
            this.loading = false;
          } else {
            this.loading = false;
          }
        }
      });
    });
  },
  methods: {
    updateLoadingStatus(status){
      this.loadingStep = status
    },
    hasNMRiumInfo(study) {
      let info = true;
      study.datasets.forEach((ds) => {
        if (ds.nmrium_info == null || !ds.nmrium_info) {
          info = false;
        }
      });
      return info;
    },
    updateProject() {
      this.loadingStep = true;
      this.updateProjectForm.name = this.project.name;
      this.updateProjectForm.owner_id = this.project.owner_id;
      this.updateProjectForm.license_id = this.updateProjectForm.license.id;
      this.updateProjectForm.team_id = this.project.team_id;
      this.updateProjectForm.description = this.project.description;
      this.updateProjectForm.post(route("dashboard.project.update", this.project.id), {
        preserveScroll: true,
        onSuccess: () => {
          this.loadingStep = false;
        },
        onError: (err) => {
          this.loadingStep = false;
        },
      });
    },

    loadLicenses() {
      axios.get(route("licenses")).then((res) => {
        this.licenses = res.data;
      });
    },

    setReleaseDate() {
      var current_date = new Date();
      var relase_date = new Date();
      relase_date.setDate(current_date.getDate() + 7);
      return relase_date;
    },

    saveStudyDetails() {
      this.loadingStep = true;
      axios
        .put("/dashboard/studies/" + this.selectedStudy.id + "/update", {
          name: this.studyName,
          description: this.studyDescription,
          tags: this.studyTags.map((a) => a.text),
        })
        .catch((error) => {
          console.log(error);
          this.loadingStep = false;
        })
        .then((response) => {
          this.loadingStep = false;
          this.currentStudy = response;
        });
    },

    getSVGString(molecule) {
      if (molecule.MOL) {
        let mol = OCL.Molecule.fromMolfile("\n  " + molecule.MOL.replaceAll('"', ""));
        return mol.toSVG(200, 200);
      } else {
        console.log(molecule);
      }
    },

    saveMolecule() {
      let mol = this.editor.getMolFile();
      axios
        .post("https://www.cheminfo.org/webservices/inchi", "molfile=" + mol)
        .then((response) => {
          let InChI = response.data.output.InChI;
          let InChIKey = response.data.output.InChIKey;
          let MF = this.editor.getMolecule().getMolecularFormula().formula;
          axios
            .post("/dashboard/studies/" + this.selectedStudy.id + "/molecule", {
              InChI: InChI,
              InChIKey: InChIKey,
              percentage: this.percentage,
              formula: MF,
              mol: mol,
            })
            .then((res) => {
              this.selectedStudy.sample.molecules = res.data;
              this.smiles = "";
              this.percentage = 0;
              this.editor.setSmiles("");
            });
        });
    },

    loadSmiles() {
      if (this.smiles && this.smiles != "") {
        this.editor.setSmiles(this.smiles);
      }
    },

    selectTab(tab) {
      this.tabs.forEach((t) => {
        if (t.name == tab.name) {
          t.current = true;
          this.$nextTick(() => {
            this.editor = OCL.StructureEditor.createSVGEditor("structureSearchEditor", 1);
          });
        } else {
          t.current = false;
        }
      });
    },

    closeDraft() {
      this.loadingStep = true;
      axios
        .post("/dashboard/drafts/" + this.currentDraft.id + "/complete", {})
        .catch((error) => {
          console.log(error);
          this.loadingStep = false;
        })
        .then((response) => {
          this.project = response.data.project;
          if (this.project) {
            this.loadingStep = false;
            this.loadLicenses();
            this.selectStep(3);
            this.trackProject();
          }
        });
    },

    trackProject() {
      axios.get("/projects/" + this.project.id + "/status").then((response) => {
        this.project.status = response.data.status;
        if (this.project.status != "complete") {
          setTimeout(() => this.trackProject(), 20000);
        }
      });
    },

    loadSpectra() {
      const iframe = window.frames.submissionNMRiumIframe;
      this.currentMolecules = [];
      if (iframe) {
        if (!this.selectedDataset.nmrium_info) {
          let data = {
            data: [
              this.url +
                "/download/asc/datasets/" +
                this.selectedDataset.id +
                "/" +
                this.selectedDataset.name +
                ".zip",
            ],
            type: "url",
          };
          iframe.postMessage({ type: `nmr-wrapper:load`, data }, "*");
        } else {
          let nmrium_info = JSON.parse(this.selectedDataset.nmrium_info);
          let mols = JSON.parse(nmrium_info.molecules);
          if (mols) {
            this.currentMolecules = mols;
          }
          mols.forEach((mol) => {
            mol.molfile = "\n" + mol.molfile + "\n";
          });
          let data = {
            data: {
              spectra: [JSON.parse(nmrium_info.spectra)],
              molecules: mols,
            },
            type: "nmrium",
          };
          iframe.postMessage({ type: `nmr-wrapper:load`, data }, "*");
        }
      }
    },

    openSelectDraftsView() {
      this.steps.forEach( s => {
        s.status = "upcoming"
      })
      this.currentDraft = null;
    },

    selectDataset(dataset) {
      this.selectedDataset = dataset;
      this.loadSpectra();
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
      this.selectDataset(this.selectedStudy.datasets[0]);
    },

    createNewDraft() {
      this.selectDraft(this.defaultDraft);
    },

    process() {
      this.loadingStep = true;
      axios
        .post("/dashboard/drafts/" + this.currentDraft.id + "/process", {
          name: this.draftName,
          description: this.draftDescription,
          tags: this.draftTags.map((a) => a.text),
        })
        .then((response) => {
          this.project = response.data.project;
          this.studies = response.data.studies;
          if (this.project && this.studies.length > 0) {
            this.selectStudy(this.studies[0]);
            this.selectedDataset = this.selectedStudy.datasets[0];
            this.loadingStep = false;
            this.selectStep(2);
          }
        });
    },

    selectStep(id) {
      this.steps.forEach((step) => {
        if (parseInt(step.id) < id) {
          step.status = "complete";
        } else if (parseInt(step.id) == id) {
          step.status = "current";
        } else {
          step.status = "upcoming";
        }
      });
      if (id == 1) {
        // this.loadingStep = true;
        this.$nextTick(function () {
          this.$refs.fsbRef.annotate();
        });
      }
    },

    toggleOpenCreateDatasetDialog() {
      this.openCreateDatasetDialog = !this.openCreateDatasetDialog;
      const saveNMRiumUpdates = (e) => {
        if (e.origin != "https://nmriumdev.nmrxiv.org") {
          return;
        }
        if (e.data.type == "nmr-wrapper:dataChange") {
          let actionType = e.data.data.actionType;

          if (
            actionType == "" ||
            (actionType == "INITIATE" &&
              this.selectedDataset &&
              this.selectedDataset.type != null)
          ) {
            return;
          }

          this.selectedSpectraData = e.data.data.data.find(
            (d) => d.info.type == "NMR Spectrum"
          );
          if (
            actionType == "ADD_MOLECULE" ||
            actionType == "DELETE_MOLECULE" ||
            e.data.data.molecules
          ) {
            this.currentMolecules = e.data.data.molecules;
          }
          this.updateDataSet();
        }
      };

      if (this.openCreateDatasetDialog) {
        if (!this.$props.eventRegistered) {
          window.addEventListener("message", saveNMRiumUpdates);
          this.$props.eventRegistered = true;
        }
      } else {
        window.removeEventListener("message", saveNMRiumUpdates);
        this.$props.eventRegistered = false;
      }
    },

    updateDataSet() {
      if (this.selectedSpectraData != null) {
        this.autoSaving = true;
        axios
          .post("/dashboard/datasets/" + this.selectedDataset.id + "/nmriumInfo", {
            spectra: this.selectedSpectraData,
            molecules: this.currentMolecules,
          })
          .then((response) => {
            this.autoSaving = false;
            this.selectedDataset.nmrium_info = response.data.nmrium_info;
          });
      }
    },

    fetchDrafts() {
      this.loading = true;
      return axios.get("/dashboard/drafts");
    },

    selectDraft(draft) {
      this.currentDraft = draft;
      this.draftName = this.currentDraft.name;
      this.draftDescription = this.currentDraft.description;
      let tags = [];
      this.file = null;
      if (this.currentDraft.tags) {
        this.currentDraft.tags.forEach((t) => {
          tags.push({
            text: t.name["en"],
          });
        });
        this.draftTags = tags;
      }
      this.selectStep(1);
    },
  },
  computed: {
    currentStep() {
      return this.steps.filter((s) => s.status == "current")[0];
    },

    currentTab() {
      return this.tabs.find((t) => t.current);
    },

    nmriumURL() {
      return this.$page.props.nmriumURL
        ? String(this.$page.props.nmriumURL + "?workspace=embedded&id=" + Math.random())
        : "http://nmriumdev.nmrxiv.org?workspace=embedded&id=" + Math.random();
    },

    url() {
      return String(this.$page.props.url);
    },

    getMax() {
      if (this.selectedStudy) {
        let totalCount = 0;
        this.selectedStudy.sample.molecules.forEach((mol) => {
          totalCount += parseInt(mol.pivot.percentage_composition);
        });
        return 100 - totalCount;
      } else {
        return 100;
      }
    },
  },
};
</script>
