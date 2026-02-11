"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { useParams } from "next/navigation";
import { api } from "@/lib/api";
import type { Formulario, Pergunta } from "@/types";

export default function PerguntasPage() {
  const params = useParams();
  const id = Number(params.id);
  const [formulario, setFormulario] = useState<Formulario | null>(null);
  const [list, setList] = useState<Pergunta[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    if (!id) return;
    Promise.all([
      api<Formulario>(`/formularios/${id}`).then(setFormulario),
      api<Pergunta[]>(`/perguntas?formulario_id=${id}`).then(setList),
    ]).catch((e) => setError(e instanceof Error ? e.message : "Erro")).finally(() => setLoading(false));
  }, [id]);

  async function handleDelete(perguntaId: number) {
    if (!confirm("Excluir esta pergunta?")) return;
    try {
      await api(`/perguntas/${perguntaId}`, { method: "DELETE" });
      setList((prev) => prev.filter((p) => p.id !== perguntaId));
    } catch (e) {
      setError(e instanceof Error ? e.message : "Erro ao excluir");
    }
  }

  if (loading) return <p className="text-gray-500">Carregando…</p>;
  if (error && !formulario) return <div className="text-red-600">{error} <Link href="/dashboard/formularios" className="text-primary ml-2">Voltar</Link></div>;

  const sorted = [...list].sort((a, b) => a.numero_da_pergunta - b.numero_da_pergunta);

  return (
    <div>
      <div className="mb-6 flex items-center justify-between flex-wrap gap-4">
        <Link href="/dashboard/formularios" className="text-gray-600 hover:text-primary text-sm">← Formulários</Link>
        <Link href={`/dashboard/formularios/${id}/perguntas/new`} className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark">Nova pergunta</Link>
      </div>
      <h1 className="text-2xl font-bold text-emotive-gray-header mb-2">Perguntas</h1>
      {formulario && <p className="text-gray-600 mb-6">Formulário: {formulario.nome}</p>}
      <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table className="w-full text-left">
          <thead className="bg-emotive-panel-bg border-b border-gray-200">
            <tr>
              <th className="px-4 py-3 font-medium text-gray-700 w-20">Nº</th>
              <th className="px-4 py-3 font-medium text-gray-700">Pergunta</th>
              <th className="px-4 py-3 font-medium text-gray-700 w-32">Ações</th>
            </tr>
          </thead>
          <tbody>
            {sorted.map((p) => (
              <tr key={p.id} className="border-b border-gray-100 last:border-0">
                <td className="px-4 py-3">{p.numero_da_pergunta}</td>
                <td className="px-4 py-3">{p.pergunta}</td>
                <td className="px-4 py-3 flex gap-2">
                  <Link href={`/dashboard/formularios/${id}/perguntas/${p.id}/edit`} className="text-primary text-sm hover:underline">Editar</Link>
                  <button type="button" onClick={() => handleDelete(p.id)} className="text-red-600 text-sm hover:underline">Excluir</button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
      {sorted.length === 0 && <p className="text-gray-500 mt-4">Nenhuma pergunta. Adicione a primeira.</p>}
    </div>
  );
}
