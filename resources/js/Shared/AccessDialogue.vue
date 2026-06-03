<template>
    <div
        v-if="!hideTrigger"
        class="relative z-10 mr-2 flex flex-row-reverse items-center justify-end"
        @click="open = true"
    >
        <button
            v-if="members.length > 0"
            type="button"
            class="inline-flex items-center gap-1.5 rounded-full border py-1 pl-1.5 pr-3 text-xs font-medium shadow-sm transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900"
            :class="
                isSharingDisabled
                    ? 'cursor-pointer border-gray-100 bg-gray-50 text-gray-500 dark:border-gray-700 dark:bg-gray-800/60 dark:text-gray-400'
                    : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800'
            "
            :title="
                isSharingDisabled
                    ? 'View who has access (read-only)'
                    : 'Manage sharing'
            "
            :aria-label="
                isSharingDisabled ? 'View who has access' : 'Manage sharing'
            "
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="inline h-4 w-4 shrink-0 text-gray-400"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
            >
                <path
                    d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"
                />
            </svg>
            {{ isSharingDisabled ? "Shared with" : "Share" }}
        </button>
        <img
            v-for="user in members"
            :key="user.id"
            class="-mr-2 h-8 w-8 rounded-full border-2 border-white dark:border-gray-900"
            :src="user.profile_photo_url"
            :alt="user.first_name"
        />
        <img
            v-for="user in team ? team.users : []"
            :key="'team-' + user.id"
            class="-mr-2 h-8 w-8 rounded-full border-2 border-white dark:border-gray-900"
            :src="user.profile_photo_url"
            :alt="user.first_name"
        />
        <img
            v-if="team && !team.personal_team"
            class="-mr-2 h-8 w-8 rounded-full border-2 border-white dark:border-gray-900"
            :src="team.owner.profile_photo_url"
            :alt="team.owner.first_name"
        />
    </div>
    <TransitionRoot :show="open" as="template" appear>
        <Dialog as="div" class="relative z-50" @close="open = false">
            <TransitionChild
                as="template"
                enter="ease-out duration-200"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="ease-in duration-150"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div
                    class="fixed inset-0 bg-gray-900/40 transition-opacity dark:bg-black/50"
                />
            </TransitionChild>

            <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6">
                <TransitionChild
                    as="template"
                    enter="ease-out duration-200"
                    enter-from="opacity-0 scale-95"
                    enter-to="opacity-100 scale-100"
                    leave="ease-in duration-150"
                    leave-from="opacity-100 scale-100"
                    leave-to="opacity-0 scale-95"
                >
                    <DialogPanel
                        class="mx-auto w-full max-w-lg transform overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl transition-all dark:border-gray-700 dark:bg-gray-900"
                    >
                        <div
                            class="flex items-start justify-between gap-4 border-b border-gray-100 px-5 py-4 dark:border-gray-800"
                        >
                            <div class="min-w-0 flex-1">
                                <h3
                                    class="text-base font-semibold text-gray-900 dark:text-gray-100"
                                >
                                    Share with users
                                </h3>
                                <p
                                    class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                                >
                                    Manage who can access this {{ model }}.
                                    <a
                                        href="https://docs.nmrxiv.org/submission-guides/sharing.html"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="font-medium text-teal-700 hover:text-teal-900 dark:text-teal-400 dark:hover:text-teal-300"
                                    >
                                        Learn more
                                    </a>
                                </p>
                            </div>
                            <div class="flex shrink-0 items-center gap-2">
                                <button
                                    v-if="!addUser && canManageSharing"
                                    type="button"
                                    class="inline-flex items-center gap-1.5 rounded-md bg-teal-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm transition-colors hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                                    @click="addUser = true"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"
                                        />
                                    </svg>
                                    Add user
                                </button>
                                <button
                                    v-if="addUser && canManageSharing"
                                    type="button"
                                    class="inline-flex items-center rounded-md border border-gray-200 bg-white px-2 py-1.5 text-gray-600 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                                    aria-label="Back to sharing overview"
                                    @click="goBackToSharingOverview"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        aria-hidden="true"
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

                        <div
                            v-if="isSharingDisabled"
                            class="border-b border-amber-200/80 bg-amber-50 px-5 py-3 text-sm text-amber-900 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-100"
                        >
                            This project is published. Sharing settings are
                            read-only.
                        </div>

                        <div class="px-5 py-4">
                            <div v-if="!addUser" class="space-y-6">
                                <div v-if="members.length > 0">
                                    <h4
                                        class="text-sm font-bold text-gray-900 dark:text-gray-100"
                                    >
                                        People with access
                                    </h4>
                                    <ul
                                        role="list"
                                        class="mt-3 divide-y divide-gray-100 rounded-lg border border-gray-200 dark:divide-gray-800 dark:border-gray-700"
                                    >
                                        <li
                                            v-for="person in members"
                                            :key="person.email"
                                        >
                                            <div
                                                class="flex items-center gap-3 px-3 py-3"
                                            >
                                                <img
                                                    class="h-9 w-9 shrink-0 rounded-full"
                                                    :src="
                                                        person.profile_photo_url
                                                    "
                                                    :alt="person.first_name"
                                                />
                                                <div class="min-w-0 flex-1">
                                                    <p
                                                        class="truncate text-sm font-medium text-gray-900 dark:text-gray-100"
                                                    >
                                                        {{ person.first_name }}
                                                        {{ person.last_name }}
                                                        <span
                                                            v-if="
                                                                $page.props.auth
                                                                    .user
                                                                    .email ==
                                                                person.email
                                                            "
                                                            class="font-normal text-gray-500"
                                                            >(you)</span
                                                        >
                                                    </p>
                                                    <p
                                                        class="truncate text-xs text-gray-500 dark:text-gray-400"
                                                    >
                                                        {{ person.email }}
                                                    </p>
                                                </div>
                                                <div
                                                    class="flex shrink-0 items-center gap-2"
                                                >
                                                    <div
                                                        v-if="
                                                            canManageSharing &&
                                                            !isProjectAlreadyShared
                                                        "
                                                    >
                                                        <div
                                                            v-if="
                                                                personRole(
                                                                    person
                                                                ) == 'creator'
                                                            "
                                                        >
                                                            <span
                                                                class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium capitalize text-gray-700 dark:bg-gray-800 dark:text-gray-300"
                                                            >
                                                                Creator
                                                            </span>
                                                        </div>
                                                        <div
                                                            v-else
                                                            class="flex items-center gap-2"
                                                        >
                                                            <button
                                                                type="button"
                                                                class="text-xs font-medium text-red-600 hover:text-red-800 dark:text-red-400"
                                                                @click="
                                                                    removeModelMember(
                                                                        person
                                                                    )
                                                                "
                                                            >
                                                                Remove
                                                            </button>
                                                            <Menu
                                                                as="div"
                                                                class="relative text-left"
                                                            >
                                                                <MenuButton
                                                                    type="button"
                                                                    class="inline-flex items-center gap-1 rounded-md border border-gray-200 bg-white px-2.5 py-1 text-xs font-medium capitalize text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                                                                >
                                                                    {{
                                                                        personRole(
                                                                            person
                                                                        )
                                                                    }}
                                                                    <ChevronDownIcon
                                                                        class="h-4 w-4"
                                                                        aria-hidden="true"
                                                                    />
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
                                                                        class="absolute right-0 z-50 mt-1 w-48 origin-top-right rounded-md border border-gray-200 bg-white py-1 shadow-lg focus:outline-none dark:border-gray-600 dark:bg-gray-800"
                                                                    >
                                                                        <template
                                                                            v-for="role in availableRoles"
                                                                            :key="
                                                                                role.key
                                                                            "
                                                                        >
                                                                            <MenuItem
                                                                                v-if="
                                                                                    person[
                                                                                        model +
                                                                                            '_membership'
                                                                                    ]
                                                                                        ?.role !=
                                                                                    role.key
                                                                                "
                                                                                v-slot="{
                                                                                    active,
                                                                                }"
                                                                                @click="
                                                                                    updateRole(
                                                                                        role,
                                                                                        person
                                                                                    )
                                                                                "
                                                                            >
                                                                                <button
                                                                                    type="button"
                                                                                    :class="[
                                                                                        active
                                                                                            ? 'bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-gray-100'
                                                                                            : 'text-gray-700 dark:text-gray-200',
                                                                                        'block w-full px-3 py-2 text-left text-sm',
                                                                                    ]"
                                                                                >
                                                                                    {{
                                                                                        role.name
                                                                                    }}
                                                                                </button>
                                                                            </MenuItem>
                                                                        </template>
                                                                    </MenuItems>
                                                                </transition>
                                                            </Menu>
                                                        </div>
                                                    </div>
                                                    <span
                                                        v-else-if="
                                                            personRole(person)
                                                        "
                                                        class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium capitalize text-gray-700 dark:bg-gray-800 dark:text-gray-300"
                                                    >
                                                        {{ personRole(person) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>

                                <div
                                    class="rounded-lg border border-gray-200 bg-gray-50/60 p-4 dark:border-gray-700 dark:bg-gray-800/40"
                                >
                                    <div
                                        class="flex items-start justify-between gap-3"
                                    >
                                        <div class="flex min-w-0 gap-3">
                                            <GlobeAltIcon
                                                class="mt-0.5 h-5 w-5 shrink-0 text-teal-600 dark:text-teal-400"
                                                aria-hidden="true"
                                            />
                                            <div>
                                                <p
                                                    class="text-sm font-medium text-gray-900 dark:text-gray-100"
                                                >
                                                    Anyone with the link
                                                </p>
                                                <p
                                                    class="mt-1 text-xs leading-relaxed text-gray-500 dark:text-gray-400"
                                                >
                                                    View the {{ model }} and its
                                                    samples and datasets in
                                                    read-only mode.
                                                </p>
                                            </div>
                                        </div>
                                        <button
                                            id="copyLink"
                                            type="button"
                                            class="inline-flex shrink-0 items-center gap-1 rounded-md border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 transition-colors hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"
                                            :disabled="!reviewerShareUrl"
                                            @click.stop="copyLinkToClipboard"
                                        >
                                            <LinkIcon
                                                class="h-3.5 w-3.5"
                                                aria-hidden="true"
                                            />
                                            {{
                                                linkCopied
                                                    ? "Copied"
                                                    : "Copy link"
                                            }}
                                        </button>
                                    </div>
                                </div>
                                <div v-if="team && !team.personal_team">
                                    <h4
                                        class="text-sm font-bold text-gray-900 dark:text-gray-100"
                                    >
                                        {{ team.name }}
                                    </h4>
                                    <p
                                        class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        Team members inherit access to this
                                        {{ model }}.
                                    </p>
                                    <ul
                                        role="list"
                                        class="mt-3 divide-y divide-gray-100 rounded-lg border border-gray-200 dark:divide-gray-800 dark:border-gray-700"
                                    >
                                        <li
                                            v-for="person in team.users"
                                            :key="person.email"
                                        >
                                            <div
                                                class="flex items-center gap-3 px-3 py-3"
                                            >
                                                <img
                                                    class="h-9 w-9 shrink-0 rounded-full"
                                                    :src="
                                                        person.profile_photo_url
                                                    "
                                                    :alt="person.first_name"
                                                />
                                                <div class="min-w-0 flex-1">
                                                    <p
                                                        class="truncate text-sm font-medium text-gray-900 dark:text-gray-100"
                                                    >
                                                        {{ person.first_name }}
                                                    </p>
                                                    <p
                                                        class="truncate text-xs text-gray-500 dark:text-gray-400"
                                                    >
                                                        {{ person.email }}
                                                    </p>
                                                </div>
                                                <span
                                                    class="inline-flex shrink-0 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium capitalize text-gray-700 dark:bg-gray-800 dark:text-gray-300"
                                                >
                                                    {{ person.membership.role }}
                                                </span>
                                            </div>
                                        </li>
                                        <li>
                                            <div
                                                class="flex items-center gap-3 px-3 py-3"
                                            >
                                                <img
                                                    class="h-9 w-9 shrink-0 rounded-full"
                                                    :src="
                                                        team.owner
                                                            .profile_photo_url
                                                    "
                                                    :alt="team.owner.first_name"
                                                />
                                                <div class="min-w-0 flex-1">
                                                    <p
                                                        class="truncate text-sm font-medium text-gray-900 dark:text-gray-100"
                                                    >
                                                        {{
                                                            team.owner
                                                                .first_name
                                                        }}
                                                    </p>
                                                    <p
                                                        class="truncate text-xs text-gray-500 dark:text-gray-400"
                                                    >
                                                        {{ team.owner.email }}
                                                    </p>
                                                </div>
                                                <span
                                                    class="inline-flex shrink-0 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300"
                                                >
                                                    owner
                                                </span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>

                                <div v-if="modelInvitations?.length > 0">
                                    <h4
                                        class="text-sm font-bold text-gray-900 dark:text-gray-100"
                                    >
                                        Pending invitations
                                    </h4>
                                    <p
                                        class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        Invited users can join by accepting the
                                        email invitation.
                                    </p>
                                    <ul
                                        class="mt-3 divide-y divide-gray-100 rounded-lg border border-gray-200 dark:divide-gray-800 dark:border-gray-700"
                                    >
                                        <li
                                            v-for="invitation in modelInvitations"
                                            :key="invitation.id"
                                            class="flex items-center justify-between gap-3 px-3 py-3"
                                        >
                                            <span
                                                class="truncate text-sm text-gray-700 dark:text-gray-300"
                                            >
                                                {{ invitation.email }}
                                            </span>
                                            <button
                                                v-if="canManageSharing"
                                                type="button"
                                                class="shrink-0 text-xs font-medium text-red-600 hover:text-red-800 dark:text-red-400"
                                                @click="
                                                    cancelModelInvitation(
                                                        invitation
                                                    )
                                                "
                                            >
                                                Cancel
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div v-else-if="canManageSharing">
                                <jet-modal-form-section
                                    class="space-y-4"
                                    @submitted="addModelMember"
                                >
                                    <template #form>
                                        <p
                                            class="col-span-12 text-sm text-gray-600 dark:text-gray-400"
                                        >
                                            Invite someone by email. They will
                                            receive an invitation to join this
                                            {{ model }}.
                                        </p>
                                        <div class="col-span-12">
                                            <jet-label
                                                for="email"
                                                value="Email"
                                            />
                                            <jet-input
                                                id="email"
                                                v-model="addMemberForm.email"
                                                type="email"
                                                class="mt-1 block w-full"
                                            />
                                            <jet-input-error
                                                :message="
                                                    addMemberForm.errors.email
                                                "
                                                class="mt-2"
                                            />
                                        </div>
                                        <div
                                            v-if="availableRoles.length > 0"
                                            class="col-span-12"
                                        >
                                            <jet-label
                                                for="roles"
                                                value="Role"
                                            />
                                            <jet-input-error
                                                :message="
                                                    addMemberForm.errors.role
                                                "
                                                class="mt-2"
                                            />
                                            <div
                                                class="relative z-0 mt-1 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700"
                                            >
                                                <button
                                                    v-for="(
                                                        role, i
                                                    ) in availableRoles"
                                                    :key="role.key"
                                                    type="button"
                                                    class="relative inline-flex w-full px-4 py-3 text-left focus:z-10 focus:outline-none focus:ring-2 focus:ring-teal-500 dark:focus:ring-teal-400"
                                                    :class="{
                                                        'border-t border-gray-200 dark:border-gray-700':
                                                            i > 0,
                                                        'bg-teal-50 dark:bg-teal-950/30':
                                                            addMemberForm.role ==
                                                            role.key,
                                                    }"
                                                    @click="
                                                        addMemberForm.role =
                                                            role.key
                                                    "
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
                                                            <span
                                                                class="text-sm font-semibold text-gray-800 dark:text-gray-200"
                                                            >
                                                                {{ role.name }}
                                                            </span>
                                                            <svg
                                                                v-if="
                                                                    addMemberForm.role ==
                                                                    role.key
                                                                "
                                                                class="ml-2 h-4 w-4 text-teal-600 dark:text-teal-400"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                viewBox="0 0 24 24"
                                                                aria-hidden="true"
                                                            >
                                                                <path
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M5 13l4 4L19 7"
                                                                />
                                                            </svg>
                                                        </div>
                                                        <p
                                                            class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                                                        >
                                                            {{
                                                                role.description
                                                            }}
                                                        </p>
                                                    </div>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-span-12">
                                            <jet-label
                                                for="message"
                                                value="Message (optional)"
                                            />
                                            <jet-text-area
                                                id="message"
                                                v-model="addMemberForm.message"
                                                :rows="3"
                                                class="mt-1 block w-full"
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
                                            Send invitation
                                        </jet-button>
                                        <jet-action-message
                                            :on="
                                                addMemberForm.recentlySuccessful
                                            "
                                            class="mx-3 inline-flex text-sm text-gray-600"
                                        >
                                            Invitation sent.
                                        </jet-action-message>
                                    </template>
                                </jet-modal-form-section>
                            </div>
                        </div>
                    </DialogPanel>
                </TransitionChild>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script>
