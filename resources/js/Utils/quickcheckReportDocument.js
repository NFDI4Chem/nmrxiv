import OCL from "openchemlib";

const NUCLEUS_LABELS = { "13C": "¹³C", "1H": "¹H" };

const SOURCE_LABELS = {
    nmrium: "NMRium assignments",
    mnova_sdf: "Mnova SDF export",
    nmredata: "NMReDATA file",
    manual: "Manual entry",
};

const VERDICTS = {
    accept: {
        tone: "good",
        title: "Assignments fit the predicted spectra",
        hint: "The shift list and the atom assignments agree with the nmrshiftdb2 prediction.",
    },
    review: {
        tone: "warn",
        title: "Some assignments need a second look",
        hint: "Check the flagged atoms, ideally against 2D correlations (HSQC, HMBC).",
    },
    reject: {
        tone: "bad",
        title: "Assignments disagree with the predicted spectra",
        hint: "A wrong structure, misassigned atoms or a referencing problem are the common causes.",
    },
    not_assessable: {
        tone: "neutral",
        title: "Assignments could not be assessed",
        hint: "No atom could be compared with a prediction.",
    },
};

const RESULT_TONES = {
    accept: "good",
    revise: "warn",
    warning: "warn",
    reject: "bad",
};

const ATOM_STATUSES = {
    green: { tone: "good", label: "Fits" },
    yellow: { tone: "warn", label: "Borderline" },
    red: { tone: "bad", label: "Does not fit" },
    missing: { tone: "bad", label: "Missing" },
    impossible: { tone: "neutral", label: "No prediction" },
};

const ROW_STATUSES = {
    ok: { tone: "good", label: "Fits" },
    review: { tone: "warn", label: "Review" },
    fail: { tone: "bad", label: "Does not fit" },
    not_assessable: { tone: "neutral", label: "Not assessable" },
};

const ASSIGNMENT_RESULTS = {
    consistent: { tone: "good", label: "Consistent" },
    review: { tone: "warn", label: "Review" },
    inconsistent: { tone: "bad", label: "Inconsistent" },
    not_assessable: { tone: "neutral", label: "Not assessable" },
};

const REASONS = {
    low_confidence_prediction: "low-confidence prediction",
    diastereotopic_pair_mean: "CH₂ pair mean",
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

function nucleusLabel(nucleus) {
    return NUCLEUS_LABELS[nucleus] || nucleus;
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
            html: `<strong>Possible interchange of ${escapeHtml(
                suggestion.labels?.[0]
            )} and ${escapeHtml(
                suggestion.labels?.[1]
            )}.</strong> Swapping them reduces the deviation by ${formatShift(
                suggestion.error_reduction,
                suggestion.nucleus
            )} ppm.`,
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
                        ? rows.length === 1
                            ? "does"
                            : "do"
                        : rows.length === 1
                        ? "needs"
                        : "need"
                } ${
                    status === "fail" ? "not fit the prediction" : "review"
                }:</strong> ${rows
                    .map(
                        (row) =>
                            `${escapeHtml(row.label)} (${nucleusLabel(
                                row.nucleus
                            )}, Δ ${formatSigned(row.delta, row.nucleus)} ppm)`
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
            )}:</strong> ${escapeHtml(issue.message)}${
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
            html: `<strong>Possible referencing error.</strong> All shifts are offset from the prediction by a similar amount (${escapeHtml(
                offsets
            )}). Check the spectrum referencing.`,
        });
    }

    const nuclei = Object.keys(report.reports || {});
    if (nuclei.some((nucleus) => report.reports[nucleus].in_database_likely)) {
        items.push({
            tone: "neutral",
            html: "<strong>Compound probably in nmrshiftdb2.</strong> Most atoms match 6-sphere HOSE codes with very small deviations, so this fit is not independent evidence.",
        });
    }

    if (!items.some((item) => item.tone === "bad" || item.tone === "warn")) {
        items.unshift({
            tone: "good",
            html: "<strong>No problems found.</strong> Every compared shift fits the prediction within tolerance.",
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
                <td class="num">${row.spheres || "—"}</td>
                <td>${pill(status)}</td>
                <td class="hose">${escapeHtml(row.hose_code || "—")}</td>
            </tr>`;
        })
        .join("");

    return `<section>
        <h2>${nucleusLabel(nucleus)} shift comparison
            <span class="aside">Mark <b>${escapeHtml(
                data.mark
            )}/10</b> · ${pill({
        tone: resultTone,
        label: data.result,
    })}</span>
        </h2>
        <div class="penalties">
            <span>Mean deviation <b>${escapeHtml(
                penalties.mean_deviation?.ppm
            )} ppm</b> (−${escapeHtml(penalties.mean_deviation?.points)})</span>
            <span>Red or missing <b>${escapeHtml(
                penalties.red_or_missing?.count
            )}</b> (−${escapeHtml(penalties.red_or_missing?.points)})</span>
            <span>Borderline <b>${escapeHtml(
                penalties.yellow?.count
            )}</b> (−${escapeHtml(penalties.yellow?.points)})</span>
            <span>Fitting shifts <b>${escapeHtml(
                stats.accept ?? "—"
            )} of ${escapeHtml(stats.total ?? "—")}</b></span>
        </div>
        <table>
            <thead><tr>
                <th>Atom</th><th class="num">δ obs. (ppm)</th><th class="num">δ pred. (ppm)</th>
                <th class="num">Deviation</th><th class="num">Spheres</th><th>Status</th><th>HOSE code</th>
            </tr></thead>
            <tbody>${rows}</tbody>
        </table>
        ${
            data.mark_is_approximate
                ? '<p class="note">nmrshiftdb2 does not publish its mark formula; this mark approximates it from the same penalties.</p>'
                : ""
        }
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
                            REASONS[reason] || reason.replace(/_/g, " ")
                        )}</span>`
                )
                .join("");
            return `<tr${flag}>
                <td class="atom">${escapeHtml(row.label)}</td>
                <td>${nucleusLabel(row.nucleus)}</td>
                <td class="num">${formatShift(row.observed, row.nucleus)}</td>
                <td class="num">${formatShift(row.predicted, row.nucleus)}</td>
                <td class="num">${formatSigned(row.delta, row.nucleus)}</td>
                <td class="num">${row.spheres ?? "—"}</td>
                <td>${pill(status)}${reasons}</td>
            </tr>`;
        })
        .join("");

    return `<section>
        <h2>Assignment check <span class="aside">${pill(result)}</span></h2>
        <p class="lead">Are the shifts on the right atoms? Each assigned signal is compared with the prediction for its atoms. Δ = observed − predicted.</p>
        <table>
            <thead><tr>
                <th>Assignment</th><th>Nucleus</th><th class="num">Observed</th><th class="num">Predicted</th>
                <th class="num">Δ (ppm)</th><th class="num">Spheres</th><th>Status</th>
            </tr></thead>
            <tbody>${rows}</tbody>
        </table>
        ${
            report.adjustments?.length
                ? `<p class="note">${report.adjustments.length} identical shift(s) on different atoms were sent 0.001 ppm apart so that nmrshiftdb2 keeps them as separate signals.</p>`
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
                <div class="label">${nucleusLabel(nucleus)} mark</div>
                <div class="value">${escapeHtml(
                    data.mark
                )}<small> / 10</small></div>
                <div class="sub">${pill({
                    tone: RESULT_TONES[data.result] || "neutral",
                    label: data.result,
                })} ${escapeHtml(stats.accept ?? 0)} of ${escapeHtml(
                stats.total ?? 0
            )} shifts fit</div>
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
        } of ${rows.length} assignments fit${
        check.suggestions?.length
            ? ` · ${check.suggestions.length} possible interchange${
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
                            ? " Highlighted atoms need review or do not fit the prediction."
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
    <p>Each assigned shift is compared with a prediction from <a href="https://nmrshiftdb.nmr.uni-koeln.de/">nmrshiftdb2</a>, based on HOSE codes of up to six spheres, in ${escapeHtml(
        report.solvent || "the given solvent"
    )}. For each nucleus the quickcheck gives a mark from 1 to 10, with penalties for the mean deviation, for shifts outside the expected range or missing (red) and for borderline shifts (yellow).</p>
    <p>The assignment check asks whether the shifts sit on the right atoms: every assigned signal is compared with the prediction for its atoms, and swaps between assignments that lower the total deviation are reported as possible interchanges. Predictions from fewer than four HOSE spheres are too uncertain to fail an assignment and can at most ask for review.</p>
    <p>The prediction is a reference, not the truth. A poor fit flags assignments for a second look; a good fit does not prove the structure. Atom labels are the author's and atom numbers refer to the submitted structure.</p>
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
