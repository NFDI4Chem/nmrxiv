<template>
    <jet-form-section @submitted="updateSpectraPreferences">
        <template #title> Spectra preferences </template>

        <template #description>
            When a sample has more than one spectrum, choose whether NMRium
            should open a 1D nucleus or a 2D experiment first. This preference
            is saved to your account and applies whenever you view or edit
            spectra in nmrXiv. If the selected spectrum is not available, NMRium
            opens the first tab it can.
        </template>

        <template #form>
            <div class="col-span-6">
                <SpectrumTabPreferenceFields
                    :dimension="form.default_spectrum_dimension"
                    :tab="form.default_spectrum_tab"
                    :tabs1-d="tabs1D"
                    :tabs2-d="tabs2D"
                    :disabled="form.processing"
                    @update:dimension="onDimensionChange"
                    @update:tab="form.default_spectrum_tab = $event"
                />
                <jet-input-error
                    :message="form.errors.default_spectrum_tab"
                    class="mt-2"
                />
            </div>
        </template>

        <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </jet-action-message>

            <jet-button
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing || !isFormValid"
            >
                Save
            </jet-button>
        </template>
    </jet-form-section>
</template>

<script>
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import SpectrumTabPreferenceFields from "@/Shared/SpectrumTabPreferenceFields.vue";
import {
    allAllowedSpectrumTabs,
    initialSpectrumPreference,
    preferenceTabFromSelection,
    readStoredSpectrumTab,
} from "@/Utils/nmriumTabPreference.js";

export default {
    components: {
        JetActionMessage,
        JetButton,
        JetFormSection,
        JetInputError,
        SpectrumTabPreferenceFields,
    },
    props: {
        user: {
            type: Object,
            required: true,
        },
    },
    data() {
        const tabs1D = this.$page.props.defaultSpectrumTabs1D ?? [];
        const tabs2D = this.$page.props.defaultSpectrumTabs2D ?? [];
        const storedTab =
            this.user.preferences?.default_spectrum_tab ??
            readStoredSpectrumTab(allAllowedSpectrumTabs(this.$page)) ??
            "";
        const initial = storedTab
            ? initialSpectrumPreference({
                  props: {
                      ...this.$page.props,
                      auth: {
                          user: {
                              preferences: { default_spectrum_tab: storedTab },
                          },
                      },
                  },
              })
            : { dimension: "", tab: "" };

        return {
            tabs1D,
            tabs2D,
            form: this.$inertia.form({
                default_spectrum_dimension: initial.dimension,
                default_spectrum_tab: initial.tab,
            }),
        };
    },
    computed: {
        isFormValid() {
            if (!this.form.default_spectrum_dimension) {
                return true;
            }

            const options =
                this.form.default_spectrum_dimension === "1d"
                    ? this.tabs1D
                    : this.tabs2D;

            return options.includes(this.form.default_spectrum_tab);
        },
    },
    methods: {
        onDimensionChange(dimension) {
            this.form.default_spectrum_dimension = dimension;

            if (!dimension) {
                this.form.default_spectrum_tab = "";
            }
        },
        updateSpectraPreferences() {
            const tab = preferenceTabFromSelection(
                this.form.default_spectrum_dimension,
                this.form.default_spectrum_tab
            );

            this.form
                .transform(() => ({
                    default_spectrum_tab: tab,
                }))
                .put(route("user.preferences.update"), {
                    preserveScroll: true,
                });
        },
    },
};
</script>
