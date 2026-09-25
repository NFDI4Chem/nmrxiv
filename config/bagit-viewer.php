<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Offline BagIt viewer HTML path
    |--------------------------------------------------------------------------
    |
    | Absolute path to the single-file viewer produced by
    | `npm run build:bagit-viewer`. Tests may override this to a fixture.
    |
    */

    'html_path' => env(
        'BAGIT_VIEWER_HTML_PATH',
        resource_path('bagit-viewer/dist/nmrxiv-bagit-viewer.html')
    ),

];
