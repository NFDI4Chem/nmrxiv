/**
 * Normalize an entity identifier for interaction-tracking routes.
 *
 * @param {{ identifier?: string } | null | undefined} entity
 * @returns {string | null}
 */
export function trackingIdentifier(entity) {
    if (!entity?.identifier) {
        return null;
    }

    return String(entity.identifier).replace(/^NMRXIV:/i, "");
}
