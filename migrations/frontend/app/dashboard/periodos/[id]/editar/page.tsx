"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { useRouter, useParams } from "next/navigation";
import { api } from "@/lib/api";
import type { Periodo, Projeto } from "@/types";

export default function EditarPeriodoPage() {
  const router = useRouter();
  const params = useParams();
  const id = Number(params.id);
  const [periodo, setPeriodo] = useState<Periodo | null>(null);
  const [projetos, setProjetos] = useState<Projeto[]>([]);
  const [nome, setNome] = useState("");
  const [descricao, setDescricao] = useState("");
  const [projetoId, setProjetoId] = useState<string>("");
  const [dataInicio, setDataInicio] = useState("");
  const [dataFim, setDataFim] = useState("");
  const [loading, setLoading] = useState(false);
  const [loadErr, setLoadErr] = useState("");
  const [error, setError] = useState("");

  useEffect(() => {
    if (!id) return;
    api<Periodo>(`/periodos/${id}`)
      .then((p) => {
        setPeriodo(p);
        setNome(p.nome);
        setDescricao(p.descricao ?? "");
        setProjetoId(p.projeto_id != null ? String(p.projeto_id) : "");
        setDataInicio(p.data_inicio ?? "");
        setDataFim(p.data_fim ?? "");
      })
      .catch((e) => setLoadErr(e instanceof Error ? e.message : "Erro ao carregar"));
  }, [id]);

  useEffect(() => {
    if (!periodo?.cliente_id) return;
    api<Projeto[]>(`/projetos?cliente_id=${periodo.cliente_id}`).then(setProjetos).catch(() => setProjetos([]));
  }, [periodo?.cliente_id]);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError("");
    setLoading(true);
    try {
      await api(`/periodos/${id}`, {
        method: "PUT",
        body: JSON.stringify({
          nome: nome.trim() || "Sem nome",
          descricao: descricao.trim() || null,
          projeto_id: projetoId ? parseInt(projetoId, 10) : null,
          data_inicio: dataInicio || null,
          data_fim: dataFim || null,
        }),
      });
      router.push("/dashboard/periodos");
      router.refresh();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Erro ao atualizar");
    } finally {
      setLoading(false);
    }
  }

  if (loadErr) return <div className="text-red-600">{loadErr} <Link href="/dashboard/periodos" className="text-primary ml-2">Voltar</Link></div>;

  return (
    <div>
      <Link href="/dashboard/periodos" className="text-gray-600 hover:text-primary text-sm">← Períodos</Link>
      <h1 className="text-2xl font-bold text-emotive-gray-header mt-2 mb-6">Editar período</h1>
      <form onSubmit={handleSubmit} className="max-w-xl space-y-4 bg-white p-6 rounded-xl border border-gray-200">
        {error && <div className="p-3 rounded-lg bg-red-50 text-red-700 text-sm">{error}</div>}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Projeto (opcional)</label>
          <select value={projetoId} onChange={(e) => setProjetoId(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg">
            <option value="">— Nenhum —</option>
            {projetos.map((p) => (
              <option key={p.id} value={p.id}>{p.nome}</option>
            ))}
          </select>
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Nome</label>
          <input type="text" value={nome} onChange={(e) => setNome(e.target.value)} required className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Descrição (opcional)</label>
          <input type="text" value={descricao} onChange={(e) => setDescricao(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
        </div>
        <div className="grid grid-cols-2 gap-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Data início</label>
            <input type="date" value={dataInicio} onChange={(e) => setDataInicio(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Data fim</label>
            <input type="date" value={dataFim} onChange={(e) => setDataFim(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
          </div>
        </div>
        <div className="flex gap-3 pt-2">
          <button type="submit" disabled={loading} className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark disabled:opacity-50">
            {loading ? "Salvando…" : "Salvar"}
          </button>
          <Link href="/dashboard/periodos" className="px-4 py-2 border border-slate-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancelar</Link>
        </div>
      </form>
    </div>
  );
}
