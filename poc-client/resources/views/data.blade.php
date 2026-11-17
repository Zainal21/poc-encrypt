@extends('layout')
@section('content')
<section class="poc-hero">
  <h1 class="h3 mb-2">Stored Data <span class="badge text-bg-light border fw-normal align-middle fs-6">managed by API</span></h1>
  <p>Data yang sudah divalidasi &amp; disimpan API ke tabel migrasi existing.</p>
</section>

<div class="card mb-4">
  <div class="card-header">personal_data_symmetric</div>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead><tr><th>ID</th><th>NIK</th><th>Birth date</th><th>Encrypted fields</th><th>Created</th></tr></thead>
      <tbody>
      @forelse($symmetric as $r)
        <tr>
          <td>{{ $r['id'] }}</td>
          <td class="font-monospace small">{{ $r['nik'] }}</td>
          <td class="text-nowrap">{{ $r['birth_date'] }}</td>
          <td><pre class="poc-code">{{ json_encode($r['encrypted']??[], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre></td>
          <td class="text-nowrap poc-muted small">{{ $r['created_at'] }}</td>
        </tr>
      @empty
        <tr><td colspan="5" class="poc-muted">Belum ada data.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="card">
  <div class="card-header">personal_data_asymmetric</div>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead><tr><th>ID</th><th>key_id</th><th>NIK</th><th>Birth date</th><th>Decrypted (demo)</th><th>Created</th></tr></thead>
      <tbody>
      @forelse($asymmetric as $r)
        <tr>
          <td>{{ $r['id'] }}</td>
          <td class="font-monospace small">{{ $r['key_id'] }}</td>
          <td class="font-monospace small">{{ $r['nik'] }}</td>
          <td class="text-nowrap">{{ $r['birth_date'] }}</td>
          <td><pre class="poc-code">{{ json_encode($r['decrypted']??[], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre></td>
          <td class="text-nowrap poc-muted small">{{ $r['created_at'] }}</td>
        </tr>
      @empty
        <tr><td colspan="6" class="poc-muted">Belum ada data.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
<p class="poc-muted small mt-3 mb-0">Symmetric: field sensitif tersimpan ciphertext (key asli disimpan client). Asymmetric: demo menampilkan hasil dekripsi bila private key preset tersedia.</p>
@endsection
