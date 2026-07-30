<!DOCTYPE html>
<html  class="h-full bg-gray-100" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'nmrXiv') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @php
            $externalOrigins = [];
            foreach ([config('external-links.nmrium_url'), config('external-links.nmrkit_url')] as $externalHref) {
                $parsed = is_string($externalHref) ? parse_url($externalHref) : false;
                if (! is_array($parsed) || ! isset($parsed['host'])) {
                    continue;
                }

                $scheme = $parsed['scheme'] ?? 'https';
                $externalOrigins[$scheme.'://'.$parsed['host']] = true;
            }
            $nmriumOrigin = null;
            $nmriumParsed = is_string(config('external-links.nmrium_url')) ? parse_url(config('external-links.nmrium_url')) : false;
            if (is_array($nmriumParsed) && isset($nmriumParsed['host'])) {
                $nmriumOrigin = ($nmriumParsed['scheme'] ?? 'https').'://'.$nmriumParsed['host'];
            }
        @endphp
        @if ($nmriumOrigin)
            <link rel="preconnect" href="{{ $nmriumOrigin }}" crossorigin>
        @endif
        @foreach (array_keys($externalOrigins) as $externalOrigin)
            <link rel="dns-prefetch" href="{{ $externalOrigin }}">
        @endforeach

        <!-- Styles / Scripts -->
        @vite(['resources/js/app.js'])

        @routes()
        
        @env ('production')
            <!-- Matomo -->

<script>	
            var _paq = window._paq = window._paq || [];
            /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
            _paq.push(['trackPageView']);
            _paq.push(['enableLinkTracking']);
            (function() {
                var u="https://matomo.nfdi4chem.de/";
                _paq.push(['setTrackerUrl', u+'matomo.php']);
                _paq.push(['setSiteId', '1']);
                var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
                g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
            })();
            </script>
            <!-- End Matomo Code -->
        @endenv

    </head>
    <body class="font-sans antialiased h-full">
        <!-- @env (['development', 'local'])
        <div
            class="z-20 fixed bottom-0 bg-yellow-300 border-b w-screen border-black-800"
        >
            <div class="max-w-7xl mx-auto py-1 px-6 text-center">
              <small><b>DEMO SITE WARNING</b>: Please be aware that this is a demo/test server for nmrXiv and don't upload or save any sensitive data. For real data please visit <a href="https://nmrxiv.org" target="_blank" rel="noopener noreferrer" class="text-blue-600">nmrxiv.org.</a></small>
            </div>
        </div>
        @endenv -->
        
        @inertia

        @if (app()->environment('local') && config('app.browser_sync_client_url'))
            <script src="{{ rtrim((string) config('app.browser_sync_client_url'), '/') }}/browser-sync/browser-sync-client.js"></script>
        @endif

        <x-support-bubble />
    </body>
</html>
