<template>
    <div
        v-if="announcements.length > 0 && show"
        class="fixed z-50 bg-yellow-400 border-b w-screen border-black-800"
    >
        <div class="max-w-7xl mx-auto py-1 px-6">
            <p
                class="text-sm text-justify text-dark font-semibold animate-pulse"
            >
                <span
                    v-for="announcement in announcements"
                    :key="announcement.id"
                >
                    <span v-html="announcement.message"></span>
                </span>
            </p>
            <div
                class="absolute inset-y-0 right-0 pt-1 pr-1 flex items-start sm:pt-1 sm:pr-2 sm:items-start"
            >
                <button
                    type="button"
                    class="flex p-1 rounded-md focus:outline-none hover:bg-yellow-300 focus:ring-2 focus:ring-white"
                    @click="acknowledgeAnnouncement()"
                >
                    <span class="sr-only">Dismiss</span>
                    <XMarkIcon class="h-3 w-3 text-dark" aria-hidden="true" />
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { XMarkIcon } from "@heroicons/vue/24/outline";
import Button from "@/Shared/Dropdown.vue";

export default {
    components: {
        XMarkIcon,
        Button,
    },
    props: {},
    setup() {},
    data() {
        return {
            ustring: null,
            show: false,
        };
    },
    computed: {
        announcements() {
            let announcements = this.$page.props.config.announcements;
            this.ustring = this.getHash(
                this.slugify(announcements.map((a) => a.message).toString())
            );
            let userAcknowledged = localStorage.getItem(
                "announcement" + "_" + this.ustring
            );
            this.show = announcements.length > 0 && userAcknowledged == null;
            return announcements;
        },
    },
    methods: {
        acknowledgeAnnouncement() {
            let localItems = this.findLocalItems("announcement_*");
            if (localItems.length > 0) {
                localItems.forEach((element) => {
                    localStorage.removeItem(element.key);
                });
            }
            localStorage.setItem("announcement" + "_" + this.ustring, true);
            this.show = false;
        },
    },
};
</script>
