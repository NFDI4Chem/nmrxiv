@component('mail::message')

Dear nmrXiv user,

@if(!empty($digest))
We found the following projects inactive for {{ $thresholdMonths ?? 6 }} month{{ ($thresholdMonths ?? 6) == 1 ? '' : 's' }} or more:

@component('mail::table')
| ID | Name | Last Updated | Link |
|:--:|:-----|:-------------|:-----|
@foreach($projects as $p)
| {{ $p['id'] }} | {{ $p['name'] }} | {{ $p['updated_at'] }} | [Open]({{ $p['url'] }}) |
@endforeach
@endcomponent

@else
{{ __('Your project - **:project** has had no activity since :lastUpdated.', ['project' => $projectName, 'lastUpdated' => $lastUpdated]) }}

@component('mail::button', ['url' =>  $url, 'color' => 'green'])
Review Project
@endcomponent
@endif

Please review your project(s) and take any necessary action to keep them up to date. You can update metadata, upload new data, or make changes as needed.

@component('mail::panel')
⚠️ Important: These project(s) are marked as Inactive. If no updates are made within the next {{ $thresholdMonths ?? 6 }} month{{ ($thresholdMonths ?? 6) == 1 ? '' : 's' }}, they will be automatically deleted by our system.

To avoid deletion, you can:
- Update and publish the project (if it is ready), or
- Make any update to the project to remove the Inactive tag

If you are no longer working on a project, you can delete it yourself from the Project Settings tab. If no action is taken, the project will be deleted automatically after the period above.
@endcomponent

Follow our [documentation](https://docs.nmrxiv.org/) to learn more.

If you do not recognize this notification or need help, contact us at {{env('MAIL_FROM_ADDRESS')}}.

Regards,

The nmrXiv Team

@endcomponent
