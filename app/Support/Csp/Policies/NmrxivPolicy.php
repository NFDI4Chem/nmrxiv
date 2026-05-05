<?php

namespace App\Support\Csp\Policies;

use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;
use Spatie\Csp\Policy;
use Spatie\Csp\Preset;

class NmrxivPolicy implements Preset
{
    /**
     * Localhost and 127.0.0.1 HTTP/HTTPS hosts for dev server support.
     */
    private const LOCAL_HOSTS = [
        'http://localhost:*',
        'https://localhost:*',
        'http://127.0.0.1:*',
        'https://127.0.0.1:*',
    ];

    /**
     * Localhost and 127.0.0.1 WebSocket hosts for dev server support.
     */
    private const LOCAL_WS_HOSTS = [
        'ws://localhost:*',
        'wss://localhost:*',
        'ws://127.0.0.1:*',
        'wss://127.0.0.1:*',
    ];

    public function configure(Policy $policy): void
    {
        // Core security directives
        $policy
            ->add(Directive::BASE, Keyword::SELF)
            ->add(Directive::DEFAULT, Keyword::SELF)
            ->add(Directive::FORM_ACTION, Keyword::SELF)
            ->add(Directive::OBJECT, Keyword::NONE);

        // Basic asset sources
        $policy
            ->add(Directive::SCRIPT, Keyword::SELF)
            ->add(Directive::STYLE, Keyword::SELF)
            ->add(Directive::FONT, 'data:')
            ->add(Directive::CONNECT, Keyword::SELF);

        // Third-party services
        $policy
            ->add(Directive::STYLE, 'https://fonts.bunny.net')
            ->add(Directive::SCRIPT, 'https://matomo.nfdi4chem.de')
            ->add(Directive::CONNECT, 'https://matomo.nfdi4chem.de', 'https://fonts.bunny.net');

        // Add nmrXiv-specific external sources
        $this->addNmrxivSources($policy);

        // Unified rules for all environments
        $this->addUnifiedRules($policy);
    }

    private function addUnifiedRules(Policy $policy): void
    {
        // Allow unsafe-inline and unsafe-eval for maximum compatibility
        // This is acceptable when you control all inline scripts and have other security layers
        $policy
            ->add(Directive::SCRIPT, Keyword::UNSAFE_INLINE)
            ->add(Directive::SCRIPT, Keyword::UNSAFE_EVAL)
            ->add(Directive::STYLE, Keyword::UNSAFE_INLINE);

        // Development server support (for local development with Vite)
        $policy
            ->add(Directive::SCRIPT, self::LOCAL_HOSTS)
            ->add(Directive::STYLE, self::LOCAL_HOSTS)
            ->add(Directive::CONNECT, array_merge(self::LOCAL_WS_HOSTS, self::LOCAL_HOSTS));
    }

    /**
     * Add nmrXiv-specific external sources.
     * For runtime-configurable sources, use config/csp.php with CSP_ADDITIONAL_* env variables.
     */
    private function addNmrxivSources(Policy $policy): void
    {
        // Image sources
        $policy
            ->add(Directive::IMG, Keyword::SELF)
            ->add(Directive::IMG, 'data:')
            ->add(Directive::IMG, 'blob:')
            ->add(Directive::IMG, 'https://s3.uni-jena.de')
            ->add(Directive::IMG, 'https://orcid.org')
            ->add(Directive::IMG, 'https://ui-avatars.com')
            ->add(Directive::IMG, 'https://www.nfdi4chem.de')
            ->add(Directive::IMG, 'https://www.nmrium.org')
            ->add(Directive::IMG, 'https://nmriumdev.nmrxiv.org')
            ->add(Directive::IMG, 'https://upload.wikimedia.org')
            ->add(Directive::IMG, 'https://pbs.twimg.com')
            ->add(Directive::IMG, 'https://api.cheminf.studio')
            ->add(Directive::IMG, 'https://api.naturalproducts.net')
            ->add(Directive::IMG, 'https://dev.api.naturalproducts.net')
            ->add(Directive::IMG, 'https://placehold.co');

        // Connection sources
        $policy
            ->add(Directive::CONNECT, config('external-links.datacite_api'))
            ->add(Directive::CONNECT, config('external-links.crossref_api'))
            ->add(Directive::CONNECT, config('doi.datacite.endpoint'))
            ->add(Directive::CONNECT, config('external-links.nmrkit_url'))
            ->add(Directive::CONNECT, config('services.pubchem.base_url'))
            ->add(Directive::CONNECT, config('services.cas.base_url'))
            ->add(Directive::CONNECT, config('services.chemistry_standardize.url'))
            ->add(Directive::CONNECT, config('external-links.europemc_ws_api'))
            ->add(Directive::CONNECT, config('services.chemotion_tracker.base_url'))
            ->add(Directive::CONNECT, config('external-links.cm_api'))
            ->add(Directive::CONNECT, config('filesystems.disks.ceph.endpoint'))
            ->add(Directive::CONNECT, 'https://nmrium.nmrxiv.org')
            ->add(Directive::CONNECT, 'https://nmriumdev.nmrxiv.org')
            ->add(Directive::CONNECT, 'https://api.cheminf.studio')
            ->add(Directive::CONNECT, 'https://api.naturalproducts.net')
            ->add(Directive::CONNECT, 'https://dev.api.naturalproducts.net');

        // Font sources
        $policy
            ->add(Directive::FONT, 'https://fonts.googleapis.com')
            ->add(Directive::FONT, 'https://fonts.gstatic.com')
            ->add(Directive::FONT, 'https://fonts.bunny.net');

        // Frame sources
        $policy
            ->add(Directive::FRAME, Keyword::SELF)
            ->add(Directive::FRAME, 'https://api.cheminf.studio')
            ->add(Directive::FRAME, 'https://api.naturalproducts.net')
            ->add(Directive::FRAME, 'https://dev.api.naturalproducts.net')
            ->add(Directive::FRAME, 'https://nmrium.nmrxiv.org')
            ->add(Directive::FRAME, 'https://nmriumdev.nmrxiv.org');
    }
}
