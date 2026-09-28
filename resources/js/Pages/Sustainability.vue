<template>
    <div class="flex min-h-screen flex-col bg-white">
        <Head title="Sustainability - nmrXiv" />
        <FlashMessages />
        <main class="flex-1">
            <PublicSiteHeader />

            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl">
                    <p
                        class="text-sm font-semibold uppercase tracking-wide text-teal-700"
                    >
                        Long-term access
                    </p>
                    <h1
                        class="mt-2 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl"
                    >
                        Sustainability
                    </h1>
                    <p class="mt-4 text-lg text-gray-600">
                        nmrXiv is built so published NMR data remain usable
                        beyond the life of any single database, application, or
                        hosting arrangement. We invest in packaging, integrity,
                        and open tooling that keep deposits self-describing and
                        verifiable — starting with BagIt, and extending over
                        time to further preservation measures.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <Link
                            :href="route('bagit-viewer')"
                            class="inline-flex items-center rounded-lg bg-teal-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-teal-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500"
                        >
                            Offline BagIt Viewer
                        </Link>
                        <a
                            href="https://datatracker.ietf.org/doc/html/rfc8493"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50"
                        >
                            RFC 8493
                        </a>
                    </div>
                </div>

                <div
                    class="mx-auto mt-16 max-w-3xl space-y-14 text-base leading-7 text-gray-700"
                >
                    <section>
                        <h2
                            class="text-2xl font-bold tracking-tight text-gray-900"
                        >
                            Data beyond the platform
                        </h2>
                        <p class="mt-4">
                            On nmrXiv, a study’s meaning is split between
                            curated records in the database and files in object
                            storage — instrument folders, spectra, licences, and
                            analysis state. That split works well for day-to-day
                            deposit and discovery. For long-term sustainability
                            it is not enough: if the application or schema
                            changes, or the service itself is no longer
                            operated, the scientific record must still be
                            understandable without nmrXiv’s internals.
                        </p>
                        <p class="mt-4">
                            Our approach is therefore to materialise published
                            versions as portable packages that travel with their
                            own metadata and checksums, so holdings can leave
                            the live system while remaining verifiable and
                            citable.
                        </p>
                    </section>

                    <section>
                        <h2
                            class="text-2xl font-bold tracking-tight text-gray-900"
                        >
                            BagIt as a first measure
                        </h2>
                        <p class="mt-4">
                            BagIt (RFC 8493) is a deliberately simple packaging
                            convention: a
                            <code class="rounded bg-gray-100 px-1">data/</code>
                            payload, checksum manifests, a tagmanifest, and
                            <code class="rounded bg-gray-100 px-1"
                                >bag-info.txt</code
                            >
                            for key–value metadata. For nmrXiv it is the first
                            concrete step toward platform-independent
                            preservation — not a claim that packaging alone
                            makes data FAIR, but a foundation for integrity,
                            hand-off, and offline reuse.
                        </p>
                        <ul class="mt-6 space-y-5">
                            <li>
                                <h3 class="font-semibold text-gray-900">
                                    Self-describing deposits
                                </h3>
                                <p class="mt-1">
                                    A bag per published version can bundle
                                    spectra and related files with licence,
                                    provenance, and analysis state so the
                                    archive is interpretable from the filesystem
                                    alone.
                                </p>
                            </li>
                            <li>
                                <h3 class="font-semibold text-gray-900">
                                    Fixity and trust
                                </h3>
                                <p class="mt-1">
                                    Per-file checksums support verification of
                                    downloads, scheduled integrity checks, and
                                    evidence that the files behind a DOI match
                                    what was deposited.
                                </p>
                            </li>
                            <li>
                                <h3 class="font-semibold text-gray-900">
                                    Exit and migration
                                </h3>
                                <p class="mt-1">
                                    Bags are a practical hand-off format for
                                    institutional archives, NFDI4Chem
                                    infrastructure, or a future storage backend
                                    — without requiring the receiving side to
                                    understand nmrXiv’s schema.
                                </p>
                            </li>
                            <li>
                                <h3 class="font-semibold text-gray-900">
                                    Versioned citation
                                </h3>
                                <p class="mt-1">
                                    Immutable bags align with DOI versioning:
                                    each release can remain intact and citable
                                    while later corrections are issued as new
                                    versions.
                                </p>
                            </li>
                        </ul>
                    </section>

                    <section>
                        <h2
                            class="text-2xl font-bold tracking-tight text-gray-900"
                        >
                            Looking ahead
                        </h2>
                        <p class="mt-4">
                            BagIt is a starting point. Further measures — richer
                            machine-actionable metadata (for example RO-Crate
                            and Bioschemas), systematic fixity programmes, and
                            clearer pathways to long-term repositories — will
                            build on the same principle: the scientific record
                            should outlast any one stack.
                        </p>
                    </section>

                    <section
                        class="rounded-2xl border border-teal-100 bg-teal-50/60 px-6 py-8"
                    >
                        <h2
                            class="text-xl font-bold tracking-tight text-gray-900"
                        >
                            Our commitment
                        </h2>
                        <p class="mt-3 text-gray-700">
                            nmrXiv treats sustainability as continuity of the
                            scientific record: deposits must remain
                            interpretable and trustworthy even when the
                            platform, database, or storage stack changes. We
                            will keep expanding the measures that make that
                            possible — always putting data first and the
                            application second.
                        </p>
                        <p class="mt-4">
                            <Link
                                :href="route('bagit-viewer')"
                                class="font-semibold text-teal-800 underline hover:text-teal-900"
                            >
                                Download the offline BagIt Viewer
                            </Link>
                            to open and validate bags on your own computer.
                        </p>
                    </section>
                </div>
            </div>
        </main>

        <Footer />
    </div>
</template>

<script>
import { Head, Link } from "@inertiajs/vue3";
import FlashMessages from "@/Shared/FlashMessages.vue";
import PublicSiteHeader from "@/Shared/PublicSiteHeader.vue";
import Footer from "@/Shared/Footer.vue";

export default {
    components: {
        Head,
        Link,
        FlashMessages,
        PublicSiteHeader,
        Footer,
    },
};
</script>
