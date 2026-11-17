<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(title: 'Encryption POC API', version: '1.0.0', description: 'Simulasi symmetric & asymmetric encryption. Client menyimpan key dan mengirim payload terenkripsi (key, content).')]
#[OA\Server(url: 'http://127.0.0.1:8001', description: 'Local')]
abstract class Controller {}
