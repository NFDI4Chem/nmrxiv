<template>
  <jet-dialog-modal
    :max-width="'7xl'"
    :show="createDatasetDialog"
    @close="createDatasetDialog = false"
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
        <!-- <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" aria-label="Progress">
          <ol
            role="list"
            class="rounded-md overflow-hidden lg:flex lg:border-l lg:border-r lg:border-gray-200 lg:rounded-none"
          >
            <li
              v-for="(step, stepIdx) in steps"
              :key="step.id"
              class="relative overflow-hidden lg:flex-1"
            >
              <div
                :class="[
                  stepIdx === 0 ? 'border-b-0 rounded-t-md' : '',
                  stepIdx === steps.length - 1 ? 'border-t-0 rounded-b-md' : '',
                  'border border-gray-200 overflow-hidden lg:border-0',
                ]"
              >
                <a v-if="step.status === 'complete'" :href="step.href" class="group">
                  <span
                    class="absolute top-0 left-0 w-1 h-full bg-transparent group-hover:bg-gray-200 lg:w-full lg:h-1 lg:bottom-0 lg:top-auto"
                    aria-hidden="true"
                  />
                  <span
                    :class="[
                      stepIdx !== 0 ? 'lg:pl-9' : '',
                      'px-6 py-5 flex items-start text-sm font-medium',
                    ]"
                  >
                    <span class="flex-shrink-0">
                      <span
                        class="w-10 h-10 flex items-center justify-center bg-teal-600 rounded-full"
                      >
                        <CheckIcon class="w-6 h-6 text-white" aria-hidden="true" />
                      </span>
                    </span>
                    <span class="mt-0.5 ml-4 min-w-0 flex flex-col">
                      <span class="text-xs font-semibold tracking-wide uppercase">{{
                        step.name
                      }}</span>
                      <span class="text-sm font-medium text-gray-500">{{
                        step.description
                      }}</span>
                    </span>
                  </span>
                </a>
                <a
                  v-else-if="step.status === 'current'"
                  :href="step.href"
                  aria-current="step"
                >
                  <span
                    class="absolute top-0 left-0 w-1 h-full bg-teal-600 lg:w-full lg:h-1 lg:bottom-0 lg:top-auto"
                    aria-hidden="true"
                  />
                  <span
                    :class="[
                      stepIdx !== 0 ? 'lg:pl-9' : '',
                      'px-6 py-5 flex items-start text-sm font-medium',
                    ]"
                  >
                    <span class="flex-shrink-0">
                      <span
                        class="w-10 h-10 flex items-center justify-center border-2 border-teal-600 rounded-full"
                      >
                        <span class="text-teal-600">{{ step.id }}</span>
                      </span>
                    </span>
                    <span class="mt-0.5 ml-4 min-w-0 flex flex-col">
                      <span
                        class="text-xs font-semibold text-teal-600 tracking-wide uppercase"
                        >{{ step.name }}</span
                      >
                      <span class="text-sm font-medium text-gray-500">{{
                        step.description
                      }}</span>
                    </span>
                  </span>
                </a>
                <a v-else :href="step.href" class="group">
                  <span
                    class="absolute top-0 left-0 w-1 h-full bg-transparent group-hover:bg-gray-200 lg:w-full lg:h-1 lg:bottom-0 lg:top-auto"
                    aria-hidden="true"
                  />
                  <span
                    :class="[
                      stepIdx !== 0 ? 'lg:pl-9' : '',
                      'px-6 py-5 flex items-start text-sm font-medium',
                    ]"
                  >
                    <span class="flex-shrink-0">
                      <span
                        class="w-10 h-10 flex items-center justify-center border-2 border-gray-300 rounded-full"
                      >
                        <span class="text-gray-500">{{ step.id }}</span>
                      </span>
                    </span>
                    <span class="mt-0.5 ml-4 min-w-0 flex flex-col">
                      <span
                        class="text-xs font-semibold text-gray-500 tracking-wide uppercase"
                        >{{ step.name }}</span
                      >
                      <span class="text-sm font-medium text-gray-500">{{
                        step.description
                      }}</span>
                    </span>
                  </span>
                </a>
                <template v-if="stepIdx !== 0">
                  <div
                    class="hidden absolute top-0 left-0 w-3 inset-0 lg:block"
                    aria-hidden="true"
                  >
                    <svg
                      class="h-full w-full text-gray-300"
                      viewBox="0 0 12 82"
                      fill="none"
                      preserveAspectRatio="none"
                    >
                      <path
                        d="M0.5 0V31L10.5 41L0.5 51V82"
                        stroke="currentcolor"
                        vector-effect="non-scaling-stroke"
                      />
                    </svg>
                  </div>
                </template>
              </div>
            </li>
          </ol>
        </nav> -->
      </div>
    </template>

    <template #content>
      <div v-if="!loading" class="max-h-screen">
        <div v-if="drafts.length > 0 && !currentDraft">
          <label class="block text-sm font-medium text-gray-900"><b>Drafts</b></label>
          <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul role="list" class="-my-5 divide-y divide-gray-200 p-4">
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
          <div class="mt-6 justify-center items-center flex">
            <button
              @click="createNewDraft()"
              class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
            >
              Create New
            </button>
          </div>
        </div>
        <div v-else>
          <div v-if="currentStep.id == '01'">
            <div style="height: 75vh; overflow-y: scroll" v-if="currentDraft">
              <div class="mb-3">
                <label for="project-name" class="block text-sm font-medium text-gray-700">
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
                <label for="description" class="block text-sm font-medium text-gray-700">
                  Project Description
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
                <label for="description" class="block text-sm font-medium text-gray-700">
                  Tags
                </label>
                <div>
                  <vue-tags-input
                    v-model="draftTag"
                    max-width="100%"
                    :tags="draftTags"
                    @tags-changed="(newTags) => (draftTags = newTags)"
                  />
                </div>
              </div>
              <small @click="annotate()">Draft ID: {{ currentDraft.name }}</small>
              <form id="submission-dropzone" class="py-2 mb-3">
                <div id="submission-dropzone-message" class="text-center">
                  <div
                    type="button"
                    class="relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-4 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
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
                    <span class="mt-2 block text-sm font-medium text-gray-900">
                      Drop Files or Folders to upload to
                      <span v-if="$page.props.selectedFolder"
                        >"{{ $page.props.selectedFolder }}"</span
                      >
                      folder
                    </span>
                    <div v-if="dropzone" class="relative mt-5">
                      <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                        <div
                          :style="
                            'width: ' +
                            precentageUpload(dropzone.files.length, uploadedFiles) +
                            '%'
                          "
                          class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500"
                        ></div>
                      </div>
                      <span
                        v-if="status"
                        class="mt-2 block text-sm font-medium text-gray-900"
                      >
                        {{ status }}
                      </span>
                    </div>
                  </div>
                </div>
              </form>
              <div v-if="file != null" style="height: 40vh; overflow: scroll !important">
                <children :file="file"></children>
              </div>
            </div>
          </div>
          <div class="-mx-6 -my-4" v-if="currentStep.id == '02'">
            <div style="height: 80vh">
              <div class="flex-1 flex xl:overflow-hidden">
                <nav
                  aria-label="Sections"
                  class="hidden flex-shrink-0 w-64 bg-white border-r border-blue-gray-200 xl:flex xl:flex-col"
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
                            <label for="selected-tab" class="sr-only">Select a tab</label>
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
                          <div v-if="currentTab.name == 'Meta Data'" class="px-4 sm:px-6">
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
                            v-if="currentTab.name == 'Chemical Composition'"
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
                                        <div class="relative flex items-start space-x-3">
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
                                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
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
            <!-- <div style="height: 80vh; overflow: scroll !important">
              <div class="mb-4 bg-white flex items-start justify-center">
                <div class="relative z-0 inline-flex shadow-sm rounded-md">
                  <Menu as="div" class="-ml-px relative block">
                    <MenuButton
                      class="relative rounded-l inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500"
                    >
                      <b>STUDY: {{ selectedStudy.name }}</b>
                      <ChevronDownIcon class="h-5 w-5" aria-hidden="true" />
                    </MenuButton>
                    <transition
                      enter-active-class="transition ease-out duration-100"
                      enter-from-class="transform opacity-0 scale-95"
                      enter-to-class="transform opacity-100 scale-100"
                      leave-active-class="transition ease-in duration-75"
                      leave-from-class="transform opacity-100 scale-100"
                      leave-to-class="transform opacity-0 scale-95"
                    >
                      <MenuItems
                        class="origin-top-right cursor-pointer absolute right-0 mt-2 -mr-1 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                      >
                        <div class="py-1">
                          <MenuItem
                            v-for="study in studies"
                            :key="study.slug"
                            v-slot="{ active }"
                          >
                            <a
                              @click="selectStudy(study)"
                              :class="[
                                active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                                'block px-4 py-2 text-sm',
                              ]"
                            >
                              {{ study.name }}
                            </a>
                          </MenuItem>
                        </div>
                      </MenuItems>
                    </transition>
                  </Menu>
                  <Menu as="div" class="-ml-px relative block">
                    <MenuButton
                      class="relative rounded-r inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500"
                    >
                      <b>Experiment: {{ selectedDataset.name }}</b>
                      <ChevronDownIcon class="h-5 w-5" aria-hidden="true" />
                    </MenuButton>
                    <transition
                      enter-active-class="transition ease-out duration-100"
                      enter-from-class="transform opacity-0 scale-95"
                      enter-to-class="transform opacity-100 scale-100"
                      leave-active-class="transition ease-in duration-75"
                      leave-from-class="transform opacity-100 scale-100"
                      leave-to-class="transform opacity-0 scale-95"
                    >
                      <MenuItems
                        class="origin-top-right cursor-pointer absolute right-0 mt-2 -mr-1 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                      >
                        <div class="py-1">
                          <MenuItem
                            v-for="dataset in selectedStudy.datasets"
                            :key="dataset.slug"
                            v-slot="{ active }"
                          >
                            <a
                              @click="selectDataset(dataset)"
                              :class="[
                                active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                                'block px-4 py-2 text-sm',
                              ]"
                            >
                              {{ dataset.name }}
                            </a>
                          </MenuItem>
                        </div>
                      </MenuItems>
                    </transition>
                  </Menu>
                </div>
              </div>
              <iframe
                v-on:load="loadSpectra()"
                name="submissionNMRiumIframe"
                frameborder="0"
                allowfullscreen
                class="rounded-md border"
                style="width: 100%; height: 75vh"
                :src="nmriumURL"
              ></iframe>
            </div> -->
          </div>
          <div v-if="currentStep.id == '03'">Successful</div>
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

    <template v-if="!loading && currentDraft" #footer>
      <span v-if="currentStep.id == '01'">
        <jet-secondary-button @click="toggleCreateDatasetDialog">
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
      <span v-if="currentStep.id == '02'">
        <jet-button
          class="ml-2 float-left"
          @click="selectStep(1)"
          :class="{ 'opacity-25': createDatasetForm.processing }"
          :disabled="createDatasetForm.processing"
        >
          Back
        </jet-button>
        <jet-secondary-button @click="toggleCreateDatasetDialog">
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
      <span v-if="currentStep.id == '03'">
        <jet-button
          class="ml-2 float-left"
          @click="selectStep(2)"
          :class="{ 'opacity-25': createDatasetForm.processing }"
          :disabled="createDatasetForm.processing"
        >
          Back
        </jet-button>
        <jet-secondary-button @click="toggleCreateDatasetDialog">
          Cancel
        </jet-secondary-button>
      </span>
    </template>
  </jet-dialog-modal>
