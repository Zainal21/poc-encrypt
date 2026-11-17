<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Encryption POC · Client</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  body{background:#f6f8fb;color:#1f2937}
  .poc-shell{max-width:980px}
  .poc-brand{font-weight:650;letter-spacing:-.02em;color:#111827}
  .poc-brand .dot{display:inline-block;width:.65rem;height:.65rem;border-radius:50%;background:#4f46e5;margin-right:.5rem;vertical-align:middle}
  .poc-hero{padding:2.25rem 0 1.25rem}
  .poc-hero h1{font-weight:650;letter-spacing:-.03em;color:#111827}
  .poc-hero p{color:#6b7280;margin-bottom:0}
  .card{border:1px solid #e5e7eb;border-radius:.9rem;box-shadow:0 1px 2px rgba(16,24,40,.04)}
  .card-header{background:#fff;border-bottom:1px solid #eef2f6;font-weight:600;color:#111827;border-radius:.9rem .9rem 0 0!important}
  .form-label{font-weight:500;font-size:.875rem;color:#374151}
  .form-control,.form-select{border-color:#d1d5db}
  .form-control:focus,.form-select:focus{border-color:#6366f1;box-shadow:0 0 0 .2rem rgba(99,102,241,.15)}
  .btn-primary{background:#4f46e5;border-color:#4f46e5}
  .btn-primary:hover,.btn-primary:focus{background:#4338ca;border-color:#4338ca}
  .btn-outline-secondary{border-color:#d1d5db;color:#374151}
  .poc-code{background:#f8fafc;border:1px solid #e5e7eb;border-radius:.65rem;padding:.9rem;font-size:.78rem;color:#334155;white-space:pre-wrap;word-break:break-word;margin:0}
  .poc-payload{background:#fff;border:1px solid #e5e7eb;border-radius:.65rem;padding:.9rem;font-size:.78rem;color:#334155;white-space:pre-wrap;word-break:break-word;margin:0}
  .table{font-size:.85rem;margin-bottom:0}
  .table thead th{color:#6b7280;font-weight:600;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;border-bottom:1px solid #e5e7eb;white-space:nowrap}
  .table td{vertical-align:top;border-color:#eef2f6}
  .poc-footer{color:#9ca3af;font-size:.8rem}
  .poc-muted{color:#6b7280}
  code{color:#4f46e5}
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
  <div class="container poc-shell">
    <a class="navbar-brand poc-brand" href="{{ route('home') }}"><span class="dot"></span>Encryption POC <span class="badge text-bg-light border fw-normal ms-1">client</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto gap-lg-1">
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('home')?'active fw-semibold':'' }}" href="{{ route('home') }}">Home</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('symmetric')?'active fw-semibold':'' }}" href="{{ route('symmetric') }}">Symmetric</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('asymmetric')?'active fw-semibold':'' }}" href="{{ route('asymmetric') }}">Asymmetric</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('data')?'active fw-semibold':'' }}" href="{{ route('data') }}">Stored Data</a></li>
      </ul>
    </div>
  </div>
</nav>

<main class="container poc-shell">
@if(session('result'))
  @php($ok = session('result')['success'] ?? false)
  <div class="alert {{ $ok?'alert-success':'alert-danger' }} alert-dismissible fade show mt-4" role="alert">
    <div class="fw-semibold mb-1">{{ $ok?'Berhasil':'Gagal' }}</div>
    <pre class="poc-code">{{ json_encode(session('result'), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif
@if(session('sent_payload'))
  <div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>Payload terkirim</span><span class="badge text-bg-light border fw-normal">encrypted</span>
    </div>
    <div class="card-body">
      <p class="poc-muted small mb-2">Client hanya mengirim <code>key</code> dan <code>content</code> yang sudah terenkripsi.</p>
      <pre class="poc-payload">{{ json_encode(session('sent_payload'), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
    </div>
  </div>
@endif
@yield('content')
</main>

<footer class="container poc-shell py-4 text-center poc-footer">POC · client-side encryption simulation</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
