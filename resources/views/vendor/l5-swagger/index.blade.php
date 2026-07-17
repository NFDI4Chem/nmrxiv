<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('l5-swagger.documentations.'.$documentation.'.api.title') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}">
    <style>
        :root {
            --scalar-y-offset: 3.5rem;
            --nmrxiv-docs-header-height: 3.5rem;
        }

        body {
            margin: 0;
        }

        .nmrxiv-docs-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            height: var(--nmrxiv-docs-header-height);
            padding: 0 1rem;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
        }

        .nmrxiv-docs-header__brand {
            display: inline-flex;
            align-items: center;
            line-height: 0;
            text-decoration: none;
        }

        .nmrxiv-docs-header__logo {
            display: block;
            height: 2rem;
            width: auto;
        }
    </style>
</head>
<body>
<header class="nmrxiv-docs-header">
    <a class="nmrxiv-docs-header__brand" href="{{ url('/') }}" title="nmrXiv">
        <img
            class="nmrxiv-docs-header__logo"
            src="{{ asset('img/logo.svg') }}"
            alt="nmrXiv"
            width="140"
            height="37"
        >
    </a>
</header>

<div id="app"></div>

<script src="https://cdn.jsdelivr.net/npm/@scalar/api-reference@1.62.9"></script>
<script>
    Scalar.createApiReference('#app', {
        url: @json(route('api.documentation.openapi', [], $useAbsolutePath)),
        favicon: @json(asset('img/logo.svg')),
        persistAuth: @json((bool) config('l5-swagger.defaults.ui.authorization.persist_authorization', false)),
        defaultOpenAllTags: @json(config('l5-swagger.defaults.ui.display.doc_expansion', 'none') === 'full'),
        hideSearch: @json(! config('l5-swagger.defaults.ui.display.filter', true)),
        @if (! empty($operationsSorter))
        operationsSorter: @json($operationsSorter),
        @endif
        onBeforeRequest: ({ request }) => {
            request.headers.set('X-CSRF-TOKEN', @json(csrf_token()));
        },
    });
</script>
</body>
</html>
