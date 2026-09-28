/**
 * The single-file build is opened with file://. A crossorigin module script
 * makes Safari try to fetch this same HTML file as a unique file origin.
 *
 * @param {string} html
 * @returns {string}
 */
export function prepareOfflineHtml(html) {
    return html
        .replace(
            /<script type="module" crossorigin>/g,
            '<script type="module">',
        )
        .replace(/<style\b[^>]*>/, '<style>');
}
