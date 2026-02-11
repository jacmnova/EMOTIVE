"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { useRouter, useParams } from "next/navigation";
import { api, apiFormData } from "@/lib/api";
import type { Midia, Formulario } from "@/types";

export default function EditMidiaPage() {
  const router = useRouter();
  const params = useParams();
  const id = Number(params.id);
  const [midia, setMidia] = useState<Midia | null>(null);
  const [formularios, setFormularios] = useState<Formulario[]>([]);
  const [titulo, setTitulo] = useState("");
  const [tipo, setTipo] = useState<"video" | "url">("url");
  const [formulario_id, setFormularioId] = useState<number | "">("");
  const [url, setUrl] = useState("");
  const [arquivo, setArquivo] = useState<File | null>(null);
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (!id) return;
    Promise.all([
      api<Midia>(`/midias/${id}`),
      api<Formulario[]>("/formularios").catch(() => []),
    ]).then(([m, forms]) => {
      setMidia(m);
      setFormularios(forms);
      setTitulo(m.titulo);
      setTipo((m.tipo as "video" | "url") || "url");
      setFormularioId(m.formulario_id);
      setUrl(m.url ?? "");
    }).catch((e) => setError(e instanceof Error ? e.message : "Erro ao carregar"));
  }, [id]);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError("");
    setLoading(true);
    try {
      const formData = new FormData();
      formData.append("titulo", titulo);
      formData.append("tipo", tipo);
      formData.append("formulario_id", String(formulario_id));
      if (tipo === "url") formData.append("url", url);
      if (arquivo) formData.append("arquivo", arquivo);
      await apiFormData(`/midias/${id}`, formData, "PUT");
      router.push("/dashboard/midias");
      router.refresh();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Erro ao atualizar");
    } finally {
      setLoading(false);
    }
  }

  if (error && !midia) return <div className="text-red-600">{error} <Link href="/dashboard/midias" className="text-primary ml-2">Voltar</Link></div>;
  if (!midia) return <p className="text-gray-500">Carregando…</p>;

  return (
    <div>
      <div className="mb-6"><Link href="/dashboard/midias" className="text-gray-600 hover:text-primary text-sm">← Mídias</Link></div>
      <h1 className="text-2xl font-bold text-emotive-gray-header mb-6">Editar mídia</h1>
      <form onSubmit={handleSubmit} className="max-w-xl space-y-4 bg-white p-6 rounded-xl border border-gray-200">
        {error && <div className="p-3 rounded-lg bg-red-50 text-red-700 text-sm">{error}</div>}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Título</label>
          <input type="text" value={titulo} onChange={(e) => setTitulo(e.target.value)} required className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
          <select value={tipo} onChange={(e) => setTipo(e.target.value as "video" | "url")} className="w-full px-3 py-2 border border-slate-300 rounded-lg">
            <option value="url">URL do vídeo</option>
            <option value="video">Arquivo de vídeo</option>
          </select>
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Formulário</label>
          <select value={formulario_id} onChange={(e) => setFormularioId(e.target.value === "" ? "" : Number(e.target.value))} required className="w-full px-3 py-2 border border-slate-300 rounded-lg">
            {formularios.map((f) => (
              <option key={f.id} value={f.id}>{f.nome}</option>
            ))}
          </select>
        </div>
        {tipo === "url" && (
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">URL</label>
            <input type="url" value={url} onChange={(e) => setUrl(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
          </div>
        )}
        {tipo === "video" && (
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Novo arquivo (opcional)</label>
            <input type="file" accept="video/*" onChange={(e) => setArquivo(e.target.files?.[0] ?? null)} className="w-full text-sm text-gray-600" />
            {midia.arquivo && <p className="text-xs text-gray-500 mt-1">Atual: {midia.arquivo}</p>}
          </div>
        )}
        <div className="flex gap-3 pt-2">
          <button type="submit" disabled={loading} className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark disabled:opacity-50">
            {loading ? "Salvando…" : "Salvar"}
          </button>
          <Link href="/dashboard/midias" className="px-4 py-2 border border-slate-300 rounded-lg text-gray-700 hover:bg-emotive-panel-bg">Cancelar</Link>
        </div>
      </form>
    </div>
  );
}
