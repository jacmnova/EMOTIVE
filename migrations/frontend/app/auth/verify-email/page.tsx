"use client";

import { useState, useEffect, Suspense } from "react";
import Link from "next/link";
import { useSearchParams } from "next/navigation";
import { verifyEmail } from "@/lib/auth";

function VerifyEmailContent() {
  const searchParams = useSearchParams();
  const token = searchParams.get("token") ?? "";
  const [status, setStatus] = useState<"loading" | "ok" | "error">("loading");
  const [message, setMessage] = useState("");

  useEffect(() => {
    if (!token) {
      setStatus("error");
      setMessage("Falta el token de verificación. Usa el enlace del email.");
      return;
    }
    verifyEmail(token)
      .then(() => {
        setStatus("ok");
        setMessage("Email verificado correctamente. Ya puedes iniciar sesión.");
      })
      .catch(() => {
        setStatus("error");
        setMessage("Token inválido o expirado.");
      });
  }, [token]);

  return (
    <main className="min-h-screen flex items-center justify-center p-4 bg-slate-50">
      <div className="w-full max-w-md bg-white rounded-xl shadow-lg p-8 text-center">
        {status === "loading" && <p className="text-slate-600">Verificando email…</p>}
        {status === "ok" && (
          <>
            <h1 className="text-2xl font-bold text-green-700 mb-4">¡Listo!</h1>
            <p className="text-slate-600 mb-6">{message}</p>
            <Link
              href="/auth/login"
              className="inline-block px-6 py-3 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark"
            >
              Iniciar sesión
            </Link>
          </>
        )}
        {status === "error" && (
          <>
            <h1 className="text-2xl font-bold text-red-600 mb-4">Error</h1>
            <p className="text-slate-600 mb-6">{message}</p>
            <Link href="/auth/login" className="text-primary hover:underline">
              Volver al login
            </Link>
          </>
        )}
      </div>
    </main>
  );
}

export default function VerifyEmailPage() {
  return (
    <Suspense fallback={<div className="min-h-screen flex items-center justify-center">Cargando…</div>}>
      <VerifyEmailContent />
    </Suspense>
  );
}
