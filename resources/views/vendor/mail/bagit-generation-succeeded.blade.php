@component('mail::message')

Dear nmrXiv user,

Your BagIt archive for sample **{{ $study->name }}** is ready.

**Study details**
- Study ID: {{ $study->id }}
- Study Identifier: {{ $study->identifier ?? 'Not assigned' }}
- Public sample page: {{ $publicUrl }}
- Archive download: {{ $archiveUrl ?? 'Not available yet' }}

@if($archiveUrl)
@component('mail::button', ['url' => $archiveUrl, 'color' => 'green'])
Download BagIt Archive
@endcomponent
@endif

You can also view the public sample page here:

@component('mail::button', ['url' => $sampleUrl, 'color' => 'primary'])
View Sample
@endcomponent

Regards,

The nmrXiv Team

@endcomponent
