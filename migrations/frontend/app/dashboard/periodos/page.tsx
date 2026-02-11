"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { useSearchParams } from "next/navigation";
import { api } from "@/lib/api";
import { getStoredUser } from "@/lib/auth";
import type { Periodo, Cliente, Projeto, UsuarioFormulario } from "@/types";

export default function PeriodosPage() {
  const searchParams = useSearchParams();
  const user = getStoredUser();
  const [periodos, setPeriodos] = useState<Periodo[]>([]);
  const [projetos, setProjetos] = useState<Projeto[]>([]);
  const [clientes, setClientes] = useState<Cliente[]>([]);
  const [assignmentsByPeriodo, setAssignmentsByPeriodo] = useState<Record<number, number>>({});
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const criado = searchParams.get("criado") === "1";
  const canEdit = user?.admin || user?.sa || user?.gestor;
  const projetosById = Object.fromEntries(projetos.map((pr) => [pr.id, pr]));

  useEffect(() => {
    api<Projeto[]>("/projetos").then(setProjetos).catch(() => []);
  }, []);
  useEffect(() => {
    api<Periodo[]>("/periodos")
      .then((list) => {
        setPeriodos(list);
        return list;
      })
      .then((list) => {
        if (list.length === 0) return;
        Promise.all(
          list.map((p) =>
            api<UsuarioFormulario[]>(`/usuario-formulario?periodo_id=${p.id}`).then((ufs) => ({ id: p.id, count: ufs.length }))
          )
        ).then((counts) => {
          const byId: Record<number, number> = {};
          counts.forEach(({ id, count }) => (byId[id] = count));
          setAssignmentsByPeriodo(byId);
        });
      })
      .catch((e) => setError(e instanceof Error ? e.message : "Erro ao carregar"))
      .finally(() => setLoading(false));

    if (user?.admin || user?.sa) {
      api<Cliente[]>("/clientes").then(setClientes).catch(() => {});
    }
  }, [user?.admin, user?.sa]);

  if (loading) return <p className="text-gray-500">Carregando…</p>;
  if (error) return <div className="text-red-600">{error}</div>;

  return (
    <div>
      {criado && (
        <div className="mb-4 p-3 rounded-lg bg-green-50 text-green-800 text-sm">Período (onda) criado com sucesso.</div>
      )}
      <div className="mb-6 flex items-center justify-between flex-wrap gap-4">
        <div>
          <Link href="/dashboard" className="text-gray-600 hover:text-primary text-sm">← Início</Link>
          <h1 className="text-2xl font-bold text-emotive-gray-header mt-1">Períodos / Ondas</h1>
          <p className="text-gray-600 text-sm mt-0.5">Atribuições por período para relatórios e séries temporais.</p>
        </div>
        {canEdit && (
          <Link
            href="/dashboard/periodos/novo"
            className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark"
          >
            Novo período
          </Link>
        )}
      </div>

      <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
        {periodos.length === 0 ? (
          <div className="p-8 text-center text-gray-500">
            Nenhum período cadastrado.
            {canEdit && (
              <div className="mt-2">
                <Link href="/dashboard/periodos/novo" className="text-primary hover:underline">
                  Criar primeiro período
                </Link>
              </div>
            )}
          </div>
        ) : (
          <ul className="divide-y divide-gray-100">
            {periodos.map((p) => {
              const hoje = new Date().toISOString().slice(0, 10);
              const ondaCerrada = p.data_fim && p.data_fim < hoje;
              return (
              <li key={p.id} className="flex items-center justify-between px-4 py-3 hover:bg-gray-50">
                <div>
                  <span className="font-medium text-emotive-gray-header">{p.nome}</span>
                  {ondaCerrada && (
                    <span className="ml-2 px-2 py-0.5 text-xs font-medium rounded-full bg-gray-200 text-gray-700">Onda cerrada</span>
                  )}
                  {p.projeto_id && projetosById[p.projeto_id] && (
                    <span className="text-xs text-gray-500 ml-2">({projetosById[p.projeto_id].nome})</span>
                  )}
                  {p.descricao && <span className="text-gray-500 text-sm ml-2">— {p.descricao}</span>}
                  <div className="text-xs text-gray-400 mt-0.5">
                    {p.data_inicio && p.data_fim && `${p.data_inicio} a ${p.data_fim}`}
                    {p.data_inicio && !p.data_fim && `Desde ${p.data_inicio}`}
                  </div>
                </div>
                <div className="flex items-center gap-4">
                  <span className="text-sm text-gray-600">
                    {assignmentsByPeriodo[p.id] ?? 0} atribuições
                  </span>
                  <Link
                    href={`/dashboard/usuarios?periodo_id=${p.id}`}
                    className="text-sm text-primary hover:underline"
                  >
                    Ver usuários
                  </Link>
                  {canEdit && (
                    <Link
                      href={`/dashboard/periodos/${p.id}/editar`}
                      className="text-sm text-gray-600 hover:text-primary"
                    >
                      Editar
                    </Link>
                  )}
                </div>
              </li>
            );
            })}
          </ul>
        )}
      </div>

      <div className="mt-6 flex flex-wrap gap-4">
        <Link href="/dashboard/projetos" className="text-primary hover:underline font-medium">→ Projetos</Link>
        <Link href="/dashboard/relatorio-corporativo" className="text-primary hover:underline font-medium">→ Relatório corporativo</Link>
        <Link href="/dashboard/atribuicao-em-massa" className="text-primary hover:underline font-medium">→ Atribuição em massa</Link>
      </div>
    </div>
  );
}
