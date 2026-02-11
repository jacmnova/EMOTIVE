"use client";

import { useState } from "react";

const API_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000";
const API_V1 = `${API_URL}/api/v1`;

export default function FormularioContato() {
  const [nome, setNome] = useState("");
  const [email, setEmail] = useState("");
  const [mensagem, setMensagem] = useState("");
  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError(null);
    setLoading(true);
    try {
      const res = await fetch(`${API_V1}/contato/enviar`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ nome, email, mensagem }),
      });
      const data = await res.json().catch(() => ({}));
      if (!res.ok) {
        throw new Error(data.detail || data.message || `Erro ${res.status}`);
      }
      setSuccess(true);
      setNome("");
      setEmail("");
      setMensagem("");
    } catch (err) {
      setError(err instanceof Error ? err.message : "Erro ao enviar mensagem.");
    } finally {
      setLoading(false);
    }
  }

  if (success) {
    return (
      <div className="rounded-lg bg-green-50 border border-green-200 p-4 text-green-800 text-sm">
        Mensagem enviada com sucesso! Entraremos em contato em breve.
      </div>
    );
  }

  return (
    <div>
      <p className="text-slate-600 mb-4">
        Tem alguma dúvida adicional ou interesse em aplicar o E.MO.TI.VE na sua empresa? Envie sua mensagem e entraremos em contato em breve.
      </p>
      <form onSubmit={handleSubmit} className="space-y-4 text-left max-w-md mx-auto">
        <div>
          <label htmlFor="nome" className="block text-slate-700 font-medium mb-1">
            Nome <span className="text-red-500">*</span>
          </label>
          <input
            id="nome"
            type="text"
            value={nome}
            onChange={(e) => setNome(e.target.value)}
            required
            className="w-full rounded border border-slate-300 px-3 py-2 text-sm"
            placeholder="Seu nome completo"
          />
        </div>
        <div>
          <label htmlFor="email" className="block text-slate-700 font-medium mb-1">
            Email <span className="text-red-500">*</span>
          </label>
          <input
            id="email"
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
            className="w-full rounded border border-slate-300 px-3 py-2 text-sm"
            placeholder="seu@email.com"
          />
        </div>
        <div>
          <label htmlFor="mensagem" className="block text-slate-700 font-medium mb-1">
            Mensagem <span className="text-red-500">*</span>
          </label>
          <textarea
            id="mensagem"
            value={mensagem}
            onChange={(e) => setMensagem(e.target.value)}
            required
            rows={5}
            className="w-full rounded border border-slate-300 px-3 py-2 text-sm"
            placeholder="Digite sua mensagem aqui..."
          />
        </div>
        {error && (
          <p className="text-red-600 text-sm">{error}</p>
        )}
        <button
          type="submit"
          disabled={loading}
          className="w-full py-3 px-4 bg-[#0087a0] text-white font-medium rounded-lg hover:bg-[#006d82] disabled:opacity-50 transition"
        >
          {loading ? "Enviando..." : "Enviar mensagem"}
        </button>
      </form>
    </div>
  );
}
