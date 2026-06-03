@component('mail::message')

Dear admin,

@if($releasedToday)
A project has been published and is now publicly available on nmrXiv.

**Project details**

- **Project Name:** {{ $projectName }}
- **Project ID:** {{ $projectId }}

@else
A project has been processed and scheduled for public release under embargo.

**Project details**

- **Project Name:** {{ $projectName }}
- **Project ID:** {{ $projectId }}
@if(!empty($releaseDate))
- **Scheduled Release Date:** {{ $releaseDate }}
@endif

@endif

Regards,

The nmrXiv Team

@component('mail::button', ['url' =>  $url, 'color' => 'green'])
View Project
@endcomponent

@component('mail::subcopy')
You are receiving this email because you are on the nmrXiv admin list.
@endcomponent

@endcomponent