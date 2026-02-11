"use client";

import { useEffect, useState, useMemo } from "react";
import Link from "next/link";
import { api } from "@/lib/api";
import type { Formulario } from "@/types";

const PER_PAGE_OPTIONS = [10, 25, 50, 100];

function DescricaoCell({ html }: { html: string | null }) {
  if (!html || html.trim() === "") return <span className="text-gray-400">—</span>;
  return (
    <div
      className="text-gray-600 text-sm max-w-md overflow-hidden prose prose-sm prose-p:my-0.5 prose-p:leading-tight prose-b:font-semibold line-clamp-3"
      dangerouslySetInnerHTML={{ __html: html }}
    />
  );
}

export default function FormulariosPage() {
  const [list, setList] = useState<Formulario[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [search, setSearch] = useState("");
  const [page, setPage] = useState(1);
  const [perPage, setPerPage] = useState(10);
  const [togglingId, setTogglingId] = useState<number | null>(null);
  const [deletingId, setDeletingId] = useState<number | null>(null);

  useEffect(() => {
    api<Formulario[]>("/formularios")
      .then(setList)
      .catch((e) => setError(e instanceof Error ? e.message : "Erro"))
      .finally(() => setLoading(false));
  }, []);

  const filtered = useMemo(() => {
    if (!search.trim()) return list;
    const q = search.toLowerCase().trim();
    return list.filter(
      (f) =>
        f.nome.toLowerCase().includes(q) ||
        (f.label && f.label.toLowerCase().includes(q)) ||
        (f.descricao && f.descricao.toLowerCase().includes(q))
    );
  }, [list, search]);

  const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
  const paginated = useMemo(
    () => filtered.slice((page - 1) * perPage, page * perPage),
    [filtered, page, perPage]
  );

  async function handleToggleStatus(f: Formulario) {
    if (togglingId !== null) return;
    setTogglingId(f.id);
    try {
      await api<{ status: boolean }>(`/formularios/${f.id}/status`, { method: "PUT" });
      setList((prev) => prev.map((x) => (x.id === f.id ? { ...x, status: !x.status } : x)));
    } catch (_) {
      // ignore
    } finally {
      setTogglingId(null);
    }
  }

  async function handleDelete(f: Formulario) {
    if (!window.confirm(`Eliminar o formulário "${f.nome}"?`)) return;
    if (deletingId !== null) return;
    setDeletingId(f.id);
    try {
      await api(`/formularios/${f.id}`, { method: "DELETE" });
      const newLen = list.length - 1;
      if (page > 1 && (page - 1) * perPage >= newLen) setPage(page - 1);
      setList((prev) => prev.filter((x) => x.id !== f.id));
    } catch (_) {
      // ignore
    } finally {
      setDeletingId(null);
    }
  }

  if (loading) return <p className="text-gray-500">Carregando…</p>;
  if (error) return <p className="text-red-600">{error}</p>;

  return (
    <div className="space-y-4">
      {/* Header al estilo segunda imagen */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 className="text-2xl font-bold text-emotive-gray-header flex items-center gap-2">
          <svg className="w-7 h-7 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          Lista de Formulários
        </h1>
        <Link
          href="/dashboard/formularios/new"
          className="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark"
        >
          <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
          </svg>
          Adicionar Formulário
        </Link>
      </div>

      {/* Controles: mostrar X registros + pesquisar */}
      <div className="flex flex-col sm:flex-row sm:items-center gap-4 bg-white p-3 rounded-lg border border-gray-200">
        <div className="flex items-center gap-2">
          <label className="text-sm text-gray-600">Mostrar</label>
          <select
            value={perPage}
            onChange={(e) => {
              setPerPage(Number(e.target.value));
              setPage(1);
            }}
            className="border border-gray-300 rounded-md px-2 py-1.5 text-sm text-gray-700"
          >
            {PER_PAGE_OPTIONS.map((n) => (
              <option key={n} value={n}>{n}</option>
            ))}
          </select>
          <span className="text-sm text-gray-600">registros por página</span>
        </div>
        <div className="flex items-center gap-2 sm:ml-auto">
          <label className="text-sm text-gray-600">Pesquisar:</label>
          <input
            type="search"
            value={search}
            onChange={(e) => {
              setSearch(e.target.value);
              setPage(1);
            }}
            placeholder="Nome, label, descrição..."
            className="border border-gray-300 rounded-md px-3 py-1.5 text-sm w-48 sm:w-56 focus:ring-2 focus:ring-primary focus:border-transparent"
          />
        </div>
      </div>

      {/* Tabla */}
      <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table className="w-full text-left">
          <thead className="bg-emotive-panel-bg border-b border-gray-200">
            <tr>
              <th className="px-4 py-3 font-medium text-gray-700">Nome</th>
              <th className="px-4 py-3 font-medium text-gray-700 w-24 text-center">Questões</th>
              <th className="px-4 py-3 font-medium text-gray-700 w-24 text-center">Dimensões</th>
              <th className="px-4 py-3 font-medium text-gray-700">Descrição</th>
              <th className="px-4 py-3 font-medium text-gray-700">Estado</th>
              <th className="px-4 py-3 font-medium text-gray-700 text-right w-40">Ações</th>
            </tr>
          </thead>
          <tbody>
            {paginated.length === 0 ? (
              <tr>
                <td colSpan={6} className="px-4 py-8 text-center text-gray-500">
                  Nenhum formulário encontrado.
                </td>
              </tr>
            ) : (
              paginated.map((f) => (
                <tr key={f.id} className="border-b border-gray-100 last:border-0 hover:bg-gray-50/50">
                  <td className="px-4 py-3">
                    <div className="flex items-center gap-2">
                      {f.label && (
                        <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emotive-gray-header text-white">
                          {f.label}
                        </span>
                      )}
                      <span className="font-medium text-emotive-gray-header">{f.nome}</span>
                    </div>
                  </td>
                  <td className="px-4 py-3 text-center text-gray-600 tabular-nums">
                    {f.num_perguntas ?? "—"}
                  </td>
                  <td className="px-4 py-3 text-center text-gray-600 tabular-nums">
                    {f.num_variaveis ?? "—"}
                  </td>
                  <td className="px-4 py-3">
                    <DescricaoCell html={f.descricao ?? null} />
                  </td>
                  <td className="px-4 py-3">
                    <span
                      className={`inline-flex px-2 py-1 rounded text-xs font-medium ${
                        f.status ? "bg-green-100 text-green-800" : "bg-gray-100 text-gray-600"
                      }`}
                    >
                      {f.status ? "Ativo" : "Inativo"}
                    </span>
                  </td>
                  <td className="px-4 py-3">
                    <div className="flex items-center justify-end gap-1">
                      <Link
                        href={`/dashboard/formularios/${f.id}/perguntas`}
                        className="p-2 text-gray-500 hover:text-primary hover:bg-primary/10 rounded-lg"
                        title="Perguntas"
                      >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                      </Link>
                      <Link
                        href={`/dashboard/formularios/${f.id}/variaveis`}
                        className="p-2 text-gray-500 hover:text-primary hover:bg-primary/10 rounded-lg"
                        title="Variáveis"
                      >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                      </Link>
                      <Link
                        href={`/dashboard/formularios/${f.id}/edit`}
                        className="p-2 text-gray-500 hover:text-primary hover:bg-primary/10 rounded-lg"
                        title="Editar"
                      >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                      </Link>
                      <button
                        type="button"
                        onClick={() => handleDelete(f)}
                        disabled={deletingId === f.id}
                        className="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg disabled:opacity-50"
                        title="Eliminar"
                      >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                      <button
                        type="button"
                        onClick={() => handleToggleStatus(f)}
                        disabled={togglingId === f.id}
                        className={`p-2 rounded-lg disabled:opacity-50 ${
                          f.status
                            ? "text-green-600 hover:bg-green-50"
                            : "text-gray-400 hover:bg-gray-100"
                        }`}
                        title={f.status ? "Desativar" : "Ativar"}
                      >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>

      {/* Paginación */}
      {filtered.length > 0 && (
        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-sm text-gray-600">
          <p>
            Mostrando página {page} de {totalPages}
            {filtered.length !== list.length && ` (${filtered.length} de ${list.length} filtrados)`}
          </p>
          <div className="flex items-center gap-1">
            <button
              type="button"
              onClick={() => setPage((p) => Math.max(1, p - 1))}
              disabled={page <= 1}
              className="px-3 py-1.5 border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Anterior
            </button>
            <span className="px-3 py-1.5 bg-primary text-white rounded-md">{page}</span>
            <button
              type="button"
              onClick={() => setPage((p) => Math.min(totalPages, p + 1))}
              disabled={page >= totalPages}
              className="px-3 py-1.5 border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Próxima
            </button>
          </div>
        </div>
      )}
    </div>
  );
}
