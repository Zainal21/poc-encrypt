@extends('layout')
@section('content')
<h4>Asymmetric Encryption (RSA 2048-bit)</h4>
<p class="text-muted small">Key pair disimpan di <code>storage/app/keys/</code> (API). Tidak ada input/response key.</p>
<div class="mb-3">
  <form method="post" action="{{ route('asymmetric.generate') }}">@csrf
    <button class="btn btn-warning" type="submit">Generate Key Pair</button>
  </form>
</div>
<div class="row g-3">
  <div class="col-md-6">
    <div class="card"><div class="card-body">
      <h6>Encrypt (public key dari storage)</h6>
      <form method="post" action="{{ route('asymmetric.encrypt') }}">@csrf
        <textarea name="text" class="form-control mb-2" rows="4" placeholder="Plaintext" required>{{ old('text') }}</textarea>
        <button class="btn btn-primary" type="submit">Encrypt</button>
      </form>
    </div></div>
  </div>
  <div class="col-md-6">
    <div class="card"><div class="card-body">
      <h6>Decrypt (private key dari storage)</h6>
      <form method="post" action="{{ route('asymmetric.decrypt') }}">@csrf
        <textarea name="ciphertext" class="form-control mb-2" rows="4" placeholder="Ciphertext (base64)" required>{{ old('ciphertext') }}</textarea>
        <button class="btn btn-secondary" type="submit">Decrypt</button>
      </form>
    </div></div>
  </div>
</div>
@endsection
