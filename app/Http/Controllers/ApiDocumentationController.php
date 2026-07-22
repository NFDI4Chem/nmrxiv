<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class ApiDocumentationController extends Controller
{
    public function openApiSpec(): Response
    {
        foreach ([
            storage_path('api-docs/api-docs.json'),
            public_path('api-docs.json'),
        ] as $path) {
            if (is_readable($path)) {
                return response(
                    file_get_contents($path),
                    200,
                    ['Content-Type' => 'application/json'],
                );
            }
        }

        abort(404, 'OpenAPI documentation file not found.');
    }
}
