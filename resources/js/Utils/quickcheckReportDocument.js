import OCL from "openchemlib";
import {
    ASSIGNMENT_RESULT_LABELS,
    ATOM_STATUS_LABELS,
    ROW_STATUS_LABELS,
    VERDICT_TEXT,
    issueText,
    nucleusLabel,
    reasonLabel,
    resultLabel,
} from "@/Utils/quickcheckTerms.js";

const SOURCE_LABELS = {
    nmrium: "NMRium assignments",
    mnova_sdf: "Mnova SDF export",
    nmredata: "NMReDATA file",
    manual: "Manual entry",
};

const VERDICTS = {
    accept: { tone: "good", ...VERDICT_TEXT.accept },
    review: { tone: "warn", ...VERDICT_TEXT.review },
    reject: { tone: "bad", ...VERDICT_TEXT.reject },
    not_assessable: { tone: "neutral", ...VERDICT_TEXT.not_assessable },
};

const RESULT_TONES = {
    accept: "good",
    revise: "warn",
    warning: "warn",
    reject: "bad",
};

const ATOM_STATUSES = {
    green: { tone: "good", label: ATOM_STATUS_LABELS.green },
    yellow: { tone: "warn", label: ATOM_STATUS_LABELS.yellow },
    red: { tone: "bad", label: ATOM_STATUS_LABELS.red },
    missing: { tone: "bad", label: ATOM_STATUS_LABELS.missing },
    impossible: { tone: "neutral", label: ATOM_STATUS_LABELS.impossible },
};

const ROW_STATUSES = {
    ok: { tone: "good", label: ROW_STATUS_LABELS.ok },
    review: { tone: "warn", label: ROW_STATUS_LABELS.review },
    fail: { tone: "bad", label: ROW_STATUS_LABELS.fail },
    not_assessable: {
        tone: "neutral",
        label: ROW_STATUS_LABELS.not_assessable,
    },
};

const ASSIGNMENT_RESULTS = {
    consistent: { tone: "good", label: ASSIGNMENT_RESULT_LABELS.consistent },
    review: { tone: "warn", label: ASSIGNMENT_RESULT_LABELS.review },
    inconsistent: { tone: "bad", label: ASSIGNMENT_RESULT_LABELS.inconsistent },
    not_assessable: {
        tone: "neutral",
        label: ASSIGNMENT_RESULT_LABELS.not_assessable,
    },
};

