import type { TokenResponse, User } from "@/types";
import { API_V1, getToken } from "./api";

const TOKEN_KEY = "access_token";
const USER_KEY = "user";

export function setAuth(data: TokenResponse): void {
  if (typeof window === "undefined") return;
  localStorage.setItem(TOKEN_KEY, data.access_token);
  localStorage.setItem(USER_KEY, JSON.stringify(data.user));
}

export function clearAuth(): void {
  if (typeof window === "undefined") return;
  localStorage.removeItem(TOKEN_KEY);
  localStorage.removeItem(USER_KEY);
}

export function getStoredUser(): User | null {
  if (typeof window === "undefined") return null;
  const raw = localStorage.getItem(USER_KEY);
  if (!raw) return null;
  try {
    return JSON.parse(raw) as User;
  } catch {
    return null;
  }
}

export function isAuthenticated(): boolean {
  return !!getToken();
}

export async function login(email: string, password: string): Promise<TokenResponse> {
  const form = new URLSearchParams();
  form.set("username", email);
  form.set("password", password);
  const res = await fetch(`${API_V1}/auth/login`, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: form.toString(),
  });
  if (!res.ok) {
    const err = await res.json().catch(() => ({ detail: "Error de conexión" }));
    throw new Error(err.detail ?? "Error al iniciar sesión");
  }
  const data: TokenResponse = await res.json();
  setAuth(data);
  return data;
}

export async function register(params: {
  name: string;
  email: string;
  password: string;
  cliente_id?: number;
  sa?: boolean;
  admin?: boolean;
  gestor?: boolean;
  usuario?: boolean;
}): Promise<User> {
  const res = await fetch(`${API_V1}/auth/register`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(params),
  });
  if (!res.ok) {
    const err = await res.json().catch(() => ({ detail: "Error de conexión" }));
    throw new Error(err.detail ?? "Error al registrarse");
  }
  return res.json();
}

export async function forgotPassword(email: string): Promise<void> {
  const res = await fetch(`${API_V1}/auth/forgot-password`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ email }),
  });
  if (!res.ok) {
    const err = await res.json().catch(() => ({}));
    throw new Error(err.detail ?? "Error al enviar solicitud");
  }
}

export async function resetPassword(token: string, new_password: string): Promise<void> {
  const res = await fetch(`${API_V1}/auth/reset-password`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ token, new_password }),
  });
  if (!res.ok) {
    const err = await res.json().catch(() => ({}));
    throw new Error(err.detail ?? "Error al restablecer contraseña");
  }
}

export async function verifyEmail(token: string): Promise<void> {
  const res = await fetch(`${API_V1}/auth/verify-email?token=${encodeURIComponent(token)}`, {
    method: "POST",
  });
  if (!res.ok) {
    const err = await res.json().catch(() => ({}));
    throw new Error(err.detail ?? "Error al verificar email");
  }
}
