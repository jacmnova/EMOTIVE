"use client";

import { useEffect, useState } from "react";
import { useRouter, useSearchParams } from "next/navigation";
import Link from "next/link";
import { api } from "@/lib/api";
import { isAuthenticated } from "@/lib/auth";

export default function ResponderPage() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const token = searchParams.get("token");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!token || !token.trim()) {
      router.replace("/dashboard");
      return;
    }

    let cancelled = false;

    async function resolve() {
      try {
        const data = await api<{ usuario_formulario_id: number }>(
          `auth/acesso-questionario?token=${encodeURIComponent(token!)}`
        );
        if (cancelled) return;
        if (isAuthenticated()) {
          router.replace(`/dashboard/questionarios/${data.usuario_formulario_id}`);
          return;
        }
        const redirect = encodeURIComponent(`/responder?token=${token}`);
        router.replace(`/auth/login?redirect=${redirect}`);
      } catch (e) {
        if (cancelled) return;
        setError(e instanceof Error ? e.message : "Link inválido ou expirado.");
      } finally {
        if (!cancelled) setLoading(false);
      }
    }

    resolve();
    return () => { cancelled = true; };
  }, [token, router]);

  if (loading && !error) {
    return (
      <main className="min-h-screen flex flex-col items-center justify-center p-4 bg-emotive-panel-bg">
        <div className="text-emotive-gray-header">Abrindo questionário…</div>
      </main>
    );
  }

  if (error) {
    return (
      <main className="min-h-screen flex flex-col items-center justify-center p-4 bg-emotive-panel-bg">
        <div className="max-w-md w-full bg-white rounded-xl border border-gray-200 p-6 text-center">
          <h1 className="text-xl font-semibold text-emotive-gray-header mb-2">Link inválido ou expirado</h1>
          <p className="text-gray-600 text-sm mb-4">{error}</p>
          <Link
            href="/auth/login"
            className="inline-block px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark"
          >
            Ir para o login
          </Link>
        </div>
      </main>
    );
  }

  return null;
}
