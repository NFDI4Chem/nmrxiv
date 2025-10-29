<?php

namespace App\Support\Csp\Policies;

use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;
use Spatie\Csp\Policy;
use Spatie\Csp\Preset;

class NmrxivPolicy implements Preset
{
    public function configure(Policy $policy): void
    {
        // Core security directives
        $policy
            ->add(Directive::BASE, Keyword::SELF)
            ->add(Directive::DEFAULT, Keyword::SELF)
            ->add(Directive::FORM_ACTION, Keyword::SELF)
            ->add(Directive::OBJECT, Keyword::NONE);

        // Asset sources
        $policy
            ->add(Directive::SCRIPT, Keyword::SELF)
            ->add(Directive::STYLE, Keyword::SELF)
            ->add(Directive::IMG, Keyword::SELF, 'data:', 'blob:')
            ->add(Directive::FONT, 'data:', 'https://fonts.bunny.net')
            ->add(Directive::CONNECT, Keyword::SELF);

        // Third-party services (nmrXiv specific)
        $policy
            ->add(Directive::STYLE, 'https://fonts.bunny.net')
            ->add(Directive::SCRIPT, 'https://matomo.nfdi4chem.de')
            ->add(Directive::CONNECT, 'https://matomo.nfdi4chem.de', 'https://fonts.bunny.net');

        // Add nmrXiv-specific external sources
        $this->addNmrxivSources($policy);

        // Unified rules for all environments
        $this->addUnifiedRules($policy);
    }

    /**
     * Add unified CSP rules that apply to all environments
     */
    private function addUnifiedRules(Policy $policy): void
    {
        // Allow inline scripts and styles (needed for the application to function)
        $policy
            ->add(Directive::SCRIPT, Keyword::UNSAFE_INLINE, Keyword::UNSAFE_EVAL)
            ->add(Directive::STYLE, Keyword::UNSAFE_INLINE);

        // Development server support (for local development with Vite and Browser-sync)
        $policy
            ->add(Directive::SCRIPT, 'http://localhost:5173', 'http://localhost:3000')
            ->add(Directive::CONNECT, 'ws://localhost:5173', 'http://localhost:5173', 'http://localhost:3000');
    }

    /**
     * Add nmrXiv-specific external sources that the application needs
     *
     * This method includes core external sources that are essential for the application.
     * For runtime-configurable sources without code deployment, use the global
     * directives in config/csp.php with environment variables:
     * - CSP_ADDITIONAL_CONNECT_SRC
     * - CSP_ADDITIONAL_IMG_SRC
     * - CSP_ADDITIONAL_SCRIPT_SRC
     * - CSP_ADDITIONAL_STYLE_SRC
     */
    private function addNmrxivSources(Policy $policy): void
    {
        // Image sources - External domains for logos, avatars, and institutional content
        $policy->add(Directive::IMG,
            'https://www.uni-jena.de',              // University of Jena resources
            'https://s3.uni-jena.de',               // S3 storage for user-uploaded content
            'https://orcid.org',                    // ORCID logos
            'https://ui-avatars.com',               // UI Avatars service for profile pictures
            'https://www.nfdi4chem.de',             // NFDI4Chem logos and assets
            'https://www.nmrium.org',               // NMRium branding assets
            'https://upload.wikimedia.org',         // Wikipedia/Wikimedia images (ChEBI logo, etc.)
            'https://pbs.twimg.com'                 // Twitter profile images
        );

        // Connection sources - External APIs for data retrieval and services
        $policy->add(Directive::CONNECT,
            // DOI and metadata APIs (configurable via env)
            env('DATACITE_API', 'https://api.datacite.org'),
            env('CROSSREF_API', 'https://api.crossref.org/works/'),
            env('DATACITE_ENDPOINT', 'https://api.datacite.org'),

            // Chemical data and research APIs (using env config where possible)
            env('NMRKIT_URL', 'https://nodejs.nmrxiv.org'),
            config('services.pubchem.base_url'),
            config('services.cas.base_url'),
            config('services.chemistry_standardize.url'),
            env('EUROPEMC_WS_API', 'https://www.ebi.ac.uk/europepmc/webservices/rest/search'),
            env('ORCID_ID_SEARCH_API', 'https://pub.orcid.org/v2.1/search'),
            config('services.chemotion_tracker.base_url')
        );

        // Font sources - External font services
        $policy->add(Directive::FONT,
            'https://fonts.googleapis.com',
            'https://fonts.gstatic.com'
        );
    }
}
