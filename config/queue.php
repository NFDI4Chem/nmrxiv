<?php

use PhpAmqpLib\Connection\AMQPLazyConnection;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Jobs\RabbitMQJob;

return [

    'connections' => [

        /*
        |----------------------------------------------------------------------
        | Redis Queue Connection
        |----------------------------------------------------------------------
        |
        | Override the framework default `retry_after` (90s) so long-running
        | archive jobs (ZipStream over many large NMR datasets) are not
        | re-reserved by another worker mid-flight, which would otherwise cause
        | a `MaxAttemptsExceededException` once the original attempt finishes.
        |
        | `retry_after` MUST exceed both the Horizon supervisor `timeout`
        | (currently 9000s) and the realistic worst-case runtime of any single
        | archive job. The framework default is far too low for this app.
        */
        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_QUEUE_CONNECTION', 'default'),
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => (int) env('REDIS_QUEUE_RETRY_AFTER', 14400),
            'block_for' => null,
            'after_commit' => false,
        ],

        'rabbitmq' => [

            'driver' => 'rabbitmq',
            'queue' => env('RABBITMQ_QUEUE', 'default'),
            'connection' => AMQPLazyConnection::class,

            'hosts' => [
                [
                    'host' => env('RABBITMQ_HOST', '127.0.0.1'),
                    'port' => env('RABBITMQ_PORT', 5672),
                    'user' => env('RABBITMQ_USER', 'guest'),
                    'password' => env('RABBITMQ_PASSWORD', 'guest'),
                    'vhost' => env('RABBITMQ_VHOST', '/'),
                ],
            ],

            'options' => [
                'ssl_options' => [
                    'cafile' => env('RABBITMQ_SSL_CAFILE', null),
                    'local_cert' => env('RABBITMQ_SSL_LOCALCERT', null),
                    'local_key' => env('RABBITMQ_SSL_LOCALKEY', null),
                    'verify_peer' => env('RABBITMQ_SSL_VERIFY_PEER', true),
                    'passphrase' => env('RABBITMQ_SSL_PASSPHRASE', null),
                ],
                'queue' => [
                    'job' => RabbitMQJob::class,
                ],
            ],

            /*
             * Set to "horizon" if you wish to use Laravel Horizon.
             */
            'worker' => env('RABBITMQ_WORKER', 'default'),

        ],
    ],

];
