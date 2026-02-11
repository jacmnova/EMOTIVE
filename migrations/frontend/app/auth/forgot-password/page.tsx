"use client";

import { useState } from "react";
import Link from "next/link";
import { forgotPassword } from "@/lib/auth";

export default function ForgotPasswordPage() {
  const [email, setEmail] = useState("");
  const [error, setError] = useState("");
  const [sent, setSent] = useState(false);
  const [loading, setLoading] = useState(false);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError("");
    setLoading(true);
    try {
      await forgotPassword(email);
      setSent(true);
    } catch (err) {
      setError(err instanceof Error ? err.message : "Error al enviar");
    } finally {
      setLoading(false);
    }
  }

  if (sent) {
    return (
      <main className="min-h-screen flex items-center justify-center p-4 bg-slate-50">
        <div className="w-full max-w-md bg-white rounded-xl shadow-lg p-8 text-center">
          <h1 className="text-2xl font-bold text-accent mb-4">Revisa tu email</h1>
          <p className="text-slate-600 mb-6">
            Si existe una cuenta con <strong>{email}</strong>, recibirás un enlace para restablecer tu contraseña.
          </p>
          <Link
            href="/auth/login"
            className="inline-block px-6 py-3 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark"
          >
            Volver al login
          </Link>
        </div>
      </main>
    );
  }

  return (
    <main className="min-h-screen flex items-center justify-center p-4 bg-slate-50">
      <div className="w-full max-w-md bg-white rounded-xl shadow-lg p-8">
        <h1 className="text-2xl font-bold text-center text-accent mb-6">Recuperar contraseña</h1>
        <form onSubmit={handleSubmit} className="space-y-4">
          {error && (
            <div className="p-3 rounded-lg bg-red-50 text-red-700 text-sm">{error}</div>
          )}
          <div>
            <label htmlFor="email" className="block text-sm font-medium text-slate-700 mb-1">
              Email
            </label>
            <input
              id="email"
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
              className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              placeholder="tu@email.com"
            />
          </div>
          <button
            type="submit"
            disabled={loading}
            className="w-full py-3 bg-primary text-white font-medium rounded-lg hover:bg-primary-dark disabled:opacity-50 transition"
          >
            {loading ? "Enviando…" : "Enviar enlace"}
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
