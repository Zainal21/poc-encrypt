<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DemoController extends Controller
{
    private function api(): \Illuminate\Http\Client\PendingRequest
    {
        $base = rtrim(config('services.encryption_api.url', env('ENCRYPTION_API_URL', 'http://127.0.0.1:8000')), '/');
        return Http::baseUrl($base)->acceptJson()->timeout(10);
    }

    public function symmetric()
    {
        return view('demos.symmetric', ['result' => session('result')]);
    }

    public function symmetricEncrypt(Request $request)
    {
        $res = $this->api()->post('/api/symmetric/encrypt', $request->only('text'));
        return back()->with('result', $res->json())->withInput();
    }

    public function symmetricDecrypt(Request $request)
    {
        $res = $this->api()->post('/api/symmetric/decrypt', $request->only('ciphertext'));
        return back()->with('result', $res->json())->withInput();
    }

    public function asymmetric()
    {
        return view('demos.asymmetric', ['result' => session('result')]);
    }

    public function asymmetricGenerateKeys()
    {
        $res = $this->api()->post('/api/asymmetric/generate-keys');
        return back()->with('result', $res->json());
    }

    public function asymmetricEncrypt(Request $request)
    {
        $res = $this->api()->post('/api/asymmetric/encrypt', $request->only('text'));
        return back()->with('result', $res->json())->withInput();
    }

    public function asymmetricDecrypt(Request $request)
    {
        $res = $this->api()->post('/api/asymmetric/decrypt', $request->only('ciphertext'));
        return back()->with('result', $res->json())->withInput();
    }

    public function personalData()
    {
        $res = $this->api()->get('/api/personal-data');
        $list = $res->json('data') ?? [];
        return view('demos.personal', ['list' => $list, 'result' => session('result')]);
    }

    public function personalDataStore(Request $request)
    {
        $res = $this->api()->post('/api/personal-data', $request->only('full_name', 'nik', 'phone_number'));
        return back()->with('result', $res->json());
    }
}