import { ref } from "vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetModalFormSection from "@/Jetstream/ModalFormSection.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetTextArea from "@/Jetstream/TextArea.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import {
    Dialog,
    DialogPanel,
    TransitionChild,
    TransitionRoot,
} from "@headlessui/vue";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import {
    ChevronDownIcon,
    GlobeAltIcon,
    LinkIcon,
} from "@heroicons/vue/24/solid";
import { router } from "@inertiajs/vue3";

export default {
    components: {
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        ChevronDownIcon,
        Dialog,
        DialogPanel,
        TransitionChild,
        JetModalFormSection,
        TransitionRoot,
        JetActionMessage,
        JetButton,
        JetInput,
        JetInputError,
        JetLabel,
        JetTextArea,
        GlobeAltIcon,
        LinkIcon,
    },
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
        hideTrigger: {
            type: Boolean,
            default: false,
        },
    },
    emits: ["sharing-updated"],
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
            removeModelMemberForm: this.$inertia.form({}),
            linkCopied: false,
        };
    },
    computed: {
        modelObject() {
            if (this.model == "study") {
                return this.study;
            } else if (this.model == "project") {
                return this.project;
            }
            return null;
        },
        modelInvitations() {
            return this.modelObject?.[this.model + "_invitations"] ?? [];
        },
        //Check if project is already shared for when called from Study view.
        isProjectAlreadyShared() {
            var count = 0;
            var isShared = false;
            if (this.calledFrom == "studyView") {
                if (this.members) {
                    this.members.forEach((member) => {
                        if (
                            Object.prototype.hasOwnProperty.call(
                                member,
                                "project_membership"
                            )
                        ) {
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
        isSharingDisabled() {
            if (this.project?.is_public) {
                return true;
            }

            const modelObject = this.modelObject;

            return Boolean(modelObject?.is_public);
        },
        canManageSharing() {
            return this.canChangeRole && !this.isSharingDisabled;
        },
        reviewerShareUrl() {
            if (this.model === "study" && this.study?.obfuscationcode) {
                return this.route("preview", [
                    this.study.obfuscationcode,
                    this.study.id,
                    "study",
                ]);
            }

            if (this.project?.obfuscationcode) {
                return this.route("project.preview", [
                    this.project.obfuscationcode,
                ]);
            }

            return null;
        },
        sharingReloadOnlyKeys() {
            if (this.model === "study") {
                return ["study", "members"];
            }

            if (this.model !== "project") {
                return [];
            }

            const component = this.$page.component ?? "";

            if (component === "Project/Show") {
                return ["project", "members"];
            }

            if (component.includes("Public/Project")) {
                return ["workspace", "project"];
            }

            if (component === "Dashboard") {
                const workspace =
                    this.$page.props.dashboardWorkspace ??
                    this.$page.props.filters?.workspace;

                if (workspace && workspace !== "default") {
                    return ["workspaceProjects"];
                }

                return ["projects"];
            }

            return [];
        },
    },
    methods: {
        openDialog() {
            this.open = true;
            this.addUser = false;
        },
        refreshSharingData() {
            const only = this.sharingReloadOnlyKeys;

            if (!only.length) {
                this.$emit("sharing-updated");

                return Promise.resolve();
            }

            return new Promise((resolve) => {
                router.reload({
                    only,
                    preserveScroll: true,
                    onFinish: () => {
                        this.$emit("sharing-updated");
                        resolve();
                    },
                });
            });
        },
        async goBackToSharingOverview() {
            if (this.addMemberForm.recentlySuccessful) {
                await this.refreshSharingData();
            }

            this.addUser = false;
        },
        personRole(person) {
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
                    onSuccess: async () => {
                        this.addMemberForm.reset();
                        await this.refreshSharingData();
                        this.addUser = false;
                    },
                }
            );
        },
        cancelModelInvitation(invitation) {
            this.$inertia.delete(
                route(this.model + "-invitations.destroy", invitation),
                {
                    preserveScroll: true,
                    onSuccess: () => this.refreshSharingData(),
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
        async copyLinkToClipboard() {
            const url = this.reviewerShareUrl;
            if (!url) {
                return;
            }

            try {
                await navigator.clipboard.writeText(url);
            } catch {
                try {
                    const el = document.createElement("textarea");
                    el.value = url;
                    el.setAttribute("readonly", "");
                    el.style.position = "fixed";
                    el.style.left = "-9999px";
                    document.body.appendChild(el);
                    el.select();
                    document.execCommand("copy");
                    document.body.removeChild(el);
                } catch {
                    return;
                }
            }

            this.linkCopied = true;
            setTimeout(() => {
                this.linkCopied = false;
            }, 2500);
        },
    },
};
</script>
