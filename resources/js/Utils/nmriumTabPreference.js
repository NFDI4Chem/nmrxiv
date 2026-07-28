export const STORAGE_KEY = "nmrxiv-default-spectrum-tab";

/**
 * @param {string|null|undefined} tab
 * @param {string[]} allowedTabs
 * @returns {boolean}
 */
export function isAllowedSpectrumTab(tab, allowedTabs) {
    return typeof tab === "string" && allowedTabs.includes(tab);
}

/**
 * @param {{ props?: object }} page
 * @returns {string[]}
 */
export function allAllowedSpectrumTabs(page) {
    const oneD = page?.props?.defaultSpectrumTabs1D ?? [];
    const twoD = page?.props?.defaultSpectrumTabs2D ?? [];

    return [
        ...new Set([
            ...oneD,
            ...twoD,
            ...(page?.props?.defaultSpectrumTabs ?? []),
        ]),
    ];
}

/**
 * @param {string} tab
 * @param {{ props?: object }} page
 * @returns {''|'1d'|'2d'}
 */
export function dimensionForTab(tab, page) {
    if (!tab) {
        return "";
    }

    if (page?.props?.defaultSpectrumTabs1D?.includes(tab)) {
        return "1d";
    }

    if (page?.props?.defaultSpectrumTabs2D?.includes(tab)) {
        return "2d";
    }

    return "";
}

/**
 * @param {string[]} allowedTabs
 * @returns {string|null}
 */
export function readStoredSpectrumTab(allowedTabs) {
    try {
        const stored = localStorage.getItem(STORAGE_KEY);

        return isAllowedSpectrumTab(stored, allowedTabs) ? stored : null;
    } catch {
        return null;
    }
}

/**
 * @param {string|null} tab
 */
export function writeStoredSpectrumTab(tab) {
    try {
        if (!tab) {
            localStorage.removeItem(STORAGE_KEY);
        } else {
            localStorage.setItem(STORAGE_KEY, tab);
        }
    } catch {
        // localStorage may be unavailable in private browsing or embedded contexts.
    }
}

/**
 * @param {{ props?: { auth?: { user?: object }, defaultSpectrumTabs?: string[] } }} page
 * @returns {string|null}
 */
export function getDefaultSpectrumTab(page) {
    const allowedTabs = allAllowedSpectrumTabs(page);
    const user = page?.props?.auth?.user;

    if (user?.preferences?.default_spectrum_tab) {
        const serverTab = user.preferences.default_spectrum_tab;

        if (isAllowedSpectrumTab(serverTab, allowedTabs)) {
            return serverTab;
        }
    }

    if (!user) {
        return readStoredSpectrumTab(allowedTabs);
    }

    return null;
}

/**
 * @param {{ props?: object }} page
 * @returns {{ dimension: ''|'1d'|'2d', tab: string }}
 */
export function initialSpectrumPreference(page) {
    const tab = getDefaultSpectrumTab(page) ?? "";

    return {
        dimension: tab ? dimensionForTab(tab, page) : "",
        tab,
    };
}

/**
 * @param {string|undefined} nmriumURL
 * @returns {string}
 */
export function resolveNmriumTargetOrigin(nmriumURL) {
    try {
        const raw = nmriumURL || "https://nmriumdev.nmrxiv.org";
        const href = raw.startsWith("//") ? `https:${raw}` : raw;
        const withoutSession = href.split("&id=")[0].split("?id=")[0];

        return new URL(withoutSession).origin;
    } catch {
        return "https://nmriumdev.nmrxiv.org";
    }
}

/**
 * @param {object} payload
 * @param {string|null} tab
 * @returns {object}
 */
export function withActiveTab(payload, tab) {
    if (!tab) {
        return payload;
    }

    return {
        ...payload,
        activeTab: tab,
    };
}

/**
 * @param {Window} iframe
 * @param {object} payload
 * @param {string} targetOrigin
 * @param {string|null} tab
 */
export function postNmriumLoad(iframe, payload, targetOrigin, tab) {
    if (!iframe?.postMessage) {
        return;
    }

    const data = withActiveTab(payload, tab);

    iframe.postMessage({ type: "nmr-wrapper:load", data }, targetOrigin);
}

/**
 * @param {Window} iframe
 * @param {string|null} tab
 * @param {string} targetOrigin
 */
export function requestSelectTab(iframe, tab, targetOrigin) {
    if (!iframe?.postMessage || !tab) {
        return;
    }

    iframe.postMessage(
        {
            type: "nmr-wrapper:action-request",
            data: {
                type: "selectTab",
                params: { tab },
            },
        },
        targetOrigin
    );
}

/**
 * @param {{ props?: object }} page
 * @returns {string}
 */
export function initialSpectrumTabValue(page) {
    const tab = getDefaultSpectrumTab(page);

    return tab ?? "";
}

/**
 * @param {''|'1d'|'2d'} dimension
 * @param {string} tab
 * @returns {string|null}
 */
export function preferenceTabFromSelection(dimension, tab) {
    if (!dimension) {
        return null;
    }

    return tab || null;
}
