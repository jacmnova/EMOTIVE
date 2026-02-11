"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { api } from "@/lib/api";
import { getStoredUser } from "@/lib/auth";
import type { Grupo, Cliente } from "@/types";

export default function GruposPage() {
  const user = getStoredUser();
  const [clientes, setClientes] = useState<Cliente[]>([]);
  const [clienteId, setClienteId] = useState("");
  const [grupos, setGrupos] = useState<Grupo[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const isGestorOnly = user?.gestor && !user?.admin && !user?.sa;

  useEffect(() => {
    const u = getStoredUser();
    if (u?.admin || u?.sa) api<Cliente[]>("/clientes").then(setClientes).catch(() => {});
    if (u?.gestor && !u?.admin && !u?.sa && u?.cliente_id) setClienteId(String(u.cliente_id));
  }, []);

  useEffect(() => {
    const cid = clienteId ? parseInt(clienteId, 10) : (isGestorOnly ? user?.cliente_id ?? null : null);
    if (!cid) {
      setGrupos([]);
      setLoading(false);
      return;
    }
    setLoading(true);
    api<Grupo[]>("/grupos?cliente_id=" + cid)
      .then(setGrupos)
      .catch((e) => setError(e instanceof Error ? e.message : "Erro"))
      .finally(() => setLoading(false));
  }, [clienteId, isGestorOnly, user?.cliente_id]);

  const cid = clienteId ? parseInt(clienteId, 10) : (isGestorOnly ? user?.cliente_id ?? null : null);

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <h1 className="text-2xl font-bold text-emotive-gray-header">Grupos</h1>
        {cid && (
          <Link href={"/dashboard/grupos/novo" + (cid ? "?cliente_id=" + cid : "")} className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark">
            Novo grupo
          </Link>
        )}
      </div>
      {!isGestorOnly && (
        <div className="mb-4">
          <label className="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
          <select
            value={clienteId}
            onChange={(e) => setClienteId(e.target.value)}
            className="w-full max-w-xs px-3 py-2 border border-slate-300 rounded-lg"
          >
            <option value="">— Selecionar —</option>
            {clientes.map((c) => (
              <option key={c.id} value={c.id}>{c.nome_fantasia || c.razao_social || c.cpf_cnpj}</option>
            ))}
          </select>
        </div>
      )}
      {error && <div className="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">{error}</div>}
      {loading && <p className="text-gray-500">Carregando…</p>}
      {!loading && cid && grupos.length === 0 && !error && (
        <p className="text-gray-500">Nenhum grupo. Crie um em &quot;Novo grupo&quot; e depois edite para adicionar usuários; assim poderá lançar a encuesta a esse grupo na Atribuição em massa.</p>
      )}
      {!loading && grupos.length > 0 && (
        <div className="bg-white rounded-xl border border-gray-200 overflow-x-auto">
          <table className="w-full text-left min-w-[800px]">
            <thead className="bg-emotive-panel-bg border-b border-gray-200">
              <tr>
                <th className="px-4 py-3 font-medium text-gray-700">Nome</th>
                <th className="px-4 py-3 font-medium text-gray-700 text-center w-24">Nº usuários</th>
                <th className="px-4 py-3 font-medium text-gray-700">Unidade</th>
                <th className="px-4 py-3 font-medium text-gray-700">Área</th>
                <th className="px-4 py-3 font-medium text-gray-700">Nível</th>
                <th className="px-4 py-3 font-medium text-gray-700">Tempo empresa</th>
                <th className="px-4 py-3 font-medium text-gray-700">Modelo trabalho</th>
                <th className="px-4 py-3 font-medium text-gray-700">Criado em</th>
                <th className="px-4 py-3 font-medium text-gray-700 w-24">Ações</th>
              </tr>
            </thead>
            <tbody>
              {grupos.map((g) => (
                <tr key={g.id} className="border-b border-gray-100 last:border-0">
                  <td className="px-4 py-3">{g.nome}</td>
                  <td className="px-4 py-3 text-center">{g.num_usuarios != null ? g.num_usuarios : "—"}</td>
                  <td className="px-4 py-3">{g.unidade ?? "—"}</td>
                  <td className="px-4 py-3">{g.area ?? "—"}</td>
                  <td className="px-4 py-3">{g.nivel_jerarquico ?? "—"}</td>
                  <td className="px-4 py-3">{g.tempo_empresa ?? "—"}</td>
                  <td className="px-4 py-3">{g.modelo_trabalho ?? "—"}</td>
                  <td className="px-4 py-3 text-sm text-gray-600">{g.created_at ? new Date(g.created_at).toLocaleDateString("pt-BR") : "—"}</td>
                  <td className="px-4 py-3">
                    <Link href={"/dashboard/grupos/" + g.id + "/editar"} className="text-primary text-sm hover:underline">Editar</Link>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}
