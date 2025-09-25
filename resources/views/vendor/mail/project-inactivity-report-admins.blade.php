@component('mail::message')

Dear Admin,

Below is the current list of inactive projects which has past inactivity threshold of {{ $thresholdMonths }} month{{ $thresholdMonths == 1 ? '' : 's' }}:

@if(empty($projects))
No inactive projects at this time.
@else
@component('mail::table')
| ID | Name | Owner | Last Updated |
|:--:|:-----|:------|:-------------|
@foreach($projects as $p)
| {{ $p['id'] }} | {{ $p['name'] }} | {{ $p['owner'] }} | {{ $p['updated_at'] }} |
@endforeach
@endcomponent
@endif

Regards,

The nmrXiv Team

@endcomponent
