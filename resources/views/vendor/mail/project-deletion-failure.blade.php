@component('mail::message')

Dear admin,

Deletion failed for the below project.

{{ __('Project Name - **:project**', ['project' => $projectName]) }}
{{ __('Project ID - **:projectId**', ['projectId' => $projectId]) }}

Regards,

The nmrXiv Team

@component('mail::button', ['url' =>  $url, 'color' => 'red'])
Check logs
@endcomponent

@endcomponent