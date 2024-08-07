@component('mail::message')
{{ __('You have been invited to join the project - **:project** (role: :role) by :invitedBy.', ['project' => $invitation->project->name, 'invitedBy' => $invitation->invited_by,'role' => $invitation->role]) }}

@if($invitation->message)
{{ __('Message:') }}  
{{ __(':message', ['message' => $invitation->message]) }}
@endif

@if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::registration()))
{{ __('If you do not have an account, you may create one by clicking the button below. After creating an account, you may click the invitation acceptance button in this email to accept the project invitation:') }}

@component('mail::button', ['url' => route('register')])
{{ __('Create Account') }}
@endcomponent

{{ __('If you already have an account, you may accept this invitation by clicking the button below:') }}

@else
{{ __('You may accept this invitation by clicking the button below:') }}
@endif


@component('mail::button', ['url' => $acceptUrl])
{{ __('Accept Invitation') }}
@endcomponent

{{ __('If you did not expect to receive an invitation to this project, you may discard this email.') }}

Regards,

The nmrXiv Team
@endcomponent