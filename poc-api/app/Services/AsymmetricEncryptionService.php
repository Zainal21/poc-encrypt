<?php

namespace App\Services;

use App\Models\RegisteredKey;
use Illuminate\Support\Str;
use RuntimeException;

class AsymmetricEncryptionService
{
    private string $dir;

    public function __construct()
    {
        $this->dir = storage_path('app/keys');
    }

    public function generateKeys(?string $name = null): RegisteredKey
    {
        $pair = $this->makePair();
        $this->writeDefault($pair['public'], $pair['private']);
        return $this->registerKey($pair['public'], $pair['private'], $name ?: 'generated-'.now()->format('YmdHis'));
    }

    public function registerPublicKey(string $publicKey, ?string $privateKey = null, ?string $name = null): RegisteredKey
    {
        if (!openssl_pkey_get_public($publicKey)) throw new RuntimeException('Invalid public key');
        if ($privateKey && !openssl_pkey_get_private($privateKey)) throw new RuntimeException('Invalid private key');
        return $this->registerKey($publicKey, $privateKey, $name ?: 'registered-'.now()->format('YmdHis'));
    }

    public function encrypt(string $plaintext, ?string $keyId = null): string
    {
        $public = $keyId ? $this->readKey($this->pathFor($keyId, 'public')) : $this->readKey($this->dir.'/public.pem');
        $encrypted = '';
        if (!openssl_public_encrypt($plaintext, $encrypted, $public)) throw new RuntimeException('Encryption failed');
        return base64_encode($encrypted);
    }

    public function decrypt(string $ciphertext, ?string $keyId = null): string
    {
        $private = $keyId ? $this->readKey($this->pathFor($keyId, 'private')) : $this->readKey($this->dir.'/private.pem');
        $raw = base64_decode($ciphertext, true);
        if ($raw === false) throw new RuntimeException('Invalid base64 ciphertext');
        $decrypted = '';
        if (!openssl_private_decrypt($raw, $decrypted, $private)) throw new RuntimeException('Decryption failed');
        return $decrypted;
    }

    public function getRegisteredKey(string $keyId): ?RegisteredKey
    {
        return RegisteredKey::where('key_id', $keyId)->first();
    }

    private function makePair(): array
    {
        $res = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        if ($res === false) throw new RuntimeException('Key generation failed');
        openssl_pkey_export($res, $privatePem);
        $details = openssl_pkey_get_details($res);
        if ($details === false || !isset($details['key'])) throw new RuntimeException('Public key export failed');
        return ['public' => $details['key'], 'private' => $privatePem];
    }

    private function writeDefault(string $public, string $private): void
    {
        if (!is_dir($this->dir)) mkdir($this->dir, 0700, true);
        file_put_contents($this->dir.'/public.pem', $public);
        file_put_contents($this->dir.'/private.pem', $private);
        @chmod($this->dir.'/private.pem', 0600);
    }

    private function registerKey(string $public, ?string $private, string $name): RegisteredKey
    {
        $keyId = Str::random(16);
        $pubDir = $this->dir.'/public'; $privDir = $this->dir.'/private';
        if (!is_dir($pubDir)) mkdir($pubDir, 0755, true);
        if (!is_dir($privDir)) mkdir($privDir, 0700, true);
        $pubPath = "keys/public/{$keyId}.pem";
        $privPath = $private ? "keys/private/{$keyId}.pem" : null;
        file_put_contents(storage_path("app/{$pubPath}"), $public);
        if ($private) { file_put_contents(storage_path("app/{$privPath}"), $private); @chmod(storage_path("app/{$privPath}"), 0600); }
        return RegisteredKey::create([
            'key_id' => $keyId,
            'name' => $name,
            'algorithm' => 'RSA-2048',
            'public_key_path' => $pubPath,
            'private_key_path' => $privPath,
            'fingerprint' => substr(hash('sha256', $public), 0, 32),
            'is_active' => true,
        ]);
    }

    private function pathFor(string $keyId, string $type): string
    {
        $key = $this->getRegisteredKey($keyId);
        if (!$key) throw new RuntimeException('Registered key not found');
        $rel = $type === 'public' ? $key->public_key_path : $key->private_key_path;
        if (!$rel) throw new RuntimeException(ucfirst($type).' key not available for this key_id');
        $path = storage_path("app/{$rel}");
        $key->forceFill(['last_used_at' => now()])->save();
        return $path;
    }

    private function readKey(string $path): string
    {
        if (!is_file($path)) throw new RuntimeException('Key not found. Generate/register keys first.');
        $key = file_get_contents($path);
        if ($key === false || $key === '') throw new RuntimeException('Key unreadable');
        return $key;
    }
}
