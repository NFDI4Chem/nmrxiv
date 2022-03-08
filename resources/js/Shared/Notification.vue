<template>
  <!--  Appear when clicked on any particular notification --> 
  <div
    v-if="showOnClick && selectedNotificationForm"
    aria-live="assertive"
    class="
      fixed
      inset-0
      z-10
      flex
      items-end
      py-0
      pointer-events-none
      sm:p-6 sm:items-start
    "
  >
    <div class="w-full flex flex-col items-center space-y-4 sm:items-end">
      <!-- Notification panel, dynamically insert this into the live region when it needs to be displayed -->
      <transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          class="
            max-w-sm
            w-full
            bg-yellow-100
            shadow-lg
            rounded-lg
            pointer-events-auto
            ring-1 ring-black ring-opacity-5
            overflow-hidden
          "
        >
          <div class="p-4">
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <MailIcon class="h-6 w-6 text-indigo-600" aria-hidden="true" />
              </div>
              <div class="ml-3 w-0 flex-1 pt-0.5">
                <p class="text-sm font-medium text-gray-900">
                  {{ selectedNotificationForm.title }}
                </p>
                <p class="mt-1 text-sm text-gray-500">
                  {{ selectedNotificationForm.message }}
                </p>
                <div class="mt-3 flex space-x-7">
                  <button
                    type="button"
                    @click="markNotificationAsRead('onClick')"
                    class="
                      bg-yellow-100
                      rounded-md
                      text-sm
                      font-medium
                      text-indigo-600
                      hover:text-indigo-500
                      focus:outline-none
                      focus:ring-2
                      focus:ring-offset-2
                      focus:ring-indigo-500
                    "
                  >
                    Mark As Read
                  </button>
                </div>
              </div>
              <div class="ml-4 flex-shrink-0 flex">
                <button
                  @click="showOnClick = false"
                  class="
                    bg-yellow-100
                    rounded-md
                    inline-flex
                    text-gray-400
                    hover:text-gray-500
                    focus:outline-none
                    focus:ring-2
                    focus:ring-offset-2
                    focus:ring-indigo-500
                  "
                >
                  <span class="sr-only">Close</span>
                  <XIcon class="h-5 w-5" aria-hidden="true" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>
  </div>
    <!-- Appear when any notification got created -->
  <div
    v-if="showOnCreate && notification"
    aria-live="assertive"
    class="
      fixed
      inset-0
      z-10
      flex
      items-end
      px-4
      py-6
      pointer-events-none
      sm:p-6 sm:items-start
    "
  >
    <div class="w-full flex flex-col items-center space-y-4 sm:items-end">
      <!-- Notification panel, dynamically insert this into the live region when it needs to be displayed -->
      <transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          class="
            max-w-sm
            w-full
            bg-yellow-100
            shadow-lg
            rounded-lg
            pointer-events-auto
            ring-1 ring-black ring-opacity-5
            overflow-hidden
          "
        >
          <div class="p-4">
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <MailIcon class="h-6 w-6 text-indigo-600" aria-hidden="true" />
              </div>
              <div class="ml-3 w-0 flex-1 pt-0.5">
                <p class="text-sm font-medium text-gray-900">
                  {{ notification.data['title'] }}
                </p>
                <p class="mt-1 text-sm text-gray-500">
                  {{ notification.data['message'] }}
                </p>
                <div class="mt-3 flex space-x-7">
                  <button
                    type="button"
                    @click="markNotificationAsRead('onCreate')"
                    class="
                      bg-yellow-100
                      rounded-md
                      text-sm
                      font-medium
                      text-indigo-600
                      hover:text-indigo-500
                      focus:outline-none
                      focus:ring-2
                      focus:ring-offset-2
                      focus:ring-indigo-500
                    "
                  >
                    Mark As Read
                  </button>
                </div>
              </div>
              <div class="ml-4 flex-shrink-0 flex">
                <button
                  @click="showOnCreate = false"
                  class="
                    bg-yellow-100
                    rounded-md
                    inline-flex
                    text-gray-400
                    hover:text-gray-500
                    focus:outline-none
                    focus:ring-2
                    focus:ring-offset-2
                    focus:ring-indigo-500
                  "
                >
                  <span class="sr-only">Close</span>
                  <XIcon class="h-5 w-5" aria-hidden="true" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>
  </div>
</template>

<script>
import { MailIcon } from "@heroicons/vue/solid";
import { XIcon } from "@heroicons/vue/solid";

export default {
  components: {
    MailIcon,
    XIcon,
  },
  props: {
      notification: Object,
  },
  data() {
    return {
      showOnClick: false,
      showOnCreate: true,
      selectedNotificationForm: this.$inertia.form({
        _method: "POST",
        id: "",
        title: "",
        message: "",
        user_id: "",
      }),
    };
  },
  methods: {
    markNotificationAsRead(onEvent) {
      if(onEvent == 'onCreate'){
          this.selectedNotificationForm.user_id = this.notification.notifiable_id;
          this.selectedNotificationForm.id = this.notification.id;
          this.selectedNotificationForm.title = this.notification.data['title'];
          this.selectedNotificationForm.message = this.notification.data['message'];
      }
      this.selectedNotificationForm.post(
        route("announcements.markNotificationAsRead"),
        {
          preserveScroll: true,
          onSuccess: () => {
            this.showOnClick = !this.showOnClick;
            this.showOnCreate = !this.showOnCreate;
            this.selectedNotificationForm.reset();
          },
          onError: (err) => console.error(err),
        }
      );
    },
    toggleShowNotificationDialog(notification){
            this.selectedNotificationForm.user_id = notification.notifiable_id;
            this.selectedNotificationForm.id = notification.id;
            this.selectedNotificationForm.title = notification.data['title'];
            this.selectedNotificationForm.message = notification.data['message'];
            this.showOnCreate = false;
            this.showOnClick = true;

    }
  },
};
</script>