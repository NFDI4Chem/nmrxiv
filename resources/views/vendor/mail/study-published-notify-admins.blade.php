@component('mail::message')

Dear admin,

New samples have been published and are now publicly available on nmrXiv.

@foreach ($samples as $sample)
- **Sample Name:** {{ $sample->name }}
@if (filled(data_get($sample, 'public_url')))
- **Link:** [{{ data_get($sample, 'public_url') }}]({{ data_get($sample, 'public_url') }})
@endif
- **DOI:** {{ $sample->doi }}
@endforeach

Regards,

The nmrXiv Team

@component('mail::button', ['url' =>  $url, 'color' => 'green'])
View Samples
@endcomponent

@component('mail::subcopy')
You are receiving this email because you are on the nmrXiv admin list.
@endcomponent

@endcomponent