<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Encryption POC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('symmetric') }}">Encryption POC</a>
        <div class="navbar-nav">
            <a class="nav-link {{ request()->routeIs('symmetric') ? 'active' : '' }}" href="{{ route('symmetric') }}">Symmetric</a>
            <a class="nav-link {{ request()->routeIs('asymmetric') ? 'active' : '' }}" href="{{ route('asymmetric') }}">Asymmetric</a>
            <a class="nav-link {{ request()->routeIs('personal*') ? 'active' : '' }}" href="{{ route('personal') }}">Personal Data</a>
        </div>
    </div>
</nav>
<div class="container pb-5">
    @if(!empty($result))
        <div class="alert {{ ($result['success'] ?? false) ? 'alert-success' : 'alert-danger' }}">
            <strong>{{ ($result['success'] ?? false) ? 'OK' : 'Error' }}:</strong> {{ $result['message'] ?? '' }}
            <pre class="mb-0 mt-2 small">{{ json_encode($result['data'] ?? null, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    @endif
    @yield('content')
</div>
<script>
document.querySelectorAll('form').forEach(f => f.addEventListener('submit', () => {
  const b = f.querySelector('button[type=submit]'); if(!b) return;
  b.disabled = true; b.dataset.orig = b.innerText; b.innerText = 'Loading...';
}));
</script>
</body>
</html>
