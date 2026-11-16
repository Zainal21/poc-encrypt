<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Encryption API',
    description: 'POC Symmetric (AES-256) & Asymmetric (RSA 2048) encryption with personal data storage'
)]
#[OA\Server(url: 'http://127.0.0.1:8001', description: 'Local dev')]
abstract class Controller
{
}
