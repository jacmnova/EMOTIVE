"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { useRouter, useParams } from "next/navigation";
import { api } from "@/lib/api";
import RichTextEditor from "@/components/RichTextEditor";
import type { Variavel } from "@/types";

export default function EditVariavelPage() {
  const router = useRouter();
  const params = useParams();
  const formularioId = Number(params.id);
  const variavelId = Number(params.vid);
  const [nome, setNome] = useState("");
  const [descricao, setDescricao] = useState("");
  const [tag, setTag] = useState("EXEM");
  const [B, setB] = useState(0);
  const [M, setM] = useState(0);
  const [A, setA] = useState<number | "">("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (!variavelId) return;
    api<Variavel>(`/variaveis/${variavelId}`)
      .then((v) => {
        setNome(v.nome);
        setDescricao(v.descricao ?? "");
        setTag(v.tag);
        setB(v.B);
        setM(v.M);
        setA(v.A ?? "");
      })
      .catch((e) => setError(e instanceof Error ? e.message : "Erro ao carregar"));
  }, [variavelId]);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError("");
    setLoading(true);
    try {
      await api(`/variaveis/${variavelId}`, {
        method: "PUT",
        body: JSON.stringify({
          nome,
          descricao: descricao || null,
          tag,
          B,
          M,
          A: A === "" ? null : A,
        }),
      });
      router.push(`/dashboard/formularios/${formularioId}/variaveis`);
      router.refresh();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Erro ao atualizar");
    } finally {
      setLoading(false);
    }
  }

  const tags = ["EXEM", "REPR", "DECI", "FAPS", "EXTR", "ASMO"];
  if (error && !nome) return <div className="text-red-600">{error} <Link href={`/dashboard/formularios/${formularioId}/variaveis`} className="text-primary ml-2">Voltar</Link></div>;

  return (
    <div>
      <div className="mb-6"><Link href={`/dashboard/formularios/${formularioId}/variaveis`} className="text-gray-600 hover:text-primary text-sm">← Variáveis</Link></div>
      <h1 className="text-2xl font-bold text-emotive-gray-header mb-6">Editar variável</h1>
      <form onSubmit={handleSubmit} className="max-w-xl space-y-4 bg-white p-6 rounded-xl border border-gray-200">
        {error && <div className="p-3 rounded-lg bg-red-50 text-red-700 text-sm">{error}</div>}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Tag</label>
          <select value={tag} onChange={(e) => setTag(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg">
            {tags.map((t) => (
              <option key={t} value={t}>{t}</option>
            ))}
          </select>
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Nome</label>
          <input type="text" value={nome} onChange={(e) => setNome(e.target.value)} required className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
          <RichTextEditor value={descricao} onChange={setDescricao} placeholder="Descrição da variável" minHeight="160px" />
        </div>
        <div className="grid grid-cols-3 gap-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">B</label>
            <input type="number" value={B} onChange={(e) => setB(Number(e.target.value))} className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">M</label>
            <input type="number" value={M} onChange={(e) => setM(Number(e.target.value))} className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">A (opcional)</label>
            <input type="number" value={A} onChange={(e) => setA(e.target.value === "" ? "" : Number(e.target.value))} className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
          </div>
        </div>
        <div className="flex gap-3">
          <button type="submit" disabled={loading} className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark disabled:opacity-50">{loading ? "Salvando…" : "Salvar"}</button>
          <Link href={`/dashboard/formularios/${formularioId}/variaveis`} className="px-4 py-2 border border-slate-300 rounded-lg text-gray-700 hover:bg-emotive-panel-bg">Cancelar</Link>
        </div>
      </form>
    </div>
  );
}
