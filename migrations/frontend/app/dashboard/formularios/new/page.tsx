"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { api } from "@/lib/api";
import RichTextEditor from "@/components/RichTextEditor";
import type { TipoCalculo } from "@/types";

export default function NewFormularioPage() {
  const router = useRouter();
  const [nome, setNome] = useState("");
  const [label, setLabel] = useState("");
  const [descricao, setDescricao] = useState("");
  const [instrucoes, setInstrucoes] = useState("");
  const [score_ini, setScoreIni] = useState(0);
  const [score_fim, setScoreFim] = useState(6);
  const [calculo_id, setCalculoId] = useState<number | "">("");
  const [calculos, setCalculos] = useState<TipoCalculo[]>([]);
  const [status, setStatus] = useState(false);
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    api<TipoCalculo[]>("/calculos").then(setCalculos).catch(() => setCalculos([]));
  }, []);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError("");
    setLoading(true);
    try {
      await api("/formularios", {
        method: "POST",
        body: JSON.stringify({
          nome,
          label: label || nome,
          descricao,
          instrucoes,
          score_ini,
          score_fim,
          calculo_id: calculo_id === "" ? null : calculo_id,
          status,
        }),
      });
      router.push("/dashboard/formularios");
      router.refresh();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Erro ao criar formulário");
    } finally {
      setLoading(false);
    }
  }

  return (
    <div>
      <div className="mb-6"><Link href="/dashboard/formularios" className="text-gray-600 hover:text-primary text-sm">← Formulários</Link></div>
      <h1 className="text-2xl font-bold text-emotive-gray-header mb-6">Novo formulário</h1>
      <form onSubmit={handleSubmit} className="max-w-xl space-y-4 bg-white p-6 rounded-xl border border-gray-200">
        {error && <div className="p-3 rounded-lg bg-red-50 text-red-700 text-sm">{error}</div>}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Nome</label>
          <input type="text" value={nome} onChange={(e) => setNome(e.target.value)} required className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Label (opcional)</label>
          <input type="text" value={label} onChange={(e) => setLabel(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg" placeholder="Ex: E.MO.TI.VE" />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
          <RichTextEditor value={descricao} onChange={setDescricao} placeholder="Descrição do formulário" minHeight="180px" />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Instruções</label>
          <RichTextEditor value={instrucoes} onChange={setInstrucoes} placeholder="Instruções em texto ou HTML" minHeight="200px" />
        </div>
        <div className="grid grid-cols-2 gap-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Score mínimo</label>
            <input type="number" value={score_ini} onChange={(e) => setScoreIni(Number(e.target.value))} min={0} max={10} className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Score máximo</label>
            <input type="number" value={score_fim} onChange={(e) => setScoreFim(Number(e.target.value))} min={0} max={10} className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
          </div>
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Tipo de cálculo</label>
          <select value={calculo_id} onChange={(e) => setCalculoId(e.target.value === "" ? "" : Number(e.target.value))} className="w-full px-3 py-2 border border-slate-300 rounded-lg">
            <option value="">— Nenhum —</option>
            {calculos.map((c) => (
              <option key={c.id} value={c.id}>{c.nome}</option>
            ))}
          </select>
        </div>
        <div>
          <label className="flex items-center gap-2"><input type="checkbox" checked={status} onChange={(e) => setStatus(e.target.checked)} /> Ativo</label>
        </div>
        <div className="flex gap-3 pt-2">
          <button type="submit" disabled={loading} className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark disabled:opacity-50">{loading ? "Salvando…" : "Criar"}</button>
          <Link href="/dashboard/formularios" className="px-4 py-2 border border-slate-300 rounded-lg text-gray-700 hover:bg-emotive-panel-bg">Cancelar</Link>
        </div>
      </form>
    </div>
  );
}
