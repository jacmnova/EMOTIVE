"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { useRouter, useParams } from "next/navigation";
import { api } from "@/lib/api";
import RichTextEditor from "@/components/RichTextEditor";
import type { Formulario } from "@/types";

export default function NewPerguntaPage() {
  const router = useRouter();
  const params = useParams();
  const formularioId = Number(params.id);
  const [formulario, setFormulario] = useState<Formulario | null>(null);
  const [numero_da_pergunta, setNumero] = useState(1);
  const [pergunta, setPergunta] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (!formularioId) return;
    api<Formulario>(`/formularios/${formularioId}`).then(setFormulario).catch(() => {});
  }, [formularioId]);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError("");
    setLoading(true);
    try {
      await api("/perguntas", {
        method: "POST",
        body: JSON.stringify({ formulario_id: formularioId, numero_da_pergunta, pergunta }),
      });
      router.push(`/dashboard/formularios/${formularioId}/perguntas`);
      router.refresh();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Erro ao criar");
    } finally {
      setLoading(false);
    }
  }

  return (
    <div>
      <div className="mb-6"><Link href={`/dashboard/formularios/${formularioId}/perguntas`} className="text-gray-600 hover:text-primary text-sm">← Perguntas</Link></div>
      <h1 className="text-2xl font-bold text-emotive-gray-header mb-6">Nova pergunta {formulario && `· ${formulario.nome}`}</h1>
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
          <button type="submit" disabled={loading} className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark disabled:opacity-50">{loading ? "Salvando…" : "Criar"}</button>
          <Link href={`/dashboard/formularios/${formularioId}/perguntas`} className="px-4 py-2 border border-slate-300 rounded-lg text-gray-700 hover:bg-emotive-panel-bg">Cancelar</Link>
        </div>
      </form>
    </div>
  );
}
