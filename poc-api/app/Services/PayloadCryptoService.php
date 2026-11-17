<?php

namespace App\Services;

use App\Models\RegisteredKey;
use Illuminate\Support\Str;
use RuntimeException;

class PayloadCryptoService
{
    private function presetPublicPath(): string
    {
        return storage_path('app/'.config('crypto.preset.public_path', 'keys/registered/public/preset-demo.pem'));
    }

    private function presetPrivatePath(): string
    {
        return storage_path('app/'.config('crypto.preset.private_path', 'keys/registered/private/preset-demo.pem'));
    }

    public function serverPublicKey(): string
    {
        $path = $this->presetPublicPath();
        if (!is_file($path)) throw new RuntimeException('Preset public key missing: '.$path);
        return file_get_contents($path) ?: throw new RuntimeException('Server public key unavailable');
    }

    public function rsaDecryptServer(string $b64): string
    {
        $path = $this->presetPrivatePath();
        if (!is_file($path)) throw new RuntimeException('Preset private key missing: '.$path);
        $priv = file_get_contents($path);
        $raw = base64_decode($b64, true);
        if ($raw === false) throw new RuntimeException('Invalid base64 encrypted value');
        $out = '';
        if (!openssl_private_decrypt($raw, $out, $priv)) throw new RuntimeException('RSA decrypt failed (transport)');
        return $out;
    }

    public function rsaEncryptWithPublic(string $plain, string $pubPem): string
    {
        $out = '';
        if (!openssl_public_encrypt($plain, $out, $pubPem)) throw new RuntimeException('RSA encrypt failed');
        return base64_encode($out);
    }

    public function rsaDecryptWithPrivate(string $b64, string $privPem): string
    {
        $raw = base64_decode($b64, true);
        if ($raw === false) throw new RuntimeException('Invalid base64 ciphertext');
        $out = '';
        if (!openssl_private_decrypt($raw, $out, $privPem)) throw new RuntimeException('RSA decrypt failed');
        return $out;
    }

    public function aesEncrypt(string $plain, string $keyB64): string
    {
        $key = base64_decode($keyB64, true);
        if ($key === false || strlen($key) !== 32) throw new RuntimeException('AES key must be base64 32 bytes');
        $iv = random_bytes(16);
        $cipher = openssl_encrypt($plain, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        if ($cipher === false) throw new RuntimeException('AES encrypt failed');
        return base64_encode($iv.$cipher);
    }

    public function aesDecrypt(string $payloadB64, string $keyB64): string
    {
        $key = base64_decode($keyB64, true);
        $raw = base64_decode($payloadB64, true);
        if ($key === false || strlen($key) !== 32 || $raw === false || strlen($raw) < 17) throw new RuntimeException('Invalid AES payload/key');
        $iv = substr($raw, 0, 16); $cipher = substr($raw, 16);
        $plain = openssl_decrypt($cipher, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        if ($plain === false) throw new RuntimeException('AES decrypt failed');
        return $plain;
    }

    public function registerPublicKey(string $name, string $publicKey, ?string $privateKey = null): RegisteredKey
    {
        if (!openssl_pkey_get_public($publicKey)) throw new RuntimeException('Invalid public key');
        $keyId = Str::random(16);
        $dirPub = storage_path('app/keys/registered/public'); $dirPriv = storage_path('app/keys/registered/private');
        if (!is_dir($dirPub)) mkdir($dirPub, 0755, true);
        if (!is_dir($dirPriv)) mkdir($dirPriv, 0700, true);
        $pubPath = "keys/registered/public/$keyId.pem";
        $privPath = $privateKey ? "keys/registered/private/$keyId.pem" : null;
        file_put_contents(storage_path("app/$pubPath"), $publicKey);
        if ($privateKey) { file_put_contents(storage_path("app/$privPath"), $privateKey); @chmod(storage_path("app/$privPath"), 0600); }
        return RegisteredKey::create([
            'key_id'=>$keyId,'name'=>$name ?: "key-$keyId",'algorithm'=>'RSA-2048',
            'public_key_path'=>$pubPath,'private_key_path'=>$privPath,
            'fingerprint'=>substr(hash('sha256', $publicKey), 0, 32),'is_active'=>true,
        ]);
    }

    public function getRegisteredKey(string $keyId): RegisteredKey
    {
        $k = RegisteredKey::where('key_id', $keyId)->where('is_active', true)->first();
        if (!$k) {
            $preset = config('crypto.preset', []);
            if (($preset['key_id'] ?? null) === $keyId) $k = $this->ensurePresetKey();
        }
        if (!$k) throw new RuntimeException('Registered key not found');
        $k->forceFill(['last_used_at'=>now()])->save();
        return $k;
    }

    public function ensurePresetKey(): RegisteredKey
    {
        $preset = config('crypto.preset', []);
        $keyId = $preset['key_id'] ?? 'preset-demo';
        $pubRel = $preset['public_path'] ?? 'keys/registered/public/preset-demo.pem';
        $privRel = $preset['private_path'] ?? 'keys/registered/private/preset-demo.pem';
        $pubPath = storage_path('app/'.$pubRel);
        $privPath = storage_path('app/'.$privRel);
        if (!is_file($pubPath) || !is_file($privPath)) {
            throw new RuntimeException('Preset key files are missing. Add storage/app/keys/registered/{public,private}/preset-demo.pem');
        }
        $public = file_get_contents($pubPath);
        return RegisteredKey::firstOrCreate(
            ['key_id' => $keyId],
            [
                'name' => $preset['name'] ?? 'Preset Demo Key',
                'algorithm' => 'RSA-2048',
                'public_key_path' => $pubRel,
                'private_key_path' => $privRel,
                'fingerprint' => substr(hash('sha256', $public), 0, 32),
                'is_active' => true,
            ]
        );
    }
}
