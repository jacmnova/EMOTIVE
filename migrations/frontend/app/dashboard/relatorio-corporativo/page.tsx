"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { api } from "@/lib/api";
import { getStoredUser } from "@/lib/auth";
import type { Cliente, Periodo, Formulario, RelatorioGeradoItem } from "@/types";

type TabId = "dimensao" | "eixo" | "descarrilamento" | "relatorios";

interface GrupoRow {
  valor: string;
  total: number;
  completos: number;
  percentual: number;
}

interface SetorCriticoRow extends GrupoRow {
  em_risco: number;
}

interface AgregadoResponse {
  por_unidade: GrupoRow[];
  por_area: GrupoRow[];
  por_nivel_jerarquico: GrupoRow[];
  por_tempo_empresa: GrupoRow[];
  por_modelo_trabalho: GrupoRow[];
  resumo: { total_asignaciones: number; total_completos: number; percentual_geral: number };
}

interface KpisResponse {
  colaboradores_em_risco: number;
  setores_criticos_unidade: SetorCriticoRow[];
  setores_criticos_area: SetorCriticoRow[];
}

interface OndeSeConcentramArea {
  area: string;
  total: number;
  baixa: number;
  moderada: number;
  alta: number;
  critico: number;
}

interface OndeSeConcentramItem {
  unidade: string;
  total: number;
  baixa: number;
  moderada: number;
  alta: number;
  critico: number;
  iid_medio: number;
  areas: OndeSeConcentramArea[];
}

interface KpisIidResponse {
  colaboradores_em_risco: number;
  total_alta: number;
  total_critico: number;
  total_respondentes: number;
  percentual_risco: number;
  setores_criticos_unidade: { valor: string; total: number; baixa: number; moderada: number; alta: number; critico: number; iid_medio: number }[];
  setores_criticos_area: { valor: string; total: number; baixa: number; moderada: number; alta: number; critico: number; iid_medio: number }[];
  onde_se_concentram: OndeSeConcentramItem[];
}

interface EvolucaoIidItem {
  periodo_id: number;
  periodo_nome: string;
  iid_medio: number;
  total_respondentes: number;
  iid_pontos?: number[];
}

interface EvolucaoIidResponse {
  evolucao: EvolucaoIidItem[];
}

const TABS: { id: TabId; label: string }[] = [
  { id: "descarrilamento", label: "Descarregamento (IID)" },
  { id: "dimensao", label: "Dimensão" },
  { id: "eixo", label: "Eixos" },
  { id: "relatorios", label: "Relatórios" },
];

const DIMENSOES_CHIPS = [
  "Todas",
  "Exaustão Emocional",
  "Realização Profissional",
  "Despersonalização / Cinismo",
  "Fatores Psicossociais",
  "Assédio Moral",
  "Excesso de Trabalho",
];