const STYLES = `
@page {
    size: A4;
    margin: 16mm 15mm 18mm;
    @bottom-left {
        content: "nmrXiv Assignment Quickcheck";
        font: 7.5pt/1.2 "Inter", "Helvetica Neue", Arial, sans-serif;
        color: #64748b;
    }
    @bottom-right {
        content: "Page " counter(page) " of " counter(pages);
        font: 7.5pt/1.2 "Inter", "Helvetica Neue", Arial, sans-serif;
        color: #64748b;
    }
}
* { box-sizing: border-box; }
html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
body {
    margin: 0;
    color: #0f172a;
    font: 9pt/1.45 "Inter", -apple-system, "Segoe UI", "Helvetica Neue", Arial, sans-serif;
    font-variant-numeric: tabular-nums;
}
h1, h2, h3, p, dl, dd, ul, figure { margin: 0; }
.masthead {
    display: flex; align-items: center; justify-content: space-between;
    padding-bottom: 10pt; border-bottom: 2pt solid #0e7490;
}
.masthead img { height: 22pt; }
.masthead-meta { text-align: right; font-size: 7.5pt; color: #475569; }
.masthead-meta strong { display: block; font-size: 9pt; color: #0f172a; letter-spacing: 0.02em; }
.title-block { padding: 14pt 0 12pt; }
.eyebrow { font-size: 7pt; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; color: #0e7490; }
h1 { margin-top: 3pt; font-size: 17pt; line-height: 1.2; font-weight: 700; word-break: break-word; }
.meta { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8pt 14pt; margin-top: 10pt; }
.meta dt, .props dt { font-size: 6.5pt; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; color: #64748b; }
.meta dd, .props dd { margin-top: 1pt; font-weight: 500; word-break: break-word; }
.summary {
    display: grid; grid-template-columns: 1.25fr repeat(3, minmax(0, 1fr)); gap: 0;
    border: 1pt solid #e2e8f0; border-radius: 6pt; overflow: hidden; break-inside: avoid;
}
.summary .verdict { padding: 11pt 12pt; border-left: 4pt solid var(--accent); background: var(--soft); }
.verdict .label { font-size: 6.5pt; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: var(--ink); }
.verdict .title { margin-top: 3pt; font-size: 11.5pt; line-height: 1.25; font-weight: 700; color: var(--ink); }
.verdict p { margin-top: 4pt; font-size: 8pt; color: #334155; }
.kpi { padding: 11pt 12pt; border-left: 1pt solid #e2e8f0; }
.kpi .label { font-size: 6.5pt; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: #64748b; }
.kpi .value { margin-top: 3pt; font-size: 20pt; line-height: 1; font-weight: 700; }
.kpi .value small { font-size: 9pt; font-weight: 500; color: #64748b; }
.kpi .value.text { font-size: 12pt; line-height: 1.2; }
.kpi .sub { margin-top: 5pt; font-size: 7.5pt; color: #475569; }
.tone-good { --accent: #059669; --soft: #ecfdf5; --ink: #065f46; }
.tone-warn { --accent: #d97706; --soft: #fffbeb; --ink: #92400e; }
.tone-bad { --accent: #dc2626; --soft: #fef2f2; --ink: #991b1b; }
.tone-neutral { --accent: #64748b; --soft: #f8fafc; --ink: #334155; }
.pill {
    display: inline-block; padding: 1pt 6pt; border-radius: 999pt;
    background: var(--soft); color: var(--ink); box-shadow: inset 0 0 0 0.75pt var(--accent);
    font-size: 7pt; font-weight: 600; white-space: nowrap;
}
section { margin-top: 16pt; }
h2 {
    display: flex; align-items: baseline; justify-content: space-between; gap: 8pt;
    padding-bottom: 4pt; border-bottom: 0.75pt solid #cbd5e1;
    font-size: 10.5pt; font-weight: 700; break-after: avoid;
}
h2 .aside { font-size: 7.5pt; font-weight: 500; color: #475569; }
.lead { margin-top: 6pt; font-size: 8pt; color: #475569; }
.findings { margin-top: 7pt; padding: 0; list-style: none; display: grid; gap: 4pt; }
.findings li {
    padding: 5pt 8pt; border-left: 3pt solid var(--accent); border-radius: 2pt;
    background: var(--soft); color: #1e293b; font-size: 8.5pt; break-inside: avoid;
}
.findings li strong { color: var(--ink); }
.structure { display: grid; grid-template-columns: 1.5fr 1fr; gap: 14pt; margin-top: 8pt; break-inside: avoid; }
.structure.single { grid-template-columns: 1fr; }
figure { border: 0.75pt solid #e2e8f0; border-radius: 6pt; padding: 6pt; }
figure img { display: block; width: 100%; height: auto; }
figcaption { margin-top: 4pt; padding-top: 4pt; border-top: 0.75pt solid #f1f5f9; font-size: 7.5pt; color: #475569; }
.props { display: grid; gap: 7pt; align-content: start; }
.props dd.mono { font-family: "JetBrains Mono", "SFMono-Regular", Menlo, Consolas, monospace; font-size: 7pt; font-weight: 400; word-break: break-all; }
.penalties { display: flex; flex-wrap: wrap; gap: 4pt 14pt; margin-top: 6pt; font-size: 7.5pt; color: #475569; }
.penalties b { color: #0f172a; font-weight: 600; }
table { width: 100%; margin-top: 7pt; border-collapse: collapse; font-size: 8pt; }
thead { display: table-header-group; }
th {
    padding: 4pt 6pt; border-bottom: 0.75pt solid #94a3b8; background: #f8fafc;
    text-align: left; font-size: 7pt; font-weight: 600; color: #475569; white-space: nowrap;
}
.details { break-before: page; margin-top: 0; }
td { padding: 3.5pt 6pt; border-bottom: 0.5pt solid #e2e8f0; vertical-align: top; }
tr { break-inside: avoid; }
.num { text-align: right; white-space: nowrap; }
.atom { font-weight: 600; white-space: nowrap; }
.hose { width: 34%; font-family: "JetBrains Mono", "SFMono-Regular", Menlo, Consolas, monospace; font-size: 6.5pt; color: #64748b; word-break: break-all; }
.reason { display: block; margin-top: 1pt; font-size: 6.5pt; color: #64748b; }
tr.flag-warn td:first-child { box-shadow: inset 2.5pt 0 0 #d97706; }
tr.flag-bad td:first-child { box-shadow: inset 2.5pt 0 0 #dc2626; }
.note { margin-top: 5pt; font-size: 7pt; color: #64748b; }
.methods { break-inside: avoid; }
.methods p { margin-top: 5pt; font-size: 8pt; color: #334155; text-align: justify; hyphens: auto; }
.methods a { color: #0e7490; }
.colophon {
    margin-top: 16pt; padding-top: 7pt; border-top: 0.75pt solid #cbd5e1;
    display: flex; justify-content: space-between; gap: 10pt; font-size: 7pt; color: #64748b;
}
`;

