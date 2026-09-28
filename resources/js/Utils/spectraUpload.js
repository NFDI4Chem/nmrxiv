import axios from "axios";

export const SPECTRA_UPLOAD_ACCEPT = ".zip,.jdx,.dx,.jcamp";
export const SPECTRA_UPLOAD_MAX_BYTES = 200 * 1024 * 1024;

const SINGLE_FILE_PATTERN = /\.(zip|jdx|dx|jcamp)$/i;
const IGNORED_PATTERN =
    /(^|\/)(\.DS_Store|Thumbs\.db|desktop\.ini)$|(^|\/)__MACOSX\//i;

/**
 * Turn dropped entries into one uploadable file: a single zip or JCAMP-DX
 * file is sent as it is; folders and multiple files are zipped with their
 * relative paths so NMRKit can recognise Bruker, Varian and JEOL layouts.
 *
 * @param {import("./droppedEntries").DroppedEntry[]} entries
 * @param {{ onZipProgress?: (percent: number) => void }} [options]
 * @returns {Promise<File>}
 */
export async function buildSpectraUploadFile(entries, { onZipProgress } = {}) {
    const files = entries.filter((entry) => !IGNORED_PATTERN.test(entry.path));

    if (files.length === 0) {
        throw new Error("No files were found in what you dropped.");
    }

    const totalBytes = files.reduce((sum, entry) => sum + entry.size, 0);
    if (totalBytes > SPECTRA_UPLOAD_MAX_BYTES) {
        throw new Error(
            `This is too large to upload (${formatMegabytes(
                totalBytes
            )}). The limit is ${formatMegabytes(
                SPECTRA_UPLOAD_MAX_BYTES
            )}; try dropping a single experiment folder.`
        );
    }

    if (files.length === 1 && SINGLE_FILE_PATTERN.test(files[0].path)) {
        return files[0].getFile();
    }

    const { default: JSZip } = await import("jszip");
    const zip = new JSZip();
    for (const entry of files) {
        zip.file(entry.path, await entry.getFile());
    }

    const blob = await zip.generateAsync(
        { type: "blob", compression: "STORE" },
        (metadata) => onZipProgress?.(metadata.percent)
    );

    return new File([blob], `${commonRoot(files) || "spectrum"}.zip`, {
        type: "application/zip",
    });
}

/**
 * Upload a spectrum and let NMRKit detect its peaks.
 *
 * @param {File} file
 * @param {{ onUploadProgress?: (percent: number) => void, signal?: AbortSignal }} [options]
 * @returns {Promise<Array<object>>} Detected spectra
 */
export async function parseSpectraUpload(
    file,
    { onUploadProgress, signal } = {}
) {
    const form = new FormData();
    form.append("file", file);

    const { data } = await axios.post("/api/v1/search/spectra/parse", form, {
        signal,
        onUploadProgress: (event) => {
            if (event.total) {
                onUploadProgress?.((event.loaded / event.total) * 100);
            }
        },
    });

    return data.spectra ?? [];
}

/**
 * A readable message for a failed upload or parse request.
 *
 * @param {unknown} error
 */
export function spectraUploadErrorMessage(error) {
    const response = error?.response;

    if (response?.status === 429) {
        return "You have uploaded several spectra in a short time. Please wait a minute and try again.";
    }

    return (
        response?.data?.errors?.file?.[0] ||
        response?.data?.message ||
        error?.message ||
        "Something went wrong while reading this spectrum."
    );
}

function commonRoot(entries) {
    const first = entries[0].path.split("/")[0];

    return entries.every((entry) => entry.path.split("/")[0] === first) &&
        entries.some((entry) => entry.path.includes("/"))
        ? first
        : null;
}

function formatMegabytes(bytes) {
    return `${Math.round(bytes / (1024 * 1024))} MB`;
}
