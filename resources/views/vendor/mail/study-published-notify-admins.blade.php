@component('mail::message')

Dear admin,

@if($releasedToday)
New samples has been published. Please find the details below.

@foreach ($samples as $sample)
    Sample Name- {{ $sample->name }} , DOI- {{ $sample->doi }}
@endforeach

@else
New samples has been published as Embargo. Please find the details below.

@foreach ($samples as $sample)
    Sample Name- {{ $sample->name }} , DOI- {{ $sample->doi }}
@endforeach
@endif

Regards,

The nmrXiv Team

@component('mail::button', ['url' =>  $url, 'color' => 'green'])
View Samples
@endcomponent

@component('mail::subcopy')
You are recieving this mail because you are part of the admin list in nmrXiv.
@endcomponent

@endcomponent