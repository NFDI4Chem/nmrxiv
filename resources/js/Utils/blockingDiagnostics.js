/**
 * Logs main-thread blocking via the Long Tasks API (>50ms) and correlates with
 * the last user interaction (capture phase).
 *
 * Enable:
 * - Automatically when Vite dev (`import.meta.env.DEV`)
 * - Or `?perf_blocking=1` on any page load
 * - Or `localStorage.setItem('nmrxiv_perf_blocking', '1')` then reload
 */

const STORAGE_KEY = "nmrxiv_perf_blocking";

function blockingDiagnosticsEnabled() {
    try {
        if (typeof window !== "undefined") {
            const q = new URLSearchParams(window.location.search).get(
                "perf_blocking"
            );
            if (q === "0" || q === "false") {
                return false;
            }
        }
    } catch {
        /* ignore */
    }

    if (import.meta.env.DEV) {
        return true;
    }

    try {
        if (
            typeof window !== "undefined" &&
            window.localStorage?.getItem(STORAGE_KEY) === "1"
        ) {
            return true;
        }
        if (typeof window !== "undefined") {
            const q = new URLSearchParams(window.location.search).get(
                "perf_blocking"
            );

            return q === "1" || q === "true";
        }
    } catch {
        /* ignore storage / URL access */
    }

    return false;
}

/** @type {{ type: string, time: number, detail: Record<string, string|null> } | null} */
let lastUserInteraction = null;

function summarizeTarget(el) {
    if (!el || !(el instanceof Element)) {
        return null;
    }

    const tag = el.tagName?.toLowerCase() ?? "unknown";

    return {
        tag,
        id: el.id || null,
        role: el.getAttribute?.("role"),
        ariaLabel: el.getAttribute?.("aria-label"),
        dataTour: el.getAttribute?.("data-tour"),
        textPreview:
            typeof el.textContent === "string"
                ? el.textContent.trim().slice(0, 120) || null
                : null,
        className:
            typeof el.className === "string"
                ? el.className.split(/\s+/).slice(0, 6).join(" ") || null
                : null,
    };
}

function recordInteraction(event) {
    lastUserInteraction = {
        type: event.type,
        time: performance.now(),
        detail: summarizeTarget(event.target),
    };
}

function installInteractionTail() {
    const opts = { capture: true, passive: true };

    for (const type of [
        "pointerdown",
        "pointerup",
        "click",
        "keydown",
        "input",
        "scroll",
        "wheel",
        "touchstart",
    ]) {
        window.addEventListener(type, recordInteraction, opts);
    }
}

function formatAttribution(entry) {
    const raw = entry.attribution;

    if (!Array.isArray(raw) || raw.length === 0) {
        return [];
    }

    return raw.map((a) => ({
        name: a.name ?? null,
        containerType: a.containerType ?? null,
        containerSrc:
            typeof a.containerSrc === "string"
                ? a.containerSrc.slice(0, 200)
                : null,
        containerId: a.containerId ?? null,
        containerName: a.containerName ?? null,
    }));
}

export function installBlockingDiagnostics() {
    if (!blockingDiagnosticsEnabled()) {
        return;
    }

    if (
        typeof window === "undefined" ||
        typeof PerformanceObserver === "undefined"
    ) {
        return;
    }

    installInteractionTail();

    try {
        const observer = new PerformanceObserver((list) => {
            const now = performance.now();

            for (const entry of list.getEntries()) {
                if (entry.entryType !== "longtask") {
                    continue;
                }

                const durationMs = Math.round(entry.duration);
                const taskStartMs = Math.round(entry.startTime);
                const taskEndApproxMs = Math.round(
                    entry.startTime + entry.duration
                );
                const msAfterTaskEnd = Math.round(now - taskEndApproxMs);
                const msFromLastInteractionToTaskStart =
                    lastUserInteraction != null
                        ? Math.round(entry.startTime - lastUserInteraction.time)
                        : null;

                /** Observer runs after tasks finish; buffered entries can flush later */
                const likelyDelayedReport = msAfterTaskEnd > 750;

                const hints = [];
                if (typeof document !== "undefined") {
                    if (document.hidden) {
                        hints.push(
                            "document was hidden — timers/tasks can be delayed; durations look inflated vs wall clock"
                        );
                    }
                    hints.push(`visibility=${document.visibilityState}`);
                }
                if (likelyDelayedReport) {
                    hints.push(
                        "buffered/late delivery — compare taskStartMs to SpectraEditor logs; spike may not align with INITIATE wall time"
                    );
                }
                if (durationMs >= 3000) {
                    hints.push(
                        "very long parent tasks often come from huge Vue trees (e.g. Upload.vue), postMessage deserialization, or DevTools overhead — record Performance→Main to see stack"
                    );
                }

                console.warn(
                    `[nmrxiv perf] Main thread blocked ~${durationMs}ms (long task)`,
                    {
                        durationMs,
                        taskStartMs,
                        taskEndApproxMs,
                        msAfterTaskEndApprox: msAfterTaskEnd,
                        performanceNowMs: Math.round(now),
                        taskName: entry.name ?? "(anonymous)",
                        attribution: formatAttribution(entry),
                        lastUserInteraction,
                        msFromLastInteractionToTaskStart:
                            msFromLastInteractionToTaskStart ??
                            "(no interaction yet)",
                        hints,
                    }
                );
            }
        });

        observer.observe({ type: "longtask", buffered: true });

        console.info(
            "[nmrxiv perf] Long-task blocking diagnostics active (disable in prod: omit ?perf_blocking=1 and clear localStorage nmrxiv_perf_blocking)"
        );
    } catch {
        console.info(
            "[nmrxiv perf] Long Task API not supported in this browser; skipping blocking diagnostics"
        );
    }
}
