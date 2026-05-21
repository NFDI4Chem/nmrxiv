@component('mail::message')
Dear nmrXiv user,

Your submission has been processed and your sample(s) are now published on nmrXiv.

**Sample details**

@foreach ($samples as $sample)
- **Sample Name:** {{ $sample->name }}
- **DOI:** {{ $sample->doi }}
@endforeach

Follow our [documentation](https://docs.nmrxiv.org/) to learn more.

If you do not recognize this action, please contact us at {{ config('mail.from.address') }}.

Regards,

The nmrXiv Team

@component('mail::button', ['url' =>  $url, 'color' => 'green'])
View Samples
@endcomponent

@endcomponent
