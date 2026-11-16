<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AsymmetricEncryptionService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Throwable;

#[OA\Tag(name: 'Asymmetric', description: 'RSA 2048-bit encryption. Keys stored in storage/app/keys/')]
class AsymmetricController extends Controller
{
    public function __construct(private AsymmetricEncryptionService $service)
    {
    }

    #[OA\Post(
        path: '/api/asymmetric/generate-keys',
        summary: 'Generate RSA 2048 key pair and save to storage/app/keys/',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Keys generated and saved',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Key pair generated and saved to storage/app/keys'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'generated', type: 'boolean', example: true)
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 500, description: 'Key generation failed')
        ]
    )]
    public function generateKeys()
    {
        try {
            $this->service->generateKeys();
            return response()->json([
                'success' => true,
                'message' => 'Key pair generated and saved to storage/app/keys',
                'data' => ['generated' => true],
            ]);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null], 500);
        }
    }

    #[OA\Post(
        path: '/api/asymmetric/encrypt',
        summary: 'Encrypt using public key from storage/app/keys/public.pem',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['text'],
                properties: [
                    new OA\Property(property: 'text', type: 'string', example: 'secret message')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Encrypted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Encrypted with public key from storage'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'ciphertext', type: 'string', example: 'base64encoded...')
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Keys not generated or encryption failed')
        ]
    )]
    public function encrypt(Request $request)
    {
        $data = $request->validate(['text' => 'required|string']);
        try {
            $ciphertext = $this->service->encrypt($data['text']);
            return response()->json([
                'success' => true,
                'message' => 'Encrypted with public key from storage',
                'data' => ['ciphertext' => $ciphertext],
            ]);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null], 422);
        }
    }

    #[OA\Post(
        path: '/api/asymmetric/decrypt',
        summary: 'Decrypt using private key from storage/app/keys/private.pem',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['ciphertext'],
                properties: [
                    new OA\Property(property: 'ciphertext', type: 'string', example: 'base64encoded...')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Decrypted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Decrypted with private key from storage'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'plaintext', type: 'string', example: 'secret message')
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Keys not generated or decryption failed')
        ]
    )]
    public function decrypt(Request $request)
    {
        $data = $request->validate(['ciphertext' => 'required|string']);
        try {
            $plaintext = $this->service->decrypt($data['ciphertext']);
            return response()->json([
                'success' => true,
                'message' => 'Decrypted with private key from storage',
                'data' => ['plaintext' => $plaintext],
            ]);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null], 422);
        }
    }
}