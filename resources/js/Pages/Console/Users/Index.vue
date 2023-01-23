<template>
    <app-layout title="Users & Permissions">
        <template #header>
            <div class="bg-white border-b">
                <div class="px-12">
                    <div class="flex flex-nowrap justify-between py-6">
                        <div>
                            <bread-crumbs :pages="pages" />
                            <div
                                class="mt-2 md:flex md:items-center md:justify-between"
                            >
                                <div class="flex-1 min-w-0">
                                    <div
                                        class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest mt-3"
                                    >
                                        Users & Permissions
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div class="py-12 px-10">
            <div class="mb-6 flex justify-between items-center">
                <search-filter
                    v-model="form.search"
                    class="w-full max-w-md mr-4"
                    @reset="reset"
                >
                    <label class="block text-sm font-medium text-gray-700"
                        >Role:</label
                    >
                    <select
                        v-model="form.role"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                    >
                        <option :value="null">-- no filter --</option>
                        <option
                            v-for="role in roles"
                            :key="role.id"
                            :value="role.name"
                            :selected="form.role == role.name"
                        >
                            {{ role.name }}
                        </option>
                    </select>
                </search-filter>
                <Link
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                    :href="route('console.users.create')"
                >
                    <span>Create</span>&nbsp;
                    <span class="hidden md:inline">User</span>
                </Link>
            </div>
            <div>
                <div class="bg-white rounded-md shadow overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Name
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Status
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Role
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Edit</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="user in users" :key="user.email">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <img
                                                class="rounded-full h-10 w-10 object-cover"
                                                :src="user.profile_photo_url"
                                                alt=""
                                            />
                                        </div>
                                        <div class="ml-4">
                                            <div
                                                class="text-sm font-medium text-gray-900"
                                            >
                                                {{ user.first_name }}
                                                {{ user.last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ user.email }}
                                            </div>
                                            <div class="text-sm text-teal-500">
                                                {{ user.orcid_id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        v-if="user.verified_at"
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"
                                    >
                                        Verified
                                    </span>
                                    <span
                                        v-else
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-green-800"
                                    >
                                        Not verified
                                    </span>
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                >
                                    <button
                                        v-if="roles.length"
                                        class="ml-2 text-sm text-gray-400 underline"
                                        @click="manageRole(user)"
                                    >
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold capitalize rounded-full bg-indigo-100 text-green-800"
                                        >
                                            {{
                                                user.role[0]
                                                    ? user.role[0]
                                                    : "User"
                                            }}
                                        </span>
                                    </button>
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                                >
                                    <Link
                                        :href="
                                            route('console.users.edit', user.id)
                                        "
                                        class="text-indigo-600 hover:text-indigo-900"
                                        >Edit</Link
                                    >
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Role Management Modal -->
            <jet-dialog-modal
                :show="currentlyManagingRole"
                @close="currentlyManagingRole = false"
            >
                <template #title> Manage Role </template>

                <template #content>
                    <div v-if="managingRoleFor">
                        <div
                            class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer"
                        >
                            <button
                                v-for="(role, i) in roles"
                                :key="role.key"
                                type="button"
                                class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200"
                                :class="{
                                    'border-t border-gray-200 rounded-t-none':
                                        i > 0,
                                    'rounded-b-none':
                                        i !== Object.keys(roles).length - 1,
                                }"
                                @click="updateRoleForm.role = role.name"
                            >
                                <div
                                    :class="{
                                        'opacity-50':
                                            updateRoleForm.role &&
                                            updateRoleForm.role !== role.name,
                                    }"
                                >
                                    <!-- Role Name -->
                                    <div class="flex items-center">
                                        <div
                                            class="text-sm text-gray-600 capitalize"
                                            :class="{
                                                'font-semibold':
                                                    updateRoleForm.role ===
                                                    role.key,
                                            }"
                                        >
                                            {{ role.name }}
                                        </div>

                                        <svg
                                            v-if="
                                                updateRoleForm.role ===
                                                role.name
                                            "
                                            class="ml-2 h-5 w-5 text-green-400"
                                            fill="none"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                            ></path>
                                        </svg>
                                    </div>

                                    <!-- Role Description -->
                                    <div class="mt-2 text-xs text-gray-600">
                                        {{ role.description }}
                                    </div>
                                </div>
                            </button>
                        </div>
                        <div v-if="updateRoleForm.error_message" class="my-2">
                            {{ updateRoleForm.error_message }}
                        </div>
                    </div>
                </template>

                <template #footer>
                    <jet-secondary-button
                        @click="currentlyManagingRole = false"
                    >
                        Cancel
                    </jet-secondary-button>

                    <jet-button
                        class="ml-2"
                        :class="{ 'opacity-25': updateRoleForm.processing }"
                        :disabled="updateRoleForm.processing"
                        @click="updateRole"
                    >
                        Save
                    </jet-button>
                </template>
            </jet-dialog-modal>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import throttle from "lodash/throttle";
import mapValues from "lodash/mapValues";
import pickBy from "lodash/pickBy";
import SearchFilter from "@/Shared/SearchFilter.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import BreadCrumbs from "../../../Jetstream/BreadCrumbs.vue";
import { Link } from "@inertiajs/inertia-vue3";

export default {
    components: {
        AppLayout,
        SearchFilter,
        JetDialogModal,
        JetSecondaryButton,
        JetButton,
        Link,
        BreadCrumbs,
    },
    props: {
        users: Array,
        filters: Object,
        roles: Array,
    },
    data() {
        return {
            form: {
                search: this.filters.search,
                role: this.filters.role,
                trashed: this.filters.trashed,
            },

            currentlyManagingRole: false,

            managingRoleFor: null,

            updateRoleForm: this.$inertia.form({
                role: null,
                error_message: null,
            }),

            pages: [
                { name: "Console", route: "console", current: false },
                { name: "Users", route: "console.users", current: true },
            ],
        };
    },
    watch: {
        form: {
            handler: throttle(function () {
                let query = pickBy(this.form);
                this.$inertia.replace(
                    this.route(
                        "console.users",
                        Object.keys(query).length
                            ? query
                            : { remember: "forget" }
                    )
                );
            }, 150),
            deep: true,
        },
    },
    methods: {
        reset() {
            this.form = mapValues(this.form, () => null);
        },
        manageRole(user) {
            this.updateRoleForm.error_message = null;
            if (user.role[0]) {
                this.updateRoleForm.role = user.role[0];
            } else {
                this.updateRoleForm.role = null;
            }
            this.managingRoleFor = user;
            this.currentlyManagingRole = true;
        },
        updateRole() {
            this.updateRoleForm.put(
                route("console.users.update-role", [this.managingRoleFor]),
                {
                    preserveScroll: true,
                    onSuccess: () => (this.currentlyManagingRole = false),
                    onError: (data) => {
                        this.updateRoleForm.error_message = data.error_message;
                    },
                }
            );
        },
    },
};
</script>
