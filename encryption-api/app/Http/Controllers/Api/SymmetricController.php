<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SymmetricEncryptionService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Throwable;

#[OA\Tag(name: 'Symmetric', description: 'AES-256-CBC encryption via Laravel Crypt')]
class SymmetricController extends Controller
{
    public function __construct(private SymmetricEncryptionService $service)
    {
    }

    #[OA\Post(
        path: '/api/symmetric/encrypt',
        summary: 'Encrypt plaintext using AES-256-CBC',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['text'],
                properties: [
                    new OA\Property(property: 'text', type: 'string', example: 'sensitive data')
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
                        new OA\Property(property: 'message', type: 'string', example: 'Encrypted'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'ciphertext', type: 'string', example: 'eyJpdiI6I...')
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 500, description: 'Encryption failed')
        ]
    )]
    public function encrypt(Request $request)
    {
        $data = $request->validate(['text' => 'required|string']);
        try {
            $ciphertext = $this->service->encrypt($data['text']);
            return response()->json([
                'success' => true,
                'message' => 'Encrypted',
                'data' => ['ciphertext' => $ciphertext],
            ]);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null], 500);
        }
    }

    #[OA\Post(
        path: '/api/symmetric/decrypt',
        summary: 'Decrypt ciphertext using AES-256-CBC',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['ciphertext'],
                properties: [
                    new OA\Property(property: 'ciphertext', type: 'string', example: 'eyJpdiI6I...')
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
                        new OA\Property(property: 'message', type: 'string', example: 'Decrypted'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'plaintext', type: 'string', example: 'sensitive data')
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Invalid ciphertext or decryption failed')
        ]
    )]
    public function decrypt(Request $request)
    {
        $data = $request->validate(['ciphertext' => 'required|string']);
        try {
            $plaintext = $this->service->decrypt($data['ciphertext']);
            return response()->json([
                'success' => true,
                'message' => 'Decrypted',
                'data' => ['plaintext' => $plaintext],
            ]);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null], 422);
        }
    }
}