"use client";

import { useEffect, useState, useMemo } from "react";
import Link from "next/link";
import { useSearchParams } from "next/navigation";
import { api } from "@/lib/api";
import { getStoredUser } from "@/lib/auth";
import type { TipoCalculo } from "@/types";

const PER_PAGE_OPTIONS = [10, 25, 50, 100];

function formatCriadoEm(iso: string | undefined): string {
  if (!iso) return "—";
  try {
    const d = new Date(iso);
    const day = String(d.getDate()).padStart(2, "0");
    const month = String(d.getMonth() + 1).padStart(2, "0");
    const year = d.getFullYear();
    const h = String(d.getHours()).padStart(2, "0");
    const min = String(d.getMinutes()).padStart(2, "0");
    return `${day}/${month}/${year} ${h}:${min}`;
  } catch {
    return "—";
  }
}

interface ModalCalculoProps {
  editId: number | null;
  onClose: () => void;
  onSuccess: () => void;
}

function ModalCalculo({ editId, onClose, onSuccess }: ModalCalculoProps) {
  const isEdit = editId !== null;
  const [nome, setNome] = useState("");
  const [descricao, setDescricao] = useState("");
  const [operador, setOperador] = useState("");
  const [formula, setFormula] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);
  const [loadingData, setLoadingData] = useState(isEdit);

  useEffect(() => {
    if (!isEdit) {
      setNome("");
      setDescricao("");
      setOperador("");
      setFormula("");
      setError("");
      return;
    }
    setLoadingData(true);
    api<TipoCalculo>(`/calculos/${editId}`)
      .then((c) => {
        setNome(c.nome);
        setDescricao(c.descricao ?? "");
        setOperador(c.operador ?? "");
        setFormula(c.formula ?? "");
      })
      .catch((e) => setError(e instanceof Error ? e.message : "Erro ao carregar"))
      .finally(() => setLoadingData(false));
  }, [editId, isEdit]);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError("");
    if (!nome.trim()) {
      setError("Nome é obrigatório.");
      return;
    }
    setLoading(true);
    try {
      if (isEdit) {
        await api(`/calculos/${editId}`, {
          method: "PUT",
          body: JSON.stringify({
            nome: nome.trim(),
            descricao: descricao.trim() || null,
            operador: operador.trim() || null,
            formula: formula.trim() || null,
          }),
        });
      } else {
        await api("/calculos", {
          method: "POST",
          body: JSON.stringify({
            nome: nome.trim(),
            descricao: descricao.trim() || null,
            operador: operador.trim() || null,
            formula: formula.trim() || null,
          }),
        });
      }
      onSuccess();
      onClose();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Erro ao guardar");
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" onClick={onClose}>
      <div
        className="bg-white rounded-xl border border-gray-200 shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto"
        onClick={(e) => e.stopPropagation()}
      >
        <div className="p-6">
          <h2 className="text-xl font-bold text-emotive-gray-header mb-4">
            {isEdit ? "Editar Cálculo" : "Novo Cálculo"}
          </h2>
          {loadingData ? (
            <p className="text-gray-500">Carregando…</p>
          ) : (
            <form onSubmit={handleSubmit} className="space-y-4">
              <button
                type="submit"
                disabled={loading}
                className="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark disabled:opacity-50 mb-2"
              >
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                {isEdit ? "Atualizar Cálculo" : "Criar Cálculo"}
              </button>
              {error && (
                <div className="p-3 rounded-lg bg-red-50 text-red-700 text-sm">{error}</div>
              )}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  Nome <span className="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  value={nome}
                  onChange={(e) => setNome(e.target.value)}
                  required
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                  placeholder="Ex: DELTA ENTRE GRUPOS"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  Descrição <span className="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  value={descricao}
                  onChange={(e) => setDescricao(e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                  placeholder="Ex: Diferença entre médias de grupos"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  Operador <span className="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  value={operador}
                  onChange={(e) => setOperador(e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                  placeholder="Ex: diferenca"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  Fórmula <span className="text-red-500">*</span>
                </label>
                <textarea
                  value={formula}
                  onChange={(e) => setFormula(e.target.value)}
                  rows={4}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent resize-y"
                  placeholder="Ex: media(grupo1) - media(grupo2)"
                />
              </div>
            </form>
          )}
        </div>
      </div>
    </div>
  );
}

export default function CalculosPage() {
  const user = getStoredUser();
  const [list, setList] = useState<TipoCalculo[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [search, setSearch] = useState("");
  const [page, setPage] = useState(1);
  const [perPage, setPerPage] = useState(10);
  const [deletingId, setDeletingId] = useState<number | null>(null);
  const [modalOpen, setModalOpen] = useState(false);
  const [editId, setEditId] = useState<number | null>(null);
  const canCreate = user?.admin || user?.sa;
  const searchParams = useSearchParams();

  useEffect(() => {
    api<TipoCalculo[]>("/calculos")
      .then(setList)
      .catch((e) => setError(e instanceof Error ? e.message : "Erro"))
      .finally(() => setLoading(false));
  }, []);

  useEffect(() => {
    const newParam = searchParams.get("new");
    const editParam = searchParams.get("edit");
    if (newParam === "1") {
      setEditId(null);
      setModalOpen(true);
      window.history.replaceState({}, "", "/dashboard/calculos");
    } else if (editParam) {
      const id = parseInt(editParam, 10);
      if (!isNaN(id)) {
        setEditId(id);
        setModalOpen(true);
        window.history.replaceState({}, "", "/dashboard/calculos");
      }
    }
  }, [searchParams]);

  function openModal(id: number | null) {
    setEditId(id);
    setModalOpen(true);
  }

  function refreshList() {
    api<TipoCalculo[]>("/calculos").then(setList).catch(() => {});
  }

  const filtered = useMemo(() => {
    if (!search.trim()) return list;
    const q = search.toLowerCase().trim();
    return list.filter(
      (c) =>
        c.nome.toLowerCase().includes(q) ||
        (c.descricao && c.descricao.toLowerCase().includes(q))
    );
  }, [list, search]);

  const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
  const paginated = useMemo(
    () => filtered.slice((page - 1) * perPage, page * perPage),
    [filtered, page, perPage]
  );

  async function handleDelete(c: TipoCalculo) {
    if (!window.confirm(`Eliminar o tipo de cálculo "${c.nome}"?`)) return;
    if (deletingId !== null) return;
    setDeletingId(c.id);
    try {
      await api(`/calculos/${c.id}`, { method: "DELETE" });
      setList((prev) => prev.filter((x) => x.id !== c.id));
      if (paginated.length <= 1 && page > 1) setPage((p) => p - 1);
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
      {modalOpen && (
        <ModalCalculo
          editId={editId}
          onClose={() => { setModalOpen(false); setEditId(null); }}
          onSuccess={refreshList}
        />
      )}
      {/* Header: título con icono + botón Incluir Cálculo */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 className="text-2xl font-bold text-emotive-gray-header flex items-center gap-2">
          <svg className="w-7 h-7 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
          </svg>
          Lista de Cálculos
        </h1>
        {canCreate && (
          <button
            type="button"
            onClick={() => openModal(null)}
            className="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark"
          >
            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
            </svg>
            Incluir Cálculo
          </button>
        )}
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
          <div className="relative">
            <input
              type="search"
              value={search}
              onChange={(e) => {
                setSearch(e.target.value);
                setPage(1);
              }}
              placeholder="Nome ou descrição..."
              className="border border-gray-300 rounded-md pl-3 pr-9 py-1.5 text-sm w-48 sm:w-56 focus:ring-2 focus:ring-primary focus:border-transparent"
            />
            <span className="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
              <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h7" />
              </svg>
            </span>
          </div>
        </div>
      </div>

      {/* Tabla */}
      <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table className="w-full text-left">
          <thead className="bg-emotive-panel-bg border-b border-gray-200">
            <tr>
              <th className="px-4 py-3 font-medium text-gray-700">
                <span className="inline-flex items-center gap-1">
                  Nome
                  <svg className="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 16V4m0 0L3 8m4-4l4 4m6-8v12m0 0l4-4m-4 4l-4-4" />
                  </svg>
                </span>
              </th>
              <th className="px-4 py-3 font-medium text-gray-700">
                <span className="inline-flex items-center gap-1">
                  Descrição
                  <svg className="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 16V4m0 0L3 8m4-4l4 4m6-8v12m0 0l4-4m-4 4l-4-4" />
                  </svg>
                </span>
              </th>
              <th className="px-4 py-3 font-medium text-gray-700">
                <span className="inline-flex items-center gap-1">
                  Criado em
                  <svg className="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 16V4m0 0L3 8m4-4l4 4m6-8v12m0 0l4-4m-4 4l-4-4" />
                  </svg>
                </span>
              </th>
              <th className="px-4 py-3 font-medium text-gray-700 w-24">Status</th>
              <th className="px-4 py-3 font-medium text-gray-700 text-right w-40">Ações</th>
            </tr>
          </thead>
          <tbody>
            {paginated.length === 0 ? (
              <tr>
                <td colSpan={5} className="px-4 py-8 text-center text-gray-500">
                  Nenhum tipo de cálculo cadastrado.
                </td>
              </tr>
            ) : (
              paginated.map((c, idx) => (
                <tr
                  key={c.id}
                  className={`border-b border-gray-100 last:border-0 hover:bg-gray-50/50 ${
                    idx % 2 === 1 ? "bg-gray-50/50" : ""
                  }`}
                >
                  <td className="px-4 py-3 font-medium text-emotive-gray-header">{c.nome}</td>
                  <td className="px-4 py-3 text-sm text-gray-600">{c.descricao ?? "—"}</td>
                  <td className="px-4 py-3 text-sm text-gray-600">{formatCriadoEm(c.created_at)}</td>
                  <td className="px-4 py-3">
                    <span className="inline-flex px-2 py-1 rounded text-xs font-medium bg-primary/15 text-primary">
                      Ativo
                    </span>
                  </td>
                  <td className="px-4 py-3">
                    <div className="flex items-center justify-end gap-1">
                      <button
                        type="button"
                        onClick={() => openModal(c.id)}
                        className="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg"
                        title="Ver"
                      >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                      </button>
                      {canCreate && (
                        <>
                          <button
                            type="button"
                            onClick={() => openModal(c.id)}
                            className="p-2 text-gray-500 hover:text-primary hover:bg-primary/10 rounded-lg"
                            title="Editar"
                          >
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                          </button>
                          <button
                            type="button"
                            onClick={() => handleDelete(c)}
                            disabled={deletingId === c.id}
                            className="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg disabled:opacity-50"
                            title="Eliminar"
                          >
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                          </button>
                          <span
                            className="inline-flex h-6 w-10 flex-shrink-0 rounded-full bg-primary/20 p-0.5 cursor-default"
                            title="Ativo"
                          >
                            <span className="h-5 w-5 rounded-full bg-primary shadow-sm translate-x-0.5" />
                          </span>
                        </>
                      )}
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
          <p>Mostrando página {page} de {totalPages}</p>
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
