"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { api } from "@/lib/api";
import { getStoredUser } from "@/lib/auth";
import type { Cliente, Periodo, Formulario, User } from "@/types";

export default function AsignarFormularioPage() {
  const user = getStoredUser();
  const [clientes, setClientes] = useState<Cliente[]>([]);
  const [periodos, setPeriodos] = useState<Periodo[]>([]);
  const [formularios, setFormularios] = useState<Formulario[]>([]);
  const [usuarios, setUsuarios] = useState<User[]>([]);
  const [clienteId, setClienteId] = useState("");
  const [usuarioId, setUsuarioId] = useState("");
  const [formularioId, setFormularioId] = useState("");
  const [periodoId, setPeriodoId] = useState("");
  const [dataLimite, setDataLimite] = useState("");
  const [enviarInvitacao, setEnviarInvitacao] = useState(false);
  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState("");
  const [error, setError] = useState("");
  const isGestorOnly = user?.gestor && !user?.admin && !user?.sa;

  useEffect(() => {
    const u = getStoredUser();
    if (u?.admin || u?.sa) api<Cliente[]>("/clientes").then(setClientes).catch(() => {});
    api<Periodo[]>("/periodos").then(setPeriodos).catch(() => {});
    api<Formulario[]>("/formularios").then(setFormularios).catch(() => {});
    if (u?.gestor && !u?.admin && !u?.sa && u?.cliente_id) setClienteId(String(u.cliente_id));
  }, []);

  useEffect(() => {
    const cid = clienteId ? parseInt(clienteId, 10) : (isGestorOnly ? user?.cliente_id ?? null : null);
    if (!cid) {
      setUsuarios([]);
      return;
    }
    api<User[]>("/users?limit=500&cliente_id=" + cid).then(setUsuarios).catch(() => setUsuarios([]));
  }, [clienteId, isGestorOnly, user?.cliente_id]);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError("");
    setSuccess("");
    if (!usuarioId || !formularioId) {
      setError("Selecione o usuário e o formulário.");
      return;
    }
    setLoading(true);
    try {
      await api("/usuario-formulario", {
        method: "POST",
        body: JSON.stringify({
          usuario_id: parseInt(usuarioId, 10),
          formulario_id: parseInt(formularioId, 10),
          periodo_id: periodoId ? parseInt(periodoId, 10) : null,
          data_limite: dataLimite || null,
          enviar_invitacao: enviarInvitacao,
        }),
      });
      setSuccess("Formulário atribuído com sucesso.");
      setUsuarioId("");
      setFormularioId("");
      setPeriodoId("");
      setDataLimite("");
    } catch (err) {
      setError(err instanceof Error ? err.message : "Erro ao atribuir");
    } finally {
      setLoading(false);
    }
  }

  return (
    <div>
      <Link href="/dashboard" className="text-gray-600 hover:text-primary text-sm">← Início</Link>
      <h1 className="text-2xl font-bold text-emotive-gray-header mt-2 mb-2">Atribuir formulário (individual)</h1>
      <p className="text-gray-600 text-sm mb-6">Atribua um formulário a um único usuário. Para vários usuários de uma vez, use Atribuição em massa.</p>

      <form onSubmit={handleSubmit} className="max-w-xl space-y-4 bg-white p-6 rounded-xl border border-gray-200">
        {success && <div className="p-3 rounded-lg bg-green-50 text-green-800 text-sm">{success}</div>}
        {error && <div className="p-3 rounded-lg bg-red-50 text-red-700 text-sm">{error}</div>}

        {!isGestorOnly && (
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
            <select
              value={clienteId}
              onChange={(e) => setClienteId(e.target.value)}
              className="w-full px-3 py-2 border border-slate-300 rounded-lg"
              required
            >
              <option value="">— Selecionar —</option>
              {clientes.map((c) => (
                <option key={c.id} value={c.id}>{c.nome_fantasia || c.razao_social || c.cpf_cnpj}</option>
              ))}
            </select>
          </div>
        )}

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Usuário</label>
          <select
            value={usuarioId}
            onChange={(e) => setUsuarioId(e.target.value)}
            className="w-full px-3 py-2 border border-slate-300 rounded-lg"
            required
          >
            <option value="">— Selecionar —</option>
            {usuarios.map((u) => (
              <option key={u.id} value={u.id}>{u.name} ({u.email})</option>
            ))}
          </select>
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Formulário</label>
          <select
            value={formularioId}
            onChange={(e) => setFormularioId(e.target.value)}
            className="w-full px-3 py-2 border border-slate-300 rounded-lg"
            required
          >
            <option value="">— Selecionar —</option>
            {formularios.map((f) => (
              <option key={f.id} value={f.id}>{f.nome || f.label}</option>
            ))}
          </select>
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Período (onda) — opcional</label>
          <select
            value={periodoId}
            onChange={(e) => setPeriodoId(e.target.value)}
            className="w-full px-3 py-2 border border-slate-300 rounded-lg"
          >
            <option value="">— Nenhum —</option>
            {periodos.map((p) => (
              <option key={p.id} value={p.id}>{p.nome}</option>
            ))}
          </select>
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Data limite — opcional</label>
          <input
            type="date"
            value={dataLimite}
            onChange={(e) => setDataLimite(e.target.value)}
            className="w-full px-3 py-2 border border-slate-300 rounded-lg"
          />
        </div>

        <div>
          <label className="flex items-center gap-2 cursor-pointer">
            <input
              type="checkbox"
              checked={enviarInvitacao}
              onChange={(e) => setEnviarInvitacao(e.target.checked)}
              className="rounded border-slate-300 text-primary"
            />
            <span className="text-sm text-gray-700">Enviar e-mail de convite ao usuário</span>
          </label>
        </div>

        <div className="flex gap-3 pt-2">
          <button type="submit" disabled={loading} className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark disabled:opacity-50">
            {loading ? "Atribuindo…" : "Atribuir formulário"}
          </button>
          <Link href="/dashboard" className="px-4 py-2 border border-slate-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancelar</Link>
        </div>
      </form>
    </div>
  );
}
