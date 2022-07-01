<template>
  <app-layout title="Users & Permissions">
    <template #header>
      <div class="bg-white border-b">
        <div class="px-12">
          <div class="flex flex-nowrap justify-between py-6">
            <div>
              <bread-crumbs :pages="pages" />
              <div class="mt-2 md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                  <div
                    class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest mt-3"
                  >
                    Add new user
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
      <div>
        <user-profile :user="null" />
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import BreadCrumbs from "@/Jetstream/BreadCrumbs.vue";
import UserProfile from "@/Pages/Console/Users/Partials/UserProfile.vue";

export default {
  metaInfo() {
    return {
      title: `${this.form.first_name} ${this.form.last_name}`,
    };
  },
  components: {
    AppLayout,
    BreadCrumbs,
    UserProfile,
  },
  remember: "form",
  data() {
    return {
      pages: [
        { name: "Console", route: "console", current: false },
        { name: "Users", route: "console.users", current: false },
        { name: "Create User", route: "console.users.create", current: true },
      ],
    };
  },
  methods: {},
};
</script>
