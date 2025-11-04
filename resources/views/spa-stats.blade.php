@extends('layouts.app')
@section('title','Stats')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Estadísticas</h1>
  <a class="btn btn-outline-light" href="/">Volver</a>
</div>

<div id="alert" class="alert d-none"></div>

<div class="row g-3">
  <div class="col-md-3">
    <div class="glass p-4 h-100">
      <div class="text-secondary">Clicks totales</div>
      <div id="total" class="display-6 fw-bold">0</div>
    </div>
  </div>
  <div class="col-md-9">
    <div class="glass p-4 h-100">
      <div class="text-secondary">Último click</div>
      <div id="last" class="fs-5">—</div>
    </div>
  </div>
  <div class="col-12">
    <div class="glass p-4">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <strong>Últimos 30 días</strong>
      </div>
      <canvas id="daily" height="90"></canvas>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  {{-- Chart.js global (UMD) --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

  <script type="module" src="/js/api.js"></script>
  <script type="module">
    import { apiFetch, getToken } from '/js/api.js';

    const params = new URLSearchParams(location.search);
    const id = params.get('id');
    if (!id) { location.href='/'; }

    function show(msg) {
      const box = document.getElementById('alert');
      box.textContent = msg;
      box.className = 'alert alert-danger';
      box.classList.remove('d-none');
    }

    async function waitForChart(ms = 3000) {
      if (window.Chart) return;
      await new Promise((resolve, reject) => {
        const t0 = Date.now();
        (function tick(){
          if (window.Chart) return resolve();
          if (Date.now() - t0 > ms) return reject(new Error('Chart no cargó'));
          requestAnimationFrame(tick);
        })();
      });
    }

    async function load() {
      if (!getToken()) return location.href = '/login';
      try {
        const data = await apiFetch(`/links/${id}/stats/summary`);
        const sum = data?.data ?? data;

        document.getElementById('total').textContent = sum.total_clicks ?? 0;
        const last = sum.last_click_at ? new Date(sum.last_click_at).toLocaleString() : '—';
        document.getElementById('last').textContent  = last;

        const daily = Array.isArray(sum.days_30_clicks) ? sum.days_30_clicks : [];
        const labels = daily.map(d=>d.date);
        const values = daily.map(d=>Number(d.count || 0));

        await waitForChart();
        const ctx = document.getElementById('daily').getContext('2d');
        new window.Chart(ctx, {
          type: 'line',
          data: { labels, datasets: [{ label: 'Clicks', data: values, tension: .25, fill: true }] },
          options: {
            plugins: { legend: { display:false } },
            scales: { y: { beginAtZero:true, grid: { color: 'rgba(255,255,255,.08)' } },
                      x: { grid: { color: 'transparent' } } }
          }
        });
      } catch (e) { show(e.message); }
    }
    load();
  </script>
@endpush
