"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { useParams } from "next/navigation";
import { api } from "@/lib/api";
import type { Formulario, Variavel } from "@/types";

export default function VariaveisPage() {
  const params = useParams();
  const id = Number(params.id);
  const [formulario, setFormulario] = useState<Formulario | null>(null);
  const [list, setList] = useState<Variavel[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    if (!id) return;
    Promise.all([
      api<Formulario>(`/formularios/${id}`).then(setFormulario),
      api<Variavel[]>(`/variaveis?formulario_id=${id}`).then(setList),
    ]).catch((e) => setError(e instanceof Error ? e.message : "Erro")).finally(() => setLoading(false));
  }, [id]);

  async function handleDelete(variavelId: number) {
    if (!confirm("Excluir esta variável?")) return;
    try {
      await api(`/variaveis/${variavelId}`, { method: "DELETE" });
      setList((prev) => prev.filter((v) => v.id !== variavelId));
    } catch (e) {
      setError(e instanceof Error ? e.message : "Erro ao excluir");
    }
  }

  if (loading) return <p className="text-gray-500">Carregando…</p>;
  if (error && !formulario) return <div className="text-red-600">{error} <Link href="/dashboard/formularios" className="text-primary ml-2">Voltar</Link></div>;

  return (
    <div>
      <div className="mb-6 flex items-center justify-between flex-wrap gap-4">
        <Link href="/dashboard/formularios" className="text-gray-600 hover:text-primary text-sm">← Formulários</Link>
        <Link href={`/dashboard/formularios/${id}/variaveis/new`} className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark">Nova variável</Link>
      </div>
      <h1 className="text-2xl font-bold text-emotive-gray-header mb-2">Variáveis (dimensões)</h1>
      {formulario && <p className="text-gray-600 mb-6">Formulário: {formulario.nome}</p>}
      <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table className="w-full text-left">
          <thead className="bg-emotive-panel-bg border-b border-gray-200">
            <tr>
              <th className="px-4 py-3 font-medium text-gray-700">Tag</th>
              <th className="px-4 py-3 font-medium text-gray-700">Nome</th>
              <th className="px-4 py-3 font-medium text-gray-700">B / M / A</th>
              <th className="px-4 py-3 font-medium text-gray-700 w-32">Ações</th>
            </tr>
          </thead>
          <tbody>
            {list.map((v) => (
              <tr key={v.id} className="border-b border-gray-100 last:border-0">
                <td className="px-4 py-3 font-mono text-sm">{v.tag}</td>
                <td className="px-4 py-3">{v.nome}</td>
                <td className="px-4 py-3 text-sm">{v.B} / {v.M} / {v.A ?? "—"}</td>
                <td className="px-4 py-3 flex gap-2">
                  <Link href={`/dashboard/formularios/${id}/variaveis/${v.id}/edit`} className="text-primary text-sm hover:underline">Editar</Link>
                  <button type="button" onClick={() => handleDelete(v.id)} className="text-red-600 text-sm hover:underline">Excluir</button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
      {list.length === 0 && <p className="text-gray-500 mt-4">Nenhuma variável. Adicione a primeira.</p>}
    </div>
  );
}
