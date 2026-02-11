"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import Image from "next/image";
import { usePathname, useRouter } from "next/navigation";
import { getStoredUser, clearAuth, setAuth, isAuthenticated } from "@/lib/auth";
import { api } from "@/lib/api";
import type { TokenResponse } from "@/types";

const iconClass = "w-5 h-5 flex-shrink-0";

const icons = {
  dashboard: (
    <svg className={iconClass} fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
    </svg>
  ),
  questionarios: (
    <svg className={iconClass} fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
    </svg>
  ),
  chat: (
    <svg className={iconClass} fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
    </svg>
  ),
  usuarios: (
    <svg className={iconClass} fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
    </svg>
  ),
  clientes: (
    <svg className={iconClass} fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
    </svg>
  ),
  formularios: (
    <svg className={iconClass} fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
    </svg>
  ),
  calculos: (
    <svg className={iconClass} fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
    </svg>
  ),
  midias: (
    <svg className={iconClass} fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
    </svg>
  ),
  perfil: (
    <svg className={iconClass} fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
    </svg>
  ),
  notificacoes: (
    <svg className={iconClass} fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
    </svg>
  ),
  periodos: (
    <svg className={iconClass} fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
    </svg>
  ),
  relatorio: (
    <svg className={iconClass} fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
    </svg>
  ),
  projetos: (
    <svg className={iconClass} fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012 2h6a2 2 0 012 2v2M7 7h10" />
    </svg>
  ),
  atribuicao: (
    <svg className={iconClass} fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
    </svg>
  ),
  grupos: (
    <svg className={iconClass} fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012 2h6a2 2 0 012 2v2M7 7h10" />
    </svg>
  ),
};

