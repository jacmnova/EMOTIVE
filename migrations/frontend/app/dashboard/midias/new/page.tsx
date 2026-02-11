"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { api, apiFormData } from "@/lib/api";
import type { Formulario } from "@/types";

export default function NewMidiaPage() {
  const router = useRouter();
  const [formularios, setFormularios] = useState<Formulario[]>([]);
  const [titulo, setTitulo] = useState("");
  const [tipo, setTipo] = useState<"video" | "url">("url");
  const [formulario_id, setFormularioId] = useState<number | "">("");
  const [url, setUrl] = useState("");
  const [arquivo, setArquivo] = useState<File | null>(null);
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    api<Formulario[]>("/formularios").then(setFormularios).catch(() => setFormularios([]));
  }, []);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError("");
    if (!formulario_id) {
      setError("Selecione o formulário.");
      return;
    }
    if (tipo === "url" && !url.trim()) {
      setError("Informe a URL.");
      return;
    }
    if (tipo === "video" && !arquivo) {
      setError("Selecione o arquivo de vídeo.");
      return;
    }
    setLoading(true);
    try {
      const formData = new FormData();
      formData.append("titulo", titulo);
      formData.append("tipo", tipo);
      formData.append("formulario_id", String(formulario_id));
      if (tipo === "url") formData.append("url", url);
      if (tipo === "video" && arquivo) formData.append("arquivo", arquivo);
      await apiFormData("/midias", formData);
      router.push("/dashboard/midias");
      router.refresh();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Erro ao criar");
    } finally {
      setLoading(false);
    }
  }

  return (
    <div>
      <div className="mb-6"><Link href="/dashboard/midias" className="text-gray-600 hover:text-primary text-sm">← Mídias</Link></div>
      <h1 className="text-2xl font-bold text-emotive-gray-header mb-6">Nova mídia</h1>
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
            <option value="">— Selecione —</option>
            {formularios.map((f) => (
              <option key={f.id} value={f.id}>{f.nome}</option>
            ))}
          </select>
        </div>
        {tipo === "url" && (
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">URL</label>
            <input type="url" value={url} onChange={(e) => setUrl(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg" placeholder="https://..." />
          </div>
        )}
        {tipo === "video" && (
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Arquivo de vídeo</label>
            <input type="file" accept="video/*" onChange={(e) => setArquivo(e.target.files?.[0] ?? null)} className="w-full text-sm text-gray-600" />
          </div>
        )}
        <div className="flex gap-3 pt-2">
          <button type="submit" disabled={loading} className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark disabled:opacity-50">
            {loading ? "Salvando…" : "Criar"}
          </button>
          <Link href="/dashboard/midias" className="px-4 py-2 border border-slate-300 rounded-lg text-gray-700 hover:bg-emotive-panel-bg">Cancelar</Link>
        </div>
      </form>
    </div>
  );
}
