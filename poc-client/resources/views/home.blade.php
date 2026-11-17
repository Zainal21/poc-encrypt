@extends('layout')
@section('content')
<div class="py-4">
  <h1 class="display-6 fw-semibold">Simulasi Enkripsi <span class="badge text-bg-primary align-middle fs-6">symmetric + asymmetric</span></h1>
  <p class="text-secondary">Key sudah diset dari awal project. Interface hanya mengirim data personal; enkripsi payload (<code>key</code> &amp; <code>content</code>) dilakukan di client.</p>
</div>

<div class="row g-4">
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <h5 class="card-title">Symmetric</h5>
        <p class="card-text text-secondary">AES-256-CBC untuk content. AES key dienkripsi RSA server sebelum dikirim. API memvalidasi dan menyimpan ke <code>personal_data_symmetric</code>.</p>
        <a class="btn btn-primary" href="{{ route('symmetric') }}">Buka Symmetric</a>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <h5 class="card-title">Asymmetric</h5>
        <p class="card-text text-secondary">Content dienkripsi dengan public key preset. Envelope hybrid (AES + RSA) dikirim; API mendekripsi &amp; menyimpan ke <code>personal_data_asymmetric</code>.</p>
        <a class="btn btn-primary" href="{{ route('asymmetric') }}">Buka Asymmetric</a>
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Alur</h5>
        <div class="row g-3">
          <div class="col-md-4"><span class="badge text-bg-secondary">1</span><p class="mt-2 mb-0">Client siapkan data personal.</p></div>
          <div class="col-md-4"><span class="badge text-bg-secondary">2</span><p class="mt-2 mb-0">Client enkripsi content &amp; key, kirim <code>{key, content}</code>.</p></div>
          <div class="col-md-4"><span class="badge text-bg-secondary">3</span><p class="mt-2 mb-0">API dekripsi, validasi field, simpan ke tabel migrasi existing.</p></div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
