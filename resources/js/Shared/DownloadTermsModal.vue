<template>
    <jet-dialog-modal :show="show" max-width="2xl" @close="close">
        <template #title>{{ title }}</template>

        <template #content>
            <div
                class="max-h-[min(24rem,50vh)] space-y-3 overflow-y-auto rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm leading-relaxed text-gray-700"
            >
                <p v-for="(paragraph, index) in paragraphs" :key="index">
                    <template v-if="index === privacyParagraphIndex">
                        A full explanation as to how we protect your personal
                        data is provided in our
                        <a
                            :href="route('policy.show')"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="font-medium text-teal-700 underline decoration-teal-300 underline-offset-2 hover:text-teal-900"
                            >privacy policy</a
                        >.
                    </template>
                    <template v-else>
                        {{ paragraph }}
                    </template>
                </p>
                <p class="pt-1 text-xs font-medium text-gray-500">
                    {{ footer }}
                </p>
            </div>

            <p v-if="licenseTitle" class="mt-4 text-sm text-gray-600">
                This download is offered under:
                <span class="font-medium text-gray-900">{{ licenseTitle }}</span
                >.
            </p>

            <div class="mt-5">
                <label
                    for="download-terms-accept"
                    class="flex cursor-pointer items-start gap-2"
                >
                    <input
                        id="download-terms-accept"
                        v-model="accepted"
                        type="checkbox"
                        class="mt-1 rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50"
                        name="download-terms-accept"
                    />
                    <span class="text-sm text-gray-700">
                        I have read and accept these terms for data users and
                        will comply with the license connected with the dataset.
                    </span>
                </label>
            </div>
        </template>

        <template #footer>
            <jet-secondary-button type="button" @click="close">
                Cancel
            </jet-secondary-button>
            <jet-success-button
                class="ml-2"
                type="button"
                :class="{
                    'cursor-not-allowed bg-gray-200 opacity-50': !accepted,
                }"
                :disabled="!accepted"
                @click="confirm"
            >
                Accept &amp; Download
            </jet-success-button>
        </template>
    </jet-dialog-modal>
</template>

<script setup>
import axios from "axios";
import { ref, watch } from "vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetSuccessButton from "@/Jetstream/SuccessButton.vue";
import {
    DOWNLOAD_TERMS_FOOTER,
    DOWNLOAD_TERMS_PARAGRAPHS,
    DOWNLOAD_TERMS_TITLE,
} from "@/Utils/downloadTermsContent.js";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    downloadUrl: {
        type: String,
        default: null,
    },
    licenseTitle: {
        type: String,
        default: null,
    },
    downloadIdentifier: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["close", "accepted"]);

const title = DOWNLOAD_TERMS_TITLE;
const paragraphs = DOWNLOAD_TERMS_PARAGRAPHS;
const footer = DOWNLOAD_TERMS_FOOTER;
const privacyParagraphIndex = 3;
const accepted = ref(false);

watch(
    () => props.show,
    (visible) => {
        if (visible) {
            accepted.value = false;
        }
    }
);

function close() {
    accepted.value = false;
    emit("close");
}

function confirm() {
    if (!accepted.value || !props.downloadUrl) {
        return;
    }

    const url = props.downloadUrl;

    if (props.downloadIdentifier) {
        axios
            .post(route("track.download", props.downloadIdentifier))
            .catch(() => {});
    }

    emit("accepted", url);
    accepted.value = false;
    emit("close");
    window.location.href = url;
}
</script>
