function polarToCartesian(cx, cy, radius, angleInRadians) {
    return {
        x: cx + radius * Math.cos(angleInRadians),
        y: cy + radius * Math.sin(angleInRadians),
    };
}

function describeDonutSliceArc(cx, cy, innerR, outerR, startAngle, endAngle) {
    const outerStart = polarToCartesian(cx, cy, outerR, startAngle);
    const outerEnd = polarToCartesian(cx, cy, outerR, endAngle);
    const innerEnd = polarToCartesian(cx, cy, innerR, endAngle);
    const innerStart = polarToCartesian(cx, cy, innerR, startAngle);
    const largeArc = endAngle - startAngle > Math.PI ? 1 : 0;

    return [
        `M ${outerStart.x.toFixed(2)} ${outerStart.y.toFixed(2)}`,
        `A ${outerR} ${outerR} 0 ${largeArc} 1 ${outerEnd.x.toFixed(
            2
        )} ${outerEnd.y.toFixed(2)}`,
        `L ${innerEnd.x.toFixed(2)} ${innerEnd.y.toFixed(2)}`,
        `A ${innerR} ${innerR} 0 ${largeArc} 0 ${innerStart.x.toFixed(
            2
        )} ${innerStart.y.toFixed(2)}`,
        "Z",
    ].join(" ");
}

/**
 * SVG arcs cannot represent a full 360° sweep in one command.
 */
export function isFullCircleSpan(startAngle, endAngle) {
    return endAngle - startAngle >= Math.PI * 2 - 1e-6;
}

export function describeDonutSlice(
    cx,
    cy,
    innerR,
    outerR,
    startAngle,
    endAngle
) {
    if (isFullCircleSpan(startAngle, endAngle)) {
        const midAngle = startAngle + Math.PI;

        return [
            describeDonutSliceArc(cx, cy, innerR, outerR, startAngle, midAngle),
            describeDonutSliceArc(
                cx,
                cy,
                innerR,
                outerR,
                midAngle,
                startAngle + Math.PI * 2 - 1e-5
            ),
        ].join(" ");
    }

    return describeDonutSliceArc(cx, cy, innerR, outerR, startAngle, endAngle);
}

export function donutRingRadius(innerR, outerR) {
    return (innerR + outerR) / 2;
}

export function donutRingStrokeWidth(innerR, outerR) {
    return outerR - innerR;
}

export function mixChartColorWithWhite(hex, ratio) {
    const normalized = hex.replace("#", "");
    const channels = [
        parseInt(normalized.slice(0, 2), 16),
        parseInt(normalized.slice(2, 4), 16),
        parseInt(normalized.slice(4, 6), 16),
    ];

    const mixed = channels.map((channel) =>
        Math.round(channel + (255 - channel) * ratio)
    );

    return `rgb(${mixed.join(", ")})`;
}
