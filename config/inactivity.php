<?php

return [
    // Inactivity grace period in months before notifications and potential actions
    'grace_months' => (int) env('INACTIVITY_GRACE_MONTHS', 6),
];
