import axios from "axios";
import { moleculeStructureSmiles } from "@/Utils/moleculeStructureSmiles";

/**
 * Normalized study id from a folder (DB default `study_id` is 0, not a real study).
 *
 * @param {{ study_id?: number|null }} folder
 * @returns {number|null}
 */
export function folderStudyId(folder) {
    if (folder?.study_id == null) {
        return null;
    }

    const id = Number(folder.study_id);

    return Number.isInteger(id) && id > 0 ? id : null;
}

/**
 * Whether a filesystem row represents a draft sample folder.
 *
 * @param {{ model_type?: string, study_id?: number|null, type?: string, level?: number, has_children?: boolean }} folder
 */
export function isDraftSampleFolder(folder) {
    if (!folder || folder.type !== "directory") {
        return false;
    }

    if (folder.model_type === "study") {
        return true;
    }

    if (folderStudyId(folder) != null) {
        return true;
    }

    return Number(folder.level) === 0 && folder.has_children !== false;
}

/**
 * @param {import('vue').Ref|{ file?: { children?: unknown[] } }} fsbRef
 * @returns {{ valid: boolean, error: string|null }}
 */
export function validateDraftFolders(fsbRef, studies = []) {
    const browser = fsbRef?.value ?? fsbRef;
    const root = browser?.file ?? fsbRef?.file;
    const sampleFoldersPagination = browser?.sampleFoldersPagination ?? null;

    if (!root?.children?.length && !sampleFoldersPagination?.total) {
        return { valid: false, error: null };
    }

    let foldersExist = false;

    if (sampleFoldersPagination?.total > 0) {
        foldersExist = root.children?.some((fso) => fso.has_children) ?? false;

        if (!foldersExist) {
            foldersExist = sampleFoldersPagination.total > 0;
        }
    } else {
        root.children.forEach((fso) => {
            if (fso.has_children) {
                foldersExist = true;
            }
        });
    }

    if (!foldersExist) {
        return {
            valid: false,
            error: "Spectra files need to be organised into folders. Please create a folder corresponding to each sample and add all your NMR spectroscopic experiment output files to the corresponding folders.",
        };
    }

    let studiesExist = Array.isArray(studies) && studies.length > 0;

    const hasStudies = (file) => {
        if (isDraftSampleFolder(file)) {
            studiesExist = true;
        }

        if (file.has_children && file.children) {
            file.children.forEach(hasStudies);
        }
    };

    hasStudies(root);

    if (!studiesExist) {
        return {
            valid: false,
            error: "Please organize the spectral data into folders corresponding to the given samples and re-upload. Refer to the documentation for more details.",
        };
    }

    return { valid: true, error: null };
}

/**
 * @param {number|string} draftId
 * @param {Record<string, unknown>} [payload]
 */
export async function processDraft(draftId, payload = {}) {
    const response = await axios.post(
        `/dashboard/drafts/${draftId}/process`,
        payload
    );

    return {
        project: response.data.project,
        studies: response.data.studies ?? [],
        warnings: response.data.warnings ?? [],
    };
}

/**
 * @param {number|string} draftId
 */
export async function fetchDraft(draftId) {
    const response = await axios.get(`/dashboard/drafts/${draftId}/show`);

    return response.data.draft;
}

/**
 * @param {number|string} draftId
 */
export async function loadStudiesFromDraft(draftId) {
    try {
        const infoResponse = await axios.get(
            `/dashboard/drafts/${draftId}/info`
        );
        const project = infoResponse.data.project;
        const studies = infoResponse.data.studies;

        if (project) {
            return {
                project,
                studies: Array.isArray(studies) ? studies : [],
                warnings: [],
            };
        }
    } catch {
        // Fall through to process endpoint.
    }

    return null;
}

/**
 * @param {Array<{ internal_status?: string }>} studies
 */
export function firstCompleteStudy(studies) {
    return (
        studies.find((study) => study.internal_status === "complete") ?? null
    );
}

/**
 * @param {{ study_id?: number, name?: string, model_type?: string }} folder
 * @param {Array<{ id: number, name?: string, internal_status?: string }>} studies
 */
/**
 * @param {{ model_type?: string, study_id?: number, name?: string }} folder
 * @param {Array<{ id: number, internal_status?: string }>} studies
 */
/**
 * @param {{ model_type?: string, study_id?: number, name?: string }} folder
 * @param {Array<{ id: number, internal_status?: string }>} studies
 * @param {Set<number>|null} [submittedStudyIds]
 */
/**
 * @param {{ internal_status?: string|null }} study
 */
export function isStudyActivelyProcessing(study) {
    if (!study) {
        return false;
    }

    return study.internal_status === "processing";
}

