<template>
    <div
        class="cursor-pointer flex flex-row-reverse justify-end"
        @click="open = true"
    >
        <img
            v-if="members.length > 0"
            class="w-8 h-8 -ml-4 rounded-full border-2 border-white"
            src="https://ui-avatars.com/api/?name=+&color=7F9CF5&background=EBF4FF"
            alt=""
        />
        <img
            :key="user.id"
            v-for="user in members"
            class="w-8 h-8 -mr-2 rounded-full border-2 border-white"
            :src="user.profile_photo_url"
            :alt="user.first_name"
        />
        <img
            :key="user.id"
            v-for="user in team ? team.users : []"
            class="w-8 h-8 -mr-2 rounded-full border-2 border-white"
            :src="user.profile_photo_url"
            :alt="user.first_name"
        />
        <img
            v-if="team"
            class="w-8 h-8 -mr-2 rounded-full border-2 border-white"
            :src="team.owner.profile_photo_url"
            :alt="team.owner.first_name"
        />
        <span v-if="members.length == 0 && !team">
            <button
                type="button"
                class="inline-flex items-center px-0 py-2 ronded-md font-medium text-dark sm:text-sm"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"
                    /></svg
                >&nbsp; SHARE
            </button>
        </span>
    </div>
    <TransitionRoot :show="open" as="template" @after-leave="query = ''" appear>
        <Dialog as="div" class="relative z-10 p-4" @close="open = false">
            <TransitionChild
                as="template"
                enter="ease-out duration-300"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="ease-in duration-200"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div
                    class="fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity"
                />
            </TransitionChild>

            <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20">
                <TransitionChild
                    as="template"
                    enter="ease-out duration-300"
                    enter-from="opacity-0 scale-95"
                    enter-to="opacity-100 scale-100"
                    leave="ease-in duration-200"
                    leave-from="opacity-100 scale-100"
                    leave-to="opacity-0 scale-95"
                >
                    <DialogPanel
                        class="mx-auto max-w-xl transform rounded-xl bg-white p-2 shadow-2xl ring-1 ring-black ring-opacity-5 transition-all p-4"
                    >
                        <div class="sm:flex sm:items-start sm:justify-between">
                            <div>
                                <h3
                                    class="text-lg leading-6 font-medium text-gray-900"
                                >
                                    Share with users
                                </h3>
                                <div
                                    class="mt-2 max-w-xl text-sm text-gray-500"
                                >
                                    <!-- <p>
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Recusandae
                    voluptatibus corrupti atque repudiandae nam.
                  </p> -->
                                </div>
                            </div>
                            <div
                                class="mt-5 sm:mt-0 sm:ml-6 sm:flex-shrink-0 sm:flex sm:items-center"
                            >
                                <button
                                    v-if="!addUser && canChangeRole"
                                    @click="addUser = true"
                                    type="button"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none sm:text-sm"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"
                                        /></svg
                                    >&nbsp; SHARE
                                </button>
                                <button
                                    v-if="addUser && canChangeRole"
                                    @click="addUser = false"
                                    type="button"
                                    class="inline-flex items-center px-4 py-2 border shadow-sm font-medium rounded-md text-dark bg-white-600 hover:bg-white-700 focus:outline-none sm:text-sm"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M11 17l-5-5m0 0l5-5m-5 5h12"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div v-if="!addUser" class="flow-root mt-1">
                            <ul
                                v-if="members.length > 0"
                                role="list"
                                class="my-5 shadow divide-y divide-gray-200"
                            >
                                <li
                                    v-for="person in members"
                                    :key="person.email"
                                >
                                    <a class="block">
                                        <div
                                            class="flex items-center px-4 py-4 sm:px-6"
                                        >
                                            <div
                                                class="min-w-0 flex-1 pt-3 flex items-center"
                                            >
                                                <div class="flex-shrink-0">
                                                    <img
                                                        :key="person.id"
                                                        class="h-12 w-12 rounded-full"
                                                        :src="
                                                            person.profile_photo_url
                                                        "
                                                        :alt="person.first_name"
                                                    />
                                                </div>
                                                <div
                                                    class="min-w-0 flex-1 px-4"
                                                >
                                                    <div>
                                                        <p
                                                            class="text-sm font-medium text-gray-600"
                                                        >
                                                            {{
                                                                person.first_name
                                                            }}
                                                            {{
                                                                person.last_name
                                                            }}
                                                            <span
                                                                v-if="
                                                                    $page.props
                                                                        .user
                                                                        .email ==
                                                                    person.email
                                                                "
                                                                >(you)</span
                                                            >
                                                        </p>
                                                        <p
                                                            class="mt-2 flex items-center text-sm text-gray-500"
                                                        >
                                                            <svg
                                                                class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                viewBox="0 0 20 20"
                                                                fill="currentColor"
                                                                aria-hidden="true"
                                                            >
                                                                <path
                                                                    d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"
                                                                />
                                                                <path
                                                                    d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"
                                                                />
                                                            </svg>
                                                            <span>{{
                                                                person.email
                                                            }}</span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="-mt-4">
                                                <div
                                                    v-if="
                                                        role &&
                                                        canChangeRole &&
                                                        !isProjectAlreadyShared
                                                    "
                                                >
                                                    <div
                                                        v-if="
                                                            personRole(
                                                                person
                                                            ) &&
                                                            personRole(
                                                                person
                                                            ) == 'creator'
                                                        "
                                                    >
                                                        <span
                                                            class="ml-6 text-sm text-dark-500"
                                                        >
                                                            Creator
                                                        </span>
                                                    </div>
                                                    <div v-else>
                                                        <button
                                                            @click="
                                                                removeModelMember(
                                                                    person
                                                                )
                                                            "
                                                            class="cursor-pointer mr-3 text-sm text-red-500 hover:text-red-700"
                                                        >
                                                            Remove
                                                        </button>
                                                        <Menu
                                                            as="div"
                                                            class="relative inline-block text-left"
                                                        >
                                                            <div>
                                                                <MenuButton
                                                                    class="inline-flex justify-center w-full rounded-md capitalize border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500"
                                                                >
                                                                    {{
                                                                        personRole(
                                                                            person
                                                                        )
                                                                    }}
                                                                    <ChevronDownIcon
                                                                        class="-mr-1 ml-2 h-5 w-5"
                                                                        aria-hidden="true"
                                                                    />
                                                                </MenuButton>
                                                            </div>
                                                            <transition
                                                                enter-active-class="transition ease-out duration-100"
                                                                enter-from-class="transform opacity-0 scale-95"
                                                                enter-to-class="transform opacity-100 scale-100"
                                                                leave-active-class="transition ease-in duration-75"
                                                                leave-from-class="transform opacity-100 scale-100"
                                                                leave-to-class="transform opacity-0 scale-95"
                                                            >
                                                                <MenuItems
                                                                    class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                                                >
                                                                    <div
                                                                        class="py-1"
                                                                    >
                                                                        <span
                                                                            v-for="role in availableRoles"
                                                                            :key="
                                                                                role.key
                                                                            "
                                                                        >
                                                                            <MenuItem
                                                                                @click="
                                                                                    updateRole(
                                                                                        role,
                                                                                        person
                                                                                    )
                                                                                "
                                                                                v-if="
                                                                                    person[
                                                                                        model +
                                                                                            '_membership'
                                                                                    ]
                                                                                        .role !=
                                                                                    role.key
                                                                                "
                                                                                v-slot="{
                                                                                    active,
                                                                                }"
                                                                            >
                                                                                <a
                                                                                    href="#"
                                                                                    :class="[
                                                                                        active
                                                                                            ? 'bg-gray-100 text-gray-900'
                                                                                            : 'text-gray-700',
                                                                                        'block px-4 py-2 text-sm',
                                                                                    ]"
                                                                                    >{{
                                                                                        role.name
                                                                                    }}</a
                                                                                >
                                                                            </MenuItem>
                                                                        </span>
                                                                    </div>
                                                                </MenuItems>
                                                            </transition>
                                                        </Menu>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                            <span v-if="team">
                                <div class="mb-2">
                                    <h2
                                        class="text-lg uppercase leading-6 font-medium text-gray-900"
                                    >
                                        {{ team.name }}
                                    </h2>
                                    <p class="mt-1 text-sm text-gray-500">
                                        All the users (corresponding roles) in
                                        the team will be applicable to this
                                        {{ model }}.
                                    </p>
                                </div>
                                <div
                                    class="bg-white shadow overflow-hidden sm:rounded-md"
                                >
                                    <ul
                                        role="list"
                                        class="divide-y divide-gray-200"
                                    >
                                        <li
                                            v-for="person in team.users"
                                            :key="person.email"
                                        >
                                            <a class="block hover:bg-gray-50">
                                                <div
                                                    class="flex items-center px-4 py-4 sm:px-6"
                                                >
                                                    <div
                                                        class="min-w-0 flex-1 flex items-center"
                                                    >
                                                        <div
                                                            class="flex-shrink-0"
                                                        >
                                                            <img
                                                                :key="person.id"
                                                                class="h-12 w-12 rounded-full"
                                                                :src="
                                                                    person.profile_photo_url
                                                                "
                                                                :alt="
                                                                    person.first_name
                                                                "
                                                            />
                                                        </div>
                                                        <div
                                                            class="min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4"
                                                        >
                                                            <div>
                                                                <p
                                                                    class="text-sm font-medium text-gray-600 truncate"
                                                                >
                                                                    {{
                                                                        person.first_name
                                                                    }}
                                                                </p>
                                                                <p
                                                                    class="mt-2 flex items-center text-sm text-gray-500"
                                                                >
                                                                    <svg
                                                                        class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        viewBox="0 0 20 20"
                                                                        fill="currentColor"
                                                                        aria-hidden="true"
                                                                    >
                                                                        <path
                                                                            d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"
                                                                        />
                                                                        <path
                                                                            d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"
                                                                        />
                                                                    </svg>
                                                                    <span
                                                                        class="truncate"
                                                                        >{{
                                                                            person.email
                                                                        }}</span
                                                                    >
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="inline-flex capitalize items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                                            >{{
                                                                person
                                                                    .membership
                                                                    .role
                                                            }}</span
                                                        >
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="block hover:bg-gray-50">
                                                <div
                                                    class="flex items-center px-4 py-4 sm:px-6"
                                                >
                                                    <div
                                                        class="min-w-0 flex-1 flex items-center"
                                                    >
                                                        <div
                                                            class="flex-shrink-0"
                                                        >
                                                            <img
                                                                :key="
                                                                    team.owner
                                                                        .id
                                                                "
                                                                class="h-12 w-12 rounded-full"
                                                                :src="
                                                                    team.owner
                                                                        .profile_photo_url
                                                                "
                                                                :alt="
                                                                    team.owner
                                                                        .first_name
                                                                "
                                                            />
                                                        </div>
                                                        <div
                                                            class="min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4"
                                                        >
                                                            <div>
                                                                <p
                                                                    class="text-sm font-medium text-gray-600 truncate"
                                                                >
                                                                    {{
                                                                        team
                                                                            .owner
                                                                            .first_name
                                                                    }}
                                                                </p>
                                                                <p
                                                                    class="mt-2 flex items-center text-sm text-gray-500"
                                                                >
                                                                    <svg
                                                                        class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        viewBox="0 0 20 20"
                                                                        fill="currentColor"
                                                                        aria-hidden="true"
                                                                    >
                                                                        <path
                                                                            d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"
                                                                        />
                                                                        <path
                                                                            d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"
                                                                        />
                                                                    </svg>
                                                                    <span
                                                                        class="truncate"
                                                                        >{{
                                                                            team
                                                                                .owner
                                                                                .email
                                                                        }}</span
                                                                    >
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                                            >owner</span
                                                        >
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </span>
                            <div
                                class="pb-5"
                                v-if="modelInvitations.length > 0"
                            >
                                <jet-modal-form-section class="mt-10 sm:mt-0">
                                    <template #form>
                                        <div class="mb-2 mt-5 col-span-12">
                                            <h2
                                                class="text-lg leading-6 font-medium text-gray-900"
                                            >
                                                Pending Project Invitations
                                            </h2>
                                            <p
                                                class="mt-1 text-sm text-gray-500"
                                            >
                                                These people have been invited
                                                to your {{ model }} and have
                                                been sent an invitation email.
                                                They may join the {{ model }} by
                                                accepting the email invitation.
                                            </p>
                                        </div>
                                        <div class="space-y-3 col-span-12 mb-3">
                                            <div
                                                class="flex px-4 items-center justify-between"
                                                v-for="invitation in modelInvitations"
                                                :key="invitation.id"
                                            >
                                                <div class="text-gray-600">
                                                    {{ invitation.email }}
                                                </div>
                                                <div class="flex items-center">
                                                    <button
                                                        class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none"
                                                        @click="
                                                            cancelModelInvitation(
                                                                invitation
                                                            )
                                                        "
                                                    >
                                                        Cancel
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </jet-modal-form-section>
                            </div>
                        </div>
                        <div v-else>
                            <jet-modal-form-section
                                class="space-y-3"
                                @submitted="addModelMember"
                            >
                                <template #form>
                                    <div class="col-span-12">
                                        <div
                                            class="max-w-xl mt-2 text-sm text-gray-600"
                                        >
                                            Please provide the email address of
                                            the person you would like to add.
                                        </div>
                                    </div>
                                    <div class="col-span-12">
                                        <jet-label for="email" value="Email" />
                                        <jet-input
                                            id="email"
                                            type="email"
                                            class="mt-1 block w-full"
                                            v-model="addMemberForm.email"
                                        />
                                        <jet-input-error
                                            :message="
                                                addMemberForm.errors.email
                                            "
                                            class="mt-2"
                                        />
                                    </div>
                                    <div
                                        class="col-span-12"
                                        v-if="availableRoles.length > 0"
                                    >
                                        <jet-label for="roles" value="Role" />
                                        <jet-input-error
                                            :message="addMemberForm.errors.role"
                                            class="mt-2"
                                        />
                                        <div
                                            class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer"
                                        >
                                            <button
                                                type="button"
                                                class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200"
                                                :class="{
                                                    'border-t border-gray-200 rounded-t-none':
                                                        i > 0,
                                                    'rounded-b-none':
                                                        i !=
                                                        Object.keys(
                                                            availableRoles
                                                        ).length -
                                                            1,
                                                }"
                                                @click="
                                                    addMemberForm.role =
                                                        role.key
                                                "
                                                v-for="(
                                                    role, i
                                                ) in availableRoles"
                                                :key="role.key"
                                            >
                                                <div
                                                    :class="{
                                                        'opacity-50':
                                                            addMemberForm.role &&
                                                            addMemberForm.role !=
                                                                role.key,
                                                    }"
                                                >
                                                    <div
                                                        class="flex items-center"
                                                    >
                                                        <div
                                                            class="text-sm text-gray-600"
                                                            :class="{
                                                                'font-semibold':
                                                                    addMemberForm.role ==
                                                                    role.key,
                                                            }"
                                                        >
                                                            {{ role.name }}
                                                        </div>
                                                        <svg
                                                            v-if="
                                                                addMemberForm.role ==
                                                                role.key
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
                                                    <div
                                                        class="mt-2 text-xs text-gray-600"
                                                    >
                                                        {{ role.description }}
                                                    </div>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-span-12">
                                        <jet-label
                                            for="message"
                                            value="Message"
                                        />
                                        <jet-text-area
                                            id="message"
                                            :rows="3"
                                            class="mt-1 block w-full"
                                            v-model="addMemberForm.message"
                                        />
                                        <jet-input-error
                                            :message="
                                                addMemberForm.errors.message
                                            "
                                            class="mt-2"
                                        />
                                    </div>
                                </template>
                                <template #actions>
                                    <jet-button
                                        :class="{
                                            'opacity-25':
                                                addMemberForm.processing,
                                        }"
                                        :disabled="addMemberForm.processing"
                                    >
                                        SEND
                                    </jet-button>
                                    <jet-action-message
                                        :on="addMemberForm.recentlySuccessful"
                                        class="mx-3 inline-flex"
                                    >
                                        Added.
                                    </jet-action-message>
                                </template>
                            </jet-modal-form-section>
                        </div>
                    </DialogPanel>
                </TransitionChild>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script>
