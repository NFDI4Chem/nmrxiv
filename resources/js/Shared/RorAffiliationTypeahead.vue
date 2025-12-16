<template>
    <div class="relative">
        <jet-input
            :id="inputId"
            ref="inputElement"
            v-model="searchQuery"
            type="text"
            :class="inputClass"
            :placeholder="placeholder"
            :autocomplete="autocomplete"
            @input="onInput"
            @focus="onFocus"
            @blur="onBlur"
            @keydown.down.prevent="navigateDown"
            @keydown.up.prevent="navigateUp"
            @keydown.enter.prevent="selectHighlighted"
            @keydown.escape="closeDropdown"
        />

        <!-- Loading indicator -->
        <div
            v-if="loading"
            class="absolute right-3 top-1/2 transform -translate-y-1/2"
        >
            <svg
                class="animate-spin h-5 w-5 text-gray-400"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                ></circle>
                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
            </svg>
        </div>

        <!-- Dropdown suggestions -->
        <div
            v-if="showDropdown && (suggestions.length > 0 || allowFreeText)"
            class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-96 overflow-auto"
        >
            <!-- Suggestions list -->
            <div
                v-for="(org, index) in suggestions"
                :key="org.id"
                class="px-4 py-3 cursor-pointer hover:bg-gray-100 border-b border-gray-100 last:border-b-0"
                :class="{
                    'bg-gray-100': index === highlightedIndex,
                }"
                @mousedown.prevent="selectOrganization(org)"
                @mouseenter="highlightedIndex = index"
            >
                <div class="text-sm font-medium text-gray-900">
                    {{ getDisplayName(org) }}
                </div>
                <div
                    v-if="getAltNames(org)"
                    class="text-xs text-gray-500 mt-1 italic"
                >
                    {{ getAltNames(org) }}
                </div>
                <div class="text-xs text-gray-600 mt-1">
                    {{ getOrgType(org) }}
                    <span v-if="getLocation(org)">
                        &middot; {{ getLocation(org) }}
                    </span>
                </div>
            </div>

            <!-- No results message -->
            <div
                v-if="
                    suggestions.length === 0 &&
                    searchQuery.length >= minLength &&
                    !loading
                "
                class="px-4 py-3 text-sm text-gray-500"
            >
                <p>No organizations found.</p>
                <p v-if="allowFreeText" class="mt-2 text-xs text-gray-600">
                    Press Enter to use "{{ searchQuery }}" as a custom
                    affiliation.
                </p>
            </div>
        </div>
    </div>
</template>

<script>
import JetInput from "@/Jetstream/Input.vue";

