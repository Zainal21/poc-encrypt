<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SimulationController extends Controller
{
    private function api()
    {
        return Http::baseUrl(rtrim(config('services.encryption_api.url', env('ENCRYPTION_API_URL','http://127.0.0.1:8001')), '/'))->acceptJson()->timeout(15);
    }
    private function serverPub(): string
    {
        $r = $this->api()->get('/api/crypto/server-public-key')->json();
        $k = $r['data']['public_key'] ?? null;
        if (!$k) throw new RuntimeException('Cannot fetch server public key');
        return $k;
    }
    private function rsaEnc(string $plain, string $pubPem): string
    {
        $out=''; if(!openssl_public_encrypt($plain,$out,$pubPem)) throw new RuntimeException('RSA encrypt failed'); return base64_encode($out);
    }
    private function aesEncrypt(string $plain, string $keyB64): string
    {
        $key = base64_decode($keyB64, true); if($key===false||strlen($key)!==32) throw new RuntimeException('Invalid AES key');
        $iv=random_bytes(16); $c=openssl_encrypt($plain,'aes-256-cbc',$key,OPENSSL_RAW_DATA,$iv);
        if($c===false) throw new RuntimeException('AES encrypt failed'); return base64_encode($iv.$c);
    }
    private function clientPublicKey(): string
    {
        $path = config('crypto.preset_public_key_path');
        if (!is_file($path)) throw new RuntimeException('Preset client public key missing: '.$path);
        return file_get_contents($path);
    }

    public function home(){ return view('home'); }

    public function symmetric()
    {
        return view('symmetric', ['result'=>session('result')]);
    }
    public function symmetricSend(Request $r){
        $r->validate(['full_name'=>'required','nik'=>'required','email'=>'required','phone'=>'required','birth_date'=>'required','address'=>'required']);
        $aesKey = (string) config('crypto.preset_aes_key');
        if(!$aesKey) return back()->with('result',['success'=>false,'message'=>'POC_AES_KEY preset belum diset']);
        $contentPlain = json_encode($this->cleanInput($r));
        $payload = [
            'key' => $this->rsaEnc($aesKey, $this->serverPub()),
            'content' => $this->aesEncrypt($contentPlain, $aesKey),
        ];
        $res = $this->api()->post('/api/personal-data/symmetric', $payload);
        return back()->with('result',$res->json() ?: ['success'=>false,'message'=>'API error'])->with('sent_payload',$payload);
    }

    public function asymmetric()
    {
        return view('asymmetric', [
            'key_id' => config('crypto.preset_key_id'),
            'fingerprint' => is_file(config('crypto.preset_public_key_path')) ? substr(hash('sha256', file_get_contents(config('crypto.preset_public_key_path'))),0,32) : null,
            'result'=>session('result'),
        ]);
    }
    public function asymmetricSend(Request $r){
        $r->validate(['full_name'=>'required','nik'=>'required','email'=>'required','phone'=>'required','birth_date'=>'required','address'=>'required']);
        $keyId = (string) config('crypto.preset_key_id');
        $pub = $this->clientPublicKey();
        $contentPlain = json_encode($this->cleanInput($r));
        $aesKey = base64_encode(random_bytes(32));
        $contentEnc = json_encode([
            'key' => $this->rsaEnc($aesKey, $pub),
            'content' => $this->aesEncrypt($contentPlain, $aesKey),
        ]);
        $payload = [
            'key' => $this->rsaEnc($keyId, $this->serverPub()),
            'content' => $contentEnc,
        ];
        $res = $this->api()->post('/api/personal-data/asymmetric', $payload);
        return back()->with('result',$res->json() ?: ['success'=>false,'message'=>'API error'])->with('sent_payload',$payload);
    }

    private function cleanInput(Request $r): array
    {
        return array_map(fn($v) => is_string($v) ? trim($v) : $v, $r->only('full_name','nik','email','phone','birth_date','address'));
    }

    public function data()
    {
        $sym = $this->api()->get('/api/personal-data/symmetric')->json('data') ?? [];
        $asym = $this->api()->get('/api/personal-data/asymmetric')->json('data') ?? [];
        return view('data', ['symmetric'=>$sym,'asymmetric'=>$asym,'result'=>session('result')]);
    }
}
