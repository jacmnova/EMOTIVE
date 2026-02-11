import Link from "next/link";

export const metadata = {
  title: "Tutoriais | E.MO.TI.VE",
  description: "Tutoriais do sistema E.MO.TI.VE",
};

export default function TutorialPage() {
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
          <h1 className="text-2xl font-semibold text-emotive-gray-header mb-4">Tutoriais do Sistema</h1>
          <div className="text-sm text-gray-600 space-y-4">
            <p>
              Assista abaixo aos tutoriais de uso do sistema e entenda como aplicar corretamente os instrumentos de avaliação psicossocial.
            </p>
            <ul className="list-disc ml-5 space-y-2">
              <li><strong>Cadastro de usuários:</strong> vídeo explicativo sobre como registrar novos participantes.</li>
              <li><strong>Atribuição de formulários:</strong> como vincular o QRP-36 aos usuários.</li>
              <li><strong>Acompanhamento de resultados:</strong> onde e como visualizar os diagnósticos.</li>
              <li><strong>Geração de relatórios:</strong> orientações para exportar relatórios em PDF.</li>
            </ul>
            <p>Caso tenha dúvidas adicionais, entre em contato com o suporte Fellipelli.</p>
          </div>
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
