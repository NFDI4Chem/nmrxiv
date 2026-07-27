/**
 * Modern categorical palette for data visualizations.
 * Colors are ordered for maximum contrast between adjacent slices.
 */
export const CHART_COLORS = [
    "#5B5FEF",
    "#00C9A7",
    "#FF6B6B",
    "#FFB347",
    "#845EF7",
    "#339AF0",
    "#F06595",
    "#51CF66",
    "#22B8CF",
    "#FD7E14",
    "#94D82D",
    "#748FFC",
];

/** Warm palette for dimension / experiment sunbursts. */
export const EXPERIMENT_SUNBURST_COLORS = [
    "#F97316",
    "#E11D48",
    "#D946EF",
    "#F59E0B",
    "#EF4444",
    "#FB7185",
    "#F43F5E",
    "#EA580C",
];

/** Cool palette for nucleus / frequency sunbursts. */
export const FREQUENCY_SUNBURST_COLORS = [
    "#0EA5E9",
    "#06B6D4",
    "#14B8A6",
    "#3B82F6",
    "#0891B2",
    "#2563EB",
    "#2DD4BF",
    "#38BDF8",
];

export function chartColorForIndex(index, palette = CHART_COLORS) {
    return palette[index % palette.length];
}
