@extends('layout')
@section('content')
<h4>Symmetric Encryption (AES-256-CBC)</h4>
<div class="row g-3">
  <div class="col-md-6">
    <div class="card"><div class="card-body">
      <h6>Encrypt</h6>
      <form method="post" action="{{ route('symmetric.encrypt') }}">@csrf
        <textarea name="text" class="form-control mb-2" rows="4" placeholder="Plaintext" required>{{ old('text') }}</textarea>
        <button class="btn btn-primary" type="submit">Encrypt</button>
      </form>
    </div></div>
  </div>
  <div class="col-md-6">
    <div class="card"><div class="card-body">
      <h6>Decrypt</h6>
      <form method="post" action="{{ route('symmetric.decrypt') }}">@csrf
        <textarea name="ciphertext" class="form-control mb-2" rows="4" placeholder="Ciphertext" required>{{ old('ciphertext') }}</textarea>
        <button class="btn btn-secondary" type="submit">Decrypt</button>
      </form>
    </div></div>
  </div>
</div>
@endsection
