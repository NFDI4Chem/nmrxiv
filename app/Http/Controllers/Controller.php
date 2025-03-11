<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="nmrXiv"
 * )
 *
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     in="header",
 *     securityScheme="token",
 *     name="Authorization"
 * )
 */
abstract class Controller extends BaseController
{
}
