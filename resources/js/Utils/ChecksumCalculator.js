/**
 * File Checksum Calculator for Frontend
 *
 * Provides utilities to calculate MD5 and SHA-256 checksums for files
 * before upload to ensure file integrity verification.
 */

import CryptoJS from "crypto-js";

class ChecksumCalculator {
    /**
     * Calculate checksums for a file using multiple algorithms
     * @param {File} file - The file to calculate checksums for
     * @param {Array} algorithms - Array of algorithms to use ['md5', 'sha256']
     * @param {Function} progressCallback - Optional callback for progress updates
     * @returns {Promise<Object>} Object containing checksums for each algorithm
     */
    static async calculateChecksums(
        file,
        algorithms = ["sha256"],
        progressCallback = null
    ) {
        const results = {};

        // Validate inputs
        if (!file || !(file instanceof File)) {
            throw new Error("Invalid file provided");
        }

        if (!Array.isArray(algorithms) || algorithms.length === 0) {
            throw new Error("At least one algorithm must be specified");
        }

        // Additional validation to catch directories and invalid file objects
        if (file.size === 0 && file.type === "") {
            throw new Error(
                "Cannot calculate checksum for directory or empty file object"
            );
        }

        // Check for supported algorithms
        const supportedAlgorithms = ["md5", "sha256", "sha1"];
        const unsupported = algorithms.filter(
            (alg) => !supportedAlgorithms.includes(alg)
        );
        if (unsupported.length > 0) {
            throw new Error(
                `Unsupported algorithms: ${unsupported.join(", ")}`
            );
        }

        try {
            // Read file in chunks for better performance with large files
            const chunkSize = 1024 * 1024; // 1MB chunks
            const chunks = Math.ceil(file.size / chunkSize);

            // Initialize hash objects for each algorithm
            const hashers = {};
            if (algorithms.includes("md5")) {
                hashers.md5 = CryptoJS.algo.MD5.create();
            }
            if (algorithms.includes("sha256")) {
                hashers.sha256 = CryptoJS.algo.SHA256.create();
            }
            if (algorithms.includes("sha1")) {
                hashers.sha1 = CryptoJS.algo.SHA1.create();
            }

            // Process file in chunks
            for (let chunkIndex = 0; chunkIndex < chunks; chunkIndex++) {
                const start = chunkIndex * chunkSize;
                const end = Math.min(start + chunkSize, file.size);
                const chunk = file.slice(start, end);

                // Read chunk as ArrayBuffer
                const arrayBuffer = await this.readChunkAsArrayBuffer(chunk);
                const wordArray = CryptoJS.lib.WordArray.create(arrayBuffer);

                // Update each hasher with the chunk
                Object.values(hashers).forEach((hasher) =>
                    hasher.update(wordArray)
                );

                // Report progress if callback provided
                if (progressCallback) {
                    const progress = Math.round(
                        ((chunkIndex + 1) / chunks) * 100
                    );
                    progressCallback(progress, chunkIndex + 1, chunks);
                }
            }

            // Finalize hashes
            if (hashers.md5) {
                results.md5 = hashers.md5.finalize().toString();
            }
            if (hashers.sha256) {
                results.sha256 = hashers.sha256.finalize().toString();
            }
            if (hashers.sha1) {
                results.sha1 = hashers.sha1.finalize().toString();
            }

            return results;
        } catch (error) {
            console.error("Error calculating checksums:", error);
            throw new Error(`Checksum calculation failed: ${error.message}`);
        }
    }

    /**
     * Calculate SHA-256 checksum for a file (most common use case)
     * @param {File} file - The file to calculate checksum for
     * @param {Function} progressCallback - Optional callback for progress updates
     * @returns {Promise<string>} SHA-256 checksum as hex string
     */
    static async calculateSHA256(file, progressCallback = null) {
        const result = await this.calculateChecksums(
            file,
            ["sha256"],
            progressCallback
        );
        return result.sha256;
    }

    /**
     * Calculate MD5 checksum for a file
     * @param {File} file - The file to calculate checksum for
     * @param {Function} progressCallback - Optional callback for progress updates
     * @returns {Promise<string>} MD5 checksum as hex string
     */
    static async calculateMD5(file, progressCallback = null) {
        const result = await this.calculateChecksums(
            file,
            ["md5"],
            progressCallback
        );
        return result.md5;
    }

    /**
     * Calculate both MD5 and SHA-256 checksums
     * @param {File} file - The file to calculate checksums for
     * @param {Function} progressCallback - Optional callback for progress updates
     * @returns {Promise<Object>} Object with md5 and sha256 properties
     */
    static async calculateBothChecksums(file, progressCallback = null) {
        return await this.calculateChecksums(
            file,
            ["md5", "sha256"],
            progressCallback
        );
    }

    /**
     * Read a chunk of file as ArrayBuffer
     * @param {Blob} chunk - The file chunk to read
     * @returns {Promise<ArrayBuffer>} The chunk data as ArrayBuffer
     */
    static readChunkAsArrayBuffer(chunk) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => resolve(reader.result);
            reader.onerror = () => reject(reader.error);
            reader.readAsArrayBuffer(chunk);
        });
    }

    /**
     * Validate a file against expected checksums
     * @param {File} file - The file to validate
     * @param {Object} expectedChecksums - Object with expected checksums
     * @param {Function} progressCallback - Optional callback for progress updates
     * @returns {Promise<Object>} Validation results
     */
    static async validateFile(
        file,
        expectedChecksums,
        progressCallback = null
    ) {
        const algorithms = Object.keys(expectedChecksums);
        const calculatedChecksums = await this.calculateChecksums(
            file,
            algorithms,
            progressCallback
        );

        const results = {
            valid: true,
            details: {},
        };

        for (const algorithm of algorithms) {
            const calculated = calculatedChecksums[algorithm];
            const expected = expectedChecksums[algorithm];
            const matches = calculated === expected;

            results.details[algorithm] = {
                calculated,
                expected,
                matches,
            };

            if (!matches) {
                results.valid = false;
            }
        }

        return results;
    }

    /**
     * Format file size in human readable format
     * @param {number} bytes - File size in bytes
     * @returns {string} Formatted file size
     */
    static formatFileSize(bytes) {
        if (bytes === 0) return "0 Bytes";

        const k = 1024;
        const sizes = ["Bytes", "KB", "MB", "GB", "TB"];
        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
    }

    /**
     * Create a progress tracker for multiple files
     * @param {Array} files - Array of files
     * @param {Function} overallProgressCallback - Callback for overall progress
     * @returns {Function} Progress callback for individual files
     */
    static createMultiFileProgressTracker(files, overallProgressCallback) {
        const fileProgress = new Map();
        files.forEach((file) => fileProgress.set(file.name, 0));

        return (fileName, progress) => {
            fileProgress.set(fileName, progress);

            const totalProgress =
                Array.from(fileProgress.values()).reduce(
                    (sum, prog) => sum + prog,
                    0
                ) / files.length;

            if (overallProgressCallback) {
                overallProgressCallback(Math.round(totalProgress));
            }
        };
    }
}

export default ChecksumCalculator;
