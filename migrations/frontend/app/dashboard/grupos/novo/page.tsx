"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { useRouter, useSearchParams } from "next/navigation";
import { api } from "@/lib/api";
import { getStoredUser } from "@/lib/auth";
import type { Cliente } from "@/types";

export default function NovoGrupoPage() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const user = getStoredUser();
  const [clientes, setClientes] = useState<Cliente[]>([]);
  const [clienteId, setClienteId] = useState(searchParams.get("cliente_id") || "");
  const [nome, setNome] = useState("");
  const [unidade, setUnidade] = useState("");
  const [area, setArea] = useState("");
  const [nivelJerarquico, setNivelJerarquico] = useState("");
  const [tempoEmpresa, setTempoEmpresa] = useState("");
  const [modeloTrabalho, setModeloTrabalho] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);
  const isGestorOnly = user?.gestor && !user?.admin && !user?.sa;

  useEffect(() => {
    if (user?.admin || user?.sa) api<Cliente[]>("/clientes").then(setClientes).catch(() => {});
    if (isGestorOnly && user?.cliente_id) setClienteId(String(user.cliente_id));
  }, [user, isGestorOnly]);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError("");
    const cid = isGestorOnly ? user!.cliente_id! : (clienteId ? parseInt(clienteId, 10) : null);
    if (!cid || !nome.trim()) {
      setError("Selecione o cliente e informe o nome do grupo.");
      return;
    }
    setLoading(true);
    try {
      await api("/grupos", {
        method: "POST",
        body: JSON.stringify({
          cliente_id: cid,
          nome: nome.trim(),
          unidade: unidade || null,
          area: area || null,
          nivel_jerarquico: nivelJerarquico || null,
          tempo_empresa: tempoEmpresa || null,
          modelo_trabalho: modeloTrabalho || null,
        }),
      });
      router.push("/dashboard/grupos");
      router.refresh();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Erro ao criar grupo");
    } finally {
      setLoading(false);
    }
  }

  return (
    <div>
      <div className="mb-6"><Link href="/dashboard/grupos" className="text-gray-600 hover:text-primary text-sm">← Grupos</Link></div>
      <h1 className="text-2xl font-bold text-emotive-gray-header mb-6">Novo grupo</h1>
      <form onSubmit={handleSubmit} className="max-w-xl space-y-4 bg-white p-6 rounded-xl border border-gray-200">
        {error && <div className="p-3 rounded-lg bg-red-50 text-red-700 text-sm">{error}</div>}
        {!isGestorOnly && (
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
          <label className="block text-sm font-medium text-gray-700 mb-1">Nome do grupo</label>
          <input type="text" value={nome} onChange={(e) => setNome(e.target.value)} required placeholder="Ex.: Equipe Comercial SP" className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
        </div>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label className="block text-xs text-gray-500 mb-0.5">Unidade</label>
            <input type="text" value={unidade} onChange={(e) => setUnidade(e.target.value)} placeholder="Ex.: São Paulo" className="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm" />
          </div>
          <div>
            <label className="block text-xs text-gray-500 mb-0.5">Área</label>
            <input type="text" value={area} onChange={(e) => setArea(e.target.value)} placeholder="Ex.: Vendas" className="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm" />
          </div>
          <div>
            <label className="block text-xs text-gray-500 mb-0.5">Nível hierárquico</label>
            <input type="text" value={nivelJerarquico} onChange={(e) => setNivelJerarquico(e.target.value)} placeholder="Ex.: Coordenador" className="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm" />
          </div>
          <div>
            <label className="block text-xs text-gray-500 mb-0.5">Tempo de empresa</label>
            <input type="text" value={tempoEmpresa} onChange={(e) => setTempoEmpresa(e.target.value)} placeholder="Ex.: 2-5 anos" className="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm" />
          </div>
          <div className="sm:col-span-2">
            <label className="block text-xs text-gray-500 mb-0.5">Modelo de trabalho</label>
            <input type="text" value={modeloTrabalho} onChange={(e) => setModeloTrabalho(e.target.value)} placeholder="Ex.: Híbrido" className="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm" />
          </div>
        </div>
        <div className="flex gap-3 pt-2">
          <button type="submit" disabled={loading} className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark disabled:opacity-50">{loading ? "Salvando…" : "Criar"}</button>
          <Link href="/dashboard/grupos" className="px-4 py-2 border border-slate-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancelar</Link>
        </div>
      </form>
    </div>
  );
}
