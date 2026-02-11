"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { api } from "@/lib/api";
import type { Cliente } from "@/types";

export default function ClientesPage() {
  const [list, setList] = useState<Cliente[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    api<Cliente[]>("/clientes")
      .then(setList)
      .catch((e) => setError(e instanceof Error ? e.message : "Error"))
      .finally(() => setLoading(false));
  }, []);

  if (loading) return <p className="text-gray-500">Carregando…</p>;
  if (error) return <p className="text-red-600">{error}</p>;

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <h1 className="text-2xl font-bold text-emotive-gray-header">Clientes</h1>
        <Link href="/dashboard/clientes/new" className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark">
          Novo cliente
        </Link>
      </div>
      <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table className="w-full text-left">
          <thead className="bg-emotive-panel-bg border-b border-gray-200">
            <tr>
              <th className="px-4 py-3 font-medium text-gray-700">Nome</th>
              <th className="px-4 py-3 font-medium text-gray-700">CPF/CNPJ</th>
              <th className="px-4 py-3 font-medium text-gray-700">Email</th>
              <th className="px-4 py-3 font-medium text-gray-700">Estado</th>
              <th className="px-4 py-3 font-medium text-gray-700 w-24">Ações</th>
            </tr>
          </thead>
          <tbody>
            {list.map((c) => (
              <tr key={c.id} className="border-b border-gray-100 last:border-0">
                <td className="px-4 py-3">{c.nome_fantasia || c.razao_social || c.cpf_cnpj}</td>
                <td className="px-4 py-3">{c.cpf_cnpj}</td>
                <td className="px-4 py-3">{c.email ?? "—"}</td>
                <td className="px-4 py-3">{c.ativo ? "Ativo" : "Inativo"}</td>
                <td className="px-4 py-3">
                  <Link href={`/dashboard/clientes/${c.id}/edit`} className="text-primary text-sm hover:underline">Editar</Link>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
