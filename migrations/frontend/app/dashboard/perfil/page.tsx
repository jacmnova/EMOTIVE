"use client";

import { useState, useEffect } from "react";
import { getStoredUser } from "@/lib/auth";
import { api } from "@/lib/api";
import type { User } from "@/types";

export default function PerfilPage() {
  const stored = getStoredUser();
  const [user, setUser] = useState<User | null>(stored);
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");
  const [loading, setLoading] = useState(false);
  const [resendLoading, setResendLoading] = useState(false);
  const [resendMsg, setResendMsg] = useState("");

  useEffect(() => {
    if (!stored) return;
    setName(stored.name);
    setEmail(stored.email);
    api<User>(`/users/${stored.id}`)
      .then((u) => {
        setUser(u);
        setName(u.name);
        setEmail(u.email);
      })
      .catch(() => {});
  }, [stored?.id]);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    if (!user) return;
    setError("");
    setSuccess("");
    setLoading(true);
    try {
      const updated = await api<User>(`/users/${user.id}`, {
        method: "PUT",
        body: JSON.stringify({ name, email }),
      });
      setUser(updated);
      if (typeof window !== "undefined" && window.localStorage) {
        const raw = localStorage.getItem("user");
        if (raw) {
          const prev = JSON.parse(raw) as User;
          localStorage.setItem("user", JSON.stringify({ ...prev, name: updated.name, email: updated.email }));
        }
      }
      setSuccess("Dados atualizados.");
    } catch (err) {
      setError(err instanceof Error ? err.message : "Erro ao atualizar");
    } finally {
      setLoading(false);
    }
  }

  if (!user) return <p className="text-gray-500">Carregando…</p>;

  return (
    <div className="max-w-xl">
      <h1 className="text-2xl font-bold text-emotive-gray-header mb-6">Meu perfil</h1>
      <form onSubmit={handleSubmit} className="space-y-4 bg-white p-6 rounded-xl border border-gray-200">
        {error && <div className="p-3 rounded-lg bg-red-50 text-red-700 text-sm">{error}</div>}
        {success && <div className="p-3 rounded-lg bg-green-50 text-green-700 text-sm">{success}</div>}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Nome</label>
          <input type="text" value={name} onChange={(e) => setName(e.target.value)} required className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input type="email" value={email} onChange={(e) => setEmail(e.target.value)} required className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
        </div>
        {!user.email_verified_at && (
          <div className="pt-2 border-t border-gray-200">
            <p className="text-sm text-gray-600 mb-2">Seu e-mail ainda não foi verificado.</p>
            <button
              type="button"
              disabled={resendLoading}
              onClick={async () => {
                setResendMsg("");
                setResendLoading(true);
                try {
                  await api<{ message: string }>("/auth/reenviar-verificacion", { method: "POST" });
                  setResendMsg("E-mail de verificação reenviado! Verifique sua caixa de entrada.");
                } catch (e) {
                  setResendMsg(e instanceof Error ? e.message : "Erro ao reenviar.");
                } finally {
                  setResendLoading(false);
                }
              }}
              className="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 disabled:opacity-50"
            >
              {resendLoading ? "Enviando…" : "Reenviar e-mail de verificação"}
            </button>
            {resendMsg && <p className="mt-2 text-sm text-gray-600">{resendMsg}</p>}
          </div>
        )}
        <div className="text-sm text-gray-500">
          Para alterar sua senha, use a opção &quot;Esqueci minha senha&quot; na tela de login.
        </div>
        <button type="submit" disabled={loading} className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark disabled:opacity-50">
          {loading ? "Salvando…" : "Salvar alterações"}
        </button>
      </form>
    </div>
  );
}
