"use client";

import { useEffect, useState } from "react";
import { api } from "@/lib/api";

interface MidiaInfo {
  id: number;
  titulo: string;
  tipo: string;
  url: string;
}

interface Questionario {
  usuario_formulario_id: number;
  formulario_id: number;
  formulario_nome?: string;
  status: string;
  percentual?: number;
  etapa_atual_nome?: string;
  midia?: MidiaInfo | null;
}

export default function MeusQuestionariosPage() {
  const [list, setList] = useState<Questionario[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const API_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000";

  useEffect(() => {
    api<{ questionarios: Questionario[] }>("/meus-questionarios")
      .then((data) => setList(data.questionarios))
      .catch((e) => setError(e instanceof Error ? e.message : "Error"))
      .finally(() => setLoading(false));
  }, []);

  if (loading) return <p className="text-gray-500">Carregando…</p>;
  if (error) return <p className="text-red-600">{error}</p>;

  return (
    <div>
      <h1 className="text-2xl font-bold text-emotive-gray-header mb-6">Meus questionários</h1>
      {list.length === 0 ? (
        <p className="text-gray-600">Nenhum questionário asignado.</p>
      ) : (
        <ul className="space-y-3">
          {list.map((q) => (
            <li
              key={q.usuario_formulario_id}
              className="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200"
            >
              <div>
                <span className="font-medium">{q.formulario_nome ?? `Formulário ${q.formulario_id}`}</span>
                {q.percentual != null && (
                  <span className="ml-2 text-sm text-gray-500">{q.percentual}% · {q.etapa_atual_nome ?? q.status}</span>
                )}
              </div>
              <span className="flex items-center gap-2 flex-wrap">
                {q.midia && (
                  <a
                    href={q.midia.url.startsWith("http") ? q.midia.url : `${API_URL}${q.midia.url}`}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-gray-600 text-sm hover:underline"
                  >
                    {q.midia.tipo === "video" ? "Assistir vídeo" : "Abrir mídia"}
                  </a>
                )}
                <a href={`/dashboard/questionarios/${q.usuario_formulario_id}`} className="text-primary text-sm font-medium hover:underline">Responder</a>
                {q.percentual === 100 && (
                  <a href={`/dashboard/reporte?formulario_id=${q.formulario_id}`} className="text-gray-600 text-sm hover:underline">Ver relatório</a>
                )}
              </span>
            </li>
          ))}
        </ul>
      )}
    </div>
  );
}
