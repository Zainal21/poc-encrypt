<!doctype html>
<html lang="id" data-bs-theme="dark">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Encryption POC · Client</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  body{background:#0b1220}
  pre{white-space:pre-wrap;word-break:break-word;background:#0a1120;border:1px solid #22304d;border-radius:.75rem;padding:1rem;font-size:.8rem}
  .navbar-brand .dot{display:inline-block;width:.7rem;height:.7rem;border-radius:50%;background:linear-gradient(135deg,#4f8cff,#8b5cf6);margin-right:.45rem;box-shadow:0 0 12px rgba(79,140,255,.8)}
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom sticky-top">
  <div class="container">
    <a class="navbar-brand fw-semibold" href="{{ route('home') }}"><span class="dot"></span>Encryption POC <span class="badge text-bg-primary">client</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('home')?'active':'' }}" href="{{ route('home') }}">Home</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('symmetric')?'active':'' }}" href="{{ route('symmetric') }}">Symmetric</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('asymmetric')?'active':'' }}" href="{{ route('asymmetric') }}">Asymmetric</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('data')?'active':'' }}" href="{{ route('data') }}">Stored Data</a></li>
      </ul>
    </div>
  </div>
</nav>

<main class="container py-4">
@if(session('result'))
  @php($ok = session('result')['success'] ?? false)
  <div class="alert {{ $ok?'alert-success':'alert-danger' }} alert-dismissible fade show" role="alert">
    <strong>{{ $ok?'Berhasil':'Gagal' }}</strong>
    <pre class="mb-0 mt-2">{{ json_encode(session('result'), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif
@if(session('sent_payload'))
  <div class="card mb-4 border-info">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>Payload terkirim</span><span class="badge text-bg-info">encrypted</span>
    </div>
    <div class="card-body">
      <p class="text-secondary small mb-2">Client hanya mengirim <code>key</code> dan <code>content</code> yang sudah terenkripsi.</p>
      <pre class="mb-0">{{ json_encode(session('sent_payload'), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
    </div>
  </div>
@endif
@yield('content')
</main>

<footer class="container pb-4 text-center text-secondary small">POC · client-side encryption simulation</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
