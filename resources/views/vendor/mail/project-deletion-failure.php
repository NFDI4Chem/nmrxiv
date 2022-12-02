@component('mail::message')

Dear admin,

{{ __('Project deletion failed for project name - :project project id - :projectId.', ['project' => $projectName, 'projectId' => $projectId]) }}

Regards,
The nmrXiv Team

@component('mail::button', ['url' =>  $url, 'color' => 'red'])
Check logs
@endcomponent

@endcomponent