@component('mail::message')

Dear nmrXiv user,

{{ __('Your project - :project has been moved to trash successfully on :deletedOn. It will stay in trash until the cool-off period of 30 days after which it will be deleted permanently.', ['project' => $projectName, 'deletedOn' => $deletedOn]) }}

You can always restore your project anytime before the cool-off period. 
To restore:
Go to your Trash section click on Project >> Project Settings >> Restore Project,  or click on the button below.


If you do not recognize this action please report to us at info@nmrxiv.org.

@component('mail::button', ['url' =>  $url, 'color' => 'green'])
Restore Project
@endcomponent

@endcomponent