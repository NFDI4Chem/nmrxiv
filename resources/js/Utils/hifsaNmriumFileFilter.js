/**
 * NMRium wrapper fileFilter for samples that include HiFSA / Cosmic Truth data.
 *
 * Cosmic Truth exports nest EXTRA/ (and often a hifsa/ analysis folder) inside
 * the study archive. The wrapper applies fileFilter for type "url" / "file"
 * loads — see
 * https://github.com/NFDI4Chem/nmrium-react-wrapper/wiki/3.-Wrapper-Events
 *
 * Important: do not set include to []. In file-collection, an empty include
 * array is truthy and matches nothing (Array.some on []), so every file is
 * dropped — spectra appear empty after load.
 */

/** @type {string[]} */
export const HIFSA_NMRIUM_EXCLUDE = ["EXTRA/", "hifsa/", "HiFSA/", "HIFSA/"];

/**
 * @param {object|null|undefined} study
 * @returns {boolean}
 */
export function studyHasHifsa(study) {
    if (!study || typeof study !== "object") {
        return false;
    }

    const hifsaData = study.hifsa_data;
    if (
        hifsaData &&
        typeof hifsaData === "object" &&
        !Array.isArray(hifsaData) &&
        Object.keys(hifsaData).length > 0
    ) {
        return true;
    }

    if (Array.isArray(hifsaData) && hifsaData.length > 0) {
        return true;
    }

    const pdfUrl = study.hifsa_pdf_url;

    return typeof pdfUrl === "string" && pdfUrl.trim() !== "";
}

/**
 * Wrapper fileFilter for HiFSA samples, or null when filtering is not needed.
 *
 * Only `exclude` is set — never pass an empty `include` array.
 *
 * @param {object|null|undefined} study
 * @returns {{ exclude: string[] }|null}
 */
export function hifsaNmriumFileFilter(study) {
    if (!studyHasHifsa(study)) {
        return null;
    }

    return {
        exclude: [...HIFSA_NMRIUM_EXCLUDE],
    };
}
