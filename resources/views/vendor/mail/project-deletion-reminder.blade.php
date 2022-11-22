@component('mail::message')

Dear nmrXiv user,

{{ __('Your project - :project which is in trash, will be deleted permanently soon.', ['project' => $projectName]) }}

If you do not want to get it deleted permanently then please restore your project by clicking on restore option under project settings tab.

Click on the button below to get more details.

@component('mail::button', ['url' =>  $url, 'color' => 'green'])
View Project
@endcomponent

@endcomponent