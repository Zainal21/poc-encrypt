@extends('layout')
@section('content')
<h4>Personal Data (terenkripsi simetris di DB API)</h4>
<div class="card mb-4"><div class="card-body">
  <form method="post" action="{{ route('personal.store') }}">@csrf
    <div class="row g-2">
      <div class="col-md-4"><input name="full_name" class="form-control" placeholder="Nama lengkap" required></div>
      <div class="col-md-4"><input name="nik" class="form-control" placeholder="NIK" required></div>
      <div class="col-md-4"><input name="phone_number" class="form-control" placeholder="No. Telepon" required></div>
    </div>
    <button class="btn btn-primary mt-2" type="submit">Simpan (terenkripsi)</button>
  </form>
</div></div>

<h6>Data tersimpan (didekripsi saat tampil)</h6>
<table class="table table-sm table-striped bg-white">
  <thead><tr><th>ID</th><th>Nama</th><th>NIK</th><th>Telepon</th><th>Waktu</th></tr></thead>
  <tbody>
  @forelse($list as $row)
    <tr>
      <td>{{ $row['id'] }}</td>
      <td>{{ $row['full_name'] }}</td>
      <td>{{ $row['nik'] }}</td>
      <td>{{ $row['phone_number'] }}</td>
      <td>{{ $row['created_at'] }}</td>
    </tr>
  @empty
    <tr><td colspan="5" class="text-muted">Belum ada data.</td></tr>
  @endforelse
  </tbody>
</table>
@endsection
