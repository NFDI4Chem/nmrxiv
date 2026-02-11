<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cool Off Period
    |--------------------------------------------------------------------------
    |
    | The number of days a project remains in draft status before being
    | automatically deleted. This applies to projects marked for deletion.
    |
    */

    'cool_off_period' => (int) env('COOL_OFF_PERIOD', 30),

];
