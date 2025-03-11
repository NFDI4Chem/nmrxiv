<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    'channels' => [
        'larabug' => [
            'driver' => 'larabug',
        ],
    ],

];
