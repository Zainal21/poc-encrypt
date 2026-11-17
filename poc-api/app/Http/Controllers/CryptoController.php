<?php

namespace App\Http\Controllers;

use App\Services\PayloadCryptoService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Throwable;

class CryptoController extends Controller
{
    public function __construct(private PayloadCryptoService $svc) {}

    #[OA\Get(path: '/api/crypto/server-public-key', tags: ['Crypto'], summary: 'Ambil public key server (untuk enkripsi transport key/content dari client)')]
    #[OA\Response(response: 200, description: 'OK')]
    public function serverPublicKey()
    {
        try {
            return response()->json(['success'=>true,'data'=>['public_key'=>$this->svc->serverPublicKey()]]);
        } catch (Throwable $e) {
            return response()->json(['success'=>false,'message'=>$e->getMessage()], 500);
        }
    }

    #[OA\Post(path: '/api/crypto/keys/register', tags: ['Crypto'], summary: 'Daftarkan public key (asymmetric) milik client ke API')]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(required: ['name','public_key'], properties: [
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'public_key', type: 'string', description: 'PEM public key'),
        new OA\Property(property: 'private_key', type: 'string', nullable: true, description: 'Opsional (demo simulasi decrypt di API)'),
    ]))]
    #[OA\Response(response: 201, description: 'Registered')]
    public function registerKey(Request $request)
    {
        $data = $request->validate(['name'=>'required|string','public_key'=>'required|string','private_key'=>'nullable|string']);
        try {
            $k = $this->svc->registerPublicKey($data['name'], $data['public_key'], $data['private_key'] ?? null);
            return response()->json(['success'=>true,'message'=>'Public key registered','data'=>[
                'key_id'=>$k->key_id,'name'=>$k->name,'fingerprint'=>$k->fingerprint,'algorithm'=>$k->algorithm
            ]], 201);
        } catch (Throwable $e) {
            return response()->json(['success'=>false,'message'=>$e->getMessage()], 422);
        }
    }
}
