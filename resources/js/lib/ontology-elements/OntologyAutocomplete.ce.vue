<script setup>
import { computed, onMounted, ref, watch } from "vue";

import { searchOntologyClasses } from "@/lib/terminology-service";

const emit = defineEmits(["change"]);

const props = defineProps({
    label: {
        type: String,
        default: "",
    },
    info: {
        type: String,
        default: "",
    },
    placeholder: {
        type: String,
        default: "",
    },
    ontologies: {
        type: String,
        default: "",
    },
    value: {
        type: String,
        default: "",
    },
    styling: {
        type: String,
        default: "",
    },
    format: {
        type: String,
        default: "text",
    },
});

const searchTerm = ref("");
const matches = ref([]);
const selectedTerm = ref(null);

const selectedValue = computed(() => {
    if (!selectedTerm.value) {
        return null;
    }

    if (props.format === "json") {
        return selectedTerm.value;
    }

    return selectedTerm.value
        ? `${selectedTerm.value.label}\t${selectedTerm.value.ontology_prefix}\t${selectedTerm.value.iri}\t${selectedTerm.value.type}`
        : "";
});

function composeOntologyObject(content) {
    if (!content) {
        return "";
    }

    const data = content.split("\t");

    return {
        label: data[0],
        iri: data[2],
        ontology_prefix: data[1],
        type: data[3],
    };
}

function selectTerm(term) {
    if (term === "") {
        selectedTerm.value = null;
        searchTerm.value = "";
        return;
    }

    selectedTerm.value = term;
    searchTerm.value = term.label;
    emit("change", selectedValue.value);
    matches.value = [];
}

function highlight(content) {
    if (!searchTerm.value) {
        return content;
    }

    return content.replace(
        new RegExp(searchTerm.value, "gi"),
        (match) => `<span class="highlightText">${match}</span>`
    );
}

function concat(data) {
    return data ? data.join("") : "";
}

async function getSelectOptions() {
    if (searchTerm.value === "") {
        matches.value = [];
        return;
    }

    matches.value = await searchOntologyClasses(
        searchTerm.value,
        props.ontologies
    );
}

watch(
    () => props.value,
    (newValue) => {
        selectTerm(composeOntologyObject(newValue));
    }
);

onMounted(() => {
    if (props.value !== "") {
        selectTerm(composeOntologyObject(props.value));
    }
});
</script>

<template>
    <div class="auto-search-wrapper">
        <label v-if="label">{{ label }}</label>
        <input
            id="search"
            v-model="searchTerm"
            type="text"
            :placeholder="placeholder"
            :class="styling"
            autocomplete="off"
            @input.stop="getSelectOptions"
        />
        <p v-if="info && matches.length === 0">{{ info }}</p>
        <div
            v-else-if="matches.length > 0"
            class="auto-results-wrapper auto-is-active"
        >
            <ul tabindex="0" role="listbox">
                <li
                    v-for="doc in matches"
                    :key="doc.short_form || doc.iri"
                    role="option"
                    tabindex="-1"
                    aria-selected="false"
                    @click="selectTerm(doc)"
                >
                    <p v-html="highlight(doc.label)"></p>
                    <p>
                        <small v-html="concat(doc.description)"></small>
                    </p>
                    <small>{{ doc.ontology_prefix }}:{{ doc.iri }}</small>
                </li>
            </ul>
        </div>
        <button
            v-if="searchTerm !== '' && matches.length > 0"
            type="button"
            aria-label="clear the search query"
        ></button>
    </div>
</template>

<style>
:root {
    --close-button: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M18.984 6.422 13.406 12l5.578 5.578-1.406 1.406L12 13.406l-5.578 5.578-1.406-1.406L10.594 12 5.016 6.422l1.406-1.406L12 10.594l5.578-5.578z'/%3E%3C/svg%3E");
    --loupe-icon: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='%23929292' d='M16.041 15.856a.995.995 0 0 0-.186.186A6.97 6.97 0 0 1 11 18c-1.933 0-3.682-.782-4.95-2.05S4 12.933 4 11s.782-3.682 2.05-4.95S9.067 4 11 4s3.682.782 4.95 2.05S18 9.067 18 11a6.971 6.971 0 0 1-1.959 4.856zm5.666 4.437-3.675-3.675A8.967 8.967 0 0 0 20 11c0-2.485-1.008-4.736-2.636-6.364S13.485 2 11 2 6.264 3.008 4.636 4.636 2 8.515 2 11s1.008 4.736 2.636 6.364S8.515 20 11 20a8.967 8.967 0 0 0 5.618-1.968l3.675 3.675a.999.999 0 1 0 1.414-1.414z'/%3E%3C/svg%3E");
}

.auto-search-wrapper {
    display: block;
    position: relative;
    width: 100%;
}

.auto-search-wrapper p {
    margin: 0;
    padding: 0;
    font-size: 1.1em;
}

.auto-search-wrapper p .highlightText {
    font-weight: bold;
}

.auto-search-wrapper input {
    border: 1px solid #d7d7d7;
    box-shadow: none;
    box-sizing: border-box;
    font-size: 16px;
    padding: 12px 45px 12px 10px;
    width: 100%;
}

.auto-search-wrapper input:focus {
    border: 1px solid #858585;
    outline: none;
}

.auto-search-wrapper ul {
    list-style: none;
    margin: 0;
    overflow: auto;
    padding: 0;
}

.auto-search-wrapper ul li {
    cursor: pointer;
    margin: 0;
    overflow: hidden;
    padding: 10px;
    position: relative;
    border: 1px dotted #f1f1f2;
}

.auto-search-wrapper ul li:hover {
    background-color: #f1f1f2;
}

.auto-results-wrapper {
    background-color: #fff;
    border: 1px solid #858585;
    border-top: none;
    box-sizing: border-box;
    display: none;
    overflow: hidden;
}

.auto-results-wrapper.auto-is-active {
    display: block;
    margin-top: -1px;
    position: absolute;
    width: 100%;
    z-index: 99999;
}
</style>
