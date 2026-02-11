"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { useRouter, useSearchParams } from "next/navigation";
import { api } from "@/lib/api";
import { getStoredUser, setAuth } from "@/lib/auth";
import type { User, Periodo } from "@/types";
import type { TokenResponse } from "@/types";

const API_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000";
const API_V1 = `${API_URL}/api/v1`;

export default function UsuariosPage() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const periodoId = searchParams.get("periodo_id") || "";
  const user = getStoredUser();
  const [list, setList] = useState<User[]>([]);
  const [periodoNome, setPeriodoNome] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [impersonatingId, setImpersonatingId] = useState<number | null>(null);
  const canCreate = user?.admin || user?.sa;
  const canImpersonate = (user?.admin || user?.sa || user?.gestor) && !user?.impersonated_by;

  useEffect(() => {
    const params = new URLSearchParams();
    if (periodoId) params.set("periodo_id", periodoId);
    const q = params.toString();
    api<User[]>(`/users${q ? `?${q}` : ""}`)
      .then(setList)
      .catch((e) => setError(e instanceof Error ? e.message : "Error"))
      .finally(() => setLoading(false));
  }, [periodoId]);

  useEffect(() => {
    if (periodoId) {
      api<Periodo[]>("/periodos")
        .then((periodos) => {
          const p = periodos.find((x) => String(x.id) === periodoId);
          setPeriodoNome(p?.nome ?? null);
        })
        .catch(() => setPeriodoNome(null));
    } else {
      setPeriodoNome(null);
    }
  }, [periodoId]);

  if (loading) return <p className="text-gray-500">Carregando…</p>;
  if (error) return <p className="text-red-600">{error}</p>;

  return (
    <div>
      <div className="flex items-center justify-between mb-6 flex-wrap gap-2">
        <div>
          <h1 className="text-2xl font-bold text-emotive-gray-header">Usuários</h1>
          {periodoId && (
            <p className="text-sm text-gray-600 mt-0.5">
              Com atribuição no período: <strong>{periodoNome || `#${periodoId}`}</strong>
              {" · "}
              <Link href="/dashboard/usuarios" className="text-primary hover:underline">Ver todos</Link>
            </p>
          )}
        </div>
        <div className="flex gap-2">
          {user?.gestor && user?.cliente_id && (
            <Link
              href="/dashboard/usuarios/importar"
              className="px-4 py-2 bg-emotive-panel-bg text-gray-700 rounded-lg font-medium hover:bg-gray-200"
            >
              Importar CSV
            </Link>
          )}
          {canCreate && (
            <Link
              href="/dashboard/usuarios/new"
              className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark"
            >
              Novo usuário
            </Link>
          )}
        </div>
      </div>
      <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table className="w-full text-left">
          <thead className="bg-emotive-panel-bg border-b border-gray-200">
            <tr>
              <th className="px-4 py-3 font-medium text-emotive-gray-header">Nome</th>
              <th className="px-4 py-3 font-medium text-emotive-gray-header">Email</th>
              <th className="px-4 py-3 font-medium text-emotive-gray-header">Rol</th>
              <th className="px-4 py-3 font-medium text-emotive-gray-header">Estado</th>
              <th className="px-4 py-3 font-medium text-emotive-gray-header w-24">Ações</th>
            </tr>
          </thead>
          <tbody>
            {list.map((u) => (
              <tr key={u.id} className="border-b border-gray-100 last:border-0">
                <td className="px-4 py-3">{u.name}</td>
                <td className="px-4 py-3">{u.email}</td>
                <td className="px-4 py-3 text-sm">
                  {u.sa && "SA "}
                  {u.admin && "Admin "}
                  {u.gestor && "Gestor "}
                  {u.usuario && "Usuário"}
                </td>
                <td className="px-4 py-3">{u.ativo ? "Ativo" : "Inativo"}</td>
                <td className="px-4 py-3 flex items-center gap-3">
                  {(canCreate || user?.gestor) && (
                    <Link href={`/dashboard/usuarios/${u.id}/edit`} className="text-primary text-sm hover:underline">
                      Editar
                    </Link>
                  )}
                  {canImpersonate && u.id !== user?.id && (
                    <button
                      type="button"
                      disabled={impersonatingId === u.id}
                      onClick={async () => {
                        setImpersonatingId(u.id);
                        try {
                          const token = typeof window !== "undefined" ? localStorage.getItem("access_token") : null;
                          const res = await fetch(`${API_V1}/impersonate/start/${u.id}`, {
                            method: "POST",
                            headers: { Authorization: `Bearer ${token}` },
                          });
                          if (!res.ok) throw new Error("Erro ao personificar");
                          const data = await res.json();
                          const payload = data as TokenResponse & { impersonated_by?: number };
                          setAuth({
                            access_token: payload.access_token,
                            token_type: payload.token_type || "bearer",
                            user: { ...payload.user, impersonated_by: payload.impersonated_by ?? undefined },
                          });
                          router.push("/dashboard");
                          router.refresh();
                        } catch (e) {
                          setError(e instanceof Error ? e.message : "Erro");
                        } finally {
                          setImpersonatingId(null);
                        }
                      }}
                      className="text-gray-600 text-sm hover:underline disabled:opacity-50"
                    >
                      {impersonatingId === u.id ? "…" : "Personificar"}
                    </button>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
