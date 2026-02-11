"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { api } from "@/lib/api";
import type { Cliente } from "@/types";

export default function NewUsuarioPage() {
  const router = useRouter();
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [cliente_id, setClienteId] = useState<string>("");
  const [sa, setSa] = useState(false);
  const [admin, setAdmin] = useState(false);
  const [gestor, setGestor] = useState(false);
  const [usuario, setUsuario] = useState(true);
  const [unidade, setUnidade] = useState("");
  const [area, setArea] = useState("");
  const [nivelJerarquico, setNivelJerarquico] = useState("");
  const [tempoEmpresa, setTempoEmpresa] = useState("");
  const [modeloTrabalho, setModeloTrabalho] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);
  const [clientes, setClientes] = useState<Cliente[]>([]);

  useEffect(() => {
    api<Cliente[]>("/clientes")
      .then(setClientes)
      .catch(() => {});
  }, []);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError("");
    setLoading(true);
    try {
      await api("/users", {
        method: "POST",
        body: JSON.stringify({
          name,
          email,
          password,
          cliente_id: cliente_id ? parseInt(cliente_id, 10) : null,
          sa,
          admin,
          gestor,
          usuario,
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
      setError(err instanceof Error ? err.message : "Erro ao criar usuário");
    } finally {
      setLoading(false);
    }
  }

  return (
    <div>
      <div className="mb-6 flex items-center gap-4">
        <Link href="/dashboard/usuarios" className="text-gray-600 hover:text-primary text-sm">
          ← Usuários
        </Link>
      </div>
      <h1 className="text-2xl font-bold text-emotive-gray-header mb-6">Novo usuário</h1>
      <form onSubmit={handleSubmit} className="max-w-xl space-y-4 bg-white p-6 rounded-xl border border-gray-200">
        {error && <div className="p-3 rounded-lg bg-red-50 text-red-700 text-sm">{error}</div>}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Nome</label>
          <input
            type="text"
            value={name}
            onChange={(e) => setName(e.target.value)}
            required
            className="w-full px-3 py-2 border border-slate-300 rounded-lg"
          />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
            className="w-full px-3 py-2 border border-slate-300 rounded-lg"
          />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Senha</label>
          <input
            type="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            required
            minLength={6}
            className="w-full px-3 py-2 border border-slate-300 rounded-lg"
          />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Cliente (opcional)</label>
          <select
            value={cliente_id}
            onChange={(e) => setClienteId(e.target.value)}
            className="w-full px-3 py-2 border border-slate-300 rounded-lg"
          >
            <option value="">—</option>
            {clientes.map((c) => (
              <option key={c.id} value={String(c.id)}>
                {c.nome_fantasia || c.razao_social || c.cpf_cnpj}
              </option>
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
          <label className="flex items-center gap-2">
            <input type="checkbox" checked={sa} onChange={(e) => setSa(e.target.checked)} />
            <span className="text-sm">SA</span>
          </label>
          <label className="flex items-center gap-2">
            <input type="checkbox" checked={admin} onChange={(e) => setAdmin(e.target.checked)} />
            <span className="text-sm">Admin</span>
          </label>
          <label className="flex items-center gap-2">
            <input type="checkbox" checked={gestor} onChange={(e) => setGestor(e.target.checked)} />
            <span className="text-sm">Gestor</span>
          </label>
          <label className="flex items-center gap-2">
            <input type="checkbox" checked={usuario} onChange={(e) => setUsuario(e.target.checked)} />
            <span className="text-sm">Usuário</span>
          </label>
        </div>
        <div className="flex gap-3 pt-2">
          <button
            type="submit"
            disabled={loading}
            className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark disabled:opacity-50"
          >
            {loading ? "Salvando…" : "Criar"}
          </button>
          <Link href="/dashboard/usuarios" className="px-4 py-2 border border-slate-300 rounded-lg text-gray-700 hover:bg-emotive-panel-bg">
            Cancelar
          </Link>
        </div>
      </form>
    </div>
  );
}
