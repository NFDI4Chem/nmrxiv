@component('mail::message')
Dear nmrXiv user,

@if($releasedToday)
We have processed your submission and your project has been published successfully.

Please find the details below.

{{ __('**Project Identifier**:') }}  
{{ __(':projectIdentifier', ['projectIdentifier' => $project->identifier]) }}

{{ __('**Project Name**:') }}  
{{ __(':projectName', ['projectName' => $project->name]) }}

{{ __('**Project Description**:') }}  
{{ __(':projectDesc', ['projectDesc' => $project->description]) }}

{{ __('**DOI**:') }}  
{{ __(':doi', ['doi' => $project->doi]) }}

{{ __('**Citation**:') }}  
{{ __('Get citations to your project in various formats [here](:url)', ['url' =>  $url]) }}

{{ __('**Public URL**:') }}  
{{ __(':publicUrl', ['publicUrl' => $publicUrl]) }}

@else
{{ __('Your submission is processed and is published as Embargo and your project **:projectName** would be made public on chosen release date :releaseDate', ['projectName' => $project->name , 'releaseDate' => $releaseDate]) }}

You will recieve an email confirmation with further details once your project has been made public successfully.

Please Note: Opting for an Embargo publication grants your project a DOI, yet it stays private exclusively for you. You have the option to share the project with others and can adjust the release date or promptly make it public through the project's dashboard view.
@endif

Follow our [documentation](https://docs.nmrxiv.org/) to learn more.

If you do not recognise this action contact us at {{env('MAIL_FROM_ADDRESS')}}.

Regards,

The nmrXiv Team

@component('mail::button', ['url' =>  $url, 'color' => 'green'])
View Project
@endcomponent

@endcomponent

