"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { useRouter, useParams } from "next/navigation";
import { api } from "@/lib/api";
import type { Grupo, User } from "@/types";

export default function EditarGrupoPage() {
  const router = useRouter();
  const params = useParams();
  const id = params?.id ? String(params.id) : "";
  const [grupo, setGrupo] = useState<Grupo | null>(null);
  const [nome, setNome] = useState("");
  const [unidade, setUnidade] = useState("");
  const [area, setArea] = useState("");
  const [nivelJerarquico, setNivelJerarquico] = useState("");
  const [tempoEmpresa, setTempoEmpresa] = useState("");
  const [modeloTrabalho, setModeloTrabalho] = useState("");
  const [miembros, setMiembros] = useState<User[]>([]);
  const [clientUsers, setClientUsers] = useState<User[]>([]);
  const [addingUsers, setAddingUsers] = useState(false);
  const [selectedIds, setSelectedIds] = useState<Set<number>>(new Set());
  const [selectedUserId, setSelectedUserId] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);
  const [loadingAdd, setLoadingAdd] = useState(false);

  function loadMiembros() {
    if (!id) return;
    api<User[]>("/grupos/" + id + "/usuarios").then(setMiembros).catch(() => setMiembros([]));
  }

  const usuariosDisponiveis = clientUsers.filter((u) => !miembros.some((m) => m.id === u.id));

  useEffect(() => {
    if (!id) return;
    api<Grupo>("/grupos/" + id)
      .then((g) => {
        setGrupo(g);
        setNome(g.nome);
        setUnidade(g.unidade ?? "");
        setArea(g.area ?? "");
        setNivelJerarquico(g.nivel_jerarquico ?? "");
        setTempoEmpresa(g.tempo_empresa ?? "");
        setModeloTrabalho(g.modelo_trabalho ?? "");
        api<User[]>("/users/?limit=500&cliente_id=" + g.cliente_id).then(setClientUsers).catch(() => setClientUsers([]));
        loadMiembros();
      })
      .catch(() => setError("Grupo não encontrado"));
  }, [id]);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    if (!id || !grupo) return;
    setError("");
    setLoading(true);
    try {
      await api("/grupos/" + id, {
        method: "PUT",
        body: JSON.stringify({
          nome: nome.trim(),
          unidade: unidade || null,
          area: area || null,
          nivel_jerarquico: nivelJerarquico || null,
          tempo_empresa: tempoEmpresa || null,
          modelo_trabalho: modeloTrabalho || null,
        }),
      });
      router.push("/dashboard/grupos");
      router.refresh();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Erro ao atualizar");
    } finally {
      setLoading(false);
    }
  }

  if (error && !grupo) return <div className="p-4 text-red-600">{error} <Link href="/dashboard/grupos" className="text-primary hover:underline">Voltar</Link></div>;
  if (!grupo) return <p className="text-gray-500">Carregando…</p>;

  return (
    <div>
      <div className="mb-6"><Link href="/dashboard/grupos" className="text-gray-600 hover:text-primary text-sm">← Grupos</Link></div>
      <h1 className="text-2xl font-bold text-emotive-gray-header mb-6">Editar grupo</h1>
      <form onSubmit={handleSubmit} className="max-w-xl space-y-4 bg-white p-6 rounded-xl border border-gray-200">
        {error && <div className="p-3 rounded-lg bg-red-50 text-red-700 text-sm">{error}</div>}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Nome do grupo</label>
          <input type="text" value={nome} onChange={(e) => setNome(e.target.value)} required className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
        </div>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label className="block text-xs text-gray-500 mb-0.5">Unidade</label>
            <input type="text" value={unidade} onChange={(e) => setUnidade(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm" />
          </div>
          <div>
            <label className="block text-xs text-gray-500 mb-0.5">Área</label>
            <input type="text" value={area} onChange={(e) => setArea(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm" />
          </div>
          <div>
            <label className="block text-xs text-gray-500 mb-0.5">Nível hierárquico</label>
            <input type="text" value={nivelJerarquico} onChange={(e) => setNivelJerarquico(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm" />
          </div>
          <div>
            <label className="block text-xs text-gray-500 mb-0.5">Tempo de empresa</label>
            <input type="text" value={tempoEmpresa} onChange={(e) => setTempoEmpresa(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm" />
          </div>
          <div className="sm:col-span-2">
            <label className="block text-xs text-gray-500 mb-0.5">Modelo de trabalho</label>
            <input type="text" value={modeloTrabalho} onChange={(e) => setModeloTrabalho(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm" />
          </div>
        </div>
        <div className="flex gap-3 pt-2">
          <button type="submit" disabled={loading} className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark disabled:opacity-50">{loading ? "Salvando…" : "Guardar"}</button>
          <Link href="/dashboard/grupos" className="px-4 py-2 border border-slate-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancelar</Link>
        </div>
      </form>

      <section className="mt-8 bg-white p-6 rounded-xl border border-gray-200">
        <h2 className="text-lg font-semibold text-emotive-gray-header mb-1">Gestão de usuários do grupo</h2>
        <p className="text-sm text-gray-600 mb-4">Adicione ou remova usuários. Estes receberão a encuesta quando atribuir em massa &quot;por grupo&quot;.</p>

        {/* Selector: adicionar um usuário */}
        <div className="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
          <label className="block text-sm font-medium text-gray-700 mb-2">Adicionar usuário ao grupo</label>
          <div className="flex flex-wrap items-center gap-2">
            <select
              value={selectedUserId}
              onChange={(e) => setSelectedUserId(e.target.value)}
              className="flex-1 min-w-[200px] px-3 py-2 border border-slate-300 rounded-lg bg-white"
              aria-label="Selecionar usuário"
            >
              <option value="">— Selecionar usuário —</option>
              {usuariosDisponiveis.map((u) => (
                <option key={u.id} value={u.id}>{u.name} ({u.email})</option>
              ))}
            </select>
            <button
              type="button"
              disabled={!selectedUserId || loadingAdd}
              onClick={async () => {
                if (!selectedUserId) return;
                setError("");
                setLoadingAdd(true);
                try {
                  await api("/grupos/" + id + "/usuarios", {
                    method: "POST",
                    body: JSON.stringify({ usuario_ids: [parseInt(selectedUserId, 10)] }),
                  });
                  setSelectedUserId("");
                  loadMiembros();
                } catch (e) {
                  setError(e instanceof Error ? e.message : "Erro ao adicionar");
                } finally {
                  setLoadingAdd(false);
                }
              }}
              className="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-dark disabled:opacity-50"
            >
              {loadingAdd ? "A adicionar…" : "Adicionar ao grupo"}
            </button>
          </div>
          {usuariosDisponiveis.length === 0 && clientUsers.length > 0 && (
            <p className="text-sm text-green-700 mt-2">Todos os usuários do cliente já estão no grupo.</p>
          )}
          {clientUsers.length === 0 && (
            <p className="text-sm text-amber-700 mt-2">Nenhum usuário no cliente. Crie usuários em <Link href="/dashboard/usuarios" className="text-primary hover:underline">Usuários</Link> primeiro.</p>
          )}
        </div>

        {/* Adicionar vários de uma vez */}
        {!addingUsers ? (
          <button type="button" onClick={() => setAddingUsers(true)} className="mb-4 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200">
            Adicionar vários usuários de uma vez
          </button>
        ) : (
          <div className="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <p className="text-sm font-medium text-gray-700 mb-2">Selecione vários usuários:</p>
            <div className="max-h-48 overflow-y-auto border border-slate-200 rounded-lg p-2 space-y-1 mb-3 bg-white">
              {usuariosDisponiveis.map((u) => (
                <label key={u.id} className="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-1 rounded">
                  <input
                    type="checkbox"
                    checked={selectedIds.has(u.id)}
                    onChange={(e) => {
                      setSelectedIds((prev) => {
                        const next = new Set(prev);
                        if (e.target.checked) next.add(u.id);
                        else next.delete(u.id);
                        return next;
                      });
                    }}
                    className="rounded border-slate-300 text-primary"
                  />
                  <span className="text-sm">{u.name} ({u.email})</span>
                </label>
              ))}
              {usuariosDisponiveis.length === 0 && (
                <p className="text-gray-500 text-sm">Nenhum usuário disponível para adicionar.</p>
              )}
            </div>
            <div className="flex gap-2">
              <button
                type="button"
                disabled={selectedIds.size === 0 || loadingAdd}
                onClick={async () => {
                  if (selectedIds.size === 0) return;
                  setError("");
                  setLoadingAdd(true);
                  try {
                    await api("/grupos/" + id + "/usuarios", {
                      method: "POST",
                      body: JSON.stringify({ usuario_ids: Array.from(selectedIds) }),
                    });
                    setSelectedIds(new Set());
                    setAddingUsers(false);
                    loadMiembros();
                  } catch (e) {
                    setError(e instanceof Error ? e.message : "Erro ao adicionar");
                  } finally {
                    setLoadingAdd(false);
                  }
                }}
                className="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-dark disabled:opacity-50"
              >
                Adicionar {selectedIds.size || ""} usuário(s)
              </button>
              <button type="button" onClick={() => { setAddingUsers(false); setSelectedIds(new Set()); }} className="px-4 py-2 border border-slate-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                Cancelar
              </button>
            </div>
          </div>
        )}

        {/* Lista de miembros (CRUD: leer + eliminar) */}
        <h3 className="text-sm font-semibold text-gray-700 mt-6 mb-2">Usuários neste grupo ({miembros.length})</h3>
        {miembros.length === 0 ? (
          <p className="text-gray-500 text-sm">Nenhum usuário no grupo. Use o selector acima para adicionar.</p>
        ) : (
          <ul className="border border-gray-200 rounded-lg divide-y divide-gray-100">
            {miembros.map((u) => (
              <li key={u.id} className="flex items-center justify-between px-4 py-3 hover:bg-gray-50">
                <span className="text-sm">{u.name} — {u.email}</span>
                <button
                  type="button"
                  onClick={async () => {
                    setError("");
                    try {
                      await api("/grupos/" + id + "/usuarios/" + u.id, { method: "DELETE" });
                      loadMiembros();
                    } catch (e) {
                      setError(e instanceof Error ? e.message : "Erro ao remover");
                    }
                  }}
                  className="text-sm text-red-600 hover:underline font-medium"
                >
                  Remover do grupo
                </button>
              </li>
            ))}
          </ul>
        )}
      </section>
    </div>
  );
}