import { computed, ref } from "vue";
import { UsersIcon } from "@heroicons/vue/outline";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetActionSection from "@/Jetstream/ActionSection.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetModalFormSection from "@/Jetstream/ModalFormSection.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetTextArea from "@/Jetstream/TextArea.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetSectionBorder from "@/Jetstream/SectionBorder.vue";
import {
    Combobox,
    ComboboxInput,
    ComboboxOptions,
    ComboboxOption,
    Dialog,
    DialogPanel,
    TransitionChild,
    TransitionRoot,
} from "@headlessui/vue";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { ChevronDownIcon } from "@heroicons/vue/solid";

export default {
    props: {
        members: Object,
        project: Object,
        study: Object,
        dataset: Object,
        team: Object,
        availableRoles: Object,
        role: String,
        model: String,
        calledFrom: String,
    },
    components: {
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        ChevronDownIcon,
        Combobox,
        ComboboxInput,
        ComboboxOptions,
        ComboboxOption,
        Dialog,
        DialogPanel,
        TransitionChild,
        JetModalFormSection,
        TransitionRoot,
        UsersIcon,
        JetActionMessage,
        JetActionSection,
        JetButton,
        JetConfirmationModal,
        JetDangerButton,
        JetDialogModal,
        JetInput,
        JetInputError,
        JetLabel,
        JetSecondaryButton,
        JetSectionBorder,
        JetTextArea,
    },
    setup() {
        const open = ref(false);
        const addUser = ref(false);
        return {
            open,
            addUser,
        };
    },
    data() {
        return {
            addMemberForm: this.$inertia.form({
                email: "",
                role: null,
                message: "",
            }),
            updateRoleForm: this.$inertia.form({
                role: null,
            }),
            removeModelMemberForm: this.$inertia.form(),
        };
    },
    methods: {
        personRole(person) {
            console.log(person);
            if (person[this.model + "_membership"]) {
                return person[this.model + "_membership"].role;
            } else {
                if (person["project_membership"]) {
                    return person["project_membership"].role;
                } else if (person["study_membership"]) {
                    return person["study_membership"].role;
                } else if (person["dataset_membership"]) {
                    return person["dataset_membership"].role;
                }
            }
        },
        addModelMember() {
            this.addMemberForm.post(
                route(this.model + "-members.store", this[this.model]),
                {
                    errorBag: "addModelMember",
                    preserveScroll: true,
                    onSuccess: () => this.addMemberForm.reset(),
                }
            );
        },
        cancelModelInvitation(invitation) {
            this.$inertia.delete(
                route(this.model + "-invitations.destroy", invitation),
                {
                    preserveScroll: true,
                }
            );
        },
        updateRole(role, managingRoleFor) {
            this.updateRoleForm.role = role.key;
            this.updateRoleForm.put(
                route(this.model + "-members.update", [
                    this[this.model],
                    managingRoleFor,
                ]),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.updateRoleForm.role = null;
                    },
                }
            );
        },
        removeModelMember(modelMemberBeingRemoved) {
            this.removeModelMemberForm.delete(
                route(this.model + "-members.destroy", [
                    this[this.model],
                    modelMemberBeingRemoved,
                ]),
                {
                    errorBag: "removeModelMember",
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => {},
                }
            );
        },
    },
    computed: {
        modelObject() {
            if (this.model == "study") {
                return this.study;
            } else if (this.model == "project") {
                return this.project;
            }
        },
        modelInvitations() {
            return this.modelObject[this.model + "_invitations"];
        },
        isProjectAlreadyShared() {
            var count = 0;
            var isShared = false;
            if (this.calledFrom == "studyView") {
                if (this.members) {
                    this.members.forEach((member) => {
                        if (member.hasOwnProperty("project_membership")) {
                            count = count + 1;
                        }
                    });
                    if (count > 1) {
                        isShared = true;
                    }
                }
            }
            return isShared;
        },
        canChangeRole() {
            if (this.role) {
                return this.role == "creator" || this.role == "owner";
            } else {
                return true;
            }
        },
    },
};
</script>
