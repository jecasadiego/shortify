@extends('layouts.app')
@section('title','Crear cuenta')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card-hero p-4 mb-3">
      <h1 class="h3 mb-1">Crear cuenta</h1>
      <div class="text-secondary">Comienza a acortar y rastrear enlaces</div>
    </div>

    <div class="glass p-4">
      <div id="alert" class="alert d-none mb-4"></div>

      <form id="registerForm" class="row g-3">
        <div class="col-12">
          <label class="form-label">Nombre</label>
          <input id="name" type="text" class="form-control form-control-lg" required placeholder="Tu nombre">
        </div>
        <div class="col-12">
          <label class="form-label">Email</label>
          <input id="email" type="email" class="form-control form-control-lg" required placeholder="tu@correo.com">
        </div>
        <div class="col-12">
          <label class="form-label">Contraseña</label>
          <input id="password" type="password" class="form-control form-control-lg" required minlength="6" placeholder="mínimo 6 caracteres">
        </div>
        <div class="col-12 d-grid">
          <button class="btn btn-lg btn-brand text-white">Crear cuenta</button>
        </div>
      </form>

      <div class="text-center small mt-3">
        ¿Ya tienes cuenta? <a class="link-muted" href="{{ route('auth.login') }}">Ingresar</a>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  <script type="module" src="/js/api.js"></script>
  <script type="module">
    import { apiFetch, setToken } from '/js/api.js';
    const form = document.getElementById('registerForm');
    const alertBox = document.getElementById('alert');

    function show(type, msg) {
      alertBox.textContent = msg;
      alertBox.className = 'alert alert-' + type;
      alertBox.classList.remove('d-none');
    }

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      alertBox.classList.add('d-none');
      const name = document.getElementById('name').value.trim();
      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value;

      try {
        const reg = await apiFetch('/register', { method: 'POST', body: { name, email, password }, auth: false });
        const tokenFromRegister = reg?.data?.token || reg?.token;
        if (tokenFromRegister) {
          setToken(tokenFromRegister);
          return window.location.href = '/';
        }
        const login = await apiFetch('/login', { method: 'POST', body: { email, password }, auth: false });
        const token = login?.data?.token || login?.token;
        if (!token) throw new Error('No se recibió token de autenticación');
        setToken(token);
        window.location.href = '/';
      } catch (err) {
        show('danger', err.message || 'No se pudo registrar');
      }
    });
  </script>
@endpush
