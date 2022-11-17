<!DOCTYPE html>
<html  class="h-full bg-gray-100" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'nmrXiv') }}</title>
        <script src="https://go-echarts.github.io/go-echarts-assets/assets/echarts.min.js"></script>
        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles / Scripts -->
        @vite(['resources/js/app.js'])

        @routes

        @env ('production')
            <!-- Matomo -->
            <script>
            var _paq = window._paq = window._paq || [];
            /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
            _paq.push(['trackPageView']);
            _paq.push(['enableLinkTracking']);
            (function() {
                var u="//matomo.nmrxiv.org/";
                _paq.push(['setTrackerUrl', u+'matomo.php']);
                _paq.push(['setSiteId', '2']);
                var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
                g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
            })();
            </script>
            <!-- End Matomo Code -->
        @endenv

    </head>
    <body class="font-sans antialiased h-full">
        @env (['development', 'local'])
        <div
            class="z-20 fixed bottom-0 bg-yellow-300 border-b w-screen border-black-800"
        >
            <div class="max-w-7xl mx-auto py-1 px-6 text-center">
              <small><b>DEMO SITE WARNING</b>: Please be aware that this is a demo/test server for nmrXiv and don't upload or save any sensitive data.</small>
            </div>
        </div>
        @endenv

        @inertia

        @env ('local')
            <script src="http://localhost:3000/browser-sync/browser-sync-client.js"></script>
        @endenv

        <x-support-bubble />
    </body>
</html>
