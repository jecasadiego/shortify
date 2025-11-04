// ===== Helpers de autenticación token =====
export function getToken() { return localStorage.getItem('token') || ''; }
export function setToken(t) { localStorage.setItem('token', t || ''); }
export function clearToken() { localStorage.removeItem('token'); }

// ===== fetch estándar a /api/v1 =====
// public/js/api.js
const API_BASE = '/api/v1';

export async function apiFetch(path, { method = 'GET', body, auth = true } = {}) {
    const isAbsolute = /^https?:\/\//i.test(path);
    const normalized = path.startsWith('/') ? path : `/${path}`;
    const url = isAbsolute ? path : `${API_BASE}${normalized}`;

    const headers = { 'Content-Type': 'application/json' };
    const token = localStorage.getItem('token');
    if (auth && token) headers.Authorization = `Bearer ${token}`;

    const res = await fetch(url, {
        method,
        headers,
        body: body ? JSON.stringify(body) : undefined,
        credentials: 'same-origin',
    });

    const json = await res.json().catch(() => ({}));
    if (!res.ok) {
        throw new Error(json?.message || `HTTP ${res.status}`);
    }
    return json;
}

