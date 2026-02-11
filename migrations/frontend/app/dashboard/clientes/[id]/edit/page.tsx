"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { useRouter, useParams } from "next/navigation";
import { api } from "@/lib/api";
import type { Cliente, TipoCliente } from "@/types";

export default function EditClientePage() {
  const router = useRouter();
  const params = useParams();
  const id = Number(params.id);
  const [tipo, setTipo] = useState<TipoCliente>("cnpj");
  const [cpf_cnpj, setCpfCnpj] = useState("");
  const [nome_fantasia, setNomeFantasia] = useState("");
  const [razao_social, setRazaoSocial] = useState("");
  const [email, setEmail] = useState("");
  const [contato, setContato] = useState("");
  const [telefone, setTelefone] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (!id) return;
    api<Cliente>(`/clientes/${id}`)
      .then((c) => {
        setTipo(c.tipo);
        setCpfCnpj(c.cpf_cnpj);
        setNomeFantasia(c.nome_fantasia ?? "");
        setRazaoSocial(c.razao_social ?? "");
        setEmail(c.email ?? "");
        setContato(c.contato ?? "");
        setTelefone(c.telefone ?? "");
      })
      .catch((e) => setError(e instanceof Error ? e.message : "Erro ao carregar"));
  }, [id]);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError("");
    setLoading(true);
    try {
      await api(`/clientes/${id}`, {
        method: "PUT",
        body: JSON.stringify({
          tipo,
          cpf_cnpj,
          nome_fantasia: nome_fantasia || null,
          razao_social: razao_social || null,
          email: email || null,
          contato: contato || null,
          telefone: telefone || null,
        }),
      });
      router.push("/dashboard/clientes");
      router.refresh();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Erro ao atualizar");
    } finally {
      setLoading(false);
    }
  }

  if (error && !cpf_cnpj) return <div className="text-red-600">{error} <Link href="/dashboard/clientes" className="text-primary ml-2">Voltar</Link></div>;

  return (
    <div>
      <div className="mb-6"><Link href="/dashboard/clientes" className="text-gray-600 hover:text-primary text-sm">← Clientes</Link></div>
      <h1 className="text-2xl font-bold text-emotive-gray-header mb-6">Editar cliente</h1>
      <form onSubmit={handleSubmit} className="max-w-xl space-y-4 bg-white p-6 rounded-xl border border-gray-200">
        {error && <div className="p-3 rounded-lg bg-red-50 text-red-700 text-sm">{error}</div>}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
          <select value={tipo} onChange={(e) => setTipo(e.target.value as TipoCliente)} className="w-full px-3 py-2 border border-slate-300 rounded-lg">
            <option value="cpf">CPF</option>
            <option value="cnpj">CNPJ</option>
            <option value="internacional">Internacional</option>
          </select>
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">CPF/CNPJ</label>
          <input type="text" value={cpf_cnpj} onChange={(e) => setCpfCnpj(e.target.value)} required className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Nome fantasia</label>
          <input type="text" value={nome_fantasia} onChange={(e) => setNomeFantasia(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Razão social</label>
          <input type="text" value={razao_social} onChange={(e) => setRazaoSocial(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input type="email" value={email} onChange={(e) => setEmail(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Contato / Telefone</label>
          <input type="text" value={contato} onChange={(e) => setContato(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg mb-2" />
          <input type="text" value={telefone} onChange={(e) => setTelefone(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg" />
        </div>
        <div className="flex gap-3 pt-2">
          <button type="submit" disabled={loading} className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark disabled:opacity-50">{loading ? "Salvando…" : "Salvar"}</button>
          <Link href="/dashboard/clientes" className="px-4 py-2 border border-slate-300 rounded-lg text-gray-700 hover:bg-emotive-panel-bg">Cancelar</Link>
        </div>
      </form>
    </div>
  );
}
