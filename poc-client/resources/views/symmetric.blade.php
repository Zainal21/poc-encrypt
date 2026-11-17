@extends('layout')
@section('content')
<section class="poc-hero">
  <h1 class="h3 mb-2">Symmetric <span class="badge text-bg-light border fw-normal align-middle fs-6">preconfigured key</span></h1>
  <p>Key sudah diset dari awal project. Form ini hanya submit data; client mengenkripsi payload sebelum dikirim ke API.</p>
</section>

<div class="card">
  <div class="card-header">Kirim Personal Data</div>
  <div class="card-body p-4">
    <p class="poc-muted small mb-4">AES key preset × RSA server untuk field <code>key</code>; data JSON dienkripsi AES untuk field <code>content</code>.</p>
    <form method="post" action="{{ route('symmetric.send') }}">@csrf
      <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Full name</label><input name="full_name" class="form-control" value="{{ old('full_name') }}" placeholder="Budi Santoso" required></div>
        <div class="col-md-6"><label class="form-label">NIK</label><input name="nik" class="form-control" value="{{ old('nik') }}" placeholder="3273010101010001" inputmode="numeric" required></div>
        <div class="col-md-6"><label class="form-label">Email</label><input name="email" type="email" class="form-control" value="{{ old('email') }}" placeholder="budi@example.com" required></div>
        <div class="col-md-6"><label class="form-label">Phone</label><input name="phone" class="form-control" value="{{ old('phone') }}" placeholder="08123456789" required></div>
        <div class="col-md-6"><label class="form-label">Birth date</label><input name="birth_date" type="date" class="form-control" value="{{ old('birth_date') }}" required></div>
        <div class="col-md-6"><label class="form-label">Address</label><input name="address" class="form-control" value="{{ old('address') }}" placeholder="Jl. Contoh No. 1" required></div>
      </div>
      <button type="submit" class="btn btn-primary mt-4">Encrypt &amp; Send ke API</button>
    </form>
  </div>
</div>
@endsection
