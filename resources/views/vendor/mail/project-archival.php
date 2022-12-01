@component('mail::message')

Dear nmrXiv user,

Your project archived..

You can always restore your project anytime before the cool-off period. 
To restore:
Go to your Trash section click on Project >> Project Settings >> Restore Project,  or click on the button below.


If you do not recognize this action please report to us at info@nmrxiv.org.

@component('mail::button', ['url' =>  $url, 'color' => 'green'])
Restore Project
@endcomponent

@endcomponent