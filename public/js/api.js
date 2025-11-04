// ===== Helpers de autenticación token =====
export function getToken()   { return localStorage.getItem('token') || ''; }
export function setToken(t)  { localStorage.setItem('token', t || ''); }
export function clearToken() { localStorage.removeItem('token'); }

// ===== fetch estándar a /api/v1 =====
export async function apiFetch(path, { method='GET', body=null, auth=true } = {}) {
  const headers = { 'Accept': 'application/json', 'Content-Type': 'application/json' };
  if (auth) {
    const token = getToken();
    if (token) headers['Authorization'] = `Bearer ${token}`;
  }
  const res = await fetch(`/api/v1${path}`, {
    method,
    headers,
    body: body ? JSON.stringify(body) : null,
  });

  const isJson = res.headers.get('content-type')?.includes('application/json');
  const payload = isJson ? await res.json() : null;

  // Manejo genérico de errores y 401
  if (!res.ok) {
    if (res.status === 401) {
      clearToken();
      window.location.href = '/login?e=401';
      return;
    }
    const msg = (payload && (payload.message || payload.error)) || `HTTP ${res.status}`;
    throw new Error(msg);
  }

  return payload;
}
