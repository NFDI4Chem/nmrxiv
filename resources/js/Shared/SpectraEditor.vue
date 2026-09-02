<template>
    <div>
        <div>
            <div class="mb-2 clearfix">
                <DefaultSpectrumTabSelect
                    v-if="!isViewerLoading"
                    @changed="onDefaultSpectrumTabChanged"
                />
                <small class="float-right">
                    <a
                        class="text-xs cursor-pointer hover:text-blue-700 mr-2"
                        href="https://docs.nmrxiv.org/advanced-guides/nmrium/nmrium.html"
                        target="_blank"
                        rel="noopener noreferrer"
                        >Learn more
                    </a>
                    <a
                        class="cursor-pointer mr-3 border px-2 py-1 rounded-md"
                        @click="resetStudy"
                        >Reset</a
                    >
                    <a
                        class="cursor-pointer border px-2 py-1 rounded-md"
                        @click="exportPreview()"
                    >
                        <ArrowPathIcon class="w-4 h-4 inline" />
                        Preview</a
                    >
                </small>
            </div>
            <small class="text-gray-400">
                <span v-if="info">{{ info }}</span>
            </small>
        </div>

        <div
            v-if="nmriumLoadError"
            class="mb-3 flex flex-wrap items-center gap-2 rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900 dark:border-amber-800 dark:bg-amber-950/50 dark:text-amber-100"
            role="alert"
        >
            <span class="min-w-0 flex-1">{{ nmriumLoadError }}</span>
            <button
                type="button"
                class="shrink-0 rounded-md border border-amber-300 bg-white px-2 py-1 text-xs font-medium text-amber-900 hover:bg-amber-100 dark:border-amber-700 dark:bg-amber-900 dark:text-amber-100 dark:hover:bg-amber-800"
                @click="retryNmriumLoad"
            >
                Retry
            </button>
        </div>

        <iframe
            name="submissionNMRiumIframe"
            frameborder="0"
            allowfullscreen
            loading="eager"
            class="rounded-md border"
            style="width: 100%; height: 75vh; max-height: 600px"
            :src="iframeSrc"
            @load="onNmriumIframeLoad"
        ></iframe>
        <div
            v-if="spectraError && spectraError.length > 0"
            class="mt-4"
            role="status"
        >
            <div
                class="rounded-md border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-950/50"
            >
                <div class="flex">
                    <div class="flex-shrink-0">
                        <ExclamationTriangleIcon
                            class="h-5 w-5 text-amber-500 dark:text-amber-400"
                            aria-hidden="true"
                        />
                    </div>
                    <div class="ml-3 flex-1">
                        <button
                            type="button"
                            class="flex w-full items-center justify-between text-left text-sm font-medium text-amber-900 focus:outline-none dark:text-amber-100"
                            :aria-expanded="!spectraErrorsCollapsed"
                            aria-controls="spectra-warnings-list"
                            @click="
                                spectraErrorsCollapsed = !spectraErrorsCollapsed
                            "
                        >
                            <span>Some spectra loaded with warnings</span>
                            <ChevronDownIcon
                                class="h-4 w-4 text-amber-600 transition-transform duration-150 dark:text-amber-400"
                                :class="{
                                    'rotate-180': !spectraErrorsCollapsed,
                                }"
                                aria-hidden="true"
                            />
                        </button>
                        <div
                            v-show="!spectraErrorsCollapsed"
                            id="spectra-warnings-list"
                            class="mt-2 text-sm text-amber-800 dark:text-amber-200"
                        >
                            <ul role="list" class="list-disc space-y-1 pl-5">
                                <li
                                    v-for="(
                                        group, index
                                    ) in groupedSpectraErrors"
                                    :key="index"
                                    class="break-words"
                                >
                                    {{ group.message }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Versions ref="versionsElement" :dataset="dataset" />
    </div>
</template>

<script>
import Versions from "./Versions.vue";
import DefaultSpectrumTabSelect from "./DefaultSpectrumTabSelect.vue";
import {
    getDefaultSpectrumTab,
    postNmriumLoad,
    requestSelectTab,
    resolveNmriumTargetOrigin,
} from "@/Utils/nmriumTabPreference.js";
import { hifsaNmriumFileFilter } from "@/Utils/hifsaNmriumFileFilter.js";
import {
    ArrowPathIcon,
    ChevronDownIcon,
    ExclamationTriangleIcon,
} from "@heroicons/vue/24/outline";
import { markRaw, ref } from "vue";

const NMRIUM_LOAD_TIMEOUT_MS = 20000;
/**
 * Time after we post `nmr-wrapper:load` to NMRium before we hide the parent's
 * loading overlay. NMRium can take many seconds to parse large saved JSON and
 * emit `INITIATE`, but it shows its own internal progress UI in the meantime.
 * Keeping our blocking overlay until INITIATE made the page feel frozen.
 */
const NMRIUM_HANDOFF_MS = 1200;
const DEBUG_SPECTRA_TIMING = import.meta.env.DEV;
const dbg = (...args) => {
    if (!DEBUG_SPECTRA_TIMING) {
        return;
    }
    const t = (performance.now() / 1000).toFixed(3);
    console.log(`[SpectraEditor +${t}s]`, ...args);
};

/**
 * Stable per-instance iframe URL so the browser can cache NMRium assets.
 * Pass a new sessionKey only when forcing a full iframe reload (e.g. Reset).
 *
 * @param {string|undefined} nmriumURLProp
 * @param {string} sessionKey
 * @returns {string}
 */
function buildNmriumIframeSrc(nmriumURLProp, sessionKey) {
    const raw =
        nmriumURLProp ||
        "https://nmriumdev.nmrxiv.org?defaultEmptyMessage=''&workspace=embedded&id=";
    if (typeof raw === "string" && raw.includes("&id=")) {
        return raw + sessionKey;
    }
    const base = String(raw);
    const sep = base.includes("?") ? "&" : "?";

    return `${base}${sep}id=${sessionKey}`;
}

export default {
    components: {
        Versions,
        DefaultSpectrumTabSelect,
        ArrowPathIcon,
        ChevronDownIcon,
        ExclamationTriangleIcon,
    },
    props: {
        dataset: Object,
        project: Object,
        study: Object,
    },
    emits: ["loading"],
    setup() {
        const versionsElement = ref(null);
        return {
            versionsElement,
        };
    },
    data() {
        const iframeSessionKey =
            typeof crypto !== "undefined" && crypto.randomUUID
                ? crypto.randomUUID()
                : `${Date.now()}-${Math.random().toString(36).slice(2)}`;

        return {
            iframeSessionKey,
            iframeSrc: buildNmriumIframeSrc(
                this.$page.props.nmriumURL,
                iframeSessionKey
            ),
            iframeReady: false,
            /** After iframe src bump, load raw URLs once the new shell is ready */
            pendingResetFromUrls: false,
            nmriumAllowedOrigins: [],
            spectraError: [],
            spectraErrorsCollapsed: true,
            selectedSpectraData: null,
            autoSaving: false,
            currentMolecules: [],
            resetInProgress: false,
            info: null,
            version: null,
            nmriumLoadError: null,
            nmriumLoadTimeoutId: null,
            nmriumHandoffTimeoutId: null,
            nmriumMessageHandler: null,
            nmriumPostLoadAt: null,
            defaultTabApplied: false,
            nmriumInfoCache: markRaw(new Map()),
            /** Mirrors last status sent on the `loading` event so we can skip duplicate emits. */
            lastEmittedLoadingStatus: null,
            /**
             * Set of study ids for which we have already kicked off a
             * silent (background) preview snapshot in this session, so we
             * don't keep re-firing on every navigation back to the same
             * study before the page is reloaded.
             */
            silentPreviewAttemptedFor: markRaw(new Set()),
            /** True while the in-flight blob save should suppress the visible "Saved" banner. */
            previewSilent: false,
            /**
             * False while NMRium is still hydrating after a load/reset. Internal
             * data-change events during this window must not fan out chemistry
             * standardize requests for every embedded molecule.
             */
            nmriumLoadSettled: false,
            /** Per-session molfile -> standardize response promise. */
            standardizeRequestCache: markRaw(new Map()),
        };
    },
    computed: {
        url() {
            return String(this.$page.props.url)
                ? String(this.$page.props.url)
                : "https://dev.nmrxiv.org";
        },
        /**
         * Collapse identical error strings - NMRium can fire dozens of the
         * same parse error in a row when a sample has many spectra of the
         * same problematic format, and a flat list of 80+ identical bullets
         * is unscannable. Each group keeps its first occurrence's index so
         * the v-for stays stable, and a `count` so the bullet can show
         * `message · ×N` when the same error fired multiple times.
         */
        groupedSpectraErrors() {
            const errors = Array.isArray(this.spectraError)
                ? this.spectraError
                : [];
            const groups = new Map();
            errors.forEach((entry) => {
                const message = this.formatSpectraError(entry);
                const existing = groups.get(message);
                if (existing) {
                    existing.count += 1;
                } else {
                    groups.set(message, { message, count: 1 });
                }
            });
            return Array.from(groups.values());
        },
        isViewerLoading() {
            return this.lastEmittedLoadingStatus === true;
        },
        mailFromAddress() {
            return String(this.$page.props.mailFromAddress);
        },
        chemistryStandardizeUrl() {
            return this.$page.props.chemistryStandardizeUrl;
        },
        nmriumSampleLabel() {
            const s = this.study;
            if (!s) {
                return "Sample";
            }
            return (s.name && String(s.name).trim()) || s.slug || "Sample";
        },
    },
    watch: {
        study: {
            handler(newStudy, oldStudy) {
                if (!this.iframeReady || !newStudy) {
                    return;
                }
                if (oldStudy && newStudy.id === oldStudy.id) {
                    return;
                }
                this.loadSpectra();
            },
        },
    },
    mounted() {
        this.nmriumAllowedOrigins = this.buildNmriumAllowedOrigins();
        this.attachNmriumMessageListener();
    },
    beforeUnmount() {
        this.detachNmriumMessageListener();
        this.clearNmriumLoadTimeout();
        this.clearNmriumHandoff();
    },
    methods: {
        /**
         * Legacy hook for pages that call ref.registerEvents().
         * Message listeners are attached in mounted().
         */
        registerEvents() {},
        /**
         * NMRium emits `nmr-wrapper:error` payloads that come through as
         * either a plain Error (whose `.name` is the constructor name -
         * literally "Error" - and whose `.message` carries the real text)
         * or a wrapper object roughly shaped like
         *   { label: "1H/file.fid", fileName: "...", error: <Error|string>, ... }
         *
         * Users care about the meaningful text first (the failing file's
         * `label`, then the parser `message`), and only fall back to the
         * generic constructor `name` ("Error", "TypeError"…) when nothing
         * better exists - otherwise every bullet just renders as "Error".
         */
        formatSpectraError(entry) {
            if (entry === null || entry === undefined) {
                return "Unknown warning";
            }
            if (typeof entry === "string") {
                return entry;
            }
            if (typeof entry !== "object") {
                return String(entry);
            }
            const inner = entry.error;
            const candidate =
                entry.label ||
                entry.fileName ||
                entry.message ||
                (inner && typeof inner === "object"
                    ? inner.label || inner.fileName || inner.message
                    : null) ||
                (typeof inner === "string" ? inner : null) ||
                (inner && typeof inner === "object" ? inner.name : null) ||
                entry.name;
            if (candidate) {
                return String(candidate);
            }
            try {
                return JSON.stringify(entry);
            } catch (_e) {
                return "Unknown warning";
            }
        },
        /**
         * Public API: invalidate the per-study NMRium cache and re-run loadSpectra
         * for the currently bound study. Used by parent pages after auto-import
         * finishes so the iframe and on-page state pick up freshly saved data
         * even though the study id did not change.
         */
        reload() {
            if (!this.study) {
                return;
            }
            this.nmriumInfoCache.delete(this.study.id);
            this.spectraError = null;
            this.spectraErrorsCollapsed = true;
            this.nmriumLoadError = null;
            if (this.iframeReady) {
                this.loadSpectra();
            }
        },
        buildNmriumAllowedOrigins() {
            const raw =
                this.$page.props.nmriumURL || "https://nmriumdev.nmrxiv.org";
            try {
                const href = raw.startsWith("//") ? `https:${raw}` : raw;
                const u = new URL(href);
                return [
                    ...new Set([
                        u.origin,
                        "https://nmriumdev.nmrxiv.org",
                        "https://nmrium.nmrxiv.org",
                    ]),
                ];
            } catch {
                return [
                    "https://nmriumdev.nmrxiv.org",
                    "https://nmrium.nmrxiv.org",
                ];
            }
        },
        isAllowedNmriumOrigin(origin) {
            return this.nmriumAllowedOrigins.includes(origin);
        },
        attachNmriumMessageListener() {
            if (this.nmriumMessageHandler) {
                return;
            }
            this.nmriumMessageHandler = (e) => {
                this.onNmriumWindowMessage(e);
            };
            window.addEventListener("message", this.nmriumMessageHandler);
        },
        detachNmriumMessageListener() {
            if (this.nmriumMessageHandler) {
                window.removeEventListener(
                    "message",
                    this.nmriumMessageHandler
                );
                this.nmriumMessageHandler = null;
            }
        },
        onNmriumWindowMessage(e) {
            const syncStart = performance.now();

            try {
                this.handleNmriumWindowMessageSync(e);
            } finally {
                const syncMs = performance.now() - syncStart;
                if (DEBUG_SPECTRA_TIMING && syncMs >= 16) {
                    dbg(
                        `onNmriumWindowMessage (sync) took ${syncMs.toFixed(
                            1
                        )}ms`,
                        {
                            rawType: e.data?.type,
                            origin: e.origin?.slice(0, 80),
                        }
                    );
                }
            }
        },
        handleNmriumWindowMessageSync(e) {
            const { data, type } = e.data;
            if (type == "nmr-wrapper:action-response") {
                if (!this.isAllowedNmriumOrigin(e.origin)) {
                    return;
                }
                let actionType = data.type;
                if (actionType == "exportSpectraViewerAsBlob") {
                    this.saveStudyPreview(data.data);
                }
            }
            if (type == "nmr-wrapper:error") {
                console.debug(
                    "[SpectraEditor] nmr-wrapper:error payload",
                    data
                );
                if (this.spectraError) {
                    this.spectraError.push(data);
                } else {
                    this.spectraError = [data];
                }
                this.updateLoadingStatus(false);
                return;
            }
            if (data && data.source == "data") {
                if (!this.isAllowedNmriumOrigin(e.origin)) {
                    return;
                }
                let state = data.state;
                let actionType = state.data.actionType;
                if (type == "nmr-wrapper:data-change") {
                    if (state.data.spectra.length > 0) {
                        this.version = state.version;

                        if (
                            actionType == "INITIATE" &&
                            this.study?.has_nmrium &&
                            !this.resetInProgress
                        ) {
                            const sincePost =
                                this.nmriumPostLoadAt != null
                                    ? (
                                          performance.now() -
                                          this.nmriumPostLoadAt
                                      ).toFixed(0) + "ms"
                                    : "n/a";
                            dbg("nmrium INITIATE received", {
                                sincePostMessage: sincePost,
                                molecules: state.data.molecules?.length,
                                spectra: state.data.spectra?.length,
                                resetInProgress: this.resetInProgress,
                                has_nmrium: this.study?.has_nmrium,
                            });
                            dbg(
                                "INITIATE on existing nmrium -> skip storing payload (avoids Vue deep-reactivity on huge NMRium state)"
                            );
                            this.nmriumLoadSettled = true;
                            this.updateLoadingStatus(false);
                            this.applyDefaultSpectrumTab();

                            // Auto-imports come in via the parse-url backend
                            // path which never gets the chance to capture a
                            // preview SVG. The NMRium iframe IS now mounted
                            // for whatever study the user lands on, so if
                            // the saved snapshot is still missing, fire a
                            // silent background capture. The user sees no
                            // "Updating Preview" banner — the request just
                            // populates `study_photo_path` for next time.
                            if (
                                this.study &&
                                !this.study.study_photo_url &&
                                !this.silentPreviewAttemptedFor.has(
                                    this.study.id
                                )
                            ) {
                                this.silentPreviewAttemptedFor.add(
                                    this.study.id
                                );
                                window.setTimeout(() => {
                                    this.exportPreview({ silent: true });
                                }, 750);
                            }

                            return;
                        }

                        this.selectedSpectraData = markRaw(state.data);
                        if (actionType == "INITIATE") {
                            const sincePost =
                                this.nmriumPostLoadAt != null
                                    ? (
                                          performance.now() -
                                          this.nmriumPostLoadAt
                                      ).toFixed(0) + "ms"
                                    : "n/a";
                            dbg("nmrium INITIATE received", {
                                sincePostMessage: sincePost,
                                molecules: state.data.molecules?.length,
                                spectra: state.data.spectra?.length,
                                resetInProgress: this.resetInProgress,
                                has_nmrium: this.study?.has_nmrium,
                            });
                            this.applyDefaultSpectrumTab();
                            if (
                                !this.study.has_nmrium ||
                                this.resetInProgress
                            ) {
                                delete this.selectedSpectraData["actionType"];
                                this.selectedSpectraData.spectra.forEach(
                                    (spec) => {
                                        // Preserve `spec.info` (parser-derived
                                        // metadata: nucleus, frequency,
                                        // experiment, dimension, …). The
                                        // previous implementation overwrote it
                                        // with `spec.originalData`, which is
                                        // the raw FID/spectral payload — that
                                        // permanently destroyed the metadata
                                        // we rely on for sidebar labels and
                                        // reports.
                                        delete spec["data"];
                                        delete spec["meta"];
                                        delete spec["originalData"];
                                        delete spec["originalInfo"];
                                    }
                                );
                                if (this.study.study_photo_url == "") {
                                    setTimeout(
                                        function () {
                                            this.exportPreview();
                                        }.bind(this),
                                        500
                                    );
                                }
                                this.resetInProgress = false;
                                this.updateStudyNMRiumInfo();
                                return;
                            }
                        } else {
                            dbg("nmrium data-change (non-INITIATE)", {
                                actionType,
                                molecules: state.data.molecules?.length,
                            });
                            // Non-INITIATE data-change events come from NMRium's
                            // internal state syncs as well as user edits. Persist
                            // silently (no overlay/bar) so the page does not flash
                            // a "Saving" indicator after a normal load.
                            delete this.selectedSpectraData["actionType"];
                            this.selectedSpectraData.spectra.forEach((spec) => {
                                delete spec["data"];
                                delete spec["meta"];
                                delete spec["originalData"];
                                delete spec["originalInfo"];
                            });
                            this.updateStudyNMRiumInfo({ silent: true });
                            return;
                        }
                    }
                }
            }
        },
        startNmriumLoadTimeout() {
            this.clearNmriumLoadTimeout();
            this.nmriumLoadTimeoutId = window.setTimeout(() => {
                this.nmriumLoadTimeoutId = null;
                this.nmriumLoadError =
                    "The viewer did not respond in time. Check your connection or try again.";
                this.updateLoadingStatus(false);
            }, NMRIUM_LOAD_TIMEOUT_MS);
        },
        clearNmriumLoadTimeout() {
            if (this.nmriumLoadTimeoutId != null) {
                clearTimeout(this.nmriumLoadTimeoutId);
                this.nmriumLoadTimeoutId = null;
            }
        },
        scheduleNmriumHandoff() {
            this.clearNmriumHandoff();
            this.nmriumHandoffTimeoutId = window.setTimeout(() => {
                this.nmriumHandoffTimeoutId = null;
                dbg(
                    `handoff fired after ${NMRIUM_HANDOFF_MS}ms -> hiding parent overlay (NMRium continues internally)`
                );
                this.nmriumLoadSettled = true;
                this.updateLoadingStatus(false);
            }, NMRIUM_HANDOFF_MS);
        },
        clearNmriumHandoff() {
            if (this.nmriumHandoffTimeoutId != null) {
                clearTimeout(this.nmriumHandoffTimeoutId);
                this.nmriumHandoffTimeoutId = null;
            }
        },
        onNmriumIframeLoad() {
            dbg("iframe @load fired (NMRium app shell ready)");
            this.iframeReady = true;
            if (this.pendingResetFromUrls) {
                this.pendingResetFromUrls = false;
                this.loadFromURLs();

                return;
            }
            if (this.study) {
                this.loadSpectra();
            }
        },
        bumpNmriumIframeSrc() {
            this.iframeSessionKey =
                typeof crypto !== "undefined" && crypto.randomUUID
                    ? crypto.randomUUID()
                    : `${Date.now()}-${Math.random().toString(36).slice(2)}`;
            this.iframeSrc = buildNmriumIframeSrc(
                this.$page.props.nmriumURL,
                this.iframeSessionKey
            );
            this.iframeReady = false;
        },
        /**
         * @param {object} nmrium_info Raw body from GET /nmriumInfo (same shape postMessage expects wrapped).
         */
        postNmriumInfoToIframe(nmrium_info) {
            const iframe = window.frames.submissionNMRiumIframe;
            if (!iframe || !nmrium_info) {
                return false;
            }
            const payload = {
                data: nmrium_info,
                type: "nmrium",
            };
            this.nmriumPostLoadAt = performance.now();
            this.defaultTabApplied = false;
            dbg("iframe.postMessage(nmr-wrapper:load) -> sent");
            postNmriumLoad(
                iframe,
                payload,
                this.nmriumTargetOrigin(),
                getDefaultSpectrumTab(this.$page)
            );
            this.scheduleNmriumHandoff();

            return true;
        },
        retryNmriumLoad() {
            this.nmriumLoadError = null;
            if (this.iframeReady && this.study) {
                this.loadSpectra();
            }
        },
        loadSpectra() {
            dbg("loadSpectra() called", {
                studyId: this.study?.id,
                has_nmrium: this.study?.has_nmrium,
                iframeReady: this.iframeReady,
            });
            this.nmriumLoadSettled = false;
            const iframe = window.frames.submissionNMRiumIframe;
            this.spectraError = null;
            this.spectraErrorsCollapsed = true;
            this.currentMolecules = [];
            this.nmriumLoadError = null;
            this.defaultTabApplied = false;
            if (!iframe || !this.study) {
                dbg("loadSpectra: aborted (no iframe or study)");
                return;
            }
            this.updateLoadingStatus(true, {
                viewerMeta: {
                    phase: "spectra",
                    sampleLabel: this.nmriumSampleLabel,
                },
            });
            this.startNmriumLoadTimeout();
            if (this.study && this.study.has_nmrium) {
                const cached = this.nmriumInfoCache.get(this.study.id);
                if (cached !== undefined && cached !== null) {
                    dbg("nmriumInfo cache hit", { studyId: this.study.id });
                    this.postNmriumInfoToIframe(cached);

                    return;
                }

                const t0 = performance.now();
                dbg("GET /nmriumInfo -> start", { studyId: this.study.id });
                axios
                    .get("/dashboard/studies/" + this.study.id + "/nmriumInfo")
                    .then((response) => {
                        const dur = (performance.now() - t0).toFixed(0);
                        dbg(`GET /nmriumInfo <- end in ${dur}ms`, {
                            hasData: !!response.data,
                            molecules: response.data?.data?.molecules?.length,
                            spectra: response.data?.data?.spectra?.length,
                        });
                        let nmrium_info = response.data;
                        if (nmrium_info) {
                            try {
                                this.nmriumInfoCache.set(
                                    this.study.id,
                                    JSON.parse(JSON.stringify(nmrium_info))
                                );
                            } catch {
                                this.nmriumInfoCache.set(
                                    this.study.id,
                                    nmrium_info
                                );
                            }
                            this.postNmriumInfoToIframe(nmrium_info);
                        } else {
                            dbg("nmriumInfo empty, falling back to URLs");
                            this.loadFromURLs();
                        }
                    })
                    .catch((err) => {
                        const dur = (performance.now() - t0).toFixed(0);
                        dbg(`GET /nmriumInfo <- error after ${dur}ms`, err);
                        this.clearNmriumLoadTimeout();
                        this.nmriumLoadError =
                            "Could not load saved NMRium data for this sample.";
                        this.updateLoadingStatus(false);
                    });
            } else {
                if (this.study.datasets.length > 0) {
                    this.loadFromURLs();
                } else {
                    dbg("no nmrium and no datasets, clearing loader");
                    this.clearNmriumLoadTimeout();
                    this.updateLoadingStatus(false);
                }
            }
        },
        loadFromURLs(urls) {
            const iframe = window.frames.submissionNMRiumIframe;
            if (!iframe || !this.study) {
                this.clearNmriumLoadTimeout();
                this.updateLoadingStatus(false);

                return;
            }
            if (!urls || urls.length === 0) {
                let url = null;
                if (this.study.download_url) {
                    url = this.study.download_url;
                } else {
                    let username = this.$page.props.team.owner
                        ? this.$page.props.team.owner.username
                        : this.project.owner.username;
                    url =
                        this.url +
                        "/" +
                        username +
                        "/datasets/" +
                        this.project.slug +
                        "/" +
                        this.study.slug;
                }
                urls = [url];
            }

            let data = {
                data: urls,
                type: "url",
            };

            this.nmriumPostLoadAt = performance.now();
            this.defaultTabApplied = false;
            dbg("iframe.postMessage(nmr-wrapper:load url) -> sent");
            postNmriumLoad(
                iframe,
                data,
                this.nmriumTargetOrigin(),
                getDefaultSpectrumTab(this.$page),
                hifsaNmriumFileFilter(this.study)
            );
            this.scheduleNmriumHandoff();
        },
        nmriumTargetOrigin() {
            return resolveNmriumTargetOrigin(this.$page.props.nmriumURL);
        },
        applyDefaultSpectrumTab() {
            const tab = getDefaultSpectrumTab(this.$page);
            if (!tab || this.defaultTabApplied) {
                return;
            }

            this.defaultTabApplied = true;
            requestSelectTab(
                window.frames.submissionNMRiumIframe,
                tab,
                this.nmriumTargetOrigin()
            );
        },
        onDefaultSpectrumTabChanged() {
            this.defaultTabApplied = false;
            this.applyDefaultSpectrumTab();
        },
        /**
         * Persist the current NMRium project to the study.
         *
         * @param {{ silent?: boolean }} [options]
         *   silent: when true, do not toggle the page-level loader (used for
         *   internal NMRium syncs that are not user edits).
         */
        updateStudyNMRiumInfo(options) {
            const silent = !!(options && options.silent);
            const shouldSyncMolecules =
                options?.syncMolecules !== false &&
                (!silent || this.nmriumLoadSettled);
            if (this.study != null && this.selectedSpectraData) {
                let _version = this.version ? this.version : 4;
                const t0 = performance.now();
                dbg("POST /nmriumInfo -> start", { silent });
                axios
                    .post(
                        "/dashboard/studies/" + this.study.id + "/nmriumInfo",
                        {
                            version: _version,
                            data: this.selectedSpectraData,
                        }
                    )
                    .then((response) => {
                        const dur = (performance.now() - t0).toFixed(0);
                        dbg(`POST /nmriumInfo <- end in ${dur}ms`, {
                            has_nmrium: response.data?.has_nmrium,
                        });
                        this.infoLog("Spectra saved successfully", true);
                        this.study.has_nmrium = response.data.has_nmrium;
                        this.nmriumInfoCache.delete(this.study.id);
                        if (shouldSyncMolecules) {
                            this.syncMissingMolecules(
                                this.selectedSpectraData.molecules
                            );
                        }
                    })
                    .catch((err) => {
                        const dur = (performance.now() - t0).toFixed(0);
                        dbg(`POST /nmriumInfo <- error after ${dur}ms`, err);
                        this.infoLog("Error saving spectra info");
                        console.error(
                            "Error saving the nmrium info. Please contact us at " +
                                this.mailFromAddress +
                                " if the error persist."
                        );
                    })
                    .finally(() => {
                        if (!silent) {
                            this.clearNmriumLoadTimeout();
                            this.updateLoadingStatus(false);
                        }
                        this.autoSaving = false;
                    });
            }
        },
        fixLineError(mol) {
            let lineNumber = mol.molfile
                .substring(0, mol.molfile.indexOf("V2000"))
                .split("\n").length;
            if (lineNumber == 3) {
                mol.molfile = "\n" + mol.molfile;
            }
            return mol;
        },
        requestStandardizedMolecule(molfile) {
            const key = String(molfile ?? "").trim();
            if (!key) {
                return Promise.reject(new Error("Empty molfile"));
            }
            if (this.standardizeRequestCache.has(key)) {
                return this.standardizeRequestCache.get(key);
            }
            const promise = axios
                .post(this.chemistryStandardizeUrl, key)
                .then((response) => response.data);
            this.standardizeRequestCache.set(key, promise);
            return promise;
        },
        /**
         * Standardize and persist any new molecules from NMRium that are not
         * already linked to this study. Uses InChI as the equality key (after
         * standardization) and skips already-known ones to avoid the per-open
         * burst of duplicate POSTs that previously caused the page to freeze.
         */
        syncMissingMolecules(molecules) {
            if (!molecules || molecules.length === 0) {
                dbg("syncMissingMolecules: nothing to sync");
                return;
            }
            const known = this.study?.sample?.molecules || [];
            const knownInchis = new Set(
                known.map((m) => m && m.standard_inchi).filter(Boolean)
            );
            dbg("syncMissingMolecules: start", {
                incoming: molecules.length,
                alreadyKnown: known.length,
            });
            molecules.forEach((mol, idx) => {
                mol = this.fixLineError(mol);
                const tStandardize = performance.now();
                this.requestStandardizedMolecule(mol.molfile)
                    .then((_mol) => {
                        const stdDur = (
                            performance.now() - tStandardize
                        ).toFixed(0);
                        if (knownInchis.has(_mol.inchi)) {
                            dbg(
                                `mol[${idx}] standardize ${stdDur}ms -> already known, skipping POST /molecule`
                            );
                            return;
                        }
                        knownInchis.add(_mol.inchi);
                        const tAssoc = performance.now();
                        dbg(
                            `mol[${idx}] standardize ${stdDur}ms -> new, POST /molecule`
                        );
                        return axios
                            .post(
                                "/dashboard/studies/" +
                                    this.study.id +
                                    "/molecule",
                                {
                                    InChI: _mol.inchi,
                                    InChIKey: _mol.inchikey,
                                    percentage: 0,
                                    mol: _mol.standardized_mol,
                                    canonical_smiles: _mol.canonical_smiles,
                                }
                            )
                            .then((res2) => {
                                const assocDur = (
                                    performance.now() - tAssoc
                                ).toFixed(0);
                                dbg(
                                    `mol[${idx}] POST /molecule ${assocDur}ms -> linked`
                                );
                                this.study.sample.molecules = res2.data;
                            });
                    })
                    .catch((err) => {
                        dbg(`mol[${idx}] sync failed`, err);
                    });
            });
        },
        updateMolecularData(molecules) {
            this.syncMissingMolecules(molecules);
        },
        updateDataSet() {
            if (this.dataset != null && this.selectedSpectraData.length > 0) {
                axios
                    .post(
                        "/dashboard/datasets/" +
                            this.dataset.id +
                            "/nmriumInfo",
                        {
                            spectra: this.selectedSpectraData,
                            molecules: this.currentMolecules,
                            version: this.version || 4,
                        }
                    )
                    .then((response) => {
                        this.infoLog("Spectra saved successfully", true);
                        this.autoSaving = false;
                        this.dataset.has_nmrium = response.data.has_nmrium;
                    })
                    .catch(() => {
                        this.infoLog("Error saving spectra info");
                        console.error(
                            "Error saving the nmrium info. Please contact us at {{mailFromAddress}} if the error persist."
                        );
                        this.autoSaving = false;
                    });
            }
        },
        loadMol(molFile) {
            let svgString = null;
            let mol = OCL.Molecule.fromMolfile(molFile);
            if (mol.toIsomericSmiles() != "") {
                svgString = mol.toSVG(300, 300);
            }
            return svgString;
        },
        /**
         * Ask the embedded NMRium for an SVG snapshot of the current
         * spectra. When `silent` is true, no user-facing "Updating Preview"
         * banner is shown — used for the background capture that runs after
         * an auto-imported study first opens, so the user never notices the
         * preview being generated.
         */
        exportPreview(opts = {}) {
            const silent = opts && opts.silent === true;
            if (!silent) {
                this.infoLog("Updating Preview");
            }
            this.previewSilent = silent;
            const iframe = window.frames.submissionNMRiumIframe;
            if (iframe) {
                let data = {
                    type: "exportSpectraViewerAsBlob",
                };
                iframe.postMessage(
                    {
                        type: `nmr-wrapper:action-request`,
                        data,
                    },
                    this.nmriumTargetOrigin()
                );
            }
        },
        saveStudyPreview(data) {
            if (this.study) {
                const silent = this.previewSilent === true;
                this.previewSilent = false;
                const reader = new FileReader();
                reader.addEventListener("loadend", () => {
                    let svg = reader.result;
                    axios
                        .post(
                            "/dashboard/studies/" + this.study.id + "/snapshot",
                            {
                                img: svg,
                            }
                        )
                        .then(() => {
                            if (!silent) {
                                this.infoLog(
                                    "Saved preview successfully",
                                    true
                                );
                            }
                        })
                        .catch((err) => {
                            console.warn(
                                "[SpectraEditor] preview snapshot failed",
                                err
                            );
                        });
                });
                reader.readAsText(data.blob);
            }
        },
        getSVGString(molecule) {
            if (molecule.MOL) {
                let mol = OCL.Molecule.fromMolfile(
                    "\n  " + molecule.MOL.replaceAll('"', "")
                );
                return mol.toSVG(200, 200);
            }
        },
        updateLoadingStatus(status, detail) {
            const payload = { status };
            if (!status) {
                this.clearNmriumLoadTimeout();
                this.clearNmriumHandoff();
                if (this.lastEmittedLoadingStatus === false) {
                    dbg(
                        "updateLoadingStatus(false) -> already off, skipping emit"
                    );

                    return;
                }
                dbg("updateLoadingStatus(false) -> emit loading off");
                this.lastEmittedLoadingStatus = false;
                this.$emit("loading", payload);

                return;
            }
            this.lastEmittedLoadingStatus = true;
            if (detail && typeof detail === "object" && detail.viewerMeta) {
                payload.viewerMeta = detail.viewerMeta;
            } else if (typeof detail === "string" && detail.length > 0) {
                payload.message = detail;
            }
            dbg("updateLoadingStatus(true)", payload);
            this.$emit("loading", payload);
        },
        infoLog(message, reset) {
            this.info = message;
            if (reset) {
                setTimeout(() => {
                    this.info = "";
                }, 5000);
            }
        },
        resetStudy() {
            this.resetInProgress = true;
            this.nmriumLoadError = null;
            this.pendingResetFromUrls = true;
            this.updateLoadingStatus(true, {
                viewerMeta: {
                    phase: "spectra",
                    sampleLabel: this.nmriumSampleLabel,
                },
            });
            this.startNmriumLoadTimeout();
            this.bumpNmriumIframeSrc();
        },
        showVersions() {
            this.versionsElement.toggleVersions();
        },
    },
};
</script>
