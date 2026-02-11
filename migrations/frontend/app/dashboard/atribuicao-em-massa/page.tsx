"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { api } from "@/lib/api";
import { getStoredUser } from "@/lib/auth";
import type { Cliente, Periodo, Formulario, User, Grupo } from "@/types";

export default function AtribuicaoEmMassaPage() {
  const user = getStoredUser();
  const [clientes, setClientes] = useState<Cliente[]>([]);
  const [periodos, setPeriodos] = useState<Periodo[]>([]);
  const [formularios, setFormularios] = useState<Formulario[]>([]);
  const [clienteId, setClienteId] = useState("");
  const [formularioId, setFormularioId] = useState("");
  const [periodoId, setPeriodoId] = useState("");
  const [dataLimite, setDataLimite] = useState("");
  const [unidade, setUnidade] = useState("");
  const [area, setArea] = useState("");
  const [nivelJerarquico, setNivelJerarquico] = useState("");
  const [tempoEmpresa, setTempoEmpresa] = useState("");
  const [modeloTrabalho, setModeloTrabalho] = useState("");
  const [loading, setLoading] = useState(false);
  const [result, setResult] = useState<{ criados: number; total_populacao: number } | null>(null);
  const [error, setError] = useState("");
  const isGestorOnly = user?.gestor && !user?.admin && !user?.sa;
  const [opcoesUnidade, setOpcoesUnidade] = useState<string[]>([]);
  const [opcoesArea, setOpcoesArea] = useState<string[]>([]);
  const [grupos, setGrupos] = useState<Grupo[]>([]);
  const [grupoId, setGrupoId] = useState("");
  const [modoPorGrupo, setModoPorGrupo] = useState(false);
  const [enviarInvitacao, setEnviarInvitacao] = useState(false);

  useEffect(() => {
    const u = getStoredUser();
    if (u?.admin || u?.sa) api<Cliente[]>("/clientes").then(setClientes).catch(() => {});
    api<Periodo[]>("/periodos").then(setPeriodos).catch(() => {});
    api<Formulario[]>("/formularios").then(setFormularios).catch(() => {});
    if (u?.gestor && !u?.admin && !u?.sa && u?.cliente_id) setClienteId(String(u.cliente_id));
  }, []);

  useEffect(() => {
    const cid = clienteId ? parseInt(clienteId, 10) : (isGestorOnly ? user?.cliente_id : null);
    if (!cid) return;
    api<User[]>("/users?limit=500&cliente_id=" + cid)
      .then((users) => {
        const u = new Set<string>();
        const a = new Set<string>();
        users.forEach((x: User) => {
          if (x.unidade) u.add(x.unidade);
          if (x.area) a.add(x.area);
        });
        setOpcoesUnidade(Array.from(u).sort());
        setOpcoesArea(Array.from(a).sort());
      })
      .catch(() => {});
    api<Grupo[]>("/grupos?cliente_id=" + cid).then(setGrupos).catch(() => setGrupos([]));
  }, [clienteId, isGestorOnly, user?.cliente_id]);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError("");
    setResult(null);
    const cid = isGestorOnly ? user!.cliente_id! : (clienteId ? parseInt(clienteId, 10) : null);
    if (!cid || !formularioId) {
      setError("Selecione o cliente e o formulário.");
      return;
    }
    if (modoPorGrupo && !grupoId) {
      setError("Selecione um grupo.");
      return;
    }
    setLoading(true);
    try {
      const body: Record<string, unknown> = {
        cliente_id: cid,
        formulario_id: parseInt(formularioId, 10),
        periodo_id: periodoId ? parseInt(periodoId, 10) : null,
        data_limite: dataLimite || null,
      };
      if (modoPorGrupo && grupoId) {
        body.grupo_id = parseInt(grupoId, 10);
      } else {
        body.unidade = unidade || null;
        body.area = area || null;
        body.nivel_jerarquico = nivelJerarquico || null;
        body.tempo_empresa = tempoEmpresa || null;
        body.modelo_trabalho = modeloTrabalho || null;
      }
      body.enviar_invitacao = enviarInvitacao;
      const res = await api<{ criados: number; total_populacao: number }>("/usuario-formulario/em-massa", {
        method: "POST",
        body: JSON.stringify(body),
      });
      setResult(res);
    } catch (err) {
      setError(err instanceof Error ? err.message : "Erro ao atribuir em massa");
    } finally {
      setLoading(false);
    }
  }

  return (
    <div>
      <Link href="/dashboard/periodos" className="text-gray-600 hover:text-primary text-sm">← Períodos</Link>
      <h1 className="text-2xl font-bold text-emotive-gray-header mt-2 mb-2">Atribuição em massa</h1>
      <p className="text-gray-600 text-sm mb-6">Você está liberando o questionário para um período (onda). Pode fazer várias atribuições para o mesmo período — por exemplo, 100 hoje e 100 na próxima semana — escolhendo filtros ou um grupo diferente a cada vez.</p>

      <form onSubmit={handleSubmit} className="max-w-xl space-y-4 bg-white p-6 rounded-xl border border-gray-200">
        {error && <div className="p-3 rounded-lg bg-red-50 text-red-700 text-sm">{error}</div>}
        {result && (
          <div className="p-3 rounded-lg bg-green-50 text-green-800 text-sm">
            Atribuições criadas: <strong>{result.criados}</strong> de <strong>{result.total_populacao}</strong> usuários na população.
            {enviarInvitacao && result.criados > 0 && (
              <span className="block mt-1">E-mails de convite enviados aos usuários que receberam a atribuição.</span>
            )}
          </div>
        )}
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
          <label className="block text-sm font-medium text-gray-700 mb-1">Formulário</label>
          <select value={formularioId} onChange={(e) => setFormularioId(e.target.value)} required className="w-full px-3 py-2 border border-slate-300 rounded-lg">
            <option value="">— Selecionar —</option>
            {formularios.map((f) => (
              <option key={f.id} value={f.id}>{f.nome || f.label}</option>
            ))}
          </select>
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Período (opcional)</label>
          <select value={periodoId} onChange={(e) => setPeriodoId(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg">
            <option value="">— Nenhum —</option>
            {periodos.map((p) => (
              <option key={p.id} value={p.id}>{p.nome}</option>
            ))}
          </select>
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Data limite (opcional)</label>
          <input type="date" value={dataLimite} onChange={(e) => setDataLimite(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
        </div>
        <div className="border-t border-gray-200 pt-4">
          <div className="flex items-center gap-2 mb-2">
            <label className="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" checked={modoPorGrupo} onChange={(e) => { setModoPorGrupo(e.target.checked); if (!e.target.checked) setGrupoId(""); }} className="rounded border-gray-300" />
              <span className="text-sm font-medium text-gray-700">Atribuir por grupo</span>
            </label>
          </div>
          {modoPorGrupo ? (
            <div>
              <label className="block text-sm text-gray-700 mb-1">Grupo</label>
              <select value={grupoId} onChange={(e) => setGrupoId(e.target.value)} required className="w-full px-3 py-2 border border-slate-300 rounded-lg">
                <option value="">— Selecionar grupo —</option>
                {grupos.map((g) => (
                  <option key={g.id} value={g.id}>{g.nome}</option>
                ))}
              </select>
              <p className="text-xs text-gray-500 mt-1">A população será filtrada pelos critérios do grupo (unidade, área, etc.).</p>
            </div>
          ) : (
            <>
          <p className="text-sm font-medium text-gray-700 mb-2">Filtros de população (opcional — deixe em branco para todos)</p>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label className="block text-xs text-gray-500 mb-0.5">Unidade</label>
              <select value={unidade} onChange={(e) => setUnidade(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                <option value="">— Todos —</option>
                {opcoesUnidade.map((v) => (
                  <option key={v} value={v}>{v}</option>
                ))}
              </select>
            </div>
            <div>
              <label className="block text-xs text-gray-500 mb-0.5">Área</label>
              <select value={area} onChange={(e) => setArea(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                <option value="">— Todos —</option>
                {opcoesArea.map((v) => (
                  <option key={v} value={v}>{v}</option>
                ))}
              </select>
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
            </>
          )}
        </div>
        <div className="pt-2">
          <label className="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" checked={enviarInvitacao} onChange={(e) => setEnviarInvitacao(e.target.checked)} className="rounded border-slate-300 text-primary" />
            <span className="text-sm text-gray-700">Enviar e-mail de convite aos usuários atribuídos</span>
          </label>
          <p className="text-xs text-gray-500 mt-1">Cada usuário receberá um e-mail com link para acessar o painel e responder o questionário.</p>
        </div>
        <div className="flex gap-3 pt-2">
          <button type="submit" disabled={loading} className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark disabled:opacity-50">
            {loading ? "Atribuindo…" : "Atribuir em massa"}
          </button>
          <Link href="/dashboard/periodos" className="px-4 py-2 border border-slate-300 rounded-lg text-gray-700 hover:bg-gray-50">Voltar</Link>
        </div>
      </form>
    </div>
  );
}
