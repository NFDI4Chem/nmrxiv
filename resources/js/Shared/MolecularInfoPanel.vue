<template>
    <ul
        role="list"
        class="mt-4 space-y-8"
    >
        <li
            v-for="molecule in molecules"
            :key="moleculeKey(molecule)"
            class="min-w-0 space-y-4"
        >
            <div
                v-if="hasSmiles(molecule)"
                class="flex max-h-48 items-center justify-center overflow-hidden rounded-md bg-white [&_img]:max-h-44 [&_img]:w-auto [&_img]:object-contain [&_img]:p-2 dark:bg-gray-950"
            >
                <Depictor2D
                    class="max-h-48 max-w-full"
                    :molecule="String(molecule.canonical_smiles)"
                    :width="220"
                    :height="220"
                    :show-download="false"
                />
            </div>
            <dl class="space-y-3">
                <div
                    v-for="field in moleculeFields(molecule)"
                    :key="field.label"
                    class="min-w-0"
                >
                    <dt
                        class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400"
                    >
                        {{ field.label }}
                    </dt>
                    <dd
                        :class="[
                            'mt-0.5 break-words text-gray-900 dark:text-gray-100',
                            field.mono
                                ? 'font-mono text-xs leading-relaxed text-gray-800 dark:text-gray-200'
                                : 'text-sm font-medium tabular-nums',
                        ]"
                    >
                        {{ field.value }}
                    </dd>
                </div>
            </dl>
            <p
                v-if="!hasSmiles(molecule) && moleculeFields(molecule).length === 0"
                class="text-sm text-gray-500 dark:text-gray-400"
            >
                No molecular data available
            </p>
        </li>
    </ul>
</template>

<script>
import Depictor2D from "@/Shared/Depictor2D.vue";

export default {
    name: "MolecularInfoPanel",

    components: {
        Depictor2D,
    },

    props: {
        molecules: {
            type: Array,
            required: true,
        },
    },

    methods: {
        moleculeKey(molecule) {
            return molecule?.id ?? molecule?.standard_inchi ?? molecule?.canonical_smiles;
        },
        hasSmiles(molecule) {
            const smiles = molecule?.canonical_smiles;

            return smiles != null && String(smiles).trim() !== "";
        },
        moleculeInchi(molecule) {
            const standard = molecule?.standard_inchi;
            if (standard != null && String(standard).trim() !== "") {
                return String(standard).trim();
            }

            const inchi = molecule?.inchi;
            if (inchi != null && String(inchi).trim() !== "") {
                return String(inchi).trim();
            }

            return null;
        },
        formattedWeight(molecule) {
            const raw = molecule?.molecular_weight;
            if (raw == null || raw === "") {
                return null;
            }
            const n = Number(raw);
            if (!Number.isFinite(n)) {
                const trimmed = String(raw).trim();

                return trimmed !== "" ? trimmed : null;
            }

            const formatted =
                Math.abs(n - Math.round(n)) < 0.001
                    ? String(Math.round(n))
                    : n.toFixed(2).replace(/\.?0+$/, "");

            return `${formatted} g/mol`;
        },
        moleculeFields(molecule) {
            const fields = [];

            const smiles = molecule?.canonical_smiles;
            if (smiles != null && String(smiles).trim() !== "") {
                fields.push({
                    label: "SMILES",
                    value: String(smiles).trim(),
                    mono: true,
                });
            }

            const formula = molecule?.molecular_formula;
            if (formula != null && String(formula).trim() !== "") {
                fields.push({
                    label: "Molecular formula",
                    value: String(formula).trim(),
                    mono: true,
                });
            }

            const weight = this.formattedWeight(molecule);
            if (weight != null) {
                fields.push({
                    label: "Molecular weight",
                    value: weight,
                    mono: false,
                });
            }

            const inchi = this.moleculeInchi(molecule);
            if (inchi != null) {
                fields.push({
                    label: "InChI",
                    value: inchi,
                    mono: true,
                });
            }

            return fields;
        },
    },
};
</script>
