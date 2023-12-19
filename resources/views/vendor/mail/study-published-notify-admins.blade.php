@component('mail::message')

Dear admin,

New samples has been published. Please find the details below.

@foreach ($samples as $sample)
    Sample Name- {{ $sample->name }} , DOI- {{ $sample->doi }}
@endforeach

Regards,

The nmrXiv Team

@component('mail::button', ['url' =>  $url, 'color' => 'green'])
View Samples
@endcomponent

@component('mail::subcopy')
You are recieving this mail because you are part of the admin list in nmrXiv.
@endcomponent

@endcomponent