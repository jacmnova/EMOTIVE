import Link from "next/link";
import Image from "next/image";

export default function HomePage() {
  return (
    <main className="min-h-screen flex flex-col bg-white">
      <header className="border-b border-gray-200">
        <div className="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
          <Link href="/" className="flex items-center gap-2">
            <Image
              src="/logo-emotive.png"
              alt="E.MO.TI.VE"
              width={140}
              height={44}
              className="h-10 w-auto object-contain"
              priority
            />
            
          </Link>
          <nav className="flex items-center gap-6">
            <Link href="/termos" className="text-sm text-gray-600 hover:text-primary">Termos</Link>
            <Link href="/tutorial" className="text-sm text-gray-600 hover:text-primary">Tutorial</Link>
            <Link href="/faqs" className="text-sm text-gray-600 hover:text-primary">FAQ</Link>
            <Link href="/auth/login" className="text-sm font-medium text-primary hover:text-primary-dark">Entrar</Link>
            <Link href="/auth/register" className="btn-primary text-sm">Registrar</Link>
          </nav>
        </div>
      </header>
      <div className="flex-1 flex flex-col items-center justify-center p-8">
        <div className="text-center max-w-2xl">
          <h1 className="text-4xl font-bold text-emotive-gray-header mb-4">E.MO.TI.VE</h1>
          <p className="text-gray-600 mb-8">
            Sistema de avaliação e relatórios. Inicie sessão ou registre-se para continuar.
          </p>
          <div className="flex gap-4 justify-center flex-wrap">
            <Link
              href="/auth/login"
              className="btn-primary px-6 py-3 rounded-lg"
            >
              Iniciar sessão
            </Link>
            <Link
              href="/auth/register"
              className="px-6 py-3 border-2 border-primary text-primary rounded-lg font-medium hover:bg-primary/5 transition"
            >
              Registrarse
            </Link>
          </div>
          <div className="mt-8 flex gap-6 justify-center flex-wrap text-sm">
            <Link href="/termos" className="text-primary hover:underline">Termos de uso</Link>
            <Link href="/tutorial" className="text-primary hover:underline">Tutorial</Link>
            <Link href="/faqs" className="text-primary hover:underline">FAQ</Link>
          </div>
        </div>
      </div>
      <footer className="bg-emotive-gray-footer text-white text-sm py-3 px-6">
        <div className="max-w-6xl mx-auto flex items-center justify-between">
          <span>Todos os direitos reservados a Fellipelli Consultoria.</span>
          <Link href="/faqs" className="text-gray-300 hover:text-white">Preciso de ajuda.</Link>
        </div>
      </footer>
    </main>
  );
}
