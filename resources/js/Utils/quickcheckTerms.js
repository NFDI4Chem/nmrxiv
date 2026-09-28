export const NUCLEUS_LABELS = { "13C": "¹³C", "1H": "¹H" };

export const VERDICT_TEXT = {
    accept: {
        title: "Assignments match the predicted shifts",
        hint: "The assigned shifts agree with the nmrshiftdb2 prediction.",
    },
    review: {
        title: "Some assignments need checking",
        hint: "Check the highlighted atoms, ideally with HSQC and HMBC.",
    },
    reject: {
        title: "Assignments do not match the predicted shifts",
        hint: "Common causes: a wrong structure, swapped assignments or incorrect shift referencing.",
    },
    not_assessable: {
        title: "Assignments could not be checked",
        hint: "No shift could be compared with a prediction.",
    },
};

/** nmrshiftdb2 result per nucleus. */
export const RESULT_LABELS = {
    accept: "good fit",
    revise: "needs checking",
    warning: "needs checking",
    reject: "poor fit",
};

/** nmrshiftdb2 status per atom in the shift comparison. */
export const ATOM_STATUS_LABELS = {
    green: "matches",
    yellow: "borderline",
    red: "does not match",
    missing: "not assigned",
    impossible: "no prediction",
};

export const ROW_STATUS_LABELS = {
    ok: "matches",
    review: "check",
    fail: "does not match",
    not_assessable: "not checked",
};

export const ASSIGNMENT_RESULT_LABELS = {
    consistent: "Consistent",
    review: "Needs checking",
    inconsistent: "Inconsistent",
    not_assessable: "Not checked",
};

export const REASON_LABELS = {
    low_confidence_prediction: "less reliable prediction",
    diastereotopic_pair_mean: "diastereotopic CH₂, averaged",
};

export const ENVIRONMENT_MATCH_HINT =
    "How many bonds around the atom match a reference environment in nmrshiftdb2 (HOSE code spheres, 1 to 6). More bonds give a more reliable prediction.";

export function nucleusLabel(nucleus) {
    return NUCLEUS_LABELS[nucleus] || nucleus;
}

export function resultLabel(result) {
    return RESULT_LABELS[result] || result;
}

export function reasonLabel(reason) {
    return REASON_LABELS[reason] || String(reason).replace(/_/g, " ");
}

/**
 * Plain-language text of an assignment check issue, falling back to the
 * validator's own message for types it does not know.
 *
 * @param {{ type: string, nucleus: string, message: string }} issue
 * @returns {string}
 */
export function issueText(issue) {
    const proton = issue.nucleus === "1H";
    return (
        {
            unknown_atom: proton
                ? "The assigned atom has no attached proton in the structure."
                : "The assigned atom is not a carbon in the structure.",
            prediction_impossible:
                "No predicted shift is available for this atom.",
            accidental_overlap:
                "One shift is assigned to non-equivalent atoms. Check that the signals really overlap.",
            equivalence_violation:
                "Chemically equivalent atoms are given different shifts.",
            missing_signal: proton
                ? "Protons without an assigned shift."
                : "Carbons without an assigned shift.",
            solvent_assigned:
                "The shift is at the residual solvent signal and does not match the prediction.",
        }[issue.type] || issue.message
    );
}
