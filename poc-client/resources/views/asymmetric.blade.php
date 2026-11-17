@extends('layout')
@section('content')
<section class="poc-hero">
  <h1 class="h3 mb-2">Asymmetric <span class="badge text-bg-light border fw-normal align-middle fs-6">preset keypair</span></h1>
  <p>Public key sudah diset dari awal project dan terdaftar di API. Form hanya submit data; client memakai key tersebut untuk enkripsi.</p>
</section>

<div class="card mb-4">
  <div class="card-body px-4 py-3">
    <div class="row g-3 align-items-end">
      <div class="col-md-6"><label class="form-label mb-1">key_id</label><div class="font-monospace small">{{ $key_id }}</div></div>
      <div class="col-md-6"><label class="form-label mb-1">Fingerprint public key</label><div class="font-monospace small text-break">{{ $fingerprint ?? '-' }}</div></div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">Kirim Personal Data</div>
  <div class="card-body p-4">
    <p class="poc-muted small mb-4">Content dienkripsi hybrid memakai public key preset; <code>key_id</code> dienkripsi dengan server public key untuk transport.</p>
    <form method="post" action="{{ route('asymmetric.send') }}">@csrf
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
