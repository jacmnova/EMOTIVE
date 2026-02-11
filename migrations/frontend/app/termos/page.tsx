import Link from "next/link";

export const metadata = {
  title: "Termos de Uso | E.MO.TI.VE",
  description: "Termos de uso da plataforma E.MO.TI.VE",
};

export default function TermosPage() {
  return (
    <main className="min-h-screen flex flex-col bg-white">
      <header className="border-b border-gray-200">
        <div className="max-w-6xl mx-auto px-4 py-4">
          <Link href="/" className="inline-flex items-center gap-2">
            <img src="/logo-emotive.png" alt="E.MO.TI.VE" className="h-10 w-auto object-contain" />
            
          </Link>
        </div>
      </header>
      <div className="flex-1 p-6 md:p-10 bg-emotive-panel-bg">
        <div className="max-w-2xl mx-auto bg-white rounded-lg border border-gray-200 p-6 md:p-8">
          <h1 className="text-xl font-bold text-emotive-gray-header mb-4">Termos de Uso da Plataforma</h1>
          <ol className="list-decimal pl-5 space-y-2 text-sm leading-relaxed text-gray-700">
            <li>O acesso à plataforma é restrito aos usuários autorizados pela Fellipelli ou instituições parceiras.</li>
            <li>Os dados coletados são de responsabilidade da instituição usuária, conforme a LGPD.</li>
            <li>É proibida a reprodução parcial ou total do conteúdo, sem prévia autorização da Fellipelli.</li>
            <li>O uso inadequado poderá acarretar na suspensão do acesso.</li>
            <li>Ao utilizar a plataforma, o usuário concorda integralmente com estes termos.</li>
          </ol>
          <div className="mt-6">
            <Link href="/" className="text-sm text-primary hover:underline">← Voltar ao início</Link>
          </div>
        </div>
      </div>
      <footer className="bg-emotive-gray-footer text-white text-sm py-3 px-6">
        <div className="max-w-6xl mx-auto flex justify-between">
          <span>Todos os direitos reservados a Fellipelli Consultoria.</span>
          <Link href="/faqs" className="text-gray-300 hover:text-white">Preciso de ajuda.</Link>
        </div>
      </footer>
    </main>
  );
}