export default {
    components: {
        JetInput,
    },
    props: {
        modelValue: {
            type: String,
            default: "",
        },
        rorId: {
            type: String,
            default: "",
        },
        inputId: {
            type: String,
            default: "affiliation",
        },
        inputClass: {
            type: String,
            default: "mt-1 block w-full",
        },
        placeholder: {
            type: String,
            default: "Start typing organization name...",
        },
        autocomplete: {
            type: String,
            default: "organization",
        },
        minLength: {
            type: Number,
            default: 3,
        },
        debounceDelay: {
            type: Number,
            default: 300,
        },
        allowFreeText: {
            type: Boolean,
            default: true,
        },
    },
    emits: ["update:modelValue", "update:rorId", "organizationSelected"],
    data() {
        return {
            searchQuery: this.modelValue,
            suggestions: [],
            showDropdown: false,
            loading: false,
            debounceTimeout: null,
            highlightedIndex: -1,
            selectedRorId: this.rorId,
        };
    },
    watch: {
        modelValue(newVal) {
            if (newVal !== this.searchQuery) {
                this.searchQuery = newVal;
            }
        },
        rorId(newVal) {
            this.selectedRorId = newVal;
        },
    },
    methods: {
        onInput() {
            this.$emit("update:modelValue", this.searchQuery);

            // Clear previous timeout
            if (this.debounceTimeout) {
                clearTimeout(this.debounceTimeout);
            }

            // Clear ROR ID when user types (manual entry)
            if (this.selectedRorId) {
                this.selectedRorId = "";
                this.$emit("update:rorId", "");
            }

            // Don't search if query is too short
            if (this.searchQuery.length < this.minLength) {
                this.suggestions = [];
                this.showDropdown = false;
                return;
            }

            // Debounce the API call
            this.debounceTimeout = setTimeout(() => {
                this.searchOrganizations();
            }, this.debounceDelay);
        },
        onFocus() {
            if (
                this.searchQuery.length >= this.minLength &&
                this.suggestions.length > 0
            ) {
                this.showDropdown = true;
            }
        },
        onBlur() {
            // Delay hiding dropdown to allow click events to fire
            setTimeout(() => {
                this.showDropdown = false;
                this.highlightedIndex = -1;
            }, 200);
        },
        async searchOrganizations() {
            if (this.searchQuery.length < this.minLength) {
                return;
            }

            this.loading = true;

            try {
                const response = await axios.get(this.route("ror.search"), {
                    params: {
                        query: this.searchQuery,
                    },
                });

                if (response.data && response.data.items) {
                    this.suggestions = response.data.items;
                    this.showDropdown = true;
                    this.highlightedIndex = -1;
                }
            } catch (error) {
                console.error("Error fetching ROR organizations:", error);
                this.suggestions = [];

                // Show error message if available
                if (error.response?.data?.error) {
                    console.error(error.response.data.error);
                }
            } finally {
                this.loading = false;
            }
        },
        selectOrganization(org) {
            const displayName = this.getDisplayName(org);
            const altNames = this.getAltNames(org);
            const orgType = this.getOrgType(org);
            const location = this.getLocation(org);

            // Format complete organization details
            let fullDetails = displayName;

            if (altNames) {
                fullDetails += ` (${altNames})`;
            }

            if (orgType || location) {
                const typeAndLocation = [orgType, location]
                    .filter(Boolean)
                    .join(" · ");
                if (typeAndLocation) {
                    fullDetails += ` - ${typeAndLocation}`;
                }
            }

            this.searchQuery = fullDetails;
            this.selectedRorId = org.id || "";

            this.$emit("update:modelValue", fullDetails);
            this.$emit("update:rorId", this.selectedRorId);
            this.$emit("organizationSelected", {
                name: displayName,
                fullDetails: fullDetails,
                rorId: this.selectedRorId,
                organization: org,
            });

            this.showDropdown = false;
            this.suggestions = [];
            this.highlightedIndex = -1;
        },
        navigateDown() {
            if (!this.showDropdown || this.suggestions.length === 0) {
                return;
            }
            this.highlightedIndex =
                (this.highlightedIndex + 1) % this.suggestions.length;
        },
        navigateUp() {
            if (!this.showDropdown || this.suggestions.length === 0) {
                return;
            }
            this.highlightedIndex =
                this.highlightedIndex <= 0
                    ? this.suggestions.length - 1
                    : this.highlightedIndex - 1;
        },
        selectHighlighted() {
            if (
                this.highlightedIndex >= 0 &&
                this.highlightedIndex < this.suggestions.length
            ) {
                this.selectOrganization(
                    this.suggestions[this.highlightedIndex]
                );
            } else if (this.allowFreeText && this.searchQuery.length > 0) {
                // Allow free text entry
                this.$emit("update:modelValue", this.searchQuery);
                this.$emit("update:rorId", "");
                this.showDropdown = false;
            }
        },
        closeDropdown() {
            this.showDropdown = false;
            this.highlightedIndex = -1;
        },
        getDisplayName(org) {
            if (org.names && org.names.length > 0) {
                const displayName = org.names.find((name) =>
                    name.types.includes("ror_display")
                );
                return displayName ? displayName.value : org.names[0].value;
            }
            return org.name || "";
        },
        getAltNames(org) {
            if (!org.names || org.names.length === 0) {
                return "";
            }

            const altNames = org.names
                .filter(
                    (name) =>
                        (name.types.includes("alias") ||
                            name.types.includes("acronym") ||
                            name.types.includes("label")) &&
                        !name.types.includes("ror_display")
                )
                .map((name) => name.value)
                .slice(0, 3); // Limit to first 3 alt names

            return altNames.join(", ");
        },
        getOrgType(org) {
            if (org.types && org.types.length > 0) {
                return (
                    org.types[0].charAt(0).toUpperCase() + org.types[0].slice(1)
                );
            }
            return "";
        },
        getLocation(org) {
            if (org.locations && org.locations.length > 0) {
                const location = org.locations[0];
                const city = location.geonames_details?.name || "";
                const country = location.geonames_details?.country_name || "";

                if (city && country) {
                    return `${city}, ${country}`;
                } else if (country) {
                    return country;
                }
            }
            return "";
        },
    },
};
</script>
