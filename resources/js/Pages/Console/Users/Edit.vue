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
                    {{ edituser.first_name }} {{ edituser.last_name }}
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
        <user-profile :user="edituser" />
        <div class="hidden sm:block">
          <div class="py-8"><div class="border-t border-gray-200"></div></div>
        </div>
        <user-password :user="edituser" />
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import BreadCrumbs from "../../../Jetstream/BreadCrumbs.vue";
import UserProfile from "@/Pages/Console/Users/Partials/UserProfile.vue";
import UserPassword from "@/Pages/Console/Users/Partials/UserPassword.vue";

export default {
  metaInfo() {
    return {
      title: `${this.form.first_name} ${this.form.last_name}`,
    };
  },
  components: {
    UserProfile,
    UserPassword,
    AppLayout,
    BreadCrumbs,
  },
  props: {
    edituser: Object,
  },
  remember: "form",
  data() {
    return {
      pages: [
        { name: "Console", route: "console", current: false },
        { name: "Users", route: "console.users", current: false },
        {
          name: this.edituser.first_name + " " + this.edituser.last_name,
          route: "console.users.update",
          current: true,
          id: this.edituser.id,
        },
      ],
    };
  },
  methods: {
    update() {
      this.form.post(this.route("console.users.update", this.edituser.id), {
        onSuccess: () => this.form.reset("password", "photo"),
      });
    },
  },
};
</script>
