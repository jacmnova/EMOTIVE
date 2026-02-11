const API_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000";
const API_V1 = `${API_URL}/api/v1`;

function getToken(): string | null {
  if (typeof window === "undefined") return null;
  return localStorage.getItem("access_token");
}

function clearAuthOn401(): void {
  if (typeof window === "undefined") return;
  localStorage.removeItem("access_token");
  localStorage.removeItem("user");
}

/**
 * Normaliza path para evitar 307: FastAPI redirige /clientes → /clientes/.
 * Añade barra final solo a rutas de un segmento (ej. /users, /clientes) para evitar 404 en rutas como /grupos/1/usuarios.
 */
function normalizePath(path: string): string {
  if (!path) return path;
  const [base, qs] = path.includes("?") ? path.split("?", 2) : [path, ""];
  const p = base.startsWith("/") ? base : `/${base}`;
  const segments = p.replace(/^\//, "").split("/").filter(Boolean);
  const needsSlash = segments.length <= 1 && !p.endsWith("/");
  const withSlash = needsSlash ? p + "/" : p;
  return qs ? `${withSlash}?${qs}` : withSlash;
}

export async function api<T>(
  path: string,
  options: RequestInit = {}
): Promise<T> {
  const token = getToken();
  const headers: HeadersInit = {
    "Content-Type": "application/json",
    ...options.headers,
  };
  if (token) {
    (headers as Record<string, string>)["Authorization"] = `Bearer ${token}`;
  }

  const normalizedPath = normalizePath(path);
  const res = await fetch(`${API_V1}${normalizedPath}`, { ...options, headers });
  if (res.status === 401) {
    clearAuthOn401();
    if (typeof window !== "undefined") window.location.href = "/auth/login";
    throw new Error("Sesión expirada. Redirigiendo al login.");
  }
  if (!res.ok) {
    const err = await res.json().catch(() => ({ detail: res.statusText }));
    throw new Error(Array.isArray(err.detail) ? err.detail[0]?.msg ?? String(err.detail) : err.detail ?? res.statusText);
  }
  return res.json();
}

export async function apiFormData<T>(
  path: string,
  formData: FormData,
  method: string = "POST"
): Promise<T> {
  const token = getToken();
  const headers: HeadersInit = {};
  if (token) {
    headers["Authorization"] = `Bearer ${token}`;
  }

  const res = await fetch(`${API_V1}${path}`, {
    method,
    headers,
    body: formData,
  });
  if (res.status === 401) {
    clearAuthOn401();
    if (typeof window !== "undefined") window.location.href = "/auth/login";
    throw new Error("Sesión expirada. Redirigiendo al login.");
  }
  if (!res.ok) {
    const err = await res.json().catch(() => ({ detail: res.statusText }));
    throw new Error(err.detail ?? res.statusText);
  }
  return res.json();
}

export { API_URL, API_V1, getToken };
