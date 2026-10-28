@component('mail::message')

Dear {{ $admin ? 'admin' : 'nmrXiv user' }},

@if($admin)
A {{ $eln }} submission could not be completed in nmrXiv.
@else
Unfortunately your {{ $eln }} submission could not be completed in nmrXiv.
@endif

- {{ $eln }} submission ID: {{ $draft->external_id }}
- Step that failed: {{ $step }}
@if($admin)
- Draft ID: {{ $draft->id }}
@if($error)
- Error: {{ $error }}
@endif
@endif

@if($admin)
You are receiving this email because you are on the nmrXiv admin list.
@else
Please send an email to the nmrXiv helpdesk at info.nmrxiv@uni-jena.de for further assistance and mention the submission ID above.
@endif

Regards,

The nmrXiv Team

@endcomponent
