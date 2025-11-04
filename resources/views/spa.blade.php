@extends('layouts.app')
@section('title', 'Shortify')

@section('content')
  <div class="card-hero p-4 mb-4">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
      <div>
        <h2 class="h4 mb-1">Panel de enlaces</h2>
        <div class="text-secondary">Crea, comparte y consulta estadísticas en tiempo real</div>
      </div>
      <div class="d-flex gap-2">
        <a href="/stats" class="btn btn-outline-light">Ver estadísticas</a>
        <button id="logoutBtn" class="btn btn-brand text-white">Salir</button>
      </div>
    </div>
  </div>

  <div id="alert" class="alert d-none"></div>

  <section class="glass p-4 mb-4">
    <h3 class="h5 mb-3">Crear enlace</h3>
    <form id="createForm" class="row g-2">
      <div class="col-md-6">
        <input id="dest" type="url" class="form-control form-control-lg" placeholder="https://destino.com" required>
      </div>
      <div class="col-md-3">
        <input id="slug" type="text" class="form-control form-control-lg" placeholder="slug (opcional)">
      </div>
      <div class="col-md-2">
        <input id="expires" type="datetime-local" class="form-control form-control-lg" placeholder="expira">
      </div>
      <div class="col-md-1 d-grid">
        <button class="btn btn-lg btn-brand text-white">Guardar</button>
      </div>
    </form>
  </section>

  <section class="glass p-0">
    <div class="p-3 d-flex justify-content-between align-items-center border-bottom" style="border-color:rgba(255,255,255,.08)!important">
      <strong>Mis enlaces</strong>
      <input id="q" class="form-control form-control-sm" style="max-width:260px" placeholder="Buscar...">
    </div>
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th>Slug</th>
            <th>URL corta</th>
            <th>Destino</th>
            <th class="text-center">Clicks</th>
            <th>Expira</th>
            <th>Estado</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody id="tbody">
          <tr><td colspan="7" class="text-center text-secondary py-4">Cargando…</td></tr>
        </tbody>
      </table>
    </div>
    <div id="pager" class="p-3 d-flex gap-2"></div>
  </section>
@endsection

@push('scripts')
  <script type="module" src="/js/api.js"></script>
  <script type="module">
    import { apiFetch, getToken, clearToken } from '/js/api.js';

    const alertBox = document.getElementById('alert');
    const tbody = document.getElementById('tbody');
    const pager = document.getElementById('pager');
    const q = document.getElementById('q');

    function ensureAuth() { if (!getToken()) window.location.href = '/login'; }
    ensureAuth();

    function showAlert(type, msg) {
      alertBox.className = 'alert alert-' + type;
      alertBox.textContent = msg;
      alertBox.classList.remove('d-none');
      setTimeout(()=>alertBox.classList.add('d-none'), 2200);
    }

    async function loadLinks(url = '/links') {
      const query = q.value?.trim() ? `?q=${encodeURIComponent(q.value.trim())}` : '';
      const data = await apiFetch(url + query);
      const items = (data.data?.data) ?? data.data ?? data;
      renderTable(items);
      renderPager((data.links ?? data.meta ?? null));
    }

    function renderTable(items) {
      tbody.innerHTML = '';
      if (!items || !items.length) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center text-secondary py-4">Sin enlaces</td></tr>`;
        return;
      }
      for (const l of items) {
        const shortUrl = `${location.origin}/r/${l.slug}`;
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td><code>${l.slug}</code></td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <a href="${shortUrl}" target="_blank" class="text-decoration-none">${shortUrl}</a>
              <span class="badge badge-soft copy-btn" data-copy="${shortUrl}">copiar</span>
            </div>
          </td>
          <td class="text-truncate" style="max-width:420px" title="${l.destination_url}">
            <a href="${l.destination_url}" class="text-decoration-none" target="_blank">${l.destination_url}</a>
          </td>
          <td class="text-center">${l.clicks_count ?? 0}</td>
          <td>${l.expires_at ?? '—'}</td>
          <td>${
            !l.is_active ? '<span class="badge text-bg-danger">Inactivo</span>' :
            (l.expires_at && new Date(l.expires_at) < new Date()) ? '<span class="badge text-bg-warning">Expirado</span>' :
            '<span class="badge text-bg-success">Activo</span>'
          }</td>
          <td class="text-end">
            <a href="/stats?id=${l.id}" class="btn btn-sm btn-outline-info">Stats</a>
            <button class="btn btn-sm btn-outline-danger" data-action="deactivate" data-id="${l.id}">Desactivar</button>
          </td>
        `;
        tbody.appendChild(tr);
      }
      document.querySelectorAll('.copy-btn').forEach(el=>{
        el.onclick = async () => {
          await navigator.clipboard.writeText(el.dataset.copy);
          el.textContent = 'copiado'; setTimeout(()=> el.textContent='copiar', 1200);
        };
      });
      document.querySelectorAll('[data-action="deactivate"]').forEach(btn=>{
        btn.onclick = async ()=>{
          if (!confirm('¿Desactivar enlace?')) return;
          try { await apiFetch(`/links/${btn.dataset.id}`, { method:'DELETE' });
            showAlert('success','Desactivado'); loadLinks();
          } catch(e){ showAlert('danger', e.message); }
        };
      });
    }

    function renderPager(metaOrLinks) {
      pager.innerHTML = '';
      const links = metaOrLinks?.links || metaOrLinks;
      if (!Array.isArray(links)) return;
      for (const l of links) {
        if (!l.url) continue;
        const a = document.createElement('a');
        a.href = '#';
        a.className = 'btn btn-sm ' + (l.active ? 'btn-brand text-white' : 'btn-outline-secondary');
        a.innerHTML = l.label.replace('&laquo;', '«').replace('&raquo;', '»');
        a.onclick = (e)=>{ e.preventDefault(); loadLinks(l.url.replace('/api/v1','')); };
        pager.appendChild(a);
      }
    }

    document.getElementById('createForm').addEventListener('submit', async (e)=>{
      e.preventDefault();
      try {
        await apiFetch('/links', {
          method: 'POST',
          body: {
            destination_url: document.getElementById('dest').value,
            slug: document.getElementById('slug').value || null,
            expires_at: document.getElementById('expires').value || null,
            is_active: true
          }
        });
        showAlert('success','Creado');
        e.target.reset(); loadLinks();
      } catch (err) { showAlert('danger', err.message); }
    });

    q.addEventListener('input', ()=> { clearTimeout(window.__t); window.__t=setTimeout(()=>loadLinks(), 350); });
    document.getElementById('logoutBtn')?.addEventListener('click', ()=>{ clearToken(); window.location.href = '/login'; });

    loadLinks().catch(()=> { showAlert('danger','Inicia sesión'); window.location.href='/login'; });
  </script>
@endpush
