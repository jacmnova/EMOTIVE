"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { api } from "@/lib/api";
import { getStoredUser } from "@/lib/auth";
import type { Cliente, Projeto } from "@/types";

export default function NovoPeriodoPage() {
  const router = useRouter();
  const user = getStoredUser();
  const [clientes, setClientes] = useState<Cliente[]>([]);
  const [projetos, setProjetos] = useState<Projeto[]>([]);
  const [clienteId, setClienteId] = useState<string>("");
  const [projetoId, setProjetoId] = useState<string>("");
  const [nome, setNome] = useState("");
  const [descricao, setDescricao] = useState("");
  const [dataInicio, setDataInicio] = useState("");
  const [dataFim, setDataFim] = useState("");
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const isGestorOnly = user?.gestor && !user?.admin && !user?.sa;

  useEffect(() => {
    if (user?.admin || user?.sa) {
      api<Cliente[]>("/clientes").then(setClientes).catch(() => {});
    } else if (user?.gestor && user?.cliente_id) {
      setClienteId(String(user.cliente_id));
    }
  }, [user]);

  useEffect(() => {
    const cid = clienteId || (isGestorOnly ? String(user?.cliente_id ?? "") : "");
    if (!cid) {
      setProjetos([]);
      return;
    }
    api<Projeto[]>(`/projetos?cliente_id=${cid}`).then(setProjetos).catch(() => setProjetos([]));
  }, [clienteId, isGestorOnly, user?.cliente_id]);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError("");
    const cid = isGestorOnly ? user!.cliente_id! : (clienteId ? parseInt(clienteId, 10) : null);
    if (!cid) {
      setError("Selecione o cliente.");
      return;
    }
    setLoading(true);
    try {
      await api("/periodos", {
        method: "POST",
        body: JSON.stringify({
          cliente_id: cid,
          nome: nome.trim() || "Sem nome",
          descricao: descricao.trim() || null,
          data_inicio: dataInicio || null,
          data_fim: dataFim || null,
        }),
      });
      router.push("/dashboard/periodos?criado=1");
      router.refresh();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Erro ao criar período");
    } finally {
      setLoading(false);
    }
  }

  return (
    <div>
      <Link href="/dashboard/periodos" className="text-gray-600 hover:text-primary text-sm">← Períodos</Link>
      <h1 className="text-2xl font-bold text-emotive-gray-header mt-2 mb-6">Novo período</h1>
      <form onSubmit={handleSubmit} className="max-w-xl space-y-4 bg-white p-6 rounded-xl border border-gray-200">
        {error && <div className="p-3 rounded-lg bg-red-50 text-red-700 text-sm">{error}</div>}
        {!isGestorOnly && (user?.admin || user?.sa) && (
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
            <select
              value={clienteId}
              onChange={(e) => setClienteId(e.target.value)}
              required
              className="w-full px-3 py-2 border border-slate-300 rounded-lg"
            >
              <option value="">— Selecionar —</option>
              {clientes.map((c) => (
                <option key={c.id} value={c.id}>{c.nome_fantasia || c.razao_social || c.cpf_cnpj}</option>
              ))}
            </select>
          </div>
        )}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Projeto (opcional)</label>
          <select value={projetoId} onChange={(e) => setProjetoId(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg">
            <option value="">— Nenhum —</option>
            {projetos.map((p) => (
              <option key={p.id} value={p.id}>{p.nome}</option>
            ))}
          </select>
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Nome</label>
          <input type="text" value={nome} onChange={(e) => setNome(e.target.value)} required className="w-full px-3 py-2 border border-slate-300 rounded-lg" placeholder="Ex.: Onda 1 - 2025" />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Descrição (opcional)</label>
          <input type="text" value={descricao} onChange={(e) => setDescricao(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
        </div>
        <div className="grid grid-cols-2 gap-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Data início</label>
            <input type="date" value={dataInicio} onChange={(e) => setDataInicio(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Data fim</label>
            <input type="date" value={dataFim} onChange={(e) => setDataFim(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
          </div>
        </div>
        <div className="flex gap-3 pt-2">
          <button type="submit" disabled={loading} className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark disabled:opacity-50">
            {loading ? "Criando…" : "Criar"}
          </button>
          <Link href="/dashboard/periodos" className="px-4 py-2 border border-slate-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancelar</Link>
        </div>
      </form>
    </div>
  );
}