export default function RelatorioCorporativoPage() {
  const user = getStoredUser();
  const [clientes, setClientes] = useState<Cliente[]>([]);
  const [periodos, setPeriodos] = useState<Periodo[]>([]);
  const [formularios, setFormularios] = useState<Formulario[]>([]);
  const [clienteId, setClienteId] = useState<string>("");
  const [periodoId, setPeriodoId] = useState<string>("");
  const [formularioId, setFormularioId] = useState<string>("");
  const [unidadeFilter, setUnidadeFilter] = useState<string>("");
  const [areaFilter, setAreaFilter] = useState<string>("");
  const [nivelFilter, setNivelFilter] = useState<string>("");
  const [tempoFilter, setTempoFilter] = useState<string>("");
  const [modeloFilter, setModeloFilter] = useState<string>("");
  const [data, setData] = useState<AgregadoResponse | null>(null);
  const [kpis, setKpis] = useState<KpisResponse | null>(null);
  const [kpisIid, setKpisIid] = useState<KpisIidResponse | null>(null);
  const [evolucaoIid, setEvolucaoIid] = useState<EvolucaoIidItem[]>([]);
  const [tab, setTab] = useState<TabId>("dimensao");
  const [dimensionChip, setDimensionChip] = useState<string>("Todas");
  const [expandedRiscos, setExpandedRiscos] = useState<Set<string>>(new Set());
  const [expandedUnidades, setExpandedUnidades] = useState<Set<string>>(new Set());
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [relatoriosGerados, setRelatoriosGerados] = useState<RelatorioGeradoItem[]>([]);
  const [loadingGerados, setLoadingGerados] = useState(false);
  const isGestorOnly = user?.gestor && !user?.admin && !user?.sa;

  // Cargar listas una sola vez al montar para evitar cientos de llamadas al backend
  useEffect(() => {
    const u = getStoredUser();
    if (u?.admin || u?.sa) {
      api<Cliente[]>("/clientes").then(setClientes).catch(() => {});
    }
    api<Periodo[]>("/periodos").then(setPeriodos).catch(() => {});
    api<Formulario[]>("/formularios").then(setFormularios).catch(() => {});
    if (u?.gestor && !u?.admin && !u?.sa && u?.cliente_id) setClienteId(String(u.cliente_id));
  }, []);

  function loadAgregado() {
    const params = new URLSearchParams();
    if (!isGestorOnly && clienteId) params.set("cliente_id", clienteId);
    if (periodoId) params.set("periodo_id", periodoId);
    if (formularioId) params.set("formulario_id", formularioId);
    setError("");
    setLoading(true);
    const q = params.toString();
    const periodoIdsForEvolucao = periodos.slice(0, 4).map((p) => p.id).join(",");
    const promises: Promise<unknown>[] = [
      api<AgregadoResponse>(`/reportes/agregado-grupo?${q}`),
      api<KpisResponse>(`/reportes/kpis?${q}`).catch(() => null),
    ];
    if (formularioId) {
      const qIid = new URLSearchParams();
      if (!isGestorOnly && clienteId) qIid.set("cliente_id", clienteId);
      if (periodoId) qIid.set("periodo_id", periodoId);
      qIid.set("formulario_id", formularioId);
      promises.push(api<KpisIidResponse>(`/reportes/kpis-iid?${qIid.toString()}`).catch(() => null));
      const qEv = new URLSearchParams();
      if (!isGestorOnly && clienteId) qEv.set("cliente_id", clienteId);
      qEv.set("formulario_id", formularioId);
      if (periodoIdsForEvolucao) qEv.set("periodo_ids", periodoIdsForEvolucao);
      promises.push(api<EvolucaoIidResponse>(`/reportes/evolucao-iid?${qEv.toString()}`).catch(() => ({ evolucao: [] })));
    }
    Promise.all(promises)
      .then((results) => {
        setData(results[0] as AgregadoResponse);
        setKpis((results[1] as KpisResponse | null) ?? null);
        if (formularioId && results[2] != null) setKpisIid((results[2] as KpisIidResponse) ?? null);
        else setKpisIid(null);
        if (formularioId && results[3] != null) setEvolucaoIid((results[3] as EvolucaoIidResponse)?.evolucao ?? []);
        else setEvolucaoIid([]);
      })
      .catch((e) => {
        setData(null);
        setKpis(null);
        setKpisIid(null);
        setEvolucaoIid([]);
        setError(e instanceof Error ? e.message : "Erro ao carregar");
      })
      .finally(() => setLoading(false));
  }

  useEffect(() => {
    if (isGestorOnly && user?.cliente_id) loadAgregado();
  }, [isGestorOnly, user?.cliente_id]);

  function loadRelatoriosGerados() {
    const cid = clienteId || (isGestorOnly && user?.cliente_id ? String(user.cliente_id) : "");
    if (!cid) {
      setRelatoriosGerados([]);
      return;
    }
    setLoadingGerados(true);
    api<{ relatorios: RelatorioGeradoItem[] }>("/reportes/gerados?cliente_id=" + cid)
      .then((r) => setRelatoriosGerados(r.relatorios ?? []))
      .catch(() => setRelatoriosGerados([]))
      .finally(() => setLoadingGerados(false));
  }

  useEffect(() => {
    if (tab === "relatorios") loadRelatoriosGerados();
  }, [tab, clienteId, isGestorOnly, user?.cliente_id]);

  const useIid = !!kpisIid && (kpisIid.total_respondentes > 0 || kpisIid.colaboradores_em_risco > 0);
  const totalColaboradores = useIid ? (kpisIid?.total_respondentes ?? 0) : (data?.resumo?.total_asignaciones ?? 0);
  const percentualRisco = useIid ? (kpisIid?.percentual_risco ?? 0) : (totalColaboradores ? Math.round(((kpis?.colaboradores_em_risco ?? 0) / totalColaboradores) * 100) : 0);
  const setoresCriticosList = kpis?.setores_criticos_area ?? [];
  const setoresCriticosIid = kpisIid?.setores_criticos_area ?? [];
  const numSetoresCriticos = useIid ? setoresCriticosIid.length : setoresCriticosList.length;
  const ondeSeConcentram = kpisIid?.onde_se_concentram ?? [];

  function toggleRisco(valor: string) {
    setExpandedRiscos((prev) => {
      const next = new Set(prev);
      if (next.has(valor)) next.delete(valor);
      else next.add(valor);
      return next;
    });
  }
  function toggleUnidade(unidade: string) {
    setExpandedUnidades((prev) => {
      const next = new Set(prev);
      if (next.has(unidade)) next.delete(unidade);
      else next.add(unidade);
      return next;
    });
  }

  function barColor(row: SetorCriticoRow) {
    if (row.em_risco > 0 && row.percentual < 50) return "bg-red-500";
    if (row.em_risco > 0 || row.percentual < 70) return "bg-amber-500";
    return "bg-primary/70";
  }

  return (
    <div className="flex flex-col h-full">
      {/* Tabs superiores: Descarregamento (IID) | Dimensão | Eixos | Relatórios */}
      <div className="flex items-center gap-1 border-b border-gray-200 mb-4 pb-2">
        {TABS.map((t) => (
          <button
            key={t.id}
            type="button"
            onClick={() => setTab(t.id)}
            className={
              tab === t.id
                ? "px-4 py-2 rounded-t-lg font-medium bg-primary/10 text-primary border-b-2 border-primary"
                : "px-4 py-2 rounded-t-lg text-gray-600 hover:bg-gray-100 hover:text-emotive-gray-header"
            }
          >
            {t.label}
          </button>
        ))}
      </div>

      <div className="flex gap-6 flex-1 min-h-0">
        {/* Sidebar filtros (como no diseño) */}
        <aside className="w-56 flex-shrink-0 bg-emotive-panel-bg rounded-xl border border-gray-200 p-4 h-fit">
          <h3 className="text-sm font-semibold text-emotive-gray-header mb-3">Filtros</h3>
          {!isGestorOnly && (
            <>
              <div className="mb-3">
                <label className="block text-xs font-medium text-gray-600 mb-1">Cliente</label>
                <select
                  value={clienteId}
                  onChange={(e) => setClienteId(e.target.value)}
                  className="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg"
                >
                  <option value="">— Selecionar —</option>
                  {clientes.map((c) => (
                    <option key={c.id} value={c.id}>{c.nome_fantasia || c.razao_social || c.cpf_cnpj}</option>
                  ))}
                </select>
              </div>
            </>
          )}
          <div className="mb-3">
            <label className="block text-xs font-medium text-gray-600 mb-1">Período</label>
            <select
              value={periodoId}
              onChange={(e) => setPeriodoId(e.target.value)}
              className="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg"
            >
              <option value="">Todos</option>
              {periodos.map((p) => (
                <option key={p.id} value={p.id}>{p.nome}</option>
              ))}
            </select>
          </div>
          <div className="mb-3">
            <label className="block text-xs font-medium text-gray-600 mb-1">Formulário</label>
            <select
              value={formularioId}
              onChange={(e) => setFormularioId(e.target.value)}
              className="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg"
            >
              <option value="">Todos</option>
              {formularios.map((f) => (
                <option key={f.id} value={f.id}>{f.nome || f.label}</option>
              ))}
            </select>
          </div>
          <div className="mb-3">
            <label className="block text-xs font-medium text-gray-600 mb-1">Unidade / Diretoria</label>
            <select
              value={unidadeFilter}
              onChange={(e) => setUnidadeFilter(e.target.value)}
              className="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg"
            >
              <option value="">Todos</option>
              {(data?.por_unidade ?? []).map((r) => (
                <option key={r.valor} value={r.valor}>{r.valor}</option>
              ))}
            </select>
          </div>
          <div className="mb-3">
            <label className="block text-xs font-medium text-gray-600 mb-1">Área / Setor</label>
            <select
              value={areaFilter}
              onChange={(e) => setAreaFilter(e.target.value)}
              className="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg"
            >
              <option value="">Todos</option>
              {(data?.por_area ?? []).map((r) => (
                <option key={r.valor} value={r.valor}>{r.valor}</option>
              ))}
            </select>
          </div>
          <div className="mb-3">
            <label className="block text-xs font-medium text-gray-600 mb-1">Nível Hierárquico</label>
            <select
              value={nivelFilter}
              onChange={(e) => setNivelFilter(e.target.value)}
              className="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg"
            >
              <option value="">Todos</option>
              {(data?.por_nivel_jerarquico ?? []).map((r) => (
                <option key={r.valor} value={r.valor}>{r.valor}</option>
              ))}
            </select>
          </div>
          <div className="mb-3">
            <label className="block text-xs font-medium text-gray-600 mb-1">Tempo de Empresa</label>
            <select
              value={tempoFilter}
              onChange={(e) => setTempoFilter(e.target.value)}
              className="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg"
            >
              <option value="">Todos</option>
              {(data?.por_tempo_empresa ?? []).map((r) => (
                <option key={r.valor} value={r.valor}>{r.valor}</option>
              ))}
            </select>
          </div>
          <div className="mb-3">
            <label className="block text-xs font-medium text-gray-600 mb-1">Modelo de Trabalho</label>
            <select
              value={modeloFilter}
              onChange={(e) => setModeloFilter(e.target.value)}
              className="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg"
            >
              <option value="">Todos</option>
              {(data?.por_modelo_trabalho ?? []).map((r) => (
                <option key={r.valor} value={r.valor}>{r.valor}</option>
              ))}
            </select>
          </div>
          <button
            type="button"
            onClick={loadAgregado}
            disabled={loading || (!isGestorOnly && !clienteId)}
            className="w-full px-3 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-dark disabled:opacity-50"
          >
            {loading ? "Carregando…" : "Aplicar"}
          </button>
        </aside>

        {/* Conteúdo principal */}
        <div className="flex-1 min-w-0 overflow-auto">
          {tab === "relatorios" ? (
            /* Tab Relatórios */
            <div className="space-y-6">
              <section className="bg-white rounded-xl border border-gray-200 p-6">
                <h2 className="text-lg font-semibold text-emotive-gray-header mb-1">Gerar Relatório</h2>
                <p className="text-sm text-gray-600 mb-4">
                  O relatório será gerado com os filtros atualmente aplicados.
                </p>
                <div className="flex flex-wrap items-center gap-3">
                  <select className="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option>Arquivo PDF</option>
                  </select>
                  <button
                    type="button"
                    className="px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary-dark flex items-center gap-2 disabled:opacity-70"
                    title="Descarregar relatório corporativo em PDF (filtros atuais)"
                    disabled={!clienteId || !periodoId || !formularioId}
                    onClick={async () => {
                      const token = typeof window !== "undefined" ? localStorage.getItem("access_token") : null;
                      if (!token || !clienteId || !periodoId || !formularioId) return;
                      const base = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000";
                      const params = new URLSearchParams({
                        cliente_id: clienteId,
                        periodo_id: periodoId,
                        formulario_id: formularioId,
                      });
                      if (unidadeFilter) params.set("unidade", unidadeFilter);
                      if (areaFilter) params.set("area", areaFilter);
                      if (nivelFilter) params.set("nivel_jerarquico", nivelFilter);
                      if (tempoFilter) params.set("tempo_empresa", tempoFilter);
                      if (modeloFilter) params.set("modelo_trabalho", modeloFilter);
                      const url = `${base}/api/v1/pdf/relatorio-corporativo?${params.toString()}`;
                      try {
                        const res = await fetch(url, { headers: { Authorization: `Bearer ${token}` } });
                        if (!res.ok) throw new Error(await res.text());
                        const blob = await res.blob();
                        const link = document.createElement("a");
                        link.href = URL.createObjectURL(blob);
                        link.download = "relatorio_corporativo_emotive.pdf";
                        link.click();
                        URL.revokeObjectURL(link.href);
                        await api("/reportes/gerados", {
                          method: "POST",
                          body: JSON.stringify({
                            cliente_id: parseInt(clienteId, 10),
                            periodo_id: parseInt(periodoId, 10),
                            formulario_id: parseInt(formularioId, 10),
                            tipo: "corporativo",
                          }),
                        }).catch(() => {});
                        loadRelatoriosGerados();
                      } catch (e) {
                        setError(e instanceof Error ? e.message : "Erro ao descarregar PDF");
                      }
                    }}
                  >
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    BAIXAR
                  </button>
                  {(!clienteId || !periodoId || !formularioId) && (
                    <span className="text-xs text-gray-500">Selecione Cliente, Período e Formulário para gerar o PDF.</span>
                  )}
                </div>
              </section>
              <section className="bg-white rounded-xl border border-gray-200 p-6">
                <h2 className="text-lg font-semibold text-emotive-gray-header mb-1">Relatórios gerados</h2>
                <p className="text-sm text-gray-600 mb-2">
                  Relatórios criados anteriormente com base nos filtros aplicados.
                </p>
                <p className="text-sm text-gray-600 mb-4">
                  Para comparar dois períodos (evolução): <Link href="/dashboard/evolucao" className="text-primary font-medium hover:underline">Comparar períodos</Link>.
                </p>
                {loadingGerados && <p className="text-sm text-gray-500">Carregando…</p>}
                {!loadingGerados && relatoriosGerados.length === 0 && (
                  <div className="space-y-3 text-sm text-gray-500">
                    Nenhum relatório gerado ainda. Use &quot;Gerar Relatório&quot; acima para criar o primeiro.
                  </div>
                )}
                {!loadingGerados && relatoriosGerados.length > 0 && (
                  <ul className="space-y-3">
                    {relatoriosGerados.map((r) => (
                      <li key={r.id} className="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <div>
                          <span className="font-medium text-gray-800">{r.periodo_nome} — {r.formulario_nome}</span>
                          {r.created_at && (
                            <span className="text-xs text-gray-500 ml-2">{new Date(r.created_at).toLocaleString("pt-BR")}</span>
                          )}
                        </div>
                        <button
                          type="button"
                          className="px-3 py-1.5 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-dark flex items-center gap-1"
                          onClick={async () => {
                            const token = typeof window !== "undefined" ? localStorage.getItem("access_token") : null;
                            if (!token) return;
                            const base = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000";
                            const url = `${base}/api/v1/pdf/relatorio-corporativo?cliente_id=${r.cliente_id}&periodo_id=${r.periodo_id}&formulario_id=${r.formulario_id}`;
                            try {
                              const res = await fetch(url, { headers: { Authorization: `Bearer ${token}` } });
                              if (!res.ok) throw new Error(await res.text());
                              const blob = await res.blob();
                              const link = document.createElement("a");
                              link.href = URL.createObjectURL(blob);
                              link.download = "relatorio_corporativo_emotive.pdf";
                              link.click();
                              URL.revokeObjectURL(link.href);
                            } catch (e) {
                              setError(e instanceof Error ? e.message : "Erro ao descarregar PDF");
                            }
                          }}
                        >
                          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                          </svg>
                          BAIXAR
                        </button>
                      </li>
                    ))}
                  </ul>
                )}
              </section>
            </div>
          ) : (
            /* Tabs Dimensão / Eixo analítico / Descarrilamento */
            <>
              <div className="flex flex-wrap gap-2 mb-4">
                {DIMENSOES_CHIPS.map((d) => (
                  <button
                    key={d}
                    type="button"
                    onClick={() => setDimensionChip(d)}
                    className={
                      dimensionChip === d
                        ? "px-3 py-1.5 rounded-full text-sm font-medium bg-primary text-white"
                        : "px-3 py-1.5 rounded-full text-sm text-gray-600 bg-gray-100 hover:bg-gray-200"
                    }
                  >
                    {d}
                  </button>
                ))}
              </div>

              {error && (
                <div className="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">{error}</div>
              )}

              {!data && !kpisIid && !loading && !error && (
                <div className="rounded-xl border border-gray-200 bg-gray-50 p-6 text-center">
                  <p className="text-gray-700 font-medium mb-2">Dados e gráficos do relatório</p>
                  <p className="text-gray-600 text-sm mb-4">
                    Selecione Cliente (se for admin), Período e Formulário na barra lateral e clique em <strong>Aplicar</strong> para carregar KPIs, gráfico temporal e mapa de riscos.
                  </p>
                  <p className="text-sm text-gray-500">Para comparar dois períodos (evolução), use o link abaixo ou o menu &quot;Comparar períodos&quot;.</p>
                  <Link href="/dashboard/evolucao" className="inline-block mt-3 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-dark">
                    Comparar períodos (Evolução)
                  </Link>
                </div>
              )}

              {(data || kpisIid) && (
                <>
                  {totalColaboradores === 0 && (
                    <div className="mb-4 p-3 rounded-lg bg-amber-50 text-amber-800 text-sm border border-amber-200">
                      Nenhum dado encontrado para os filtros aplicados. Altere Cliente, Período ou Formulário e clique em Aplicar novamente.
                    </div>
                  )}
                  {/* KPIs: Colaboradores em Risco + Setores Críticos (igual ao design) */}
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div className="rounded-xl border-2 border-amber-200 bg-amber-50/80 p-5">
                      <div className="flex items-baseline gap-2">
                        <span className="text-3xl font-bold text-amber-800">{useIid ? (kpisIid?.colaboradores_em_risco ?? 0) : (kpis?.colaboradores_em_risco ?? 0)}</span>
                        <span className="text-amber-700 font-medium">({percentualRisco}%)</span>
                      </div>
                      {useIid && (kpisIid?.total_alta != null || kpisIid?.total_critico != null) && (
                        <p className="text-sm text-amber-800 mt-1">
                          {kpisIid?.total_alta ?? 0} Laranja / {kpisIid?.total_critico ?? 0} Vermelho
                        </p>
                      )}
                      <p className="text-xs text-amber-800 mt-1">
                        Número e percentual de colaboradores nas faixas de risco &quot;Atenção&quot; (Laranja) e &quot;Crítico&quot; (Vermelho).
                      </p>
                    </div>
                    <div className="rounded-xl border-2 border-red-200 bg-red-50/80 p-5">
                      <div className="text-3xl font-bold text-red-800">{numSetoresCriticos}</div>
                      <p className="text-xs text-red-800 mt-1">
                        {(useIid ? setoresCriticosIid : setoresCriticosList).length > 0
                          ? (useIid ? setoresCriticosIid : setoresCriticosList).slice(0, 3).map((s) => s.valor).join(", ") + ((useIid ? setoresCriticosIid : setoresCriticosList).length > 3 ? "…" : "")
                          : "—"}
                      </p>
                      <p className="text-xs text-red-700 mt-1">
                        Número de departamentos ou setores com IID médio na faixa &quot;Atenção&quot; ou &quot;Crítico&quot;.
                      </p>
                    </div>
                  </div>

                  {/* Gráfico temporal único: eixo X = ondas, nuvem de pontos (IID por pessoa) + linha do promedio */}
                  {evolucaoIid.length >= 1 && (
                    <section className="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                      <h3 className="font-semibold text-emotive-gray-header mb-4">Estamos Melhorando ou Piorando?</h3>
                      <p className="text-sm text-gray-500 mb-4">Cada ponto é uma pessoa (anonimizada); a linha conecta a média da empresa em cada onda.</p>
                      <div className="relative h-64 pl-10 pr-4 pb-10">
                        {/* Zonas de fundo */}
                        <div className="absolute left-10 right-0 top-0 bottom-10 flex flex-col-reverse">
                          <div className="h-1/4 bg-green-200/80" title="Saudável (0-25%)" />
                          <div className="h-1/4 bg-amber-200/80" title="Preventiva (25-50%)" />
                          <div className="h-1/4 bg-orange-200/80" title="Atenção (50-75%)" />
                          <div className="h-1/4 bg-red-200/80" title="Crítica (75-100%)" />
                        </div>
                        <div className="absolute left-0 top-0 bottom-10 w-8 flex flex-col justify-between text-xs text-gray-500">
                          <span>100%</span>
                          <span>75%</span>
                          <span>50%</span>
                          <span>25%</span>
                          <span>0%</span>
                        </div>
                        <div className="absolute left-10 right-0 top-0 bottom-10">
                          {/* Nuvem de pontos: um ponto por IID individual por período, com leve jitter no eixo X */}
                          {evolucaoIid.map((p, i) => {
                            const n = evolucaoIid.length;
                            const bandWidth = n > 1 ? 100 / (n - 1) : 100;
                            const xCenter = n > 1 ? (i / (n - 1)) * 100 : 50;
                            const pontos = p.iid_pontos ?? [];
                            return pontos.map((valor, j) => {
                              const jitter = (j % 7) / 7 - 0.5;
                              const xPct = Math.min(100, Math.max(0, xCenter + jitter * bandWidth * 0.35));
                              return (
                                <div
                                  key={`${p.periodo_id}-${j}`}
                                  className="absolute w-2 h-2 rounded-full bg-gray-600/70 z-[5]"
                                  style={{ left: `${xPct}%`, bottom: `${Math.min(100, Math.max(0, valor))}%`, marginLeft: -4, marginBottom: -4 }}
                                  title={`${p.periodo_nome}: IID ${valor}%`}
                                />
                              );
                            });
                          })}
                          {/* Linha da média global da empresa (onda a onda) */}
                          {evolucaoIid.length >= 2 && (
                            <svg className="absolute inset-0 w-full h-full pointer-events-none z-10" viewBox="0 0 100 100" preserveAspectRatio="none">
                              <polyline
                                fill="none"
                                stroke="currentColor"
                                strokeWidth="1.2"
                                className="text-gray-800"
                                points={evolucaoIid
                                  .map((p, i) => {
                                    const n = evolucaoIid.length;
                                    const x = n > 1 ? (i / (n - 1)) * 100 : 50;
                                    return `${x} ${p.iid_medio}`;
                                  })
                                  .join(" ")}
                              />
                            </svg>
                          )}
                          {/* Marcador da média em cada onda */}
                          {evolucaoIid.map((p, i) => {
                            const n = evolucaoIid.length;
                            const leftPct = n > 1 ? (i / (n - 1)) * 100 : 50;
                            return (
                              <div
                                key={`avg-${p.periodo_id}`}
                                className="absolute w-6 h-6 -ml-3 z-[11] flex items-center justify-center rounded-full border-2 border-gray-800 bg-white shadow"
                                style={{ left: `${leftPct}%`, bottom: `${p.iid_medio}%`, marginBottom: -12 }}
                                title={`${p.periodo_nome}: média ${p.iid_medio}% (${p.total_respondentes} pessoas)`}
                              >
                                <span className="text-[10px] font-bold">{p.iid_medio}</span>
                              </div>
                            );
                          })}
                        </div>
                        <div className="absolute left-10 right-0 bottom-0 h-10 flex justify-between items-center px-2 text-xs text-gray-600">
                          {evolucaoIid.map((p) => (
                            <span key={p.periodo_id}>{p.periodo_nome}</span>
                          ))}
                        </div>
                      </div>
                      <div className="flex flex-wrap gap-4 mt-2 text-xs text-gray-500">
                        <span className="bg-gray-600/70 px-2 py-0.5 rounded">Ponto = pessoa (anonimizada)</span>
                        <span className="bg-white border border-gray-400 px-2 py-0.5 rounded">Círculo = média da empresa</span>
                        <span className="bg-green-200 px-2 py-0.5 rounded">Saudável (0-25%)</span>
                        <span className="bg-amber-200 px-2 py-0.5 rounded">Preventiva (25-50%)</span>
                        <span className="bg-orange-200 px-2 py-0.5 rounded">Atenção (50-75%)</span>
                        <span className="bg-red-200 px-2 py-0.5 rounded">Crítica (75-100%)</span>
                      </div>
                    </section>
                  )}

                  {/* Onde se Concentram os Riscos? (hierarquia Unidade → Área com faixas IID igual ao design) */}
                  <section className="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <h3 className="px-4 py-3 bg-gray-50 font-semibold text-emotive-gray-header border-b border-gray-200">
                      Onde se Concentram os Riscos?
                    </h3>
                    <div className="p-4 space-y-2">
                      {useIid && ondeSeConcentram.length > 0 ? (
                        ondeSeConcentram.map((item) => {
                          const isExpanded = expandedUnidades.has(item.unidade);
                          const emRisco = item.alta + item.critico;
                          const barColorUnidade = item.critico > 0 ? "bg-red-500" : item.alta > 0 ? "bg-amber-500" : "bg-primary/70";
                          return (
                            <div key={item.unidade} className="border border-gray-200 rounded-lg overflow-hidden">
                              <button
                                type="button"
                                className="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-gray-50"
                                onClick={() => toggleUnidade(item.unidade)}
                              >
                                <span className={`flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center ${item.critico > 0 ? "bg-red-500 text-white" : "bg-amber-400 text-white"}`}>
                                  {item.critico > 0 ? (
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" /></svg>
                                  ) : (
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                  )}
                                </span>
                                <div className={`flex-1 h-3 rounded-full overflow-hidden min-w-[120px] ${barColorUnidade}`} style={{ width: `${Math.min(100, item.total ? ((emRisco / item.total) * 100 + 20) : 0)}%` }} />
                                <span className="font-medium text-gray-800">{item.unidade}</span>
                                <span className="text-gray-500 text-sm">{item.total}</span>
                                <svg className={`w-5 h-5 text-gray-400 transition-transform ${isExpanded ? "rotate-180" : ""}`} fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" /></svg>
                              </button>
                              {isExpanded && item.areas.length > 0 && (
                                <div className="px-4 pb-4 pt-0 border-t border-gray-100 bg-gray-50/50 space-y-3">
                                  {item.areas.map((ar) => (
                                    <div key={ar.area} className="pl-4 border-l-2 border-gray-200">
                                      <p className="font-medium text-gray-800 text-sm">{ar.area}</p>
                                      <p className="text-xs text-gray-600 mt-1">Colaboradores por faixa:</p>
                                      <div className="flex flex-wrap gap-3 text-sm mt-1">
                                        <span className="text-green-700">Baixa = {ar.baixa}</span>
                                        <span className="text-yellow-700">Moderada = {ar.moderada}</span>
                                        <span className="text-orange-600">Alta = {ar.alta}</span>
                                        <span className="text-red-700">Crítico = {ar.critico}</span>
                                      </div>
                                    </div>
                                  ))}
                                </div>
                              )}
                            </div>
                          );
                        })
                      ) : (
                        (areaFilter ? setoresCriticosList.filter((s) => s.valor === areaFilter) : setoresCriticosList).map((row) => {
                          const isExpanded = expandedRiscos.has(row.valor);
                          const incompletosNoPrazo = row.total - row.completos - row.em_risco;
                          return (
                            <div key={row.valor} className="border border-gray-200 rounded-lg overflow-hidden">
                              <button
                                type="button"
                                className="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-gray-50"
                                onClick={() => toggleRisco(row.valor)}
                              >
                                <span className={`flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center ${row.em_risco > 0 ? "bg-red-500 text-white" : "bg-amber-400 text-white"}`}>
                                  {row.em_risco > 0 ? (
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" /></svg>
                                  ) : (
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                  )}
                                </span>
                                <div className={`flex-1 h-3 rounded-full overflow-hidden min-w-[120px] ${barColor(row)}`} style={{ width: `${Math.min(100, (row.total ? (row.em_risco / row.total) * 100 + 30 : 0))}%` }} />
                                <span className="font-medium text-gray-800">{row.valor}</span>
                                <span className="text-gray-500 text-sm">{row.total}</span>
                                <svg className={`w-5 h-5 text-gray-400 transition-transform ${isExpanded ? "rotate-180" : ""}`} fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" /></svg>
                              </button>
                              {isExpanded && (
                                <div className="px-4 pb-4 pt-0 border-t border-gray-100 bg-gray-50/50">
                                  <p className="text-xs font-medium text-gray-600 mt-2 mb-2">Colaboradores por faixa</p>
                                  <div className="flex flex-wrap gap-4 text-sm">
                                    <span className="text-green-700">Baixa (completo) = {row.completos}</span>
                                    <span className="text-yellow-700">Moderada = {incompletosNoPrazo}</span>
                                    <span className="text-red-700">Crítico (em risco) = {row.em_risco}</span>
                                  </div>
                                </div>
                              )}
                            </div>
                          );
                        })
                      )}
                      {!useIid && setoresCriticosList.length === 0 && ondeSeConcentram.length === 0 && (
                        <p className="text-gray-500 text-sm py-4">Nenhum setor com dados de risco para os filtros aplicados. Selecione um formulário e aplique para ver dados por IID.</p>
                      )}
                    </div>
                    <p className="px-4 py-2 text-xs text-gray-500 border-t border-gray-100">
                      Este mapa representa a distribuição de riscos psicossociais por área. Quanto mais intensa a cor, maior a necessidade de atenção.
                    </p>
                  </section>

                  {/* Mapa de calor: Área vs faixa de risco (IID) */}
                  {useIid && ondeSeConcentram.length > 0 && (
                    <section className="bg-white rounded-xl border border-gray-200 overflow-hidden mt-6">
                      <h3 className="px-4 py-3 bg-gray-50 font-semibold text-emotive-gray-header border-b border-gray-200">
                        Mapa de calor — Área vs faixa de risco
                      </h3>
                      <div className="p-4 overflow-x-auto">
                        {(() => {
                          const rows: { label: string; baixa: number; moderada: number; alta: number; critico: number }[] = [];
                          ondeSeConcentram.forEach((item) => {
                            item.areas.forEach((ar) => {
                              rows.push({
                                label: item.unidade ? `${item.unidade} · ${ar.area}` : ar.area,
                                baixa: ar.baixa,
                                moderada: ar.moderada,
                                alta: ar.alta,
                                critico: ar.critico,
                              });
                            });
                          });
                          const maxVal = Math.max(1, ...rows.flatMap((r) => [r.baixa, r.moderada, r.alta, r.critico]));
                          const cellColor = (v: number) => {
                            if (v === 0) return "bg-gray-100";
                            const pct = v / maxVal;
                            if (pct <= 0.33) return "bg-green-200";
                            if (pct <= 0.66) return "bg-amber-300";
                            return "bg-red-400";
                          };
                          return (
                            <table className="w-full min-w-[400px] text-sm border-collapse">
                              <thead>
                                <tr className="border-b border-gray-200">
                                  <th className="text-left py-2 px-3 font-medium text-gray-700">Setor / Área</th>
                                  <th className="py-2 px-2 font-medium text-green-800 w-20">Baixa</th>
                                  <th className="py-2 px-2 font-medium text-amber-800 w-20">Moderada</th>
                                  <th className="py-2 px-2 font-medium text-orange-800 w-20">Alta</th>
                                  <th className="py-2 px-2 font-medium text-red-800 w-20">Crítico</th>
                                </tr>
                              </thead>
                              <tbody>
                                {rows.map((r, i) => (
                                  <tr key={i} className="border-b border-gray-100 hover:bg-gray-50/50">
                                    <td className="py-2 px-3 text-gray-800 font-medium">{r.label}</td>
                                    <td className={`py-2 px-2 text-center ${cellColor(r.baixa)}`} title={`Baixa: ${r.baixa}`}>{r.baixa}</td>
                                    <td className={`py-2 px-2 text-center ${cellColor(r.moderada)}`} title={`Moderada: ${r.moderada}`}>{r.moderada}</td>
                                    <td className={`py-2 px-2 text-center ${cellColor(r.alta)}`} title={`Alta: ${r.alta}`}>{r.alta}</td>
                                    <td className={`py-2 px-2 text-center ${cellColor(r.critico)}`} title={`Crítico: ${r.critico}`}>{r.critico}</td>
                                  </tr>
                                ))}
                              </tbody>
                            </table>
                          );
                        })()}
                      </div>
                      <p className="px-4 py-2 text-xs text-gray-500 border-t border-gray-100">
                        Intensidade da cor indica quantidade de colaboradores na faixa. Verde = menor concentração; vermelho = maior.
                      </p>
                    </section>
                  )}
                </>
              )}
            </>
          )}
        </div>
      </div>

      <div className="mt-4 flex flex-wrap gap-3 text-sm border-t border-gray-200 pt-4">
        <Link href="/dashboard" className="text-primary hover:underline">← Início</Link>
        <Link href="/dashboard/evolucao" className="text-primary hover:underline font-medium">Comparar períodos (Evolução)</Link>
        <Link href="/dashboard/respondentes" className="text-primary hover:underline">Monitor de respondentes</Link>
        <Link href="/dashboard/atribuicao-em-massa" className="text-primary hover:underline">Atribuição em massa</Link>
      </div>
    </div>
  );
}
