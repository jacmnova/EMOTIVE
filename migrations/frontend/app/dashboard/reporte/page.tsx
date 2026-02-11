"use client";

import { useState, useEffect, Suspense } from "react";
import Link from "next/link";
import { useSearchParams } from "next/navigation";
import { api, getToken, API_V1 } from "@/lib/api";
import { getStoredUser } from "@/lib/auth";
import type { Relatorio } from "@/types";

function ReporteContent() {
  const searchParams = useSearchParams();
  const formulario_id = searchParams.get("formulario_id");
  const usuario_id = searchParams.get("usuario_id");
  const user = getStoredUser();
  const [data, setData] = useState<Relatorio & { formulario?: { nome: string }; user?: { name: string }; pontuacoes?: { tag?: string; nome: string; valor: number }[]; indices?: Record<string, number>; nivel_risco?: string; plan_desenvolvimento?: string[]; analise_texto?: string } | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [pdfLoading, setPdfLoading] = useState(false);

  const uid = usuario_id ? Number(usuario_id) : user?.id;
  const fid = formulario_id ? Number(formulario_id) : null;

  useEffect(() => {
    if (!fid || !uid) {
      setError("Parâmetros formulario_id e usuario_id são necessários");
      setLoading(false);
      return;
    }
    api(`/reportes?formulario_id=${fid}&usuario_id=${uid}`)
      .then(setData)
      .catch((e) => setError(e instanceof Error ? e.message : "Erro ao carregar relatório"))
      .finally(() => setLoading(false));
  }, [fid, uid]);

  async function handleDownloadPdf() {
    if (!fid || !uid) return;
    setPdfLoading(true);
    try {
      const token = getToken();
      const res = await fetch(
        `${API_V1}/pdf/relatorio?formulario_id=${fid}&usuario_id=${uid}`,
        { headers: token ? { Authorization: `Bearer ${token}` } : {} }
      );
      if (!res.ok) throw new Error("Falha ao gerar PDF");
      const blob = await res.blob();
      const url = URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = `relatorio-${fid}-${uid}.pdf`;
      a.click();
      URL.revokeObjectURL(url);
    } catch (e) {
      setError(e instanceof Error ? e.message : "Erro ao baixar PDF");
    } finally {
      setPdfLoading(false);
    }
  }

  if (loading) return <p className="text-gray-500">Carregando relatório…</p>;
  if (error && !data) {
    const isIncomplete = error.includes("Complete todas as perguntas");
    return (
      <div className="max-w-lg mx-auto mt-8 p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
        <h2 className="text-xl font-semibold text-emotive-gray-header mb-2">
          {isIncomplete ? "Relatório ainda não disponível" : "Erro"}
        </h2>
        <p className="text-gray-600 mb-4">
          {isIncomplete
            ? "Responda a todas as perguntas do questionário para poder ver o relatório."
            : error}
        </p>
        <Link
          href="/dashboard/meus-questionarios"
          className="inline-block px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark"
        >
          Ir para Meus questionários
        </Link>
      </div>
    );
  }

  return (
    <div>
      <div className="mb-6 flex items-center justify-between flex-wrap gap-4">
        <Link href="/dashboard/meus-questionarios" className="text-gray-600 hover:text-primary text-sm">← Meus questionários</Link>
        <button
          type="button"
          onClick={handleDownloadPdf}
          disabled={pdfLoading}
          className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark disabled:opacity-50"
        >
          {pdfLoading ? "Gerando PDF…" : "Baixar PDF"}
        </button>
      </div>
      {data && (
        <>
          <h1 className="text-2xl font-bold text-emotive-gray-header mb-2">{uid === user?.id ? "Tu reporte personal" : "Relatório"}</h1>
          {data.formulario && <p className="text-gray-600 mb-6">{data.formulario.nome} {data.user && ` · ${data.user.name}`}</p>}

          {data.pontuacoes && data.pontuacoes.length > 0 && (
            <section className="mb-8">
              <h2 className="text-lg font-semibold text-emotive-gray-header mb-3">Pontuações por dimensão</h2>
              <ul className="bg-white rounded-xl border border-gray-200 divide-y divide-slate-100">
                {data.pontuacoes.map((p, i) => (
                  <li key={i} className="px-4 py-3 flex justify-between">
                    <span>{p.nome || p.tag}</span>
                    <span className="font-medium">{p.valor}</span>
                  </li>
                ))}
              </ul>
            </section>
          )}

          {data.indices && Object.keys(data.indices).length > 0 && (
            <section className="mb-8">
              <h2 className="text-lg font-semibold text-emotive-gray-header mb-3">Índices</h2>
              <div className="bg-white rounded-xl border border-gray-200 p-4 flex flex-wrap gap-6">
                {Object.entries(data.indices).map(([k, v]) => (
                  <div key={k}><span className="text-gray-600">{k}:</span> <strong>{typeof v === "number" ? v.toFixed(2) : v}</strong></div>
                ))}
              </div>
            </section>
          )}

          {data.nivel_risco && (
            <section className="mb-8">
              <h2 className="text-lg font-semibold text-emotive-gray-header mb-3">Nível de risco</h2>
              <p className="bg-white rounded-xl border border-gray-200 p-4">{data.nivel_risco}</p>
            </section>
          )}

          {data.plan_desenvolvimento && data.plan_desenvolvimento.length > 0 && (
            <section className="mb-8">
              <h2 className="text-lg font-semibold text-emotive-gray-header mb-3">Plano de desenvolvimento</h2>
              <ul className="list-disc list-inside bg-white rounded-xl border border-gray-200 p-4 space-y-1">
                {data.plan_desenvolvimento.map((item, i) => (
                  <li key={i}>{item}</li>
                ))}
              </ul>
            </section>
          )}

          {data.analise_texto && (
            <section className="mb-8">
              <h2 className="text-lg font-semibold text-emotive-gray-header mb-3">Análise</h2>
              <div className="bg-white rounded-xl border border-gray-200 p-4 whitespace-pre-wrap text-gray-700">{data.analise_texto}</div>
            </section>
          )}
        </>
      )}
    </div>
  );
}

export default function ReportePage() {
  return (
    <Suspense fallback={<p className="text-gray-500">Carregando…</p>}>
      <ReporteContent />
    </Suspense>
  );
}
