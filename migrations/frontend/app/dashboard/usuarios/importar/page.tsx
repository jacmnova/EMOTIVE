"use client";

import { useState } from "react";
import Link from "next/link";
import { apiFormData } from "@/lib/api";
import { getStoredUser } from "@/lib/auth";

export default function ImportarUsuariosPage() {
  const user = getStoredUser();
  const [file, setFile] = useState<File | null>(null);
  const [loading, setLoading] = useState(false);
  const [message, setMessage] = useState<{ type: "success" | "error"; text: string } | null>(null);

  const canImport = user?.gestor && user?.cliente_id;

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    if (!file || !canImport) return;
    setMessage(null);
    setLoading(true);
    try {
      const formData = new FormData();
      formData.append("arquivo", file);
      const data = await apiFormData<{ message: string; cadastrados: number }>("/users/importar", formData);
      setMessage({ type: "success", text: data.message });
      setFile(null);
      const input = document.getElementById("arquivo") as HTMLInputElement;
      if (input) input.value = "";
    } catch (err) {
      setMessage({
        type: "error",
        text: err instanceof Error ? err.message : "Erro ao importar",
      });
    } finally {
      setLoading(false);
    }
  }

  if (!canImport) {
    return (
      <div>
        <p className="text-gray-600">Apenas gestores com cliente associado podem importar usuários.</p>
        <Link href="/dashboard/usuarios" className="text-primary mt-2 inline-block">
          ← Voltar
        </Link>
      </div>
    );
  }

  return (
    <div className="max-w-2xl">
      <div className="flex items-center gap-4 mb-6">
        <Link href="/dashboard/usuarios" className="text-gray-600 hover:text-emotive-gray-header">
          ← Voltar
        </Link>
        <h1 className="text-2xl font-bold text-emotive-gray-header">Importação de Usuários em Lote</h1>
      </div>
      <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
        {message && (
          <div
            className={`px-6 py-3 ${message.type === "success" ? "bg-green-50 text-green-800" : "bg-red-50 text-red-800"}`}
          >
            {message.text}
          </div>
        )}
        <form onSubmit={handleSubmit} className="p-6">
          <div className="rounded-lg bg-emotive-panel-bg border border-gray-200 p-4 mb-6">
            <h2 className="font-semibold text-emotive-gray-header mb-2">Instruções</h2>
            <p className="text-sm text-gray-600 mb-2">
              Envie um arquivo <strong>CSV</strong> com as colunas:
            </p>
            <ul className="list-disc list-inside text-sm text-gray-600 mb-2">
              <li><strong>email</strong>: endereço de e-mail válido</li>
              <li><strong>nome</strong>: nome completo</li>
            </ul>
            <p className="text-sm text-gray-600 mb-2">A primeira linha deve ser o cabeçalho. Exemplo:</p>
            <pre className="bg-white p-2 border rounded text-left text-xs overflow-x-auto">
              email,nome{"\n"}joao@empresa.com,João Silva{"\n"}maria@empresa.com,Maria Souza
            </pre>
            <p className="text-sm text-gray-600 mt-2">
              Os usuários importados receberão uma senha padrão e farão parte do seu cliente.
            </p>
          </div>
          <div className="mb-4">
            <label htmlFor="arquivo" className="block text-sm font-medium text-gray-700 mb-1">
              Arquivo CSV
            </label>
            <input
              id="arquivo"
              type="file"
              accept=".csv,.txt"
              required
              onChange={(e) => setFile(e.target.files?.[0] ?? null)}
              className="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-primary file:text-white"
            />
          </div>
          <button
            type="submit"
            disabled={loading || !file}
            className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 disabled:opacity-50"
          >
            {loading ? "Importando…" : "Importar lote"}
          </button>
        </form>
      </div>
    </div>
  );
}
