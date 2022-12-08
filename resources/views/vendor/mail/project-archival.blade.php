@component('mail::message')

Dear nmrXiv user,

{{ __('Your project - **:project** has been archived successfully.', ['project' => $projectName]) }}

A warning will now be displayed on your project, indicating that the project is no longer maintained.

To unarchive, go to project, click Project settings > Unarchive Project.
Or click on the button below.

Follow our [documentation](https://docs.nmrxiv.org/) to learn more.

If you do not recognize this action, contact us at info@nmrxiv.org.

Regards,

The nmrXiv Team

@component('mail::button', ['url' =>  $url, 'color' => 'green'])
Unarchive Project
@endcomponent

@endcomponent