<?php

return [
    // Preset dari awal project (tidak digenerate lewat UI client)
    'preset_aes_key' => env('POC_AES_KEY', 'oJbFgDDAmhIS0x9dlHE7OVyTfMBiR07sDt1hv6QdqOc='),
    'preset_key_id' => env('POC_PRESET_KEY_ID', 'preset-demo'),
    'preset_public_key_path' => env('POC_PRESET_PUBLIC_KEY_PATH', storage_path('app/keys/preset-public.pem')),
    'preset_private_key_path' => env('POC_PRESET_PRIVATE_KEY_PATH', storage_path('app/keys/preset-private.pem')),
];
