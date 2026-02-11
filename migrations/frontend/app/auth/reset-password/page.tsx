"use client";

import { useState, useEffect, Suspense } from "react";
import Link from "next/link";
import { useRouter, useSearchParams } from "next/navigation";
import { resetPassword } from "@/lib/auth";

function ResetPasswordForm() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const token = searchParams.get("token") ?? "";
  const [password, setPassword] = useState("");
  const [confirm, setConfirm] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (!token) setError("Falta el token de recuperación. Usa el enlace del email.");
  }, [token]);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError("");
    if (password !== confirm) {
      setError("Las contraseñas no coinciden");
      return;
    }
    if (password.length < 6) {
      setError("La contraseña debe tener al menos 6 caracteres");
      return;
    }
    setLoading(true);
    try {
      await resetPassword(token, password);
      router.push("/auth/login?reset=1");
      router.refresh();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Error al restablecer");
    } finally {
      setLoading(false);
    }
  }

  if (!token) {
    return (
      <main className="min-h-screen flex items-center justify-center p-4 bg-slate-50">
        <div className="w-full max-w-md bg-white rounded-xl shadow-lg p-8 text-center">
          <p className="text-red-600 mb-4">{error}</p>
          <Link href="/auth/forgot-password" className="text-primary hover:underline">
            Solicitar nuevo enlace
          </Link>
        </div>
      </main>
    );
  }

  return (
    <main className="min-h-screen flex items-center justify-center p-4 bg-slate-50">
      <div className="w-full max-w-md bg-white rounded-xl shadow-lg p-8">
        <h1 className="text-2xl font-bold text-center text-accent mb-6">Nueva contraseña</h1>
        <form onSubmit={handleSubmit} className="space-y-4">
          {error && (
            <div className="p-3 rounded-lg bg-red-50 text-red-700 text-sm">{error}</div>
          )}
          <div>
            <label htmlFor="password" className="block text-sm font-medium text-slate-700 mb-1">
              Nueva contraseña
            </label>
            <input
              id="password"
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
              minLength={6}
              className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
            />
          </div>
          <div>
            <label htmlFor="confirm" className="block text-sm font-medium text-slate-700 mb-1">
              Confirmar contraseña
            </label>
            <input
              id="confirm"
              type="password"
              value={confirm}
              onChange={(e) => setConfirm(e.target.value)}
              required
              minLength={6}
              className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
            />
          </div>
          <button
            type="submit"
            disabled={loading}
            className="w-full py-3 bg-primary text-white font-medium rounded-lg hover:bg-primary-dark disabled:opacity-50 transition"
          >
            {loading ? "Guardando…" : "Restablecer contraseña"}
          </button>
        </form>
        <p className="mt-6 text-center">
          <Link href="/auth/login" className="text-slate-500 text-sm hover:underline">
            ← Volver al login
          </Link>
        </p>
      </div>
    </main>
  );
}

export default function ResetPasswordPage() {
  return (
    <Suspense fallback={<div className="min-h-screen flex items-center justify-center">Cargando…</div>}>
      <ResetPasswordForm />
    </Suspense>
  );
}
