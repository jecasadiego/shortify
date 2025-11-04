<!doctype html>
<html lang="es" data-bs-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Shortify')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --grad-1: #0f172a; /* slate-900 */
      --grad-2: #111827; /* gray-900 */
      --glass-bg: rgba(255,255,255,.05);
      --glass-bd: rgba(255,255,255,.08);
      --accent: #22d3ee; /* cyan-400 */
      --accent-2: #a78bfa; /* violet-400 */
    }
    body {
      min-height: 100vh;
      background:
        radial-gradient(1200px 600px at -10% -10%, rgba(34,211,238,.08), transparent 60%),
        radial-gradient(900px 500px at 110% 0%, rgba(167,139,250,.08), transparent 60%),
        linear-gradient(180deg, var(--grad-1), var(--grad-2));
      backdrop-filter: saturate(120%);
    }
    .container-narrow { max-width: 1100px; }
    .glass {
      background: var(--glass-bg);
      border: 1px solid var(--glass-bd);
      box-shadow: 0 10px 25px rgba(0,0,0,.25), inset 0 1px 0 rgba(255,255,255,.04);
      border-radius: 16px;
    }
    .brand {
      font-weight: 800; letter-spacing:.3px;
      background: linear-gradient(90deg, #fff, var(--accent), var(--accent-2));
      -webkit-background-clip: text; background-clip: text; color: transparent;
    }
    .btn-brand {
      border: 1px solid rgba(255,255,255,.12);
      background: linear-gradient(90deg, rgba(34,211,238,.15), rgba(167,139,250,.15));
      transition: transform .15s ease, box-shadow .2s ease, background .2s ease;
    }
    .btn-brand:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(34,211,238,.15); }
    .link-muted { color: rgba(255,255,255,.8); }
    .link-muted:hover { color: #fff; }
    .table > :not(caption) > * > * { border-color: rgba(255,255,255,.08) !important; }
    .table thead th { color: rgba(255,255,255,.85); font-weight: 600; }
    .badge-soft { background: rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.08); }
    .copy-btn { cursor:pointer }
    .card-hero {
      position: relative; overflow: hidden;
      background:
        radial-gradient(600px 300px at 0% 0%, rgba(34,211,238,.18), transparent 70%),
        radial-gradient(600px 300px at 100% 0%, rgba(167,139,250,.18), transparent 70%),
        var(--glass-bg);
      border: 1px solid var(--glass-bd); border-radius: 18px;
    }
    .fade-in { animation: fade .35s ease both; }
    @keyframes fade { from {opacity:0; transform: translateY(4px);} to {opacity:1; transform:none;} }
  </style>
  @stack('head')
</head>
<body class="pb-5">
  <nav class="navbar navbar-expand-lg border-bottom border-0" style="background:transparent">
    <div class="container container-narrow">
      <a class="navbar-brand brand" href="/">🔗 Shortify</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div id="nav" class="collapse navbar-collapse">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link link-muted" href="/">App</a></li>
        </ul>
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link link-muted" href="/login">Ingresar</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <main class="container container-narrow mt-4 fade-in">
    @yield('content')
  </main>

  <footer class="container container-narrow mt-5 text-center small text-secondary">
    <hr class="border-secondary" />
    <div>Hecho con 💙 — Shortify</div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
