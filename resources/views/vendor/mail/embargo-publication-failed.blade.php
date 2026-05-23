@component('mail::message')

Dear {{ $admin ? 'admin' : 'nmrXiv user' }},

The embargo release for **{{ $project->name }}** could not be completed automatically.

**Reason**

{{ $reason }}

**Project details**
- Project ID: {{ $project->id }}
- Project Identifier: {{ $project->identifier ?? 'Not assigned' }}
- Release date: {{ optional($project->release_date)->toDateString() ?? 'Not set' }}
- Status: {{ $project->status }}

@if(count($validationFailures) > 0)
**Required items to complete**

@foreach($validationFailures as $failure)
- {{ $failure }}
@endforeach
@endif

@if($admin && $exceptionClass)
**Technical details**
- Exception: {{ $exceptionClass }}
@endif

Please review the project and complete the missing information before trying again.

If you need help, please contact us at {{ env('MAIL_FROM_ADDRESS') }}.

Regards,

The nmrXiv Team

@component('mail::button', ['url' => $url, 'color' => 'primary'])
View Project
@endcomponent

@endcomponent
