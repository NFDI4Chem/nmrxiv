@component('mail::message')

Dear admin,

The BagIt metadata extraction job failed for sample **{{ $study->name }}**.

**Study details**
- Study ID: {{ $study->id }}
- Study Identifier: {{ $study->identifier ?? 'Not assigned' }}
- Status: {{ $study->metadata_bagit_generation_status ?? 'failed' }}
- Attempts: {{ $attempts }}
- Public URL: {{ $study->public_url ?? 'Not available' }}

**Error message**
{{ $reason }}

**Exception trace**
```text
{{ $exception->getTraceAsString() }}
```

@component('mail::button', ['url' => $url, 'color' => 'red'])
View Study
@endcomponent

You are receiving this email because you are on the nmrXiv admin list.

Regards,

The nmrXiv Team

@endcomponent
