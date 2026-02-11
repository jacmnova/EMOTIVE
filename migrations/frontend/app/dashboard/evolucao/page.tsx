"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { api } from "@/lib/api";
import { getStoredUser } from "@/lib/auth";
import type { Cliente, Periodo } from "@/types";

interface ComparacaoRow {
  valor: string;
  periodo_1: { total: number; completos: number; percentual: number };
  periodo_2: { total: number; completos: number; percentual: number };
  variacao_percentual: number;
}

interface EvolucaoResponse {
  periodo_1_id: number;
  periodo_2_id: number;
  periodo_1: Record<string, { total: number; completos: number; percentual: number }>;
  periodo_2: Record<string, { total: number; completos: number; percentual: number }>;
  comparacao: ComparacaoRow[];
}

export default function EvolucaoPage() {
  const user = getStoredUser();
  const [clientes, setClientes] = useState<Cliente[]>([]);
  const [periodos, setPeriodos] = useState<Periodo[]>([]);
  const [clienteId, setClienteId] = useState("");
  const [periodoId1, setPeriodoId1] = useState("");
  const [periodoId2, setPeriodoId2] = useState("");
  const [grupo, setGrupo] = useState<"unidade" | "area" | "nivel_jerarquico">("unidade");
  const [data, setData] = useState<EvolucaoResponse | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const isGestorOnly = user?.gestor && !user?.admin && !user?.sa;

  useEffect(() => {
    const u = getStoredUser();
    if (u?.admin || u?.sa) api<Cliente[]>("/clientes").then(setClientes).catch(() => {});
    api<Periodo[]>("/periodos").then(setPeriodos).catch(() => {});
    if (u?.gestor && !u?.admin && !u?.sa && u?.cliente_id) setClienteId(String(u.cliente_id));
  }, []);

  function loadEvolucao() {
    if (!periodoId1 || !periodoId2) {
      setError("Selecione os dois períodos.");
      return;
    }
    const params = new URLSearchParams();
    params.set("periodo_id_1", periodoId1);
    params.set("periodo_id_2", periodoId2);
    params.set("grupo", grupo);
    if (!isGestorOnly && clienteId) params.set("cliente_id", clienteId);
    setError("");
    setLoading(true);
    api<EvolucaoResponse>(`/reportes/evolucao?${params.toString()}`)
      .then(setData)
      .catch((e) => {
        setData(null);
        setError(e instanceof Error ? e.message : "Erro ao carregar");
      })
      .finally(() => setLoading(false));
  }

  return (
    <div>
      <Link href="/dashboard/relatorio-corporativo" className="text-gray-600 hover:text-primary text-sm">← Relatório corporativo</Link>
      <h1 className="text-2xl font-bold text-emotive-gray-header mt-2 mb-2">Evolução (comparar períodos)</h1>
      <p className="text-gray-600 text-sm mb-6">Compare participação e % completo do mesmo grupo em dois períodos.</p>

      <div className="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-200 flex flex-wrap items-end gap-4">
        {!isGestorOnly && (
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
            <select value={clienteId} onChange={(e) => setClienteId(e.target.value)} className="px-3 py-2 border border-slate-300 rounded-lg min-w-[200px]">
              <option value="">— Selecionar —</option>
              {clientes.map((c) => (
                <option key={c.id} value={c.id}>{c.nome_fantasia || c.razao_social || c.cpf_cnpj}</option>
              ))}
            </select>
          </div>
        )}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Período 1</label>
          <select value={periodoId1} onChange={(e) => setPeriodoId1(e.target.value)} className="px-3 py-2 border border-slate-300 rounded-lg min-w-[180px]">
            <option value="">— Selecionar —</option>
            {periodos.map((p) => (
              <option key={p.id} value={p.id}>{p.nome}</option>
            ))}
          </select>
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Período 2</label>
          <select value={periodoId2} onChange={(e) => setPeriodoId2(e.target.value)} className="px-3 py-2 border border-slate-300 rounded-lg min-w-[180px]">
            <option value="">— Selecionar —</option>
            {periodos.map((p) => (
              <option key={p.id} value={p.id}>{p.nome}</option>
            ))}
          </select>
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Agrupar por</label>
          <select value={grupo} onChange={(e) => setGrupo(e.target.value as any)} className="px-3 py-2 border border-slate-300 rounded-lg">
            <option value="unidade">Unidade</option>
            <option value="area">Área</option>
            <option value="nivel_jerarquico">Nível hierárquico</option>
          </select>
        </div>
        <button type="button" onClick={loadEvolucao} disabled={loading || (!isGestorOnly && !clienteId)} className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark disabled:opacity-50">
          {loading ? "Carregando…" : "Comparar"}
        </button>
      </div>

      {error && <div className="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">{error}</div>}
      {data && data.comparacao.length === 0 && !loading && (
        <div className="mb-4 p-3 rounded-lg bg-amber-50 text-amber-800 text-sm border border-amber-200">
          Nenhum dado para comparar para este cliente e períodos. Verifique se há usuários e atribuições nos dois períodos.
        </div>
      )}
      {data && data.comparacao.length > 0 && (
        <>
          <div className="mb-6 bg-white rounded-xl border border-gray-200 overflow-hidden">
            <h3 className="px-4 py-3 bg-gray-50 font-medium text-emotive-gray-header border-b border-gray-200">Gráfico – comparação P1 vs P2 (% completo)</h3>
            <div className="p-4 space-y-4">
              {data.comparacao.slice(0, 10).map((r) => (
                <div key={r.valor}>
                  <p className="text-sm font-medium text-gray-700 mb-1 truncate" title={r.valor}>{r.valor}</p>
                  <div className="flex items-center gap-2">
                    <div className="flex-1 flex gap-1">
                      <div className="flex-1 h-6 bg-gray-100 rounded overflow-hidden" title={`P1: ${r.periodo_1.percentual}%`}>
                        <div className="h-full bg-slate-400 rounded" style={{ width: `${Math.min(100, r.periodo_1.percentual)}%` }} />
                      </div>
                      <div className="flex-1 h-6 bg-gray-100 rounded overflow-hidden" title={`P2: ${r.periodo_2.percentual}%`}>
                        <div className="h-full bg-primary rounded" style={{ width: `${Math.min(100, r.periodo_2.percentual)}%` }} />
                      </div>
                    </div>
                    <span className={`text-xs font-medium w-16 ${r.variacao_percentual >= 0 ? "text-green-600" : "text-red-600"}`}>
                      {r.variacao_percentual >= 0 ? "+" : ""}{r.variacao_percentual} p.p.
                    </span>
                  </div>
                  <p className="text-xs text-gray-500 mt-0.5">P1: {r.periodo_1.percentual}% · P2: {r.periodo_2.percentual}%</p>
                </div>
              ))}
            </div>
          </div>
          <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table className="w-full text-left">
              <thead className="bg-gray-50 border-b border-gray-200">
                <tr>
                  <th className="px-4 py-3 font-medium text-gray-700">Grupo</th>
                  <th className="px-4 py-3 font-medium text-gray-700">P1 total</th>
                  <th className="px-4 py-3 font-medium text-gray-700">P1 %</th>
                  <th className="px-4 py-3 font-medium text-gray-700">P2 total</th>
                  <th className="px-4 py-3 font-medium text-gray-700">P2 %</th>
                  <th className="px-4 py-3 font-medium text-gray-700">Variação (p.p.)</th>
                </tr>
              </thead>
              <tbody>
                {data.comparacao.map((r) => (
                  <tr key={r.valor} className="border-b border-gray-50">
                    <td className="px-4 py-2">{r.valor}</td>
                    <td className="px-4 py-2">{r.periodo_1.total}</td>
                    <td className="px-4 py-2">{r.periodo_1.percentual}%</td>
                    <td className="px-4 py-2">{r.periodo_2.total}</td>
                    <td className="px-4 py-2">{r.periodo_2.percentual}%</td>
                    <td className={`px-4 py-2 font-medium ${r.variacao_percentual >= 0 ? "text-green-600" : "text-red-600"}`}>
                      {r.variacao_percentual >= 0 ? "+" : ""}{r.variacao_percentual} p.p.
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </>
      )}
      {data && data.comparacao.length === 0 && <p className="text-gray-500">Nenhum dado para comparar nos períodos selecionados.</p>}
    </div>
  );
}
