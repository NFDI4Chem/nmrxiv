<?php

namespace App\Http\Controllers;


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
abstract class Controller
{
}
