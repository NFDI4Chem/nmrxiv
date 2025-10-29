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

        // Development server support (for local development with Vite)
        $policy
            ->add(Directive::SCRIPT, 'http://localhost:5173')
            ->add(Directive::CONNECT, 'ws://localhost:5173', 'http://localhost:5173');
    }

    /**
     * Add nmrXiv-specific external sources that the application needs
     */
    private function addNmrxivSources(Policy $policy): void
    {
        // University of Jena resources (for logos and institutional content)
        $policy->add(Directive::IMG, 'https://www.uni-jena.de');

        // S3 storage for user-uploaded content and static assets
        $policy->add(Directive::IMG, 'https://s3.uni-jena.de');

        // Web fonts from various providers
        $policy->add(Directive::FONT, 'https://fonts.googleapis.com', 'https://fonts.gstatic.com');
    }
}
