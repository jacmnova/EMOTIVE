"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { useRouter, useParams } from "next/navigation";
import { api } from "@/lib/api";
import RichTextEditor from "@/components/RichTextEditor";
import type { Formulario, Etapa, TipoCalculo } from "@/types";

export default function EditFormularioPage() {
  const router = useRouter();
  const params = useParams();
  const id = Number(params.id);
  const [nome, setNome] = useState("");
  const [label, setLabel] = useState("");
  const [descricao, setDescricao] = useState("");
  const [instrucoes, setInstrucoes] = useState("");
  const [score_ini, setScoreIni] = useState(0);
  const [score_fim, setScoreFim] = useState(6);
  const [status, setStatus] = useState(false);
  const [calculo_id, setCalculoId] = useState<number | "">("");
  const [calculos, setCalculos] = useState<TipoCalculo[]>([]);
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);
  const [etapas, setEtapas] = useState<Etapa[]>([]);
  const [etapaNum, setEtapaNum] = useState(1);
  const [etapaDe, setEtapaDe] = useState(1);
  const [etapaAte, setEtapaAte] = useState(1);
  const [etapasLoading, setEtapasLoading] = useState(false);

  useEffect(() => {
    if (!id) return;
    api<Formulario>(`/formularios/${id}`)
      .then((f) => {
        setNome(f.nome);
        setLabel(f.label);
        setDescricao(f.descricao ?? "");
        setInstrucoes(f.instrucoes ?? "");
        setScoreIni(f.score_ini);
        setScoreFim(f.score_fim);
        setStatus(f.status);
        setCalculoId(f.calculo_id ?? "");
      })
      .catch((e) => setError(e instanceof Error ? e.message : "Erro ao carregar"));
  }, [id]);

  useEffect(() => {
    api<TipoCalculo[]>("/calculos")
      .then((list) => setCalculos(list))
      .catch(() => setCalculos([]));
  }, []);

  useEffect(() => {
    if (!id) return;
    setEtapasLoading(true);
    api<Etapa[]>(`/formularios/${id}/etapas`)
      .then(setEtapas)
      .catch(() => setEtapas([]))
      .finally(() => setEtapasLoading(false));
  }, [id]);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError("");
    setLoading(true);
    try {
      await api(`/formularios/${id}`, {
        method: "PUT",
        body: JSON.stringify({
          nome,
          label,
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
      setError(err instanceof Error ? err.message : "Erro ao atualizar");
    } finally {
      setLoading(false);
    }
  }

  async function handleAddEtapa(e: React.FormEvent) {
    e.preventDefault();
    try {
      const newEtapa = await api<Etapa>(`/formularios/${id}/etapas`, {
        method: "POST",
        body: JSON.stringify({ etapa: etapaNum, de: etapaDe, ate: etapaAte }),
      });
      setEtapas((prev) => [...prev, newEtapa]);
      setEtapaNum((n) => n + 1);
    } catch {}
  }

  async function handleRemoveEtapa(etapaId: number) {
    try {
      await api(`/formularios/${id}/etapas/${etapaId}`, { method: "DELETE" });
      setEtapas((prev) => prev.filter((x) => x.id !== etapaId));
    } catch {}
  }

  if (error && !nome) {
    return (
      <div className="text-red-600">
        {error}{" "}
        <Link href="/dashboard/formularios" className="text-primary ml-2">Voltar</Link>
      </div>
    );
  }

  return (
    <div>
      <div className="mb-6">
        <Link href="/dashboard/formularios" className="text-gray-600 hover:text-primary text-sm">← Formulários</Link>
      </div>
      <h1 className="text-2xl font-bold text-emotive-gray-header mb-6">Editar Formulário</h1>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Coluna esquerda: dados do formulário */}
        <div className="lg:col-span-2">
          <form onSubmit={handleSubmit} className="bg-white p-6 rounded-xl border border-gray-200 space-y-5">
            {error && (
              <div className="p-3 rounded-lg bg-red-50 text-red-700 text-sm">{error}</div>
            )}

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Nome do Formulário</label>
              <input
                type="text"
                value={nome}
                onChange={(e) => setNome(e.target.value)}
                required
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Label do Formulário</label>
              <input
                type="text"
                value={label}
                onChange={(e) => setLabel(e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              />
            </div>

            <div>
              <h3 className="text-sm font-medium text-gray-700 mb-1">Descrição</h3>
              <RichTextEditor
                value={descricao}
                onChange={setDescricao}
                placeholder="Conteúdo em texto ou HTML"
                minHeight="200px"
              />
            </div>

            <div>
              <h3 className="text-sm font-medium text-gray-700 mb-1">Instruções</h3>
              <RichTextEditor
                value={instrucoes}
                onChange={setInstrucoes}
                placeholder="Instruções em texto ou HTML"
                minHeight="240px"
              />
            </div>

            <div>
              <h3 className="text-sm font-medium text-gray-700 mb-1">Cálculo:</h3>
              <select
                value={calculo_id}
                onChange={(e) => setCalculoId(e.target.value === "" ? "" : Number(e.target.value))}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              >
                <option value="">— Nenhum —</option>
                {calculos.map((c) => (
                  <option key={c.id} value={c.id}>
                    {c.nome}{c.descricao ? ` - ${c.descricao}` : ""}
                  </option>
                ))}
              </select>
            </div>

            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Score Inicial</label>
                <input
                  type="number"
                  value={score_ini}
                  onChange={(e) => setScoreIni(Number(e.target.value))}
                  min={0}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Score Final</label>
                <input
                  type="number"
                  value={score_fim}
                  onChange={(e) => setScoreFim(Number(e.target.value))}
                  min={0}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                />
              </div>
            </div>

            <div className="flex items-center gap-2">
              <input
                type="checkbox"
                id="status"
                checked={status}
                onChange={(e) => setStatus(e.target.checked)}
                className="rounded border-gray-300 text-primary focus:ring-primary"
              />
              <label htmlFor="status" className="text-sm text-gray-700">Formulário ativo</label>
            </div>

            <div className="flex gap-3 pt-2">
              <button
                type="submit"
                disabled={loading}
                className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark disabled:opacity-50"
              >
                {loading ? "Salvando…" : "Salvar"}
              </button>
              <Link
                href="/dashboard/formularios"
                className="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-emotive-panel-bg"
              >
                Cancelar
              </Link>
            </div>
          </form>
        </div>

        {/* Coluna direita: Etapas do Formulário */}
        <div className="lg:col-span-1">
          <div className="bg-white p-6 rounded-xl border border-gray-200">
            <h2 className="text-lg font-semibold text-emotive-gray-header mb-4 flex items-center gap-2">
              <svg className="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 10h16M4 14h16M4 18h16" />
              </svg>
              Etapas do Formulário
            </h2>

            {etapasLoading ? (
              <p className="text-gray-500 text-sm">Carregando…</p>
            ) : (
              <>
                <form onSubmit={handleAddEtapa} className="space-y-3 mb-6">
                  <div className="grid grid-cols-3 gap-2">
                    <div>
                      <label className="block text-xs text-gray-600 mb-1">Etapa</label>
                      <input
                        type="number"
                        min={1}
                        value={etapaNum}
                        onChange={(e) => setEtapaNum(Number(e.target.value))}
                        className="w-full px-2 py-1.5 border border-gray-300 rounded text-sm"
                      />
                    </div>
                    <div>
                      <label className="block text-xs text-gray-600 mb-1">De</label>
                      <input
                        type="number"
                        min={1}
                        value={etapaDe}
                        onChange={(e) => setEtapaDe(Number(e.target.value))}
                        className="w-full px-2 py-1.5 border border-gray-300 rounded text-sm"
                      />
                    </div>
                    <div>
                      <label className="block text-xs text-gray-600 mb-1">Até</label>
                      <input
                        type="number"
                        min={1}
                        value={etapaAte}
                        onChange={(e) => setEtapaAte(Number(e.target.value))}
                        className="w-full px-2 py-1.5 border border-gray-300 rounded text-sm"
                      />
                    </div>
                  </div>
                  <button
                    type="submit"
                    className="w-full px-4 py-2 bg-emotive-gray-header text-white rounded-lg font-medium hover:bg-gray-700 text-sm"
                  >
                    + Adicionar Etapa
                  </button>
                </form>

                {etapas.length === 0 ? (
                  <p className="text-gray-500 text-sm">Nenhuma etapa. Adicione acima.</p>
                ) : (
                  <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                      <thead className="bg-emotive-panel-bg border-b border-gray-200">
                        <tr>
                          <th className="px-3 py-2 font-medium text-emotive-gray-header">Etapa</th>
                          <th className="px-3 py-2 font-medium text-emotive-gray-header">De</th>
                          <th className="px-3 py-2 font-medium text-emotive-gray-header">Até</th>
                          <th className="px-3 py-2 font-medium text-emotive-gray-header">Ações</th>
                        </tr>
                      </thead>
                      <tbody>
                        {etapas.map((e) => (
                          <tr key={e.id} className="border-b border-gray-100 last:border-0">
                            <td className="px-3 py-2">{e.etapa}</td>
                            <td className="px-3 py-2">{e.de}</td>
                            <td className="px-3 py-2">{e.ate}</td>
                            <td className="px-3 py-2">
                              <button
                                type="button"
                                onClick={() => handleRemoveEtapa(e.id)}
                                className="inline-flex items-center gap-1 px-2 py-1 text-red-600 hover:bg-red-50 rounded text-sm font-medium"
                              >
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Remover
                              </button>
                            </td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                )}
              </>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
