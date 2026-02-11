"use client";

import { useState } from "react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { register } from "@/lib/auth";

export default function RegisterPage() {
  const router = useRouter();
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError("");
    setLoading(true);
    try {
      await register({ name, email, password, usuario: true });
      router.push("/auth/login?registered=1");
      router.refresh();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Error al registrarse");
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
        <h1 className="text-2xl font-bold text-center text-emotive-gray-header mb-6">Registrar-se</h1>
        <form onSubmit={handleSubmit} className="space-y-4">
          {error && (
            <div className="p-3 rounded-lg bg-red-50 text-red-700 text-sm">{error}</div>
          )}
          <div>
            <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-1">Nome</label>
            <input
              id="name"
              type="text"
              value={name}
              onChange={(e) => setName(e.target.value)}
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              placeholder="Seu nome"
            />
          </div>
          <div>
            <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-1">Email</label>
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
            <label htmlFor="password" className="block text-sm font-medium text-gray-700 mb-1">Senha</label>
            <input
              id="password"
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
              minLength={6}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              placeholder="Mínimo 6 caracteres"
            />
          </div>
          <button
            type="submit"
            disabled={loading}
            className="w-full py-3 bg-primary text-white font-medium rounded-lg hover:bg-primary-dark disabled:opacity-50 transition"
          >
            {loading ? "Registrando…" : "Registrar-se"}
          </button>
        </form>
        <p className="mt-6 text-center text-gray-600 text-sm">
          Já tem conta?{" "}
          <Link href="/auth/login" className="text-primary font-medium hover:underline">Iniciar sessão</Link>
        </p>
        <p className="mt-2 text-center">
          <Link href="/" className="text-gray-500 text-sm hover:underline">← Voltar ao início</Link>
        </p>
      </div>
    </main>
  );
}
