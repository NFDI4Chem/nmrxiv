export const depositDataOptions = [
    {
        id: "publication",
        title: "Publication Deposition",
        description:
            "Deposit spectra linked to a manuscript, preprint, or thesis.",
    },
    {
        id: "community",
        title: "Community Contribution",
        description: "Contribute raw or processed spectra for open science.",
    },
];

/**
 * @param {'publication' | 'community'} deposition
 * @returns {string}
 */
export function depositDataUrl(deposition) {
    if (deposition === "community") {
        return route("community-contribution");
    }

    return route("upload", { deposition });
}

/**
 * @returns {string}
 */
export function communityContributionUploadUrl() {
    return route("community-contribution");
}
