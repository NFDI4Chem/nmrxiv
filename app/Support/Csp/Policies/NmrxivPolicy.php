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
        // NOTE: combine img-src values into a single add call to avoid
        // multiple `add` calls for the same directive being merged in an
        // unexpected way by the package implementation. All other asset
        // directives can be added normally.
        $policy
            ->add(Directive::SCRIPT, Keyword::SELF)
            ->add(Directive::STYLE, Keyword::SELF)
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
        // Add image sources using chained add() calls for better compatibility
        $policy
            ->add(Directive::IMG, Keyword::SELF)
            ->add(Directive::IMG, 'data:')
            ->add(Directive::IMG, 'blob:')
            ->add(Directive::IMG, 'https://www.uni-jena.de')              // University of Jena resources
            ->add(Directive::IMG, 'https://s3.uni-jena.de')               // S3 storage for user-uploaded content
            ->add(Directive::IMG, 'https://orcid.org')                    // ORCID logos
            ->add(Directive::IMG, 'https://ui-avatars.com')               // UI Avatars service for profile pictures
            ->add(Directive::IMG, 'https://www.nfdi4chem.de')             // NFDI4Chem logos and assets
            ->add(Directive::IMG, 'https://www.nmrium.org')               // NMRium branding assets
            ->add(Directive::IMG, 'https://upload.wikimedia.org')         // Wikipedia/Wikimedia images (ChEBI logo, etc.)
            ->add(Directive::IMG, 'https://pbs.twimg.com');                // Twitter profile images

        // Connection sources - External APIs for data retrieval and services
        // Using chained add() calls for better compatibility
        $policy
            // DOI and metadata APIs (configurable via env)
            ->add(Directive::CONNECT, env('DATACITE_API', 'https://api.datacite.org'))
            ->add(Directive::CONNECT, env('CROSSREF_API', 'https://api.crossref.org/works/'))
            ->add(Directive::CONNECT, env('DATACITE_ENDPOINT', 'https://api.datacite.org'))
            
            // Chemical data and research APIs (using env config where possible)
            ->add(Directive::CONNECT, env('NMRKIT_URL', 'https://nodejs.nmrxiv.org'))
            ->add(Directive::CONNECT, config('services.pubchem.base_url'))
            ->add(Directive::CONNECT, config('services.cas.base_url'))
            ->add(Directive::CONNECT, config('services.chemistry_standardize.url'))
            ->add(Directive::CONNECT, env('EUROPEMC_WS_API', 'https://www.ebi.ac.uk/europepmc/webservices/rest/search'))
            ->add(Directive::CONNECT, env('ORCID_ID_SEARCH_API', 'https://pub.orcid.org/v2.1/search'))
            ->add(Directive::CONNECT, config('services.chemotion_tracker.base_url'));

        // Font sources - External font services
        $policy
            ->add(Directive::FONT, 'https://fonts.googleapis.com')
            ->add(Directive::FONT, 'https://fonts.gstatic.com');
    }
}
