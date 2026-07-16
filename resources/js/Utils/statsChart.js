import { router } from "@inertiajs/vue3";

export function formatStatsNumber(value) {
    return new Intl.NumberFormat().format(value);
}

export function formatStatsPercent(value) {
    return `${value.toFixed(1)}%`;
}

export function chartSliceInteractionProps(searchHref, ariaLabel) {
    return {
        class: searchHref
            ? "cursor-pointer transition-opacity hover:opacity-90"
            : "",
        role: searchHref ? "link" : undefined,
        tabindex: searchHref ? 0 : undefined,
        "aria-label": searchHref ? ariaLabel : undefined,
    };
}

export function visitStatsSearch(href) {
    if (href) {
        router.visit(href);
    }
}

export function hasDistributionData(rows = []) {
    return rows.some((row) => row.count > 0);
}

export function panelsWithDistributionData(panels) {
    return panels.filter((panel) => hasDistributionData(panel.rows));
}

export function groupsWithDistributionData(groups = []) {
    return groups.some((group) => group.count > 0);
}
