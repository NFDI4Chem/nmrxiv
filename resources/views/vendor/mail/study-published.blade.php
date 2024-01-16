@component('mail::message')
Dear nmrXiv user,

@if($releaseToday)
We have processed your submission and your sample(s) has been published successfully.

Please find the details below.

@foreach ($samples as $sample)
    Sample Name- {{ $sample->name }} , DOI- {{ $sample->doi }}
@endforeach

@else
{{ __('Your submission is processed and is published as Embargo and your sample(s) would be made public on chosen release date :releaseDate', ['releaseDate' => $releaseDate]) }}

You will recieve an email confirmation with further details once your sample has been made public successfully.

Please Note: Opting for an Embargo publication grants your sample a DOI, yet it stays private exclusively for you. You have the option to share the sample with others and can adjust the release date or promptly make it public through the project's dashboard view.
@endif

Follow our [documentation](https://docs.nmrxiv.org/) to learn more.

If you do not recognise this action contact us at {{env('MAIL_FROM_ADDRESS')}}.

Regards,

The nmrXiv Team

@component('mail::button', ['url' =>  $url, 'color' => 'green'])
View Samples
@endcomponent

@endcomponent