</template>
<script>
import { Dropzone } from "dropzone";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import { CheckCircleIcon } from "@heroicons/vue/solid";
import { Link } from "@inertiajs/inertia-vue3";
import { Tab, TabGroup, TabList, TabPanel, TabPanels } from "@headlessui/vue";
import { AtSymbolIcon, CodeIcon, LinkIcon } from "@heroicons/vue/solid";
import JetInputError from "@/Jetstream/InputError.vue";
import { ref } from "vue";
import { Switch, SwitchDescription, SwitchGroup, SwitchLabel } from "@headlessui/vue";
import { inject } from "vue";
import { CheckIcon } from "@heroicons/vue/solid";
import axiosRetry from "axios-retry";
import OCL from "openchemlib/full";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { ChevronLeftIcon, ChevronRightIcon, ChevronDownIcon } from "@heroicons/vue/solid";
import slider from "vue3-slider";
import VueTagsInput from "@sipec/vue3-tags-input";

export default {
  components: {
    CheckIcon,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,

    Tab,
    TabGroup,
    TabList,
    TabPanel,
    TabPanels,
    Switch,
    SwitchDescription,
    SwitchGroup,
    SwitchLabel,
    AtSymbolIcon,
    CodeIcon,
    LinkIcon,
    JetDialogModal,
    JetSecondaryButton,
    JetButton,
    Link,
    CheckCircleIcon,
    ChevronDownIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    JetInputError,
    slider,
    VueTagsInput,
  },

  mounted() {
    const emitter = inject("emitter");
    emitter.on("openDatasetCreateDialog", (data) => {
      this.fetchDrafts().then((response) => {
        this.drafts = response.data.drafts;
        if (data.draft_id) {
          let selectedDraft = this.drafts.find((d) => d.id == data.draft_id);
          this.selectDraft(selectedDraft);
          this.loading = false;
          this.createDatasetDialog = true;
          this.loadDropZone();
        } else {
          this.defaultDraft = response.data.default;
          this.createDatasetDialog = true;
          if (this.drafts.length == 0) {
            this.currentDraft = this.defaultDraft;
            this.loading = false;
            this.loadDropZone();
          } else {
            this.loading = false;
          }
        }
      });
    });
  },

  onUpdated() {},

  data() {
    return {
      status: null,
      dropzone: null,
      selectedSpectraData: null,
      drafts: [],
      defaultDraft: null,
      draftTags: [],
      draftTag: "",
      studyName: "",
      studyDescription: "",
      studyTags: [],
      studyTag: "",
      currentDraft: null,
      selectedStudy: null,
      selectedDataset: null,
      loading: false,
      loadingStep: false,
      uploadedFiles: 0,
      autoSaving: false,
      project: null,
      studies: null,
      progress: 0,
      file: null,
      editor: null,
      percentage: 1,
      smiles: "",
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
      steps: [
        {
          id: "01",
          name: "Files Upload",
          description: "Vitae sed mi luctus laoreet.",
          href: "#",
          status: "current",
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
        { name: "Chemical Composition", current: false },
        { name: "Meta Data", current: false },
      ],
      createDatasetDialog: false,
      currentMolecules: [],
    };
  },

  props: [],

  methods: {
    getSVGString(molecule) {
      if (molecule.MOL) {
        let mol = OCL.Molecule.fromMolfile("\n  " + molecule.MOL.replaceAll('"', ""));
        return mol.toSVG(200, 200);
      } else {
        console.log(molecule);
      }
    },
    loadDropZone() {
      this.$nextTick(() => {
        const vm = this;
        vm.count = 0;
        vm.processedBatchesCount = 0;
        vm.batchesCount = 0;
        vm.filesBatch = [];
        vm.refreshing = false;
        vm.files = [];
        vm.lastStep = false;
        vm.$page.props.selectedFileSystemObject = vm.file;
        vm.$page.props.selectedFolder = "/";
        let options = {
          url: "/",
          method: "put",
          sending(file, xhr) {
            let _send = xhr.send;
            xhr.send = () => {
              _send.call(xhr, file);
            };
          },
          autoProcessQueue: false,
          uploadMultiple: true,
          disablePreviews: true,
          parallelUploads: 100,
          autoQueue: false,
          maxFiles: 100,
          dictDefaultMessage: document.querySelector("#submission-dropzone-message")
            .innerHTML,
          accept(file) {
            if (vm.count > 99) {
              vm.processFilesDZL(vm, file);
            } else {
              vm.count += 1;
            }
            vm.processedCount += 1;
          },
          totaluploadprogress: function (progress) {
            vm.progress = Math.ceil(progress);
          },
        };
        vm.dropzone = new Dropzone("#submission-dropzone", options);
        vm.dropzone.on("processing", (file) => {
          vm.dropzone.options.url = file.uploadURL;
          vm.status = "UPLOAD IN PROGRESS";
        });
        vm.dropzone.on("addedfile", (file) => {
          vm.files.push(file);
          if (vm.count <= 100) {
            vm.filesBatch.push(file);
          }
        });
        vm.dropzone.on("success", (file) => {
          vm.uploadedFiles += 1;
          if (vm.batchesCount == vm.processedBatchesCount && vm.filesBatch.length > 0) {
            vm.lastStep = true;
            vm.processFilesDZL(vm);
          }
          if (
            vm.batchesCount == vm.processedBatchesCount &&
            vm.uploadedFiles == vm.dropzone.files.length
          ) {
            vm.status = "UPLOAD COMPLETE";
            vm.annotate();
            vm.$page.props.selectedFileSystemObject = this.files[0];
          }
        });
      });
    },
    processFilesDZL(vm) {
      vm.batchesCount += 1;
      const url = "/dashboard/storage/signed-draft-storage-url";
      const client = axios.create({ baseURL: window.location.origin });
      axiosRetry(client, {
        retries: 3,
        retryCondition: (error) => {
          return error.response.status === 500;
        },
      });
      client
        .post(url, {
          draft_files: vm.filesBatch,
          destination: vm.$page.props.selectedFolder,
          draft_id: vm.currentDraft.id,
        })
        .catch((err) => {
          if (err.response.status !== 200 || err.response.status !== 201) {
            throw new Error(
              `API call failed with status code: ${err.response.status} after multiple attempts`
            );
          }
        })
        .then(function (response) {
          vm.processedBatchesCount += 1;
          let data = response.data;
          data.forEach((u) => {
            let cFile = vm.files.find((f) => f.fullPath == u.fullPath);
            if (cFile) {
              let headers = u.headers;
              if ("Host" in headers) {
                delete headers.Host;
              }
              cFile.uploadURL = u.url;
              setTimeout(() => vm.dropzone.processFile(cFile));
            }
          });
        });
      vm.filesBatch = [];
      vm.count = 0;
    },
    createNewDraft() {
      this.selectDraft(this.defaultDraft);
    },
    annotate() {
      this.status = "PROCESSING FILES";
      axios
        .get("/dashboard/drafts/" + this.currentDraft.id + "/annotate")
        .then((response) => {
          this.loadFiles(true);
        });
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
    closeDraft() {
      this.loadingStep = true;
      axios
        .post("/dashboard/drafts/" + this.currentDraft.id + "/complete", {})
        .then((response) => {
          this.project = response.data.project;
          this.studies = response.data.studies;
          if (this.project && this.studies.length > 0) {
            this.loadingStep = false;
            this.selectStep(3);
          }
        });
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
    selectDraft(draft) {
      this.currentDraft = draft;
      this.draftName = this.currentDraft.name;
      this.draftDescription = this.currentDraft.description;
      let tags = [];
      if (this.currentDraft.tags) {
        this.currentDraft.tags.forEach((t) => {
          tags.push({
            text: t.name["en"],
          });
        });
        this.draftTags = tags;
      }
      this.loadDropZone();
      this.loadFiles(false);
    },
    fetchDrafts() {
      this.loading = true;
      return axios.get("/dashboard/drafts");
    },
    loadSpectra() {
      const iframe = window.frames.submissionNMRiumIframe;
      if (iframe) {
        if (!this.selectedDataset.nmrium_info) {
          let data = {
            urls: [
              this.url +
                "/download/asc/datasets/" +
                this.selectedDataset.id +
                "/" +
                this.selectedDataset.name +
                ".zip",
            ],
          };
          iframe.postMessage({ type: `nmr-wrapper:loadURLs`, data }, "*");
        } else {
          let data = { spectra: [JSON.parse(this.selectedDataset.nmrium_info)] };
          iframe.postMessage({ type: `nmr-wrapper:load`, data }, "*");
        }

        window.addEventListener("message", (e) => {
          if (e.origin != "https://nmriumdev.nmrxiv.org") {
            return;
          }
          if (e.data.type == "nmr-wrapper:dataChange") {
            this.selectedSpectraData = e.data.data.data.find(
              (d) => d.info.type == "NMR Spectrum"
            );
            this.currentMolecules = e.data.data.molecules;
            this.updateDataSet();
          }
        });
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
          });
      }
    },
    loadFiles(updateStatus) {
      if (updateStatus) {
        this.status = "REFRESHING FILES";
      }
      axios
        .get("/dashboard/drafts/" + this.currentDraft.id + "/files")
        .then((response) => {
          this.file = response.data.file;
          if (updateStatus) {
            this.status = "UPLOAD COMPLETE";
            setTimeout(() => {
              this.status = null;
            }, 5000);
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
    },
    createDataset() {
      this.createDatasetForm.owner_id = this.$page.props.user.id;
      this.createDatasetForm.team_id = this.$page.props.user.current_team.id;
      this.createDatasetForm.project_id = this.project.id;
      this.createDatasetForm.post(route("dashboard.dataset.create"), {
        preserveScroll: true,
        onSuccess: () => {
          this.createDatasetDialog = false;
          this.createDatasetForm.reset();
        },
        onError: (err) => console.error(err),
      });
    },
    toggleCreateDatasetDialog() {
      this.createDatasetDialog = !this.createDatasetDialog;
      this.createDatasetForm.clearErrors();
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
    loadSmiles() {
      if (this.smiles && this.smiles != "") {
        this.editor.setSmiles(this.smiles);
      }
    },
    saveStudyDetails() {
      this.loadingStep = true;
      axios
        .put("/dashboard/studies/" + this.selectedStudy.id + "/update", {
          name: this.studyName,
          description: this.studyDescription,
          tags: this.studyTags.map((a) => a.text),
        })
        .then((response) => {
          this.currentStudy = response;
        });
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
    precentageUpload(a, b) {
      return (parseInt(b) / parseInt(a)) * 100;
    },
  },
  computed: {
    currentStep() {
      return this.steps.filter((s) => s.status == "current")[0];
    },
    url() {
      return String(this.$page.props.url);
    },
    nmriumURL() {
      return this.$page.props.nmriumURL
        ? String(this.$page.props.nmriumURL + "?workspace=embedded&id=" + Math.random())
        : "http://nmriumdev.nmrxiv.org?workspace=embedded&id=" + Math.random();
    },
    currentTab() {
      return this.tabs.find((t) => t.current);
    },
    getMax() {
      if (this.selectedStudy) {
        let totalCount = 0;
        this.selectedStudy.sample.molecules.forEach((mol) => {
          totalCount += parseInt(mol.pivot.percentage_composition);
        });
        console.log(totalCount);
        return 100 - totalCount;
      } else {
        return 100;
      }
    },
  },
};
</script>
