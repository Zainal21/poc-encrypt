<?php

namespace App\Http\Controllers;

use App\Models\PersonalDataAsymmetric;
use App\Models\PersonalDataSymmetric;
use App\Services\PayloadCryptoService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Throwable;

class PersonalDataController extends Controller
{
    public function __construct(private PayloadCryptoService $svc) {}

    #[OA\Post(path: '/api/personal-data/symmetric', tags: ['PersonalData'], summary: 'Simpan personal data (symmetric). Payload terenkripsi: key & content.')]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(required: ['key','content'], properties: [
        new OA\Property(property: 'key', type: 'string', description: 'AES key (base64 32B) terenkripsi RSA dengan server public key'),
        new OA\Property(property: 'content', type: 'string', description: 'JSON personal data terenkripsi AES-256-CBC dengan key tersebut (base64 iv+cipher)'),
    ]))]
    #[OA\Response(response: 201, description: 'Stored')]
    public function storeSymmetric(Request $request)
    {
        $data = $request->validate(['key'=>'required|string','content'=>'required|string']);
        try {
            $aesKey = $this->svc->rsaDecryptServer($data['key']);
            $plain = $this->svc->aesDecrypt($data['content'], $aesKey);
            $p = $this->validatedPayload($plain);
            $enc = $this->encryptFieldsSymmetric($p, $aesKey);
            $row = PersonalDataSymmetric::create($enc + ['raw_payload'=>$enc]);
            return response()->json(['success'=>true,'message'=>'Stored (symmetric)','data'=>['id'=>$row->id]], 201);
        } catch (Throwable $e) {
            return response()->json(['success'=>false,'message'=>'Validation/decrypt failed: '.$e->getMessage()], 422);
        }
    }

    #[OA\Post(path: '/api/personal-data/asymmetric', tags: ['PersonalData'], summary: 'Simpan personal data (asymmetric hybrid). Payload terenkripsi: key & content.')]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(required: ['key','content'], properties: [
        new OA\Property(property: 'key', type: 'string', description: 'key_id terdaftar terenkripsi RSA dengan server public key (transport)'),
        new OA\Property(property: 'content', type: 'string', description: 'JSON {key, content}: AES key dienkripsi RSA public key client, data dienkripsi AES key tsb'),
    ]))]
    #[OA\Response(response: 201, description: 'Stored')]
    public function storeAsymmetric(Request $request)
    {
        $data = $request->validate(['key'=>'required|string','content'=>'required|string']);
        try {
            $keyId = trim($this->svc->rsaDecryptServer($data['key']));
            $reg = $this->svc->getRegisteredKey($keyId);
            $privPath = $reg->private_key_path ? storage_path('app/'.$reg->private_key_path) : null;
            if (!$privPath || !is_file($privPath)) throw new \RuntimeException('Private key not available for decryption simulation');
            $privPem = file_get_contents($privPath);
            $hybrid = json_decode($data['content'], true);
            if (!is_array($hybrid) || !isset($hybrid['key'], $hybrid['content'])) throw new \RuntimeException('Invalid asymmetric content envelope');
            $aesKey = $this->svc->rsaDecryptWithPrivate($hybrid['key'], $privPem);
            $plain = $this->svc->aesDecrypt($hybrid['content'], $aesKey);
            $p = $this->validatedPayload($plain);
            $enc = $this->encryptFieldsAsymmetric($p, file_get_contents(storage_path('app/'.$reg->public_key_path)));
            $row = PersonalDataAsymmetric::create($enc + [
                'registered_key_id'=>$reg->id,'key_id'=>$reg->key_id,'raw_payload'=>$enc
            ]);
            return response()->json(['success'=>true,'message'=>'Stored (asymmetric)','data'=>['id'=>$row->id,'key_id'=>$reg->key_id]], 201);
        } catch (Throwable $e) {
            return response()->json(['success'=>false,'message'=>'Validation/decrypt failed: '.$e->getMessage()], 422);
        }
    }

    #[OA\Get(path: '/api/personal-data/symmetric', tags: ['PersonalData'], summary: 'List personal data symmetric (ciphertext)')]
    #[OA\Response(response: 200, description: 'OK')]
    public function indexSymmetric()
    {
        $rows = PersonalDataSymmetric::latest()->get()->map(fn($r)=>$this->outSym($r));
        return response()->json(['success'=>true,'data'=>$rows]);
    }

    #[OA\Get(path: '/api/personal-data/asymmetric', tags: ['PersonalData'], summary: 'List personal data asymmetric (decrypted untuk demo, butuh private key tersimpan)')]
    #[OA\Response(response: 200, description: 'OK')]
    public function indexAsymmetric()
    {
        $rows = PersonalDataAsymmetric::with('registeredKey')->latest()->get()->map(fn($r)=>$this->outAsym($r));
        return response()->json(['success'=>true,'data'=>$rows]);
    }

    private function validatedPayload(string $json): array
    {
        $p = json_decode($json, true);
        if (!is_array($p)) throw new \RuntimeException('Content must be JSON object');
        $p = array_map(fn($v) => is_string($v) ? trim($v) : $v, $p);
        foreach (['full_name','nik','email','phone','birth_date','address'] as $f) {
            if (!isset($p[$f]) || $p[$f]==='') throw new \RuntimeException("Missing field: $f");
        }
        if (!preg_match('/^\d{8,16}$/', (string)$p['nik'])) throw new \RuntimeException('Invalid nik: must be 8-16 digits (received '.strlen((string)$p['nik']).' chars)');
        return $p;
    }

    private function encryptFieldsSymmetric(array $p, string $keyB64): array
    {
        $e = [];
        foreach (['full_name','email','phone','address'] as $f) $e[$f] = $this->svc->aesEncrypt((string)$p[$f], $keyB64);
        $e['nik'] = (string)$p['nik'];
        $e['birth_date'] = (string)$p['birth_date'];
        return $e;
    }

    private function encryptFieldsAsymmetric(array $p, string $pubPem): array
    {
        $e = [];
        foreach (['full_name','email','phone','address'] as $f) $e[$f] = $this->svc->rsaEncryptWithPublic((string)$p[$f], $pubPem);
        $e['nik'] = (string)$p['nik'];
        $e['birth_date'] = (string)$p['birth_date'];
        return $e;
    }

    private function outSym($r): array
    {
        return ['id'=>$r->id,'nik'=>$r->nik,'birth_date'=>$r->birth_date,'created_at'=>$r->created_at,'encrypted'=>['full_name'=>$r->full_name,'email'=>$r->email,'phone'=>$r->phone,'address'=>$r->address]];
    }
    private function outAsym($r): array
    {
        $dec = ['full_name'=>null,'email'=>null,'phone'=>null,'address'=>null];
        try {
            $privPath = $r->registeredKey?->private_key_path ? storage_path('app/'.$r->registeredKey->private_key_path) : null;
            if ($privPath && is_file($privPath)) {
                $priv = file_get_contents($privPath);
                foreach ($dec as $k=>$_) $dec[$k] = $this->svc->rsaDecryptWithPrivate($r->$k, $priv);
            }
        } catch (Throwable $e) {}
        return ['id'=>$r->id,'key_id'=>$r->key_id,'nik'=>$r->nik,'birth_date'=>$r->birth_date,'created_at'=>$r->created_at,'decrypted'=>$dec];
    }
}
