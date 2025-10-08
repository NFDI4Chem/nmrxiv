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

        // Environment-specific rules
        if (app()->environment(['local', 'development'])) {
            // Development: Allow Vite HMR and inline styles/scripts
            $policy
                ->add(Directive::SCRIPT, 'http://localhost:5173', Keyword::UNSAFE_EVAL, Keyword::UNSAFE_INLINE)
                ->add(Directive::STYLE, Keyword::UNSAFE_INLINE)
                ->add(Directive::CONNECT, 'ws://localhost:5173', 'http://localhost:5173');
        } else {
            // Production: Use nonces for enhanced security
            $policy
                ->addNonce(Directive::SCRIPT)
                ->addNonce(Directive::STYLE);
        }
    }
}
