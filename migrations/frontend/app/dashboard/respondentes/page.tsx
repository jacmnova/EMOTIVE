"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { api } from "@/lib/api";
import { getStoredUser } from "@/lib/auth";
import type { Cliente, Periodo, Formulario, Grupo } from "@/types";

interface RespondenteItem {
  usuario_formulario_id: number;
  usuario_id: number;
  name: string;
  email: string;
  status: string;
  data_limite: string | null;
}

export default function RespondentesPage() {
  const user = getStoredUser();
  const [clientes, setClientes] = useState<Cliente[]>([]);
  const [periodos, setPeriodos] = useState<Periodo[]>([]);
  const [formularios, setFormularios] = useState<Formulario[]>([]);
  const [clienteId, setClienteId] = useState("");
  const [periodoId, setPeriodoId] = useState("");
  const [formularioId, setFormularioId] = useState("");
  const [tipoMonitor, setTipoMonitor] = useState<"individual" | "grupo">("individual");
  const [grupos, setGrupos] = useState<Grupo[]>([]);
  const [grupoId, setGrupoId] = useState("");
  const [list, setList] = useState<RespondenteItem[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");
  const [sendingId, setSendingId] = useState<number | null>(null);
  const isGestorOnly = user?.gestor && !user?.admin && !user?.sa;

  useEffect(() => {
    const u = getStoredUser();
    if (u?.admin || u?.sa) api<Cliente[]>("/clientes").then(setClientes).catch(() => {});
    api<Periodo[]>("/periodos").then(setPeriodos).catch(() => {});
    api<Formulario[]>("/formularios").then(setFormularios).catch(() => {});
    if (u?.gestor && !u?.admin && !u?.sa && u?.cliente_id) setClienteId(String(u.cliente_id));
  }, []);

  useEffect(() => {
    const cid = clienteId ? parseInt(clienteId, 10) : (isGestorOnly && user?.cliente_id ? user.cliente_id : null);
    if (!cid) {
      setGrupos([]);
      return;
    }
    api<Grupo[]>("/grupos?cliente_id=" + cid).then(setGrupos).catch(() => setGrupos([]));
  }, [clienteId, isGestorOnly, user?.cliente_id]);

  function loadMonitor() {
    const cid = isGestorOnly ? user!.cliente_id! : (clienteId ? parseInt(clienteId, 10) : null);
    if (!cid || !periodoId || !formularioId) {
      setList([]);
      return;
    }
    setError("");
    setSuccess("");
    setLoading(true);
    const params = new URLSearchParams({ cliente_id: String(cid), periodo_id: periodoId, formulario_id: formularioId });
    if (tipoMonitor === "grupo" && grupoId) params.set("grupo_id", grupoId);
    api<{ respondentes: RespondenteItem[] }>(`/reportes/monitor-respondentes?${params.toString()}`)
      .then((r) => setList(r.respondentes ?? []))
      .catch((e) => {
        setList([]);
        setError(e instanceof Error ? e.message : "Erro ao carregar");
      })
      .finally(() => setLoading(false));
  }

  useEffect(() => {
    if (periodoId && formularioId && (isGestorOnly ? user?.cliente_id : clienteId)) {
      if (tipoMonitor === "individual" || (tipoMonitor === "grupo" && grupoId)) loadMonitor();
      else setList([]);
    } else setList([]);
  }, [clienteId, periodoId, formularioId, tipoMonitor, grupoId, isGestorOnly, user?.cliente_id]);

  const cid = isGestorOnly ? user?.cliente_id : (clienteId ? parseInt(clienteId, 10) : null);
  const canLoad = cid && periodoId && formularioId && (tipoMonitor === "individual" || grupoId);
  const completos = list.filter((r) => r.status === "completo").length;
  const pendentes = list.filter((r) => r.status === "pendente").length;

  async function enviarRecordatorio(ufId: number) {
    setError("");
    setSuccess("");
    setSendingId(ufId);
    try {
      await api<{ message: string }>(`/usuario-formulario/${ufId}/enviar-recordatorio`, { method: "POST" });
      setSuccess("Lembrete enviado por e-mail com sucesso.");
      setSendingId(null);
    } catch (e) {
      setError(e instanceof Error ? e.message : "Erro ao enviar lembrete");
      setSendingId(null);
    }
  }

  return (
    <div>
      <Link href="/dashboard/relatorio-corporativo" className="text-gray-600 hover:text-primary text-sm">← Relatório corporativo</Link>
      <h1 className="text-2xl font-bold text-emotive-gray-header mt-2 mb-2">Monitor de respondentes</h1>
      <p className="text-gray-600 text-sm mb-4">Veja quem foi atribuído ao período e formulário e o estado de resposta (pendente ou completo). Escolha vista individual ou por grupo.</p>

      <div className="mb-4 flex gap-2">
        <button
          type="button"
          onClick={() => { setTipoMonitor("individual"); setGrupoId(""); setList([]); }}
          className={`px-4 py-2 rounded-lg text-sm font-medium ${tipoMonitor === "individual" ? "bg-primary text-white" : "bg-gray-100 text-gray-700 hover:bg-gray-200"}`}
        >
          Individual
        </button>
        <button
          type="button"
          onClick={() => { setTipoMonitor("grupo"); setList([]); }}
          className={`px-4 py-2 rounded-lg text-sm font-medium ${tipoMonitor === "grupo" ? "bg-primary text-white" : "bg-gray-100 text-gray-700 hover:bg-gray-200"}`}
        >
          Por grupo
        </button>
      </div>

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
          <label className="block text-sm font-medium text-gray-700 mb-1">Período (onda)</label>
          <select value={periodoId} onChange={(e) => setPeriodoId(e.target.value)} className="px-3 py-2 border border-slate-300 rounded-lg min-w-[180px]">
            <option value="">— Selecionar —</option>
            {periodos.map((p) => (
              <option key={p.id} value={p.id}>{p.nome}</option>
            ))}
          </select>
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Formulário</label>
          <select value={formularioId} onChange={(e) => setFormularioId(e.target.value)} className="px-3 py-2 border border-slate-300 rounded-lg min-w-[180px]">
            <option value="">— Selecionar —</option>
            {formularios.map((f) => (
              <option key={f.id} value={f.id}>{f.nome || f.label}</option>
            ))}
          </select>
        </div>
        {tipoMonitor === "grupo" && (
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Grupo</label>
            <select value={grupoId} onChange={(e) => setGrupoId(e.target.value)} className="px-3 py-2 border border-slate-300 rounded-lg min-w-[200px]">
              <option value="">— Selecionar grupo —</option>
              {grupos.map((g) => (
                <option key={g.id} value={g.id}>{g.nome}</option>
              ))}
            </select>
          </div>
        )}
      </div>

      {!canLoad && <p className="text-gray-500 text-sm">{tipoMonitor === "grupo" ? "Selecione Cliente, Período, Formulário e Grupo para ver a lista." : "Selecione Cliente, Período e Formulário para ver a lista."}</p>}
      {error && <div className="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">{error}</div>}
      {success && <div className="mb-4 p-3 rounded-lg bg-green-50 text-green-800 text-sm">{success}</div>}
      {canLoad && (
        <>
          <div className="mb-4 flex flex-wrap gap-4 text-sm">
            <span className="px-3 py-1.5 rounded-lg bg-amber-100 text-amber-800"><strong>{pendentes}</strong> pendentes</span>
            <span className="px-3 py-1.5 rounded-lg bg-green-100 text-green-800"><strong>{completos}</strong> completos</span>
            <span className="text-gray-600"><strong>{list.length}</strong> no total</span>
          </div>
          {loading && <p className="text-gray-500">Carregando…</p>}
          {!loading && list.length === 0 && <p className="text-gray-500">Nenhuma atribuição encontrada para estes filtros.</p>}
          {!loading && list.length > 0 && (
            <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
              <table className="w-full text-left">
                <thead className="bg-emotive-panel-bg border-b border-gray-200">
                  <tr>
                    <th className="px-4 py-3 font-medium text-gray-700">Nome</th>
                    <th className="px-4 py-3 font-medium text-gray-700">E-mail</th>
                    <th className="px-4 py-3 font-medium text-gray-700">Estado</th>
                    <th className="px-4 py-3 font-medium text-gray-700">Data limite</th>
                    <th className="px-4 py-3 font-medium text-gray-700">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  {list.map((r) => (
                    <tr key={r.usuario_formulario_id} className="border-b border-gray-100 last:border-0">
                      <td className="px-4 py-3">{r.name}</td>
                      <td className="px-4 py-3">{r.email}</td>
                      <td className="px-4 py-3">
                        <span className={r.status === "completo" ? "text-green-700 font-medium" : "text-amber-700"}>
                          {r.status === "completo" ? "Completo" : "Pendente"}
                        </span>
                      </td>
                      <td className="px-4 py-3">{r.data_limite ? new Date(r.data_limite).toLocaleDateString("pt-BR") : "—"}</td>
                      <td className="px-4 py-3">
                        {r.status !== "completo" && (
                          <button
                            type="button"
                            disabled={sendingId === r.usuario_formulario_id}
                            onClick={() => enviarRecordatorio(r.usuario_formulario_id)}
                            className="text-sm text-primary hover:underline disabled:opacity-50"
                          >
                            {sendingId === r.usuario_formulario_id ? "Enviando…" : "Enviar recordatório"}
                          </button>
                        )}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </>
      )}
    </div>
  );
}
