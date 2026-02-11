"use client";

import { getStoredUser } from "@/lib/auth";
import Link from "next/link";

export default function DashboardPage() {
  const user = getStoredUser();
  if (!user) return null;

  return (
    <div>
      <h1 className="text-2xl font-bold text-emotive-gray-header mb-2">Bem-vindo, {user.name}</h1>
      <p className="text-gray-600 mb-8">Use o menu à esquerda para navegar.</p>

      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <Link
          href="/dashboard/meus-questionarios"
          className="block p-6 bg-white rounded-xl border border-gray-200 hover:border-primary hover:shadow-md transition"
        >
          <h2 className="font-semibold text-primary mb-1">Meus questionários</h2>
          <p className="text-sm text-gray-600">Ver e responder questionários asignados.</p>
        </Link>
        {(user.admin || user.sa || user.gestor) && (
          <Link
            href="/dashboard/usuarios"
            className="block p-6 bg-white rounded-xl border border-gray-200 hover:border-primary hover:shadow-md transition"
          >
            <h2 className="font-semibold text-primary mb-1">Usuários</h2>
            <p className="text-sm text-gray-600">{user.gestor && !user.admin ? "Ver usuários do seu cliente." : "Gerenciar usuários do sistema."}</p>
          </Link>
        )}
        {(user.admin || user.sa) && (
          <>
            <Link
              href="/dashboard/clientes"
              className="block p-6 bg-white rounded-xl border border-gray-200 hover:border-primary hover:shadow-md transition"
            >
              <h2 className="font-semibold text-primary mb-1">Clientes</h2>
              <p className="text-sm text-gray-600">Gerenciar clientes.</p>
            </Link>
            <Link
              href="/dashboard/formularios"
              className="block p-6 bg-white rounded-xl border border-gray-200 hover:border-primary hover:shadow-md transition"
            >
              <h2 className="font-semibold text-primary mb-1">Formulários</h2>
              <p className="text-sm text-gray-600">Gerenciar formulários e perguntas.</p>
            </Link>
          </>
        )}
      </div>
    </div>
  );
}
