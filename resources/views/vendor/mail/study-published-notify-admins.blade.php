@component('mail::message')

Dear admin,

New samples have been published and are now publicly available on nmrXiv.

@foreach ($samples as $sample)
- **Sample Name:** {{ $sample->name }}
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