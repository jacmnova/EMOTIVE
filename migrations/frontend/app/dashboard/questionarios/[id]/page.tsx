"use client";

import { useState, useEffect, useMemo } from "react";
import Link from "next/link";
import { useParams, useRouter } from "next/navigation";
import { api } from "@/lib/api";
import { getStoredUser } from "@/lib/auth";
import type { Pergunta, Etapa } from "@/types";

interface QuestionarioItem {
  usuario_formulario_id: number;
  formulario_id: number;
  formulario_nome: string;
  status: string;
  percentual: number;
  total_perguntas: number;
  respostas_count: number;
}

interface EtapaComPerguntas {
  etapa: Etapa;
  perguntas: Pergunta[];
}

export default function QuestionarioPage() {
  const params = useParams();
  const router = useRouter();
  const ufId = Number(params.id);
  const user = getStoredUser();
  const [questionario, setQuestionario] = useState<QuestionarioItem | null>(null);
  const [perguntas, setPerguntas] = useState<Pergunta[]>([]);
  const [etapas, setEtapas] = useState<Etapa[]>([]);
  const [respostas, setRespostas] = useState<Record<string, number>>({});
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState("");
  const [etapaAtualIndex, setEtapaAtualIndex] = useState(0);
  const [completedForReport, setCompletedForReport] = useState<{ formulario_id: number; usuario_id: number } | null>(null);

  useEffect(() => {
    if (!user || !ufId) return;
    api<{ questionarios: QuestionarioItem[] }>("/meus-questionarios")
      .then((res) => {
        const list = Array.isArray(res.questionarios) ? res.questionarios : [];
        const q = list.find((x) => x.usuario_formulario_id === ufId);
        if (!q) {
          setError("Questionário não encontrado");
          setLoading(false);
          return;
        }
        setQuestionario(q);
        const formId = q.formulario_id;
        const userId = user.id;
        Promise.all([
          api<Pergunta[]>(`/perguntas?formulario_id=${formId}&limit=500`).then((data) =>
            setPerguntas(Array.isArray(data) ? data : [])
          ),
          api<Etapa[]>(`/formularios/${formId}/etapas`).then((data) =>
            setEtapas(Array.isArray(data) ? data.sort((a, b) => a.etapa - b.etapa) : [])
          ),
        ]).finally(() => setLoading(false));
        api<{ pergunta_id: number; valor_resposta: number }[]>(
          `/respostas?usuario_id=${userId}&formulario_id=${formId}`
        )
          .then((respostasList) => {
            const r: Record<string, number> = {};
            (Array.isArray(respostasList) ? respostasList : []).forEach((x) => {
              r[String(x.pergunta_id)] = x.valor_resposta;
            });
            setRespostas(r);
          })
          .catch(() => {});
      })
      .catch((e) => {
        setError(e instanceof Error ? e.message : "Erro");
        setLoading(false);
      });
  }, [ufId, user?.id]);

  const etapasComPerguntas: EtapaComPerguntas[] = useMemo(() => {
    if (etapas.length === 0) return [];
    const sorted = [...etapas].sort((a, b) => a.etapa - b.etapa);
    return sorted.map((etapa) => ({
      etapa,
      perguntas: [...perguntas]
        .filter((p) => p.id >= etapa.de && p.id <= etapa.ate)
        .sort((a, b) => a.numero_da_pergunta - b.numero_da_pergunta),
    }));
  }, [etapas, perguntas]);

  const usaEtapas = etapasComPerguntas.length > 0;
  const etapaAtual = usaEtapas ? etapasComPerguntas[etapaAtualIndex] : null;
  const perguntasParaExibir = etapaAtual ? etapaAtual.perguntas : [...perguntas].sort((a, b) => a.numero_da_pergunta - b.numero_da_pergunta);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    if (!questionario) return;
    setError("");
    setSaving(true);
    try {
      await api("/respostas/salvar", {
        method: "POST",
        body: JSON.stringify({
          formulario_id: questionario.formulario_id,
          respostas: Object.fromEntries(
            Object.entries(respostas).filter(([, v]) => v >= 0 && v <= 6)
          ),
        }),
      });
      const irParaRelatorio = !usaEtapas || etapaAtualIndex >= etapasComPerguntas.length - 1;
      if (irParaRelatorio && user) {
        setCompletedForReport({ formulario_id: questionario.formulario_id, usuario_id: user.id });
      } else {
        router.push("/dashboard/meus-questionarios");
        router.refresh();
      }
    } catch (err) {
      setError(err instanceof Error ? err.message : "Erro ao salvar");
    } finally {
      setSaving(false);
    }
  }

  async function salvarRascunho() {
    if (!questionario) return;
    setError("");
    setSaving(true);
    try {
      await api("/respostas/salvar", {
        method: "POST",
        body: JSON.stringify({
          formulario_id: questionario.formulario_id,
          respostas: Object.fromEntries(
            Object.entries(respostas).filter(([, v]) => v >= 0 && v <= 6)
          ),
        }),
      });
      setError("");
      if (!usaEtapas || etapaAtualIndex < etapasComPerguntas.length - 1) {
        setEtapaAtualIndex((i) => Math.min(i + 1, etapasComPerguntas.length - 1));
      }
    } catch (err) {
      setError(err instanceof Error ? err.message : "Erro ao salvar");
    } finally {
      setSaving(false);
    }
  }

  if (loading) return <p className="text-gray-500">Carregando…</p>;
  if (error && !questionario) return <div className="text-red-600">{error} <Link href="/dashboard/meus-questionarios" className="text-primary ml-2">Voltar</Link></div>;

  if (completedForReport) {
    return (
      <div className="max-w-lg mx-auto mt-12 p-8 bg-white rounded-xl border border-gray-200 shadow-sm text-center">
        <div className="mb-6">
          <div className="mx-auto w-14 h-14 rounded-full bg-green-100 flex items-center justify-center mb-4">
            <svg className="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h2 className="text-xl font-semibold text-emotive-gray-header mb-2">Encuesta completada</h2>
          <p className="text-gray-600 mb-6">Obrigado por responder. Você já pode ver seu relatório individual.</p>
        </div>
        <div className="flex flex-col sm:flex-row gap-3 justify-center">
          <Link
            href={`/dashboard/reporte?formulario_id=${completedForReport.formulario_id}&usuario_id=${completedForReport.usuario_id}`}
            className="px-5 py-2.5 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark"
          >
            Ver mi reporte
          </Link>
          <Link
            href="/dashboard/meus-questionarios"
            className="px-5 py-2.5 border border-slate-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50"
          >
            Volver a Meus questionários
          </Link>
        </div>
      </div>
    );
  }

  const scoreMin = 0;
  const scoreMax = 6;
  const totalEtapas = etapasComPerguntas.length;
  const isUltimaEtapa = !usaEtapas || etapaAtualIndex >= totalEtapas - 1;
  const isPrimeiraEtapa = !usaEtapas || etapaAtualIndex <= 0;

  return (
    <div className="max-w-3xl mx-auto">
      <div className="mb-6 flex items-center justify-between flex-wrap gap-2">
        <Link href="/dashboard/meus-questionarios" className="text-gray-600 hover:text-primary text-sm font-medium">← Meus questionários</Link>
        {questionario && (
          <span className="text-sm text-gray-500">
            {questionario.respostas_count}/{questionario.total_perguntas} respondidas ({questionario.percentual}%)
          </span>
        )}
      </div>

      {questionario && (
        <>
          <div className="mb-8">
            <h1 className="text-2xl md:text-3xl font-bold text-emotive-gray-header mb-1">{questionario.formulario_nome}</h1>
            <p className="text-gray-600">Responda cada pergunta de {scoreMin} a {scoreMax}, conforme a escala.</p>
          </div>

          {usaEtapas && totalEtapas > 0 && (
            <div className="mb-8">
              <div className="flex items-center justify-between text-sm text-gray-600 mb-2">
                <span>Etapa {etapaAtualIndex + 1} de {totalEtapas}</span>
                <span>{etapaAtual?.perguntas.length ?? 0} perguntas nesta seção</span>
              </div>
              <div className="h-2 bg-gray-200 rounded-full overflow-hidden">
                <div
                  className="h-full bg-primary transition-all duration-300"
                  style={{ width: `${((etapaAtualIndex + 1) / totalEtapas) * 100}%` }}
                />
              </div>
              <div className="flex gap-1 mt-2">
                {etapasComPerguntas.map((_, i) => (
                  <button
                    key={i}
                    type="button"
                    onClick={() => setEtapaAtualIndex(i)}
                    className={`h-2 flex-1 rounded-full transition-colors ${
                      i === etapaAtualIndex ? "bg-primary" : i < etapaAtualIndex ? "bg-primary/60" : "bg-gray-200"
                    }`}
                    title={`Etapa ${i + 1}`}
                  />
                ))}
              </div>
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-6">
            {error && <div className="p-3 rounded-xl bg-red-50 text-red-700 text-sm border border-red-100">{error}</div>}

            {perguntasParaExibir.length === 0 ? (
              <div className="p-6 rounded-xl border border-amber-200 bg-amber-50 text-amber-800 text-sm">
                Nenhuma pergunta nesta seção. Entre em contato com o administrador.
              </div>
            ) : (
              <section className="space-y-4" aria-label={usaEtapas ? `Etapa ${etapaAtualIndex + 1}` : "Perguntas"}>
                {usaEtapas && etapaAtual && (
                  <h2 className="text-lg font-semibold text-emotive-gray-header pb-2 border-b border-gray-200">
                    Seção {etapaAtual.etapa.etapa}
                  </h2>
                )}
                {perguntasParaExibir.map((p) => (
                  <div
                    key={p.id}
                    className="bg-white p-5 rounded-xl border border-gray-200 shadow-sm hover:border-primary/30 transition-colors"
                  >
                    <p className="font-medium text-emotive-gray-header mb-4 leading-snug">
                      {p.numero_da_pergunta}. {p.pergunta}
                    </p>
                    <div className="flex flex-wrap justify-center gap-2 sm:gap-3">
                      {Array.from({ length: scoreMax - scoreMin + 1 }, (_, i) => scoreMin + i).map((v) => {
                        const selected = respostas[String(p.id)] === v;
                        return (
                          <label
                            key={v}
                            className={`
                              flex items-center justify-center cursor-pointer rounded-xl border-2 min-w-[2.75rem] w-11 h-11 sm:min-w-[3rem] sm:w-12 sm:h-12
                              text-base sm:text-lg font-semibold select-none transition-all duration-200
                              ${selected
                                ? "border-primary bg-primary text-white shadow-md shadow-primary/25 scale-105"
                                : "border-gray-200 bg-gray-50/80 text-gray-600 hover:border-primary/40 hover:bg-primary/5 hover:text-primary"
                              }
                            `}
                          >
                            <input
                              type="radio"
                              name={`p-${p.id}`}
                              value={v}
                              checked={selected}
                              onChange={() => setRespostas((prev) => ({ ...prev, [String(p.id)]: v }))}
                              className="sr-only"
                            />
                            <span>{v}</span>
                          </label>
                        );
                      })}
                    </div>
                  </div>
                ))}
              </section>
            )}

            <div className="flex flex-wrap items-center gap-3 pt-4 border-t border-gray-200">
              {usaEtapas && !isPrimeiraEtapa && (
                <button
                  type="button"
                  onClick={() => setEtapaAtualIndex((i) => i - 1)}
                  className="px-5 py-2.5 border border-slate-300 rounded-xl text-gray-700 font-medium hover:bg-gray-50"
                >
                  ← Anterior
                </button>
              )}
              {usaEtapas && !isUltimaEtapa && (
                <button
                  type="button"
                  onClick={() => setEtapaAtualIndex((i) => i + 1)}
                  className="px-5 py-2.5 bg-primary text-white rounded-xl font-medium hover:bg-primary-dark"
                >
                  Próxima etapa →
                </button>
              )}
              <button
                type="button"
                onClick={salvarRascunho}
                disabled={saving}
                className="px-5 py-2.5 border border-slate-300 rounded-xl text-gray-700 font-medium hover:bg-gray-50 disabled:opacity-50"
              >
                {saving ? "Salvando…" : "Salvar rascunho"}
              </button>
              <button
                type="submit"
                disabled={saving}
                className="px-6 py-2.5 bg-primary text-white rounded-xl font-medium hover:bg-primary-dark disabled:opacity-50"
              >
                {saving ? "Salvando…" : isUltimaEtapa ? "Salvar e ver relatório" : "Salvar respostas"}
              </button>
              {questionario.percentual === 100 && (
                <Link
                  href={`/dashboard/reporte?formulario_id=${questionario.formulario_id}&usuario_id=${user?.id}`}
                  className="px-5 py-2.5 text-gray-600 hover:text-primary font-medium"
                >
                  Ver relatório
                </Link>
              )}
            </div>
          </form>
        </>
      )}
    </div>
  );
}
