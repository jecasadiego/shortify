@extends('layouts.app')
@section('title','Ingresar')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card-hero p-4 mb-3">
      <h1 class="h3 mb-1">Bienvenido de nuevo</h1>
      <div class="text-secondary">Accede para gestionar tus enlaces cortos</div>
    </div>

    <div class="glass p-4">
      <div id="alert" class="alert alert-danger d-none mb-4"></div>

      <form id="loginForm" class="row g-3">
        <div class="col-12">
          <label class="form-label">Email</label>
          <input id="email" type="email" class="form-control form-control-lg" required placeholder="tu@correo.com">
        </div>
        <div class="col-12">
          <label class="form-label">Contraseña</label>
          <input id="password" type="password" class="form-control form-control-lg" required placeholder="••••••••">
        </div>
        <div class="col-12 d-grid">
          <button class="btn btn-lg btn-brand text-white">Ingresar</button>
        </div>
      </form>

      <div class="text-center small mt-3">
        ¿No tienes cuenta? <a class="link-muted" href="{{ route('auth.register') }}">Crear cuenta</a>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  <script type="module" src="/js/api.js"></script>
  <script type="module">
    import { apiFetch, setToken } from '/js/api.js';
    const form = document.getElementById('loginForm');
    const alertBox = document.getElementById('alert');

    function showError(msg) {
      alertBox.textContent = msg;
      alertBox.classList.remove('d-none');
    }

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      alertBox.classList.add('d-none');
      try {
        const payload = await apiFetch('/login', {
          method: 'POST',
          body: {
            email: document.getElementById('email').value.trim(),
            password: document.getElementById('password').value
          },
          auth: false,
        });
        const token = payload?.data?.token || payload?.token;
        if (!token) throw new Error('No se recibió token');
        setToken(token);
        window.location.href = '/';
      } catch (err) {
        showError(err.message || 'Error de autenticación');
      }
    });
  </script>
@endpush