function escapeHtml(value) {
    return String(value ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");
}

function cssString(value) {
    return `"${String(value).replace(/["\\\n\r]/g, " ")}"`;
}

function environmentMatch(spheres) {
    return spheres ? `${spheres} bonds` : "—";
}

function formatShift(value, nucleus) {
    if (value === null || value === undefined) {
        return "—";
    }
    return Number(value).toFixed(nucleus === "1H" ? 2 : 1);
}

function formatSigned(value, nucleus) {
    if (value === null || value === undefined) {
        return "—";
    }
    const text = formatShift(Math.abs(value), nucleus);
    if (Number(text) === 0) {
        return text;
    }
    return value < 0 ? `−${text}` : `+${text}`;
}

function pill(status) {
    const label = String(status.label ?? "");
    return `<span class="pill tone-${status.tone}">${escapeHtml(
        label.charAt(0).toUpperCase() + label.slice(1)
    )}</span>`;
}

function formulaHtml(formula) {
    return escapeHtml(formula).replace(/(\d+)/g, "<sub>$1</sub>");
}

/**
 * Formula, weights and SMILES of the submitted structure.
 *
 * @param {string} molfile
 * @returns {{ formula: string, weight: string, exactMass: string, smiles: string }|null}
 */
export function compoundProperties(molfile) {
    if (!molfile) {
        return null;
    }
    try {
        const molecule = OCL.Molecule.fromMolfile(molfile);
        molecule.removeAtomCustomLabels();
        const formula = molecule.getMolecularFormula();
        return {
            formula: formula.formula,
            weight: formula.relativeWeight.toFixed(2),
            exactMass: formula.absoluteWeight.toFixed(4),
            smiles: molecule.toIsomericSmiles(),
        };
    } catch {
        return null;
    }
}

function findings(report) {
    const check = report.assignment_check || {};
    const items = [];

    for (const suggestion of check.suggestions || []) {
        items.push({
            tone: "bad",
            html: `<strong>${escapeHtml(
                suggestion.labels?.[0]
            )} and ${escapeHtml(
                suggestion.labels?.[1]
            )} may be swapped.</strong> Exchanging the two assignments brings the shifts ${formatShift(
                suggestion.error_reduction,
                suggestion.nucleus
            )} ppm closer to the prediction.`,
        });
    }

    const flagged = (check.rows || []).filter((row) =>
        ["fail", "review"].includes(row.status)
    );
    for (const status of ["fail", "review"]) {
        const rows = flagged.filter((row) => row.status === status);
        if (rows.length) {
            items.push({
                tone: status === "fail" ? "bad" : "warn",
                html: `<strong>${rows.length} assignment${
                    rows.length === 1 ? "" : "s"
                } ${
                    status === "fail"
                        ? `${
                              rows.length === 1 ? "does" : "do"
                          } not match the prediction`
                        : `${rows.length === 1 ? "needs" : "need"} checking`
                }:</strong> ${rows
                    .map(
                        (row) =>
                            `${escapeHtml(row.label)} (${nucleusLabel(
                                row.nucleus
                            )}, Δδ ${formatSigned(row.delta, row.nucleus)} ppm)`
                    )
                    .join(", ")}.`,
            });
        }
    }

    for (const issue of check.issues || []) {
        items.push({
            tone: "warn",
            html: `<strong>${nucleusLabel(
                issue.nucleus
            )}:</strong> ${escapeHtml(issueText(issue))}${
                issue.labels?.length
                    ? ` (${escapeHtml(issue.labels.join(", "))})`
                    : ""
            }`,
        });
    }

    if (check.offset?.suspected_referencing_error) {
        const offsets = ["13C", "1H"]
            .filter((nucleus) => typeof check.offset[nucleus] === "number")
            .map(
                (nucleus) =>
                    `${nucleusLabel(nucleus)} ${formatSigned(
                        check.offset[nucleus],
                        nucleus
                    )} ppm`
            )
            .join(", ");
        items.push({
            tone: "warn",
            html: `<strong>Possible referencing error.</strong> All shifts differ from the prediction by about the same amount (${escapeHtml(
                offsets
            )}). Check the shift referencing (TMS or residual solvent signal).`,
        });
    }

    const nuclei = Object.keys(report.reports || {});
    if (nuclei.some((nucleus) => report.reports[nucleus].in_database_likely)) {
        items.push({
            tone: "neutral",
            html: "<strong>Compound probably already in nmrshiftdb2.</strong> Almost every atom matches a reference environment six bonds deep with a very small shift difference, so a good score here is expected and does not independently confirm the assignments.",
        });
    }

    if (!items.some((item) => item.tone === "bad" || item.tone === "warn")) {
        items.unshift({
            tone: "good",
            html: "<strong>No problems found.</strong> Every compared shift matches the prediction within the expected range.",
        });
    }

    return items;
}

function nucleusSection(report, nucleus) {
    const data = report.reports[nucleus];
    const stats = data.statistics || {};
    const penalties = data.penalties || {};
    const resultTone = RESULT_TONES[data.result] || "neutral";

    const rows = (data.atoms || [])
        .map((row) => {
            const status = ATOM_STATUSES[row.status] || {
                tone: "neutral",
                label: row.status,
            };
            const flag =
                status.tone === "bad" || status.tone === "warn"
                    ? ` class="flag-${status.tone}"`
                    : "";
            return `<tr${flag}>
                <td class="atom">${escapeHtml(row.label)}</td>
                <td class="num">${formatShift(row.observed, nucleus)}</td>
                <td class="num">${formatShift(row.predicted, nucleus)}</td>
                <td class="num">${formatShift(row.deviation, nucleus)}</td>
                <td class="num">${environmentMatch(row.spheres)}</td>
                <td>${pill(status)}</td>
                <td class="hose">${escapeHtml(row.hose_code || "—")}</td>
            </tr>`;
        })
        .join("");

    return `<section>
        <h2>${nucleusLabel(nucleus)} shift comparison
            <span class="aside">Score <b>${escapeHtml(
                data.mark
            )}/10</b> · ${pill({
        tone: resultTone,
        label: resultLabel(data.result),
    })}</span>
        </h2>
        <div class="penalties">
            <span>Mean |Δδ| <b>${escapeHtml(
                penalties.mean_deviation?.ppm
            )} ppm</b></span>
            <span>Not matching or not assigned <b>${escapeHtml(
                penalties.red_or_missing?.count
            )}</b></span>
            <span>Borderline <b>${escapeHtml(
                penalties.yellow?.count
            )}</b></span>
            <span>Matching shifts <b>${escapeHtml(
                stats.accept ?? "—"
            )} of ${escapeHtml(stats.total ?? "—")}</b></span>
        </div>
        <table>
            <thead><tr>
                <th>Atom</th><th class="num">δ obs. (ppm)</th><th class="num">δ pred. (ppm)</th>
                <th class="num">|Δδ| (ppm)</th><th class="num">Env. match</th><th>Status</th><th>HOSE code</th>
            </tr></thead>
            <tbody>${rows}</tbody>
        </table>
    </section>`;
}

function assignmentSection(report) {
    const check = report.assignment_check || {};
    const result =
        ASSIGNMENT_RESULTS[check.result] || ASSIGNMENT_RESULTS.not_assessable;

    const rows = (check.rows || [])
        .map((row) => {
            const status = ROW_STATUSES[row.status] || {
                tone: "neutral",
                label: row.status,
            };
            const flag =
                status.tone === "bad" || status.tone === "warn"
                    ? ` class="flag-${status.tone}"`
                    : "";
            const reasons = (row.reasons || [])
                .map(
                    (reason) =>
                        `<span class="reason">${escapeHtml(
                            reasonLabel(reason)
                        )}</span>`
                )
                .join("");
            return `<tr${flag}>
                <td class="atom">${escapeHtml(row.label)}</td>
                <td>${nucleusLabel(row.nucleus)}</td>
                <td class="num">${formatShift(row.observed, row.nucleus)}</td>
                <td class="num">${formatShift(row.predicted, row.nucleus)}</td>
                <td class="num">${formatSigned(row.delta, row.nucleus)}</td>
                <td class="num">${environmentMatch(row.spheres)}</td>
                <td>${pill(status)}${reasons}</td>
            </tr>`;
        })
        .join("");

    return `<section>
        <h2>Assignment check <span class="aside">${pill(result)}</span></h2>
        <p class="lead">Each assigned signal is compared with the predicted shift of its atoms. Δδ = δ observed − δ predicted.</p>
        <table>
            <thead><tr>
                <th>Assignment</th><th>Nucleus</th><th class="num">δ obs. (ppm)</th><th class="num">δ pred. (ppm)</th>
                <th class="num">Δδ (ppm)</th><th class="num">Env. match</th><th>Status</th>
            </tr></thead>
            <tbody>${rows}</tbody>
        </table>
        ${
            report.adjustments?.length
                ? `<p class="note">${report.adjustments.length} identical shift(s) on non-equivalent atoms were offset by 0.001 ppm so that each is compared as a separate signal.</p>`
                : ""
        }
    </section>`;
}

function kpis(report) {
    const tiles = ["13C", "1H"]
        .filter((nucleus) => report.reports?.[nucleus])
        .map((nucleus) => {
            const data = report.reports[nucleus];
            const stats = data.statistics || {};
            return `<div class="kpi">
                <div class="label">${nucleusLabel(nucleus)} score</div>
                <div class="value">${escapeHtml(
                    data.mark
                )}<small> / 10</small></div>
                <div class="sub">${pill({
                    tone: RESULT_TONES[data.result] || "neutral",
                    label: resultLabel(data.result),
                })} ${escapeHtml(stats.accept ?? 0)} of ${escapeHtml(
                stats.total ?? 0
            )} shifts match</div>
            </div>`;
        });

    const check = report.assignment_check || {};
    const rows = check.rows || [];
    const result =
        ASSIGNMENT_RESULTS[check.result] || ASSIGNMENT_RESULTS.not_assessable;
    tiles.push(`<div class="kpi">
        <div class="label">Assignment check</div>
        <div class="value text">${escapeHtml(result.label)}</div>
        <div class="sub">${
            rows.filter((row) => row.status === "ok").length
        } of ${rows.length} assignments match${
        check.suggestions?.length
            ? ` · ${check.suggestions.length} possible swap${
                  check.suggestions.length === 1 ? "" : "s"
              }`
            : ""
    }</div>
    </div>`);

    while (tiles.length < 3) {
        tiles.unshift('<div class="kpi"></div>');
    }
    return tiles.join("");
}

/**
 * Report document details of a stored study validation.
 *
 * @param {object|null} validation AssignmentValidation summary
 * @param {{ title?: string, doi?: string }} [study]
 * @returns {{ title?: string, source?: string, items: { label: string, value: string }[] }}
 */
export function quickcheckDetails(validation, { title = "", doi = "" } = {}) {
    const date = (value) =>
        value
            ? new Date(value).toLocaleDateString("en-GB", {
                  dateStyle: "long",
              })
            : "";
    const items = [];
    if (doi) {
        items.push({ label: "DOI", value: doi });
    }
    if (validation?.completed_at) {
        items.push({ label: "Checked", value: date(validation.completed_at) });
    }
    if (validation?.confirmed) {
        items.push({
            label: "Author confirmation",
            value: `Confirmed${
                validation.confirmed_by ? ` by ${validation.confirmed_by}` : ""
            } on ${date(validation.confirmed_at)}`,
        });
    }
    if (validation?.stale) {
        items.push({
            label: "Status",
            value: "Out of date: assignments changed since the check",
        });
    }

    return {
        title: title || undefined,
        source: validation?.source,
        items,
    };
}

/**
 * Standalone, print-ready HTML document of a Quickcheck report.
 *
 * @param {object} options
 * @param {object} options.report NMRKit validation report
 * @param {string} [options.molfile]
 * @param {string} [options.structureUrl] depiction with the author's labels
 * @param {boolean} [options.hasFlaggedAtoms]
 * @param {{ title?: string, items?: { label: string, value: string }[] }} [options.details]
 * @param {string} [options.logoUrl]
 * @param {Date} [options.generatedAt]
 * @returns {string}
 */
export function quickcheckReportHtml({
    report,
    molfile = "",
    structureUrl = "",
    hasFlaggedAtoms = false,
    details = {},
    logoUrl = "",
    generatedAt = new Date(),
}) {
    const verdict = VERDICTS[report.verdict] || VERDICTS.not_assessable;
    const title = details.title || "Assignment Quickcheck";
    const generated = generatedAt.toLocaleString("en-GB", {
        dateStyle: "long",
        timeStyle: "short",
    });
    const compound = compoundProperties(molfile);
    const source = SOURCE_LABELS[details.source] || details.source;

    const meta = [
        ...(details.items || []),
        ...(source ? [{ label: "Input", value: source }] : []),
        { label: "Solvent", value: report.solvent || "—" },
        { label: "Prediction", value: "nmrshiftdb2 (HOSE codes)" },
    ]
        .slice(0, 8)
        .map(
            (item) =>
                `<div><dt>${escapeHtml(item.label)}</dt><dd>${escapeHtml(
                    item.value
                )}</dd></div>`
        )
        .join("");

    const assignedCount = (nucleus) =>
        (report.reports?.[nucleus]?.atoms || []).filter(
            (row) => row.observed !== null && row.observed !== undefined
        ).length;
    const props = compound
        ? `<dl class="props">
            <div><dt>Molecular formula</dt><dd>${formulaHtml(
                compound.formula
            )}</dd></div>
            <div><dt>Molecular weight</dt><dd>${
                compound.weight
            } g/mol</dd></div>
            <div><dt>Monoisotopic mass</dt><dd>${
                compound.exactMass
            } Da</dd></div>
            <div><dt>Assigned signals</dt><dd>${["13C", "1H"]
                .filter((nucleus) => report.reports?.[nucleus])
                .map(
                    (nucleus) =>
                        `${assignedCount(nucleus)} ${nucleusLabel(nucleus)}`
                )
                .join(" · ")}</dd></div>
            <div><dt>SMILES</dt><dd class="mono">${escapeHtml(
                compound.smiles
            )}</dd></div>
        </dl>`
        : "";

    const structure =
        structureUrl || props
            ? `<section>
            <h2>Structure</h2>
            <div class="structure${structureUrl && props ? "" : " single"}">
                ${
                    structureUrl
                        ? `<figure>
                    <img src="${escapeHtml(
                        structureUrl
                    )}" alt="Structure with the author's atom labels">
                    <figcaption>Submitted structure with the author's carbon and proton labels.${
                        hasFlaggedAtoms
                            ? " Highlighted atoms need checking or do not match the prediction."
                            : ""
                    }</figcaption>
                </figure>`
                        : ""
                }
                ${props}
            </div>
        </section>`
            : "";

    const findingItems = findings(report)
        .map((item) => `<li class="tone-${item.tone}">${item.html}</li>`)
        .join("");

    const engine = report.engine || {};

    return `<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>${escapeHtml(title)} – nmrXiv Quickcheck</title>
<style>${STYLES}
@page { @bottom-left { content: ${cssString(
        `nmrXiv Assignment Quickcheck · ${title}`
    )}; } }
</style>
</head>
<body>
<header class="masthead">
    ${
        logoUrl
            ? `<img src="${escapeHtml(logoUrl)}" alt="nmrXiv">`
            : "<strong>nmrXiv</strong>"
    }
    <div class="masthead-meta">
        <strong>Assignment Quickcheck report</strong>
        Generated ${escapeHtml(generated)}
    </div>
</header>

<div class="title-block">
    <div class="eyebrow">NMR assignment validation</div>
    <h1>${escapeHtml(title)}</h1>
    <dl class="meta">${meta}</dl>
</div>

<div class="summary">
    <div class="verdict tone-${verdict.tone}">
        <div class="label">Overall result</div>
        <div class="title">${escapeHtml(verdict.title)}</div>
        <p>${escapeHtml(verdict.hint)}</p>
    </div>
    ${kpis(report)}
</div>

<section>
    <h2>Key findings</h2>
    <ul class="findings">${findingItems}</ul>
</section>

${structure}

<div class="details">
${["13C", "1H"]
    .filter((nucleus) => report.reports?.[nucleus])
    .map((nucleus) => nucleusSection(report, nucleus))
    .join("")}

${assignmentSection(report)}
</div>

<section class="methods">
    <h2>Method and interpretation</h2>
    <p>Each assigned shift is compared with the shift predicted by <a href="https://nmrshiftdb.nmr.uni-koeln.de/">nmrshiftdb2</a> in ${escapeHtml(
        report.solvent || "the given solvent"
    )}. The prediction uses HOSE codes: it looks up reference atoms whose environment matches up to six bonds around the atom ("Env. match"). The more bonds match, the more reliable the prediction.</p>
    <p>Each nucleus gets a score out of 10. Points are taken off for the mean shift difference, for shifts that do not match or are not assigned, and for borderline shifts. The assignment check then looks at each assigned signal and reports pairs of assignments that fit the prediction better when swapped. Predictions matching fewer than four bonds are too uncertain to reject an assignment; they can only ask for it to be checked.</p>
    <p>The prediction is a guide, not proof. A poor match points to assignments worth checking, ideally with HSQC and HMBC; a good match does not prove the structure. Atom labels are the author's, and atom numbers refer to the submitted structure.</p>
</section>

<div class="colophon">
    <span>Generated with nmrXiv Assignment Quickcheck · validation by NMRKit${
        engine.source ? ` using ${escapeHtml(engine.source)}` : ""
    }</span>
    <span>${escapeHtml(generated)}</span>
</div>
</body>
</html>`;
}

/**
 * Prints the report document from a hidden frame, so the PDF holds only the
 * report and not the surrounding page.
 *
 * @param {Parameters<typeof quickcheckReportHtml>[0]} options
 * @returns {Promise<void>}
 */
export function printQuickcheckReport(options) {
    const html = quickcheckReportHtml(options);
    const frame = document.createElement("iframe");
    frame.setAttribute("aria-hidden", "true");
    frame.style.cssText =
        "position:fixed;right:0;bottom:0;width:0;height:0;border:0;visibility:hidden";

    return new Promise((resolve) => {
        let printed = false;
        const print = () => {
            if (printed) {
                return;
            }
            printed = true;
            const pageTitle = document.title;
            document.title = frame.contentDocument?.title || pageTitle;
            try {
                frame.contentWindow.focus();
                frame.contentWindow.print();
            } finally {
                document.title = pageTitle;
                setTimeout(() => frame.remove(), 1000);
                resolve();
            }
        };

        frame.addEventListener("load", print, { once: true });
        setTimeout(print, 10000);
        frame.srcdoc = html;
        document.body.appendChild(frame);
    });
}
