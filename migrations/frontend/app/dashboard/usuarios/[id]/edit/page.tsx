"use client";

import { useState, useEffect, useCallback } from "react";
import Link from "next/link";
import { useRouter, useParams } from "next/navigation";
import { api } from "@/lib/api";
import { getStoredUser } from "@/lib/auth";
import type { User, Cliente, Formulario, UsuarioFormulario, Periodo } from "@/types";

export default function EditUsuarioPage() {
  const router = useRouter();
  const params = useParams();
  const id = Number(params.id);
  const currentUser = getStoredUser();
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [cliente_id, setClienteId] = useState<string>("");
  const [sa, setSa] = useState(false);
  const [admin, setAdmin] = useState(false);
  const [gestor, setGestor] = useState(false);
  const [usuario, setUsuario] = useState(false);
  const [ativo, setAtivo] = useState(true);
  const [unidade, setUnidade] = useState("");
  const [area, setArea] = useState("");
  const [nivelJerarquico, setNivelJerarquico] = useState("");
  const [tempoEmpresa, setTempoEmpresa] = useState("");
  const [modeloTrabalho, setModeloTrabalho] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);
  const [clientes, setClientes] = useState<Cliente[]>([]);
  const [passwordMsg, setPasswordMsg] = useState("");
  const [newPassword, setNewPassword] = useState("");
  const [passwordLoading, setPasswordLoading] = useState(false);
  const canAdminPassword = currentUser?.admin || currentUser?.sa;

  const [assignados, setAssignados] = useState<UsuarioFormulario[]>([]);
  const [formularios, setFormularios] = useState<Formulario[]>([]);
  const [formularioAdd, setFormularioAdd] = useState<string>("");
  const [periodoAdd, setPeriodoAdd] = useState<string>("");
  const [dataLimiteAdd, setDataLimiteAdd] = useState("");
  const [periodos, setPeriodos] = useState<Periodo[]>([]);
  const [addLoading, setAddLoading] = useState(false);
  const [addError, setAddError] = useState("");
  const [addSuccess, setAddSuccess] = useState("");
  const [removeLoading, setRemoveLoading] = useState<number | null>(null);
  const [enviarInvitacaoAdd, setEnviarInvitacaoAdd] = useState(false);
  const canAssign = currentUser?.admin || currentUser?.sa || currentUser?.gestor;

  const loadAssignados = useCallback(() => {
    if (!id) return;
    api<UsuarioFormulario[]>(`/usuario-formulario?usuario_id=${id}`)
      .then(setAssignados)
      .catch(() => setAssignados([]));
  }, [id]);

  useEffect(() => {
    if (!id) return;
    api<User>(`/users/${id}`)
      .then((u) => {
        setName(u.name);
        setEmail(u.email);
        setClienteId(u.cliente_id != null ? String(u.cliente_id) : "");
        setSa(u.sa);
        setAdmin(u.admin);
        setGestor(u.gestor);
        setUsuario(u.usuario);
        setAtivo(u.ativo);
        setUnidade((u as any).unidade ?? "");
        setArea((u as any).area ?? "");
        setNivelJerarquico((u as any).nivel_jerarquico ?? "");
        setTempoEmpresa((u as any).tempo_empresa ?? "");
        setModeloTrabalho((u as any).modelo_trabalho ?? "");
      })
      .catch((e) => setError(e instanceof Error ? e.message : "Erro ao carregar"));
    api<Cliente[]>("/clientes").then(setClientes).catch(() => {});
    api<Formulario[]>("/formularios").then(setFormularios).catch(() => []);
    api<Periodo[]>("/periodos").then(setPeriodos).catch(() => []);
    loadAssignados();
  }, [id, loadAssignados]);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError("");
    setLoading(true);
    try {
      await api(`/users/${id}`, {
        method: "PUT",
        body: JSON.stringify({
          name,
          email,
          cliente_id: cliente_id ? parseInt(cliente_id, 10) : null,
          sa,
          admin,
          gestor,
          usuario,
          ativo,
          unidade: unidade || null,
          area: area || null,
          nivel_jerarquico: nivelJerarquico || null,
          tempo_empresa: tempoEmpresa || null,
          modelo_trabalho: modeloTrabalho || null,
        }),
      });
      router.push("/dashboard/usuarios");
      router.refresh();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Erro ao atualizar");
    } finally {
      setLoading(false);
    }
  }

  if (error && !name) return <div className="text-red-600">{error} <Link href="/dashboard/usuarios" className="text-primary ml-2">Voltar</Link></div>;

  return (
    <div>
      <div className="mb-6">
        <Link href="/dashboard/usuarios" className="text-gray-600 hover:text-primary text-sm">← Usuários</Link>
      </div>
      <h1 className="text-2xl font-bold text-emotive-gray-header mb-6">Editar usuário</h1>
      <form onSubmit={handleSubmit} className="max-w-xl space-y-4 bg-white p-6 rounded-xl border border-gray-200">
        {error && <div className="p-3 rounded-lg bg-red-50 text-red-700 text-sm">{error}</div>}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Nome</label>
          <input type="text" value={name} onChange={(e) => setName(e.target.value)} required className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input type="email" value={email} onChange={(e) => setEmail(e.target.value)} required className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Cliente (opcional)</label>
          <select value={cliente_id} onChange={(e) => setClienteId(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg">
            <option value="">—</option>
            {clientes.map((c) => (
              <option key={c.id} value={c.id}>{c.nome_fantasia || c.razao_social || c.cpf_cnpj}</option>
            ))}
          </select>
        </div>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Unidade (relatório corporativo)</label>
            <input type="text" value={unidade} onChange={(e) => setUnidade(e.target.value)} placeholder="Ex.: Filial SP" className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Área</label>
            <input type="text" value={area} onChange={(e) => setArea(e.target.value)} placeholder="Ex.: Comercial" className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Nível hierárquico</label>
            <input type="text" value={nivelJerarquico} onChange={(e) => setNivelJerarquico(e.target.value)} placeholder="Ex.: Coordenador" className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Tempo de empresa</label>
            <input type="text" value={tempoEmpresa} onChange={(e) => setTempoEmpresa(e.target.value)} placeholder="Ex.: 2-5 anos" className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
          </div>
          <div className="sm:col-span-2">
            <label className="block text-sm font-medium text-gray-700 mb-1">Modelo de trabalho</label>
            <input type="text" value={modeloTrabalho} onChange={(e) => setModeloTrabalho(e.target.value)} placeholder="Ex.: Híbrido, Presencial, Remoto" className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
          </div>
        </div>
        <div className="flex flex-wrap gap-4">
          <label className="flex items-center gap-2"><input type="checkbox" checked={sa} onChange={(e) => setSa(e.target.checked)} /> SA</label>
          <label className="flex items-center gap-2"><input type="checkbox" checked={admin} onChange={(e) => setAdmin(e.target.checked)} /> Admin</label>
          <label className="flex items-center gap-2"><input type="checkbox" checked={gestor} onChange={(e) => setGestor(e.target.checked)} /> Gestor</label>
          <label className="flex items-center gap-2"><input type="checkbox" checked={usuario} onChange={(e) => setUsuario(e.target.checked)} /> Usuário</label>
          <label className="flex items-center gap-2"><input type="checkbox" checked={ativo} onChange={(e) => setAtivo(e.target.checked)} /> Ativo</label>
        </div>
        {canAdminPassword && (
          <div className="pt-4 border-t border-gray-200">
            <p className="text-sm font-medium text-gray-700 mb-2">Senha (admin)</p>
            <div className="flex flex-wrap items-end gap-3">
              <button
                type="button"
                disabled={passwordLoading}
                onClick={async () => {
                  setPasswordMsg("");
                  setPasswordLoading(true);
                  try {
                    await api<{ message: string }>(`/users/${id}/password/initiate`, { method: "POST" });
                    setPasswordMsg("E-mail de recuperação enviado ao usuário.");
                  } catch (e) {
                    setPasswordMsg(e instanceof Error ? e.message : "Erro ao enviar.");
                  } finally {
                    setPasswordLoading(false);
                  }
                }}
                className="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 disabled:opacity-50"
              >
                Enviar link para redefinir senha
              </button>
              <div className="flex items-center gap-2">
                <input
                  type="password"
                  placeholder="Nova senha (mín. 8)"
                  value={newPassword}
                  onChange={(e) => setNewPassword(e.target.value)}
                  className="px-3 py-2 border border-slate-300 rounded-lg text-sm w-48"
                />
                <button
                  type="button"
                  disabled={passwordLoading || newPassword.length < 8}
                  onClick={async () => {
                    setPasswordMsg("");
                    setPasswordLoading(true);
                    try {
                      await api(`/users/${id}/password/update`, {
                        method: "POST",
                        body: JSON.stringify({ new_password: newPassword }),
                      });
                      setPasswordMsg("Senha atualizada.");
                      setNewPassword("");
                    } catch (e) {
                      setPasswordMsg(e instanceof Error ? e.message : "Erro ao atualizar.");
                    } finally {
                      setPasswordLoading(false);
                    }
                  }}
                  className="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 disabled:opacity-50"
                >
                  Definir nova senha
                </button>
              </div>
            </div>
            {passwordMsg && <p className="mt-2 text-sm text-gray-600">{passwordMsg}</p>}
          </div>
        )}
        {canAssign && (
          <div className="pt-4 border-t border-gray-200">
            <h3 className="text-sm font-semibold text-gray-800 mb-2">Questionários asignados</h3>
            <div className="mb-3 flex flex-wrap items-end gap-2">
              <select
                value={formularioAdd}
                onChange={(e) => setFormularioAdd(e.target.value)}
                className="px-3 py-2 border border-slate-300 rounded-lg text-sm min-w-[200px]"
              >
                <option value="">— Selecionar formulário —</option>
                {formularios
                  .filter((f) => !assignados.some((a) => a.formulario_id === f.id))
                  .map((f) => (
                    <option key={f.id} value={f.id}>
                      {f.nome || f.label}
                    </option>
                  ))}
              </select>
              <select
                value={periodoAdd}
                onChange={(e) => setPeriodoAdd(e.target.value)}
                className="px-3 py-2 border border-slate-300 rounded-lg text-sm min-w-[160px]"
                title="Período / onda (opcional)"
              >
                <option value="">— Período (opcional) —</option>
                {periodos.map((p) => (
                  <option key={p.id} value={p.id}>
                    {p.nome}
                    {p.data_inicio ? ` (${p.data_inicio})` : ""}
                  </option>
                ))}
              </select>
              <input
                type="date"
                value={dataLimiteAdd}
                onChange={(e) => setDataLimiteAdd(e.target.value)}
                className="px-3 py-2 border border-slate-300 rounded-lg text-sm"
                placeholder="Data limite"
              />
              <label className="flex items-center gap-2 cursor-pointer whitespace-nowrap">
                <input type="checkbox" checked={enviarInvitacaoAdd} onChange={(e) => setEnviarInvitacaoAdd(e.target.checked)} className="rounded border-slate-300 text-primary" />
                <span className="text-sm text-gray-700">Enviar convite por e-mail</span>
              </label>
              <button
                type="button"
                disabled={addLoading || !formularioAdd}
                onClick={async () => {
                  setAddError("");
                  setAddLoading(true);
                  try {
                    const payload = {
                      usuario_id: id,
                      formulario_id: Number(formularioAdd),
                      periodo_id: periodoAdd ? Number(periodoAdd) : null,
                      data_limite: dataLimiteAdd || null,
                      midia_id: null,
                      enviar_invitacao: enviarInvitacaoAdd,
                    };
                    if (currentUser?.admin || currentUser?.sa) {
                      await api("/usuario-formulario/admin", { method: "POST", body: JSON.stringify(payload) });
                    } else {
                      await api("/usuario-formulario", { method: "POST", body: JSON.stringify(payload) });
                    }
                    setFormularioAdd("");
                    setPeriodoAdd("");
                    setDataLimiteAdd("");
                    loadAssignados();
                  } catch (e) {
                    setAddError(e instanceof Error ? e.message : "Erro ao asignar");
                  } finally {
                    setAddLoading(false);
                  }
                }}
                className="px-3 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-dark disabled:opacity-50"
              >
                {addLoading ? "…" : "Asignar"}
              </button>
            </div>
            {addError && <p className="text-sm text-red-600 mb-2">{addError}</p>}
            {addSuccess && <p className="text-sm text-green-700 mb-2">{addSuccess}</p>}
            {assignados.length === 0 ? (
              <p className="text-sm text-gray-500">Nenhum questionário asignado.</p>
            ) : (
              <ul className="border border-slate-200 rounded-lg divide-y divide-slate-100">
                {assignados.map((uf) => {
                  const form = formularios.find((f) => f.id === uf.formulario_id);
                  const nome = form?.nome || form?.label || `Formulário #${uf.formulario_id}`;
                  return (
                    <li key={uf.id} className="flex items-center justify-between px-3 py-2 text-sm">
                      <span className="text-gray-800">{nome}</span>
                      <span className="text-gray-500">
                        {uf.status}
                        {uf.periodo_id ? ` · ${periodos.find((p) => p.id === uf.periodo_id)?.nome ?? `Período #${uf.periodo_id}`}` : ""}
                        {uf.data_limite ? ` · Limite: ${uf.data_limite}` : ""}
                      </span>
                      <button
                        type="button"
                        disabled={removeLoading === uf.id}
                        onClick={async () => {
                          if (!confirm("Desasignar este questionário?")) return;
                          setRemoveLoading(uf.id);
                          try {
                            await api(`/usuario-formulario/${uf.id}`, { method: "DELETE" });
                            loadAssignados();
                          } catch {
                            setAddError("Erro ao desasignar");
                          } finally {
                            setRemoveLoading(null);
                          }
                        }}
                        className="text-red-600 hover:underline disabled:opacity-50"
                      >
                        {removeLoading === uf.id ? "…" : "Remover"}
                      </button>
                    </li>
                  );
                })}
              </ul>
            )}
          </div>
        )}
        <div className="flex gap-3 pt-2">
          <button type="submit" disabled={loading} className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark disabled:opacity-50">
            {loading ? "Salvando…" : "Salvar"}
          </button>
          <Link href="/dashboard/usuarios" className="px-4 py-2 border border-slate-300 rounded-lg text-gray-700 hover:bg-emotive-panel-bg">Cancelar</Link>
        </div>
      </form>
    </div>
  );
}
