@component('mail::message')
Dear nmrXiv user,

@if($releasedToday)
Your submission has been processed and your project is now published on nmrXiv.

**Project details**

- **Project Identifier:** {{ $project->identifier }}
- **Project Name:** {{ $project->name }}
- **Project Description:** {{ $project->description }}
- **DOI:** {{ $project->doi }}
- **Public URL:** {{ $publicUrl }}

Get citations to your project in various formats [here]({{ $url }}).

@else
Your submission has been processed and your project has been scheduled for public release under embargo.

**Project details**

- **Project Name:** {{ $project->name }}
- **Scheduled Release Date:** {{ $releaseDate }}
- **DOI:** {{ $project->doi }}

Until the release date, your project remains private to you and collaborators you invite. You can change the release date or publish immediately from your project dashboard.

You will receive a confirmation email once your project has been made public.
@endif

Follow our [documentation](https://docs.nmrxiv.org/) to learn more.

If you do not recognize this action, please contact us at {{ config('mail.from.address') }}.

Regards,

The nmrXiv Team

@component('mail::button', ['url' =>  $url, 'color' => 'green'])
View Project
@endcomponent

@endcomponent

