<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PersonalData;
use App\Services\SymmetricEncryptionService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Throwable;

#[OA\Tag(name: 'PersonalData', description: 'Store and retrieve encrypted personal data (symmetric)')]
class PersonalDataController extends Controller
{
    public function __construct(private SymmetricEncryptionService $crypto)
    {
    }

    #[OA\Post(
        path: '/api/personal-data',
        summary: 'Encrypt and store personal data (full_name, nik, phone_number)',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['full_name', 'nik', 'phone_number'],
                properties: [
                    new OA\Property(property: 'full_name', type: 'string', example: 'Budi Santoso'),
                    new OA\Property(property: 'nik', type: 'string', example: '3273010101010001'),
                    new OA\Property(property: 'phone_number', type: 'string', example: '08123456789')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Stored successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', type: 'object')
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error')
        ]
    )]
    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string',
            'nik' => 'required|string',
            'phone_number' => 'required|string',
        ]);

        $encrypted = [
            'full_name' => $this->crypto->encrypt($data['full_name']),
            'nik' => $this->crypto->encrypt($data['nik']),
            'phone_number' => $this->crypto->encrypt($data['phone_number']),
        ];

        $record = PersonalData::create($encrypted);

        return response()->json([
            'success' => true,
            'message' => 'Personal data encrypted and stored',
            'data' => [
                'id' => $record->id,
                'encrypted' => $encrypted,
                'decrypted' => $data,
            ],
        ], 201);
    }

    #[OA\Get(
        path: '/api/personal-data',
        summary: 'List all personal data (decrypted)',
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of personal data',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'OK'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'object'))
                    ]
                )
            )
        ]
    )]
    public function index()
    {
        $items = PersonalData::all()->map(fn (PersonalData $p) => $this->decryptRecord($p));
        return response()->json(['success' => true, 'message' => 'OK', 'data' => $items]);
    }

    #[OA\Get(
        path: '/api/personal-data/{id}',
        summary: 'Get personal data detail by ID (decrypted)',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'Record ID')
        ],
        responses: [
            new OA\Response(response: 200, description: 'Data found'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    public function show($id)
    {
        $p = PersonalData::find($id);
        if (!$p) {
            return response()->json(['success' => false, 'message' => 'Not found', 'data' => null], 404);
        }
        return response()->json(['success' => true, 'message' => 'OK', 'data' => $this->decryptRecord($p)]);
    }

    private function decryptRecord(PersonalData $p): array
    {
        $safe = function (string $cipher) {
            try { return $this->crypto->decrypt($cipher); } catch (Throwable $e) { return null; }
        };
        return [
            'id' => $p->id,
            'full_name' => $safe($p->full_name),
            'nik' => $safe($p->nik),
            'phone_number' => $safe($p->phone_number),
            'created_at' => $p->created_at,
            'updated_at' => $p->updated_at,
        ];
    }
}