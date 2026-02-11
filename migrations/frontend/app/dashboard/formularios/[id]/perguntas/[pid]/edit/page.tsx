"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { useRouter, useParams } from "next/navigation";
import { api } from "@/lib/api";
import RichTextEditor from "@/components/RichTextEditor";
import type { Pergunta } from "@/types";

export default function EditPerguntaPage() {
  const router = useRouter();
  const params = useParams();
  const formularioId = Number(params.id);
  const perguntaId = Number(params.pid);
  const [numero_da_pergunta, setNumero] = useState(1);
  const [pergunta, setPergunta] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (!perguntaId) return;
    api<Pergunta>(`/perguntas/${perguntaId}`)
      .then((p) => {
        setNumero(p.numero_da_pergunta);
        setPergunta(p.pergunta);
      })
      .catch((e) => setError(e instanceof Error ? e.message : "Erro ao carregar"));
  }, [perguntaId]);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError("");
    setLoading(true);
    try {
      await api(`/perguntas/${perguntaId}`, {
        method: "PUT",
        body: JSON.stringify({ numero_da_pergunta, pergunta }),
      });
      router.push(`/dashboard/formularios/${formularioId}/perguntas`);
      router.refresh();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Erro ao atualizar");
    } finally {
      setLoading(false);
    }
  }

  if (error && !pergunta) return <div className="text-red-600">{error} <Link href={`/dashboard/formularios/${formularioId}/perguntas`} className="text-primary ml-2">Voltar</Link></div>;

  return (
    <div>
      <div className="mb-6"><Link href={`/dashboard/formularios/${formularioId}/perguntas`} className="text-gray-600 hover:text-primary text-sm">← Perguntas</Link></div>
      <h1 className="text-2xl font-bold text-emotive-gray-header mb-6">Editar pergunta</h1>
      <form onSubmit={handleSubmit} className="max-w-xl space-y-4 bg-white p-6 rounded-xl border border-gray-200">
        {error && <div className="p-3 rounded-lg bg-red-50 text-red-700 text-sm">{error}</div>}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Número da pergunta</label>
          <input type="number" value={numero_da_pergunta} onChange={(e) => setNumero(Number(e.target.value))} min={1} className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Pergunta</label>
          <RichTextEditor value={pergunta} onChange={setPergunta} placeholder="Texto da pergunta" minHeight="160px" />
        </div>
        <div className="flex gap-3">
          <button type="submit" disabled={loading} className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark disabled:opacity-50">{loading ? "Salvando…" : "Salvar"}</button>
          <Link href={`/dashboard/formularios/${formularioId}/perguntas`} className="px-4 py-2 border border-slate-300 rounded-lg text-gray-700 hover:bg-emotive-panel-bg">Cancelar</Link>
        </div>
      </form>
    </div>
  );
}
