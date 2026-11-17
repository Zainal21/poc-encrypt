@extends('layout')
@section('content')
<section class="poc-hero">
  <h1 class="h3 mb-2">Simulasi Enkripsi</h1>
  <p>Symmetric + asymmetric · key preset dari awal project · interface hanya submit data.</p>
</section>

<div class="row g-4">
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-body p-4">
        <h2 class="h5 mb-2">Symmetric</h2>
        <p class="poc-muted small mb-3">AES-256-CBC untuk content. AES key dienkripsi RSA server sebelum dikirim. API memvalidasi dan menyimpan ke <code>personal_data_symmetric</code>.</p>
        <a class="btn btn-primary btn-sm" href="{{ route('symmetric') }}">Buka Symmetric</a>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-body p-4">
        <h2 class="h5 mb-2">Asymmetric</h2>
        <p class="poc-muted small mb-3">Content dienkripsi dengan public key preset. Envelope hybrid (AES + RSA) dikirim; API mendekripsi &amp; menyimpan ke <code>personal_data_asymmetric</code>.</p>
        <a class="btn btn-primary btn-sm" href="{{ route('asymmetric') }}">Buka Asymmetric</a>
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="card">
      <div class="card-header">Alur</div>
      <div class="card-body p-4">
        <div class="row g-4">
          <div class="col-md-4"><div class="fw-semibold mb-1">1 · Input</div><p class="poc-muted small mb-0">Client siapkan data personal.</p></div>
          <div class="col-md-4"><div class="fw-semibold mb-1">2 · Encrypt</div><p class="poc-muted small mb-0">Client enkripsi content &amp; key, kirim <code>{key, content}</code>.</p></div>
          <div class="col-md-4"><div class="fw-semibold mb-1">3 · Store</div><p class="poc-muted small mb-0">API dekripsi, validasi field, simpan ke tabel migrasi existing.</p></div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
