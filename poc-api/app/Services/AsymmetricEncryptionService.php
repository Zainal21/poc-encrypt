<?php

namespace App\Services;

use RuntimeException;

class AsymmetricEncryptionService
{
    private string $dir;
    private string $publicPath;
    private string $privatePath;

    public function __construct()
    {
        $this->dir = storage_path('app/keys');
        $this->publicPath = $this->dir . '/public.pem';
        $this->privatePath = $this->dir . '/private.pem';
    }

    public function generateKeys(): void
    {
        if (!is_dir($this->dir)) {
            mkdir($this->dir, 0700, true);
        }

        $res = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        if ($res === false) {
            throw new RuntimeException('Key generation failed');
        }

        openssl_pkey_export($res, $privatePem);
        $details = openssl_pkey_get_details($res);
        if ($details === false || !isset($details['key'])) {
            throw new RuntimeException('Public key export failed');
        }

        file_put_contents($this->privatePath, $privatePem);
        file_put_contents($this->publicPath, $details['key']);
        @chmod($this->privatePath, 0600);
        @chmod($this->publicPath, 0644);
    }

    public function encrypt(string $plaintext): string
    {
        $public = $this->readKey($this->publicPath);
        $encrypted = '';
        if (!openssl_public_encrypt($plaintext, $encrypted, $public)) {
            throw new RuntimeException('Encryption failed');
        }
        return base64_encode($encrypted);
    }

    public function decrypt(string $ciphertext): string
    {
        $private = $this->readKey($this->privatePath);
        $raw = base64_decode($ciphertext, true);
        if ($raw === false) {
            throw new RuntimeException('Invalid base64 ciphertext');
        }
        $decrypted = '';
        if (!openssl_private_decrypt($raw, $decrypted, $private)) {
            throw new RuntimeException('Decryption failed');
        }
        return $decrypted;
    }

    private function readKey(string $path): string
    {
        if (!is_file($path)) {
            throw new RuntimeException('Key not found. Generate keys first.');
        }
        $key = file_get_contents($path);
        if ($key === false || $key === '') {
            throw new RuntimeException('Key unreadable');
        }
        return $key;
    }
}
