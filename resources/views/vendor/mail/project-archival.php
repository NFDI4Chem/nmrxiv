@component('mail::message')

Dear nmrXiv user,

{{ __('Your project - :project has been archived successfully.', ['project' => $projectName]) }}

A warning will now be displayed on your project, indicating that the project is no longer maintained.

You may unarchive your project if you would like to. To unarchive your project:
Visit the project page (link).
Select the project you want to unarchive, and click Project settings > Unarchive.
In the confirmation dialogue, click confirm.

For further questions, please visit our documentation site or contact us at info@nmrxiv.org.

Regards,
The nmrXiv Team

@component('mail::button', ['url' =>  $url, 'color' => 'green'])
Unarchive Project
@endcomponent

@endcomponent