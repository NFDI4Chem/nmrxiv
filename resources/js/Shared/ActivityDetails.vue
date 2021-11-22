<template>
  <TransitionRoot as="template" :show="open">
    <Dialog as="div" class="fixed inset-0 overflow-hidden z-50" @close="open = false">
      <div class="absolute inset-0 overflow-hidden">
        <TransitionChild as="template" enter="ease-in-out duration-500" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in-out duration-500" leave-from="opacity-100" leave-to="opacity-0">
          <DialogOverlay class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
        </TransitionChild>
        <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
          <TransitionChild as="template" enter="transform transition ease-in-out duration-500 sm:duration-700" enter-from="translate-x-full" enter-to="translate-x-0" leave="transform transition ease-in-out duration-500 sm:duration-700" leave-from="translate-x-0" leave-to="translate-x-full">
            <div class="relative w-96">
              <TransitionChild as="template" enter="ease-in-out duration-500" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in-out duration-500" leave-from="opacity-100" leave-to="opacity-0">
                <div class="absolute top-0 left-0 -ml-8 pt-4 pr-2 flex sm:-ml-10 sm:pr-4">
                  <button type="button" class="rounded-md text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white" @click="open = false">
                    <span class="sr-only">Close panel</span>
                    <XIcon class="h-6 w-6" aria-hidden="true" />
                  </button>
                </div>
              </TransitionChild>
              <div class="h-full bg-white p-8 overflow-y-auto">
                <div class="pb-16 space-y-6">
                  <div>
                    <div class="mt-4 flex items-start justify-between">
                      <div>
                        <h2 class="text-lg font-medium text-gray-900">{{ project.name }}</h2>
                      </div>
                    </div>
                  </div>
                  <div>
                    <div>
                      <h2 id="timeline-title" class="text-lg font-medium text-gray-900">Activity</h2>
                      <div class="mt-6 flow-root">
                        <ul role="list" class="-mb-8">
                            <li v-for="log in audit" :key="log.auditable_id">
                              <div class="relative pb-8">
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                <div class="relative flex space-x-3">
                                  <div>
                                    <img :src="log.user.profile_photo_url" :alt="log.user.first_name + ' ' + log.user.last_name" class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white object-cover">
                                  </div>
                                  <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                    <div>
                                      <a href="#" class="font-medium text-gray-900">{{ log.user.first_name }} {{ log.user.last_name }}</a>
                                      <p class="text-sm text-gray-500">{{ log.event }}&nbsp;
                                        <a href="#" class="font-medium text-gray-900">
                                          <span v-if="log.event == 'created' || log.event == 'deleted'">
                                            {{ log.auditable_type.replace("App\\Models\\", "") }}
                                          </span>
                                          <span v-if="log.event == 'updated'">
                                            <span v-for="(property, $index) in Object.keys(log.new_values)" :key="property">
                                              <span v-if="$index > 0">,</span> {{ property }}
                                            </span>
                                          </span>
                                        </a>
                                      </p>
                                      <small><time>{{ formatDateTime(log.created_at) }}</time></small>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
                <small v-if="audit.length" class="text-gray-500"><i>No logs before <br/>{{ formatDateTime(audit[audit.length -1]['created_at']) }}</i></small>
              </div>
            </div>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script>
import { Dialog, DialogOverlay, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { HeartIcon, XIcon } from '@heroicons/vue/outline'
import { PencilIcon, PlusSmIcon } from '@heroicons/vue/solid'

export default {
  components: {
    Dialog,
    DialogOverlay,
    TransitionChild,
    TransitionRoot,
    HeartIcon,
    PencilIcon,
    PlusSmIcon,
    XIcon,
  },
  props: ['study', 'project'],
  data() {
    return {
      open: false,
      audit: []
    };
  },
  updated() {
    this.fetchActivity(this.project)
  },
  methods: {
    toggleDetails() {
      this.open = !this.open;
    },
    fetchActivity(entity){
      axios.get(route('projects.activity', entity.id)).then(res => {
        this.audit = res.data.audit
      })
    }
  },
}
</script>