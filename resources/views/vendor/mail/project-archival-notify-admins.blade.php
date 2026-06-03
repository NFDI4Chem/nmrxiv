@component('mail::message')

Dear admin,

The below project has been archived.

{{ __('**Project Name**:') }}
{{ __(':project', ['project' => $projectName]) }}

{{ __('**Project Id**:') }}
{{ __(':projectId', ['projectId' => $projectId]) }}

Regards,

The nmrXiv Team

@component('mail::button', ['url' =>  $url, 'color' => 'green'])
View Project
@endcomponent

@component('mail::subcopy')
You are receiving this email because you are on the nmrXiv admin list.
@endcomponent

@endcomponent