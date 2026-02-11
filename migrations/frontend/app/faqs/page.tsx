import Link from "next/link";
import FormularioContato from "./FormularioContato";

export const metadata = {
  title: "FAQ - Dúvidas NR-1 | E.MO.TI.VE",
  description: "Perguntas frequentes sobre NR-1 e E.MO.TI.VE",
};

const faqItems = [
  {
    title: "O que é a NR-1?",
    content: "É a norma que define os princípios e diretrizes do Gerenciamento de Riscos Ocupacionais (GRO) e do Programa de Gerenciamento de Riscos (PGR), além de tratar da capacitação em Saúde e Segurança Trabalhista (SST) e das responsabilidades de empregadores e empregados.",
  },
  {
    title: "Quem precisa cumprir a NR-1?",
    content: "Todas as empresas que contratam trabalhadores sob o regime da CLT, independentemente do porte ou setor. Pequenas empresas podem ter algumas flexibilizações, dependendo do grau de risco.",
  },
  {
    title: "Quais são as principais mudanças em 2025?",
    content: "A nova versão reforça a gestão de riscos psicossociais, como estresse e ansiedade, e exige que as empresas adotem medidas preventivas.",
  },
  {
    title: "Empresas pequenas também precisam se adequar?",
    content: "Sim, com poucas exceções, como MEIs, MEs e EPPs com grau de risco 1 ou 2 e sem exposição a agentes nocivos.",
  },
  {
    title: "Quando entram em vigor as mudanças?",
    content: "As alterações promovidas pela Portaria MTE nº 1.419 entraram em vigor em 26 de maio de 2025.",
  },
  {
    title: "Quais são os riscos se minha empresa não se adequar à NR-1?",
    content: "O descumprimento da NR-1 gera sérias consequências para as empresas em vários âmbitos: legal, financeiro, comercial e psicológico. Dentre eles destacam-se: Multas e sanções administrativas - A fiscalização do trabalho pode aplicar multas que variam conforme a gravidade da infração e o porte da empresa. Em casos mais graves, pode haver interdição de atividades. Ações judiciais e indenizações - Se for comprovado que o descumprimento da NR-1 contribuiu para o adoecimento físico ou mental de um trabalhador, a empresa pode ser responsabilizada judicialmente e obrigada a pagar indenizações por danos morais, materiais ou até pensão vitalícia. Danos à imagem e clima organizacional - Ambientes de trabalho inseguros ou negligentes afetam a confiança dos colaboradores, elevam a rotatividade e prejudicam a performance.",
  },
  {
    title: "O que são riscos psicossociais?",
    content: "Os riscos psicossociais, segundo a nova redação da NR-1, são fatores do ambiente de trabalho que podem afetar negativamente a saúde mental e emocional dos trabalhadores. Eles incluem situações como: Sobrecarga de trabalho, Assédio moral ou sexual, Ambiente tóxico, Metas abusivas ou inatingíveis, Isolamento social, Falta de suporte da liderança, Pressão constante por resultados e hiperconectividade. Esses riscos passaram a ser formalmente reconhecidos como parte do Programa de Gerenciamento de Riscos (PGR), e sua avaliação será obrigatória a partir de maio de 2026.",
  },
  {
    title: "O que é um Programa de Gerenciamento de Riscos (PGR)?",
    content: "O PGR funciona como um raio-X corporativo, iniciando com um levantamento de riscos (\"prazos apertados estão pressionando o time\", por exemplo), passando por uma avaliação da sua gravidade e probabilidade (\"elevado risco de burnout\"), e, por fim, implementando um plano de ação (ex.: Perfil de Resiliência ao Estresse e revisão do plano de metas). Trata-se de um documento vivo, que deve ser continuamente revisado e que atesta que a empresa está cuidando da segurança e saúde dos colaboradores – inclusive no campo mental.",
  },
  {
    title: "É possível blindar minha empresa de todos esses problemas e garantir a conformidade à NR-1?",
    content: "SIM! Não arrisque a saúde emocional, financeira e jurídica da sua empresa. Conte com toda a expertise da FELLIPELLI para assegurar a conformidade à NR-1 da sua organização: caminhamos lado a lado com o seu time por toda essa trilha, desde o diagnóstico, passando pelo treinamento e até a elaboração de um Programa de Gerenciamento de Riscos (PGR) sólido e seguro. Invista no internacionalmente reconhecido poder da FELLIPELLI de promover saúde mental e proteger sua empresa.",
  },
];

export default function FaqsPage() {
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
        <div className="max-w-3xl mx-auto">
          <div className="rounded-t-lg bg-primary text-white px-6 py-4">
            <h1 className="text-lg font-semibold">FAQ - Dúvidas NR-1</h1>
          </div>
          <div className="bg-white rounded-b-lg shadow border border-t-0 border-gray-200 p-6 md:p-8">
            {faqItems.map((item, i) => (
              <div key={i} className="border-b border-gray-200 pb-6 mb-6 last:border-0 last:mb-4">
                <h2 className="text-primary font-bold text-lg mb-2">{item.title}</h2>
                <p className="text-gray-600 text-sm leading-relaxed">{item.content}</p>
              </div>
            ))}
            <div className="text-center mt-8 mb-4">
              <FormularioContato />
            </div>
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
