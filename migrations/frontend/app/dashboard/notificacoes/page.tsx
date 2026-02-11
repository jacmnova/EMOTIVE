"use client";

import { useState, useEffect } from "react";
import { api } from "@/lib/api";

interface NotificacaoItem {
  id: string;
  titulo: string;
  mensagem: string;
  lida: boolean;
  data: string;
}

const STORAGE_KEY = "emotive_notificacoes";

export default function NotificacoesPage() {
  const [items, setItems] = useState<NotificacaoItem[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    try {
      const raw = typeof window !== "undefined" ? localStorage.getItem(STORAGE_KEY) : null;
      const list = raw ? (JSON.parse(raw) as NotificacaoItem[]) : [];
      setItems(Array.isArray(list) ? list : []);
    } catch {
      setItems([]);
    } finally {
      setLoading(false);
    }
  }, []);

  function marcarComoLida(id: string) {
    setItems((prev) => {
      const next = prev.map((n) => (n.id === id ? { ...n, lida: true } : n));
      if (typeof window !== "undefined") localStorage.setItem(STORAGE_KEY, JSON.stringify(next));
      return next;
    });
  }

  async function marcarTodasComoLidas() {
    const next = items.map((n) => ({ ...n, lida: true }));
    setItems(next);
    if (typeof window !== "undefined") localStorage.setItem(STORAGE_KEY, JSON.stringify(next));
    try {
      await api<{ message: string }>("/notificacoes/marcar-todas", { method: "POST" });
    } catch {
      // endpoint puede no existir aún; la UI ya actualizó
    }
  }

  function limparTodas() {
    setItems([]);
    if (typeof window !== "undefined") localStorage.removeItem(STORAGE_KEY);
  }

  if (loading) return <p className="text-gray-500">Carregando…</p>;

  return (
    <div className="max-w-2xl">
      <h1 className="text-2xl font-bold text-emotive-gray-header mb-6">Notificações</h1>
      {items.length === 0 ? (
        <div className="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-500">
          Nenhuma notificação no momento.
        </div>
      ) : (
        <>
          <div className="mb-4 flex justify-end gap-3">
            {items.some((n) => !n.lida) && (
              <button type="button" onClick={marcarTodasComoLidas} className="text-sm text-primary hover:underline">
                Marcar todas como lidas
              </button>
            )}
            <button type="button" onClick={limparTodas} className="text-sm text-gray-600 hover:text-red-600">
              Limpar todas
            </button>
          </div>
          <ul className="space-y-3">
            {items.map((n) => (
              <li
                key={n.id}
                className={`bg-white rounded-xl border border-gray-200 p-4 ${n.lida ? "opacity-75" : ""}`}
              >
                <div className="flex items-start justify-between gap-4">
                  <div>
                    <p className="font-medium text-emotive-gray-header">{n.titulo}</p>
                    <p className="text-sm text-gray-600 mt-1">{n.mensagem}</p>
                    <p className="text-xs text-slate-400 mt-2">{n.data}</p>
                  </div>
                  {!n.lida && (
                    <button
                      type="button"
                      onClick={() => marcarComoLida(n.id)}
                      className="text-primary text-sm font-medium hover:underline shrink-0"
                    >
                      Marcar como lida
                    </button>
                  )}
                </div>
              </li>
            ))}
          </ul>
        </>
      )}
      <p className="mt-6 text-sm text-gray-500">
        As notificações do sistema (ex.: confirmação de e-mail, recuperação de senha) são enviadas por e-mail. Aqui você pode ver avisos locais, se houver.
      </p>
    </div>
  );
}
