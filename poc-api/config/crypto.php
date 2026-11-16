<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Symmetric encryption keys (AES-256-CBC + HMAC-SHA256)
    |--------------------------------------------------------------------------
    |
    | Both keys MUST be base64-encoded 32-byte random values.
    | Generate with: php -r "echo base64_encode(random_bytes(32));"
    | Keep identical values in poc-api and poc-client .env files.
    */
    'aes_key' => base64_decode(env('CRYPTO_AES_KEY', '')),
    'hmac_key' => base64_decode(env('CRYPTO_HMAC_KEY', '')),

    /*
    |--------------------------------------------------------------------------
    | Asymmetric (hybrid RSA-OAEP + AES-256-CBC)
    |--------------------------------------------------------------------------
    */
    'rsa_bits' => (int) env('CRYPTO_RSA_BITS', 2048),

    /*
    |--------------------------------------------------------------------------
    | API token used by simple token middleware
    |--------------------------------------------------------------------------
    */
    'api_token' => env('POC_API_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Key storage paths
    |--------------------------------------------------------------------------
    */
    'keys_path' => storage_path('app/keys'),
    'private_path' => storage_path('app/keys/private'),
    'public_path' => storage_path('app/keys/public'),

];
