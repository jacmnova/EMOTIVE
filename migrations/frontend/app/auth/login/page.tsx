"use client";

import { useState } from "react";
import Link from "next/link";
import { useRouter, useSearchParams } from "next/navigation";
import { login } from "@/lib/auth";

export default function LoginPage() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const redirectTo = searchParams.get("redirect");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError("");
    setLoading(true);
    try {
      await login(email, password);
      const target = redirectTo ? decodeURIComponent(redirectTo) : "/dashboard";
      router.push(target.startsWith("/") ? target : "/dashboard");
      router.refresh();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Error al iniciar sesión");
    } finally {
      setLoading(false);
    }
  }

  return (
    <main className="min-h-screen flex flex-col items-center justify-center p-4 bg-emotive-panel-bg">
      <Link href="/" className="mb-8">
        <img src="/logo-emotive.png" alt="E.MO.TI.VE" className="h-10 w-auto object-contain" />
        
      </Link>
      <div className="w-full max-w-md bg-white rounded-xl shadow-lg border border-gray-200 p-8">
        <h1 className="text-2xl font-bold text-center text-emotive-gray-header mb-6">Iniciar sessão</h1>
        <form onSubmit={handleSubmit} className="space-y-4">
          {error && (
            <div className="p-3 rounded-lg bg-red-50 text-red-700 text-sm">{error}</div>
          )}
          <div>
            <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-1">
              Email
            </label>
            <input
              id="email"
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              placeholder="tu@email.com"
            />
          </div>
          <div>
            <label htmlFor="password" className="block text-sm font-medium text-gray-700 mb-1">
              Contraseña
            </label>
            <input
              id="password"
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
            />
          </div>
          <div className="text-right text-sm">
            <Link href="/auth/forgot-password" className="text-primary hover:underline">
              ¿Olvidaste tu contraseña?
            </Link>
          </div>
          <button
            type="submit"
            disabled={loading}
            className="w-full py-3 bg-primary text-white font-medium rounded-lg hover:bg-primary-dark disabled:opacity-50 transition"
          >
            {loading ? "Entrando…" : "Entrar"}
          </button>
        </form>
        <p className="mt-6 text-center text-gray-600 text-sm">
          Não tem conta?{" "}
          <Link href="/auth/register" className="text-primary font-medium hover:underline">
            Registre-se
          </Link>
        </p>
        <p className="mt-2 text-center">
          <Link href="/" className="text-gray-500 text-sm hover:underline">
            ← Voltar ao início
          </Link>
        </p>
      </div>
    </main>
  );
}
