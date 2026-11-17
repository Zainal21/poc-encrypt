@extends('layout')
@section('content')
<div class="py-4">
  <h1 class="h3 fw-semibold">Stored Data <span class="badge text-bg-secondary align-middle fs-6">managed by API</span></h1>
  <p class="text-secondary mb-0">Data yang sudah divalidasi &amp; disimpan API ke tabel migrasi existing.</p>
</div>

<div class="card mb-4">
  <div class="card-header">personal_data_symmetric</div>
  <div class="table-responsive">
    <table class="table table-dark table-striped table-hover align-middle mb-0">
      <thead><tr><th>ID</th><th>NIK</th><th>Birth date</th><th>Encrypted fields</th><th>Created</th></tr></thead>
      <tbody>
      @forelse($symmetric as $r)
        <tr><td>{{ $r['id'] }}</td><td>{{ $r['nik'] }}</td><td>{{ $r['birth_date'] }}</td><td><pre class="mb-0">{{ json_encode($r['encrypted']??[], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre></td><td class="text-nowrap">{{ $r['created_at'] }}</td></tr>
      @empty
        <tr><td colspan="5" class="text-secondary">Belum ada data.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="card">
  <div class="card-header">personal_data_asymmetric</div>
  <div class="table-responsive">
    <table class="table table-dark table-striped table-hover align-middle mb-0">
      <thead><tr><th>ID</th><th>key_id</th><th>NIK</th><th>Birth date</th><th>Decrypted (demo)</th><th>Created</th></tr></thead>
      <tbody>
      @forelse($asymmetric as $r)
        <tr><td>{{ $r['id'] }}</td><td>{{ $r['key_id'] }}</td><td>{{ $r['nik'] }}</td><td>{{ $r['birth_date'] }}</td><td><pre class="mb-0">{{ json_encode($r['decrypted']??[], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre></td><td class="text-nowrap">{{ $r['created_at'] }}</td></tr>
      @empty
        <tr><td colspan="6" class="text-secondary">Belum ada data.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
<p class="text-secondary small mt-3 mb-0">Symmetric: field sensitif tersimpan ciphertext (key asli disimpan client). Asymmetric: demo menampilkan hasil dekripsi bila private key preset tersedia.</p>
@endsection
