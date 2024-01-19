@component('mail::message')

Dear admin,

@if($releasedToday)
A new project has been published. Please find the details below.

{{ __('**Project Name**:') }}
{{ __(':project', ['project' => $projectName]) }}

{{ __('**Project Id**:') }}
{{ __(':projectId', ['projectId' => $projectId]) }}

@else
A new project has been published as Embargo. Please find the details below.

{{ __('**Project Name**:') }}
{{ __(':project', ['project' => $projectName]) }}

{{ __('**Project Id**:') }}
{{ __(':projectId', ['projectId' => $projectId]) }}

@endif

Regards,

The nmrXiv Team

@component('mail::button', ['url' =>  $url, 'color' => 'green'])
View Project
@endcomponent

@component('mail::subcopy')
You are recieving this mail because you are part of the admin list in nmrXiv.
@endcomponent

@endcomponent