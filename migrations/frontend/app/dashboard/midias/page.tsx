"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { api } from "@/lib/api";
import { getStoredUser } from "@/lib/auth";
import type { Midia, Formulario } from "@/types";

export default function MidiasPage() {
  const user = getStoredUser();
  const [list, setList] = useState<Midia[]>([]);
  const [formularios, setFormularios] = useState<Formulario[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const canCreate = user?.admin || user?.sa;

  useEffect(() => {
    Promise.all([
      api<Midia[]>("/midias"),
      api<Formulario[]>("/formularios").catch(() => []),
    ])
      .then(([midias, forms]) => {
        setList(midias);
        setFormularios(forms);
      })
      .catch((e) => setError(e instanceof Error ? e.message : "Erro"))
      .finally(() => setLoading(false));
  }, []);

  if (loading) return <p className="text-gray-500">Carregando…</p>;
  if (error) return <p className="text-red-600">{error}</p>;

  const formById = Object.fromEntries(formularios.map((f) => [f.id, f]));

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <h1 className="text-2xl font-bold text-emotive-gray-header">Mídias</h1>
        {canCreate && (
          <Link href="/dashboard/midias/new" className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark">
            Nova mídia
          </Link>
        )}
      </div>
      <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
        {list.length === 0 ? (
          <div className="p-8 text-center text-gray-500">Nenhuma mídia cadastrada.</div>
        ) : (
          <table className="w-full text-left">
            <thead className="bg-emotive-panel-bg border-b border-gray-200">
              <tr>
                <th className="px-4 py-3 font-medium text-gray-700">Título</th>
                <th className="px-4 py-3 font-medium text-gray-700">Tipo</th>
                <th className="px-4 py-3 font-medium text-gray-700">Formulário</th>
                <th className="px-4 py-3 font-medium text-gray-700 w-24">Ações</th>
              </tr>
            </thead>
            <tbody>
              {list.map((m) => (
                <tr key={m.id} className="border-b border-gray-100 last:border-0">
                  <td className="px-4 py-3">{m.titulo}</td>
                  <td className="px-4 py-3">{m.tipo === "video" ? "Vídeo" : "URL"}</td>
                  <td className="px-4 py-3 text-sm">{formById[m.formulario_id]?.nome ?? m.formulario_id}</td>
                  <td className="px-4 py-3">
                    {canCreate && (
                      <Link href={`/dashboard/midias/${m.id}/edit`} className="text-primary text-sm hover:underline">
                        Editar
                      </Link>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </div>
    </div>
  );
}
