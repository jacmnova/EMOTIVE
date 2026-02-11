"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { api } from "@/lib/api";
import { getStoredUser } from "@/lib/auth";
import type { Cliente } from "@/types";

export default function NovoProjetoPage() {
  const router = useRouter();
  const user = getStoredUser();
  const [clientes, setClientes] = useState<Cliente[]>([]);
  const [clienteId, setClienteId] = useState("");
  const [nome, setNome] = useState("");
  const [descricao, setDescricao] = useState("");
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const isGestorOnly = user?.gestor && !user?.admin && !user?.sa;

  useEffect(() => {
    if (user?.admin || user?.sa) api<Cliente[]>("/clientes").then(setClientes).catch(() => {});
    else if (user?.gestor && user?.cliente_id) setClienteId(String(user.cliente_id));
  }, [user]);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    const cid = isGestorOnly ? user!.cliente_id! : (clienteId ? parseInt(clienteId, 10) : null);
    if (!cid) {
      setError("Selecione o cliente.");
      return;
    }
    setError("");
    setLoading(true);
    try {
      await api("/projetos", {
        method: "POST",
        body: JSON.stringify({ cliente_id: cid, nome: nome.trim() || "Sem nome", descricao: descricao.trim() || null }),
      });
      router.push("/dashboard/projetos");
      router.refresh();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Erro ao criar projeto");
    } finally {
      setLoading(false);
    }
  }

  return (
    <div>
      <Link href="/dashboard/projetos" className="text-gray-600 hover:text-primary text-sm">← Projetos</Link>
      <h1 className="text-2xl font-bold text-emotive-gray-header mt-2 mb-6">Novo projeto</h1>
      <form onSubmit={handleSubmit} className="max-w-xl space-y-4 bg-white p-6 rounded-xl border border-gray-200">
        {error && <div className="p-3 rounded-lg bg-red-50 text-red-700 text-sm">{error}</div>}
        {!isGestorOnly && (user?.admin || user?.sa) && (
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
            <select value={clienteId} onChange={(e) => setClienteId(e.target.value)} required className="w-full px-3 py-2 border border-slate-300 rounded-lg">
              <option value="">— Selecionar —</option>
              {clientes.map((c) => (
                <option key={c.id} value={c.id}>{c.nome_fantasia || c.razao_social || c.cpf_cnpj}</option>
              ))}
            </select>
          </div>
        )}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Nome</label>
          <input type="text" value={nome} onChange={(e) => setNome(e.target.value)} required className="w-full px-3 py-2 border border-slate-300 rounded-lg" placeholder="Ex.: Bem-estar 2025" />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Descrição (opcional)</label>
          <input type="text" value={descricao} onChange={(e) => setDescricao(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
        </div>
        <div className="flex gap-3 pt-2">
          <button type="submit" disabled={loading} className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark disabled:opacity-50">{loading ? "Criando…" : "Criar"}</button>
          <Link href="/dashboard/projetos" className="px-4 py-2 border border-slate-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancelar</Link>
        </div>
      </form>
    </div>
  );
}