/**
 * @param {{ model_type?: string, study_id?: number, name?: string }} folder
 * @param {Array<{ id: number, internal_status?: string }>} studies
 * @param {Set<number>|null} [submittedStudyIds]
 * @param {{ studiesWorkspaceReady?: boolean, draftProcessing?: boolean }} [options]
 */
export function isStudyFolderProcessing(
    folder,
    studies,
    submittedStudyIds = null,
    options = {}
) {
    if (!folder) {
        return false;
    }

    const studyId = folderStudyId(folder);

    if (studyId != null && submittedStudyIds?.has(studyId)) {
        return false;
    }

    const study = findStudyForFolder(folder, studies);

    if (study) {
        if (submittedStudyIds?.has(Number(study.id))) {
            return false;
        }

        if (study.internal_status === "complete") {
            return false;
        }

        return isStudyActivelyProcessing(study);
    }

    if (options.studiesWorkspaceReady) {
        return false;
    }

    if (options.draftProcessing) {
        return isDraftSampleFolder(folder);
    }

    return false;
}

/**
 * @param {{ study_id?: number }} folder
 * @param {Set<number>|null} submittedStudyIds
 */
export function isStudyFolderSubmitted(folder, submittedStudyIds) {
    const studyId = folderStudyId(folder);

    if (studyId == null || !submittedStudyIds) {
        return false;
    }

    return submittedStudyIds.has(studyId);
}

/**
 * @param {{ sample?: { molecules?: Array<Record<string, unknown>> }, has_structure?: boolean }|null} study
 */
export function studyHasAssignedStructure(study) {
    if (!study) {
        return false;
    }

    if (typeof study.has_structure === "boolean") {
        return study.has_structure;
    }

    const molecules = study.sample?.molecules;

    if (!Array.isArray(molecules) || molecules.length === 0) {
        return false;
    }

    return molecules.some((molecule) =>
        Boolean(moleculeStructureSmiles(molecule)?.trim())
    );
}

/**
 * Ready to publish: processing finished, NMRium present, and structure assigned.
 *
 * @param {{ model_type?: string, study_id?: number, name?: string }} folder
 * @param {Array<{ id: number, internal_status?: string, has_nmrium?: boolean, has_structure?: boolean, sample?: { molecules?: Array<Record<string, unknown>> } }>} studies
 */
export function isStudyReadyToPublish(study) {
    if (!study || study.internal_status !== "complete") {
        return false;
    }

    return Boolean(study.has_nmrium) && studyHasAssignedStructure(study);
}

export function isSampleFolderReadyToPublish(
    folder,
    studies,
    submittedStudyIds = null
) {
    if (isStudyFolderSubmitted(folder, submittedStudyIds)) {
        return false;
    }

    const study = findStudyForFolder(folder, studies);

    return isStudyReadyToPublish(study);
}

/**
 * @param {number|string} draftId
 * @param {{ study_ids: number[], terms: boolean, conditions: boolean }} payload
 */
export async function publishCommunityStudies(draftId, payload) {
    const { data } = await axios.post(
        route("community-contribution.publish-studies", { draft: draftId }),
        {
            study_ids: payload.study_ids,
            terms: payload.terms ? 1 : 0,
            conditions: payload.conditions ? 1 : 0,
        }
    );

    return data;
}

/**
 * @param {number|string} draftId
 */
export async function fetchDraftStudiesStatus(draftId) {
    const { data } = await axios.get(`/dashboard/drafts/${draftId}/status`);

    return data.studies ?? [];
}

/**
 * @param {Array<{ id: number, internal_status?: string, has_nmrium?: boolean, has_structure?: boolean }>} studies
 * @param {Array<{ id: number, internal_status?: string, has_nmrium?: boolean, has_structure?: boolean }>} statusRows
 */
export function applyStudyStatusUpdates(studies, statusRows) {
    const byId = Object.fromEntries(statusRows.map((row) => [row.id, row]));

    studies.forEach((study) => {
        const row = byId[study.id];

        if (row) {
            study.internal_status = row.internal_status;
            study.has_nmrium = row.has_nmrium;

            if (typeof row.has_structure === "boolean") {
                study.has_structure = row.has_structure;
            }
        }
    });
}

export function findStudyForFolder(folder, studies) {
    if (!folder || !Array.isArray(studies) || studies.length === 0) {
        return null;
    }

    const linkedStudyId = folderStudyId(folder);

    if (linkedStudyId != null) {
        const byId = studies.find(
            (study) => Number(study.id) === linkedStudyId
        );

        if (byId) {
            return byId;
        }
    }

    if (isDraftSampleFolder(folder) && folder.name) {
        return studies.find((study) => study.name === folder.name) ?? null;
    }

    return null;
}
