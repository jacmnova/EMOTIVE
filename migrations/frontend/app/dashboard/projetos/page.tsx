"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { api } from "@/lib/api";
import { getStoredUser } from "@/lib/auth";
import type { Projeto, Cliente } from "@/types";

export default function ProjetosPage() {
  const user = getStoredUser();
  const [projetos, setProjetos] = useState<Projeto[]>([]);
  const [clientes, setClientes] = useState<Cliente[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const canEdit = user?.admin || user?.sa || user?.gestor;

  useEffect(() => {
    api<Projeto[]>("/projetos")
      .then(setProjetos)
      .catch((e) => setError(e instanceof Error ? e.message : "Erro ao carregar"))
      .finally(() => setLoading(false));
    if (user?.admin || user?.sa) api<Cliente[]>("/clientes").then(setClientes).catch(() => {});
  }, [user?.admin, user?.sa]);

  if (loading) return <p className="text-gray-500">Carregando…</p>;
  if (error) return <div className="text-red-600">{error}</div>;

  return (
    <div>
      <div className="mb-6 flex items-center justify-between flex-wrap gap-4">
        <div>
          <Link href="/dashboard/periodos" className="text-gray-600 hover:text-primary text-sm">← Períodos</Link>
          <h1 className="text-2xl font-bold text-emotive-gray-header mt-1">Projetos</h1>
          <p className="text-gray-600 text-sm mt-0.5">Agrupe períodos/ondas por projeto.</p>
        </div>
        {canEdit && (
          <Link href="/dashboard/projetos/novo" className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark">
            Novo projeto
          </Link>
        )}
      </div>

      <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
        {projetos.length === 0 ? (
          <div className="p-8 text-center text-gray-500">
            Nenhum projeto cadastrado.
            {canEdit && (
              <div className="mt-2">
                <Link href="/dashboard/projetos/novo" className="text-primary hover:underline">Criar primeiro projeto</Link>
              </div>
            )}
          </div>
        ) : (
          <ul className="divide-y divide-gray-100">
            {projetos.map((p) => (
              <li key={p.id} className="flex items-center justify-between px-4 py-3 hover:bg-gray-50">
                <div>
                  <span className="font-medium text-emotive-gray-header">{p.nome}</span>
                  {p.descricao && <span className="text-gray-500 text-sm ml-2">— {p.descricao}</span>}
                </div>
                {canEdit && (
                  <Link href={`/dashboard/projetos/${p.id}/editar`} className="text-sm text-gray-600 hover:text-primary">Editar</Link>
                )}
              </li>
            ))}
          </ul>
        )}
      </div>
    </div>
  );
}
