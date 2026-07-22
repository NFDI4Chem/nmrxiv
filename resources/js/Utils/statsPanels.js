import {
    groupsWithDistributionData,
    hasDistributionData,
    panelsWithDistributionData,
} from "@/Utils/statsChart";

export {
    hasDistributionData,
    panelsWithDistributionData,
    groupsWithDistributionData,
};

export function buildPiePanels(distributions, missing, panelDefinitions) {
    return panelsWithDistributionData(
        panelDefinitions.map((panel) => ({
            ...panel,
            rows: distributions[panel.key] ?? [],
            missing: missing[panel.key] ?? 0,
        }))
    );
}
