/**
 * Deterministic cover art styles from a seed string (project identifier, name, or id).
 */

export function hashString(input) {
    if (!input) {
        return 0;
    }

    let hash = 0;
    const len = input.length;

    for (let i = 0; i < len; i++) {
        hash = (hash << 5) - hash + input.charCodeAt(i);
        hash |= 0;
    }

    return Math.abs(hash);
}

function pick(hash, index, min, max) {
    const range = max - min + 1;

    return min + (((hash >> (index * 4)) & 0xffff) % range);
}

function hsl(hue, saturation, lightness) {
    return `hsl(${hue}, ${saturation}%, ${lightness}%)`;
}

/** Spread nearby hash values across the hue wheel. */
function spreadHash(value) {
    let hash = value >>> 0;

    hash ^= hash >>> 16;
    hash = Math.imul(hash, 0x7feb352d) >>> 0;
    hash ^= hash >>> 15;
    hash = Math.imul(hash, 0x846ca68b) >>> 0;
    hash ^= hash >>> 16;

    return hash >>> 0;
}

/**
 * Derive three independent hues so similar identifiers still diverge.
 *
 * @returns {{ hue1: number, hue2: number, hue3: number }}
 */
function huesFromSeed(seed) {
    const full = spreadHash(hashString(seed));
    const tail = spreadHash(hashString(seed.slice(-8) || seed));
    const mixed = spreadHash(full ^ (tail << 1) ^ (tail >>> 3));

    return {
        hue1: mixed % 360,
        hue2: spreadHash(mixed ^ 0x9e3779b9) % 360,
        hue3: spreadHash(mixed ^ 0x85ebca6b) % 360,
    };
}

/**
 * @param {object|string|null} projectOrSeed
 * @returns {string}
 */
export function seededCoverSeed(projectOrSeed) {
    if (projectOrSeed == null || projectOrSeed === "") {
        return "nmrxiv";
    }

    if (typeof projectOrSeed === "string") {
        return projectOrSeed;
    }

    return String(
        projectOrSeed.identifier ||
            projectOrSeed.name ||
            projectOrSeed.id ||
            "nmrxiv"
    );
}

/**
 * @param {object|string|null} projectOrSeed
 * @returns {{ baseGradient: string, blobs: Array<object>, overlay: string, overlaySize: string }}
 */
export function seededCoverStyle(projectOrSeed) {
    const seed = seededCoverSeed(projectOrSeed);
    const hash = hashString(seed);
    const { hue1, hue2, hue3 } = huesFromSeed(seed);
    const dotSize = 8 + (hash % 5);

    return {
        baseGradient: `linear-gradient(135deg, ${hsl(hue1, 52, 94)} 0%, ${hsl(
            hue2,
            48,
            90
        )} 45%, ${hsl(hue3, 50, 92)} 100%)`,
        blobs: [
            {
                left: `${pick(hash, 0, 5, 65)}%`,
                top: `${pick(hash, 1, -15, 35)}%`,
                width: `${pick(hash, 2, 55, 85)}%`,
                height: `${pick(hash, 3, 55, 85)}%`,
                background: hsl(hue1, 65, 72),
                opacity: 0.45,
            },
            {
                left: `${pick(hash, 4, 25, 75)}%`,
                top: `${pick(hash, 5, 15, 55)}%`,
                width: `${pick(hash, 6, 45, 70)}%`,
                height: `${pick(hash, 7, 45, 70)}%`,
                background: hsl(hue2, 60, 68),
                opacity: 0.35,
            },
            {
                left: `${pick(hash, 8, 0, 50)}%`,
                top: `${pick(hash, 9, 40, 80)}%`,
                width: `${pick(hash, 10, 40, 65)}%`,
                height: `${pick(hash, 11, 40, 65)}%`,
                background: hsl(hue3, 58, 75),
                opacity: 0.3,
            },
        ],
        overlay:
            "radial-gradient(circle at 1px 1px, rgba(255,255,255,0.35) 1px, transparent 0)",
        overlaySize: `${dotSize}px ${dotSize}px`,
    };
}
