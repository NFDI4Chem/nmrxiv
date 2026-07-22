<template>
    <span class="inline">
        <template v-for="(part, index) in parts" :key="index">
            <sub v-if="part.subscript">{{ part.text }}</sub>
            <template v-else>{{ part.text }}</template>
        </template>
    </span>
</template>

<script>
import { parseMolecularFormulaDisplayParts } from "@/Utils/molecularFormula.js";

export default {
    name: "MolecularFormula",

    props: {
        formula: {
            type: String,
            required: true,
        },
    },

    computed: {
        parts() {
            return parseMolecularFormulaDisplayParts(this.formula);
        },
    },
};
</script>