export default function DashboardLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const router = useRouter();
  const pathname = usePathname();
  const [user, setUser] = useState<ReturnType<typeof getStoredUser>>(null);
  const [mounted, setMounted] = useState(false);
  const [showUserMenu, setShowUserMenu] = useState(false);
  const [showSearch, setShowSearch] = useState(false);

  useEffect(() => {
    setMounted(true);
    if (!isAuthenticated()) {
      router.replace("/auth/login");
      return;
    }
    setUser(getStoredUser());
  }, [router]);

  function handleLogout() {
    clearAuth();
    router.replace("/auth/login");
    router.refresh();
  }

  function toggleFullscreen() {
    if (!document.fullscreenElement) {
      document.documentElement.requestFullscreen?.();
    } else {
      document.exitFullscreen?.();
    }
  }

  async function handleStopImpersonate() {
    try {
      const data = await api<TokenResponse>("/impersonate/stop", { method: "POST" });
      setAuth(data);
      setUser(data.user);
      router.refresh();
    } catch {
      router.refresh();
    }
  }

  if (!mounted) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-emotive-panel-bg">
        <p className="text-emotive-gray-header">Carregando…</p>
      </div>
    );
  }

  if (!user) {
    return null;
  }

  // Menú: Colaborador → Gestão → Configuração (admin) → Conta
  const nav: { href: string; label: string; icon: keyof typeof icons }[] = [
    { href: "/dashboard", label: "Início", icon: "dashboard" },
    { href: "/dashboard/meus-questionarios", label: "Meus questionários", icon: "questionarios" },
  ];
  if (user.admin || user.sa || user.gestor) {
    nav.push(
      { href: "/dashboard/usuarios", label: "Usuários", icon: "usuarios" },
      { href: "/dashboard/periodos", label: "Períodos / Ondas", icon: "periodos" },
      { href: "/dashboard/asignar-formulario", label: "Atribuir formulário (1 usuário)", icon: "atribuicao" },
      { href: "/dashboard/atribuicao-em-massa", label: "Atribuição em massa", icon: "atribuicao" },
      { href: "/dashboard/grupos", label: "Grupos", icon: "grupos" },
      { href: "/dashboard/relatorio-corporativo", label: "Relatório corporativo (BI)", icon: "relatorio" },
      { href: "/dashboard/evolucao", label: "Comparar períodos", icon: "relatorio" },
      { href: "/dashboard/respondentes", label: "Monitor de respondentes", icon: "usuarios" },
      { href: "/dashboard/projetos", label: "Projetos", icon: "projetos" }
    );
  }
  if (user.admin || user.sa) {
    nav.push(
      { href: "/dashboard/clientes", label: "Clientes", icon: "clientes" },
      { href: "/dashboard/formularios", label: "Formulários", icon: "formularios" }
    );
  }
  nav.push(
    { href: "/dashboard/tutorial-admin", label: "Como usar (tutorial)", icon: "questionarios" },
    { href: "/dashboard/perfil", label: "Perfil", icon: "perfil" },
    { href: "/dashboard/notificacoes", label: "Notificações", icon: "notificacoes" }
  );

  return (
    <div className="min-h-screen flex bg-white">
      {/* Sidebar – con iconos referencia imagen 2 */}
      <aside className="w-56 flex-shrink-0 bg-emotive-panel-bg border-r border-gray-200 flex flex-col">
        <div className="p-4 border-b border-gray-200">
          <Link href="/dashboard" className="block">
            <div className="flex items-center gap-2">
              <Image
                src="/logo-emotive.png"
                alt="E.MO.TI.VE"
                width={120}
                height={40}
                className="h-9 w-auto object-contain"
                priority
              />
            </div>
          </Link>
        </div>
        <nav className="flex-1 py-4 px-2 space-y-0.5 overflow-y-auto">
          {nav.map((item) => {
            const isActive = pathname === item.href || (item.href !== "/dashboard" && pathname.startsWith(item.href));
            return (
              <Link
                key={item.href}
                href={item.href}
                className={
                  isActive
                    ? "sidebar-link-active flex items-center gap-3 px-3 py-2.5 rounded-r-lg"
                    : "flex items-center gap-3 px-3 py-2.5 rounded-r-lg text-gray-600 hover:bg-gray-200/80 hover:text-emotive-gray-header"
                }
              >
                {icons[item.icon]}
                <span>{item.label}</span>
              </Link>
            );
          })}
        </nav>
      </aside>

      <div className="flex-1 flex flex-col min-w-0">
        {/* Header – búsqueda, fullscreen, notificaciones, perfil con avatar e iconos (imagen 1) */}
        <header className="h-14 flex-shrink-0 bg-white border-b border-gray-200 flex items-center justify-between px-6 gap-4">
          <div className="flex items-center gap-3 flex-1 max-w-xl">
            <button
              type="button"
              onClick={() => setShowSearch((s) => !s)}
              className="p-2 text-gray-500 hover:text-emotive-gray-header rounded-lg hover:bg-gray-100"
              title="Pesquisar"
            >
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </button>
            {showSearch && (
              <input
                type="search"
                placeholder="Pesquisar..."
                className="flex-1 min-w-0 px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                autoFocus
                onBlur={() => setShowSearch(false)}
              />
            )}
          </div>
          <div className="flex items-center gap-2">
            <button
              type="button"
              onClick={toggleFullscreen}
              className="p-2 text-gray-500 hover:text-emotive-gray-header rounded-lg hover:bg-gray-100"
              title="Ecrã inteiro"
            >
              <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z" />
              </svg>
            </button>
            <Link
              href="/dashboard/notificacoes"
              className="p-2 text-gray-500 hover:text-primary rounded-lg hover:bg-gray-100 relative"
              title="Notificações"
            >
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
            </Link>
            <div className="relative">
              <button
                type="button"
                onClick={() => setShowUserMenu((m) => !m)}
                className="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-lg hover:bg-gray-100"
              >
                <span className="flex h-9 w-9 items-center justify-center rounded-full bg-primary/10 text-primary shadow-sm">
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </span>
                <span className="text-sm font-medium text-gray-700 max-w-[140px] truncate">{user.name}</span>
              </button>
              {showUserMenu && (
                <>
                  <div className="fixed inset-0 z-10" aria-hidden onClick={() => setShowUserMenu(false)} />
                  <div className="absolute right-0 top-full mt-1 py-1 w-48 bg-white rounded-lg border border-gray-200 shadow-lg z-20">
                    <Link
                      href="/dashboard/perfil"
                      className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                      onClick={() => setShowUserMenu(false)}
                    >
                      Perfil
                    </Link>
                    <button
                      type="button"
                      onClick={() => { setShowUserMenu(false); handleLogout(); }}
                      className="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                    >
                      Sair
                    </button>
                  </div>
                </>
              )}
            </div>
          </div>
        </header>

        {user?.impersonated_by != null && (
          <div className="bg-amber-50 border-b border-amber-200 px-6 py-2 flex items-center justify-between">
            <span className="text-amber-800 text-sm">
              Você está personificando <strong>{user.name}</strong> ({user.email}).
            </span>
            <button
              type="button"
              onClick={handleStopImpersonate}
              className="text-amber-700 font-medium text-sm hover:underline"
            >
              Sair da personificação
            </button>
          </div>
        )}

        <main className="flex-1 overflow-auto p-6 bg-white">{children}</main>

        {/* Footer – estilo prototipo */}
        <footer className="flex-shrink-0 bg-emotive-gray-footer text-white text-sm py-3 px-6 flex items-center justify-between">
          <span>Todos os direitos reservados a Fellipelli Consultoria.</span>
          <Link href="/faqs" className="text-gray-300 hover:text-white flex items-center gap-1">
            Preciso de ajuda.
          </Link>
        </footer>
      </div>
    </div>
  );
}
