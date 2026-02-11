"use client";

import Link from "next/link";

export default function TutorialAdminPage() {
  return (
    <div className="max-w-3xl mx-auto pb-12">
      <Link href="/dashboard" className="text-gray-600 hover:text-primary text-sm">← Início</Link>
      <h1 className="text-2xl font-bold text-emotive-gray-header mt-2 mb-6">Como usar a aplicação (tutorial para gestores e administradores)</h1>

      <div className="space-y-8 text-gray-700">
        <section>
          <h2 className="text-lg font-semibold text-emotive-gray-header mb-2">1. O que é cada secção do menú</h2>
          <ul className="list-disc pl-5 space-y-1 text-sm">
            <li><strong>Início</strong> — Resumo e acesso rápido.</li>
            <li><strong>Meus questionários</strong> — Para o colaborador: ver e responder os questionários atribuídos.</li>
            <li><strong>Usuários</strong> — Criar, editar e importar usuários; ver questionários asignados a cada um.</li>
            <li><strong>Períodos / Ondas</strong> — Criar ondas de aplicação (ex.: Onda 1, Trimestre 1). Cada atribuição pode estar ligada a um período.</li>
            <li><strong>Atribuir formulário (1 usuário)</strong> — Atribuir um formulário a um único usuário (período e data limite opcionais; opção de enviar e-mail de convite).</li>
            <li><strong>Atribuição em massa</strong> — Atribuir o mesmo formulário a muitos usuários de uma vez, por filtros (Unidade, Área, etc.) ou por <strong>Grupo</strong>.</li>
            <li><strong>Grupos</strong> — Criar “populações nomeadas” com filtros (Unidade, Área, Nível, Tempo de empresa, Modelo de trabalho). Depois, em Atribuição em massa, escolha um grupo para atribuir a todos que cumprem esses critérios.</li>
            <li><strong>Relatório corporativo</strong> — KPIs, setores críticos, gráficos e evolução por período; filtros por Unidade, Área, etc.</li>
            <li><strong>Monitor de respondentes</strong> — Ver quem foi atribuído a um Período + Formulário e se está pendente ou completo; enviar recordatório por e-mail. Pode ver <strong>Individual</strong> (todos) ou <strong>Por grupo</strong> (só os usuários que cumprem os filtros do grupo escolhido).</li>
            <li><strong>Projetos</strong> — Agrupar períodos (opcional).</li>
            <li><strong>Clientes</strong> e <strong>Formulários</strong> — Só Admin/SA; gerir clientes e formulários disponíveis.</li>
          </ul>
        </section>

        <section>
          <h2 className="text-lg font-semibold text-emotive-gray-header mb-2">2. Como atribuir questionários</h2>
          <p className="text-sm mb-2">Três formas:</p>
          <ol className="list-decimal pl-5 space-y-2 text-sm">
            <li><strong>Individual (um usuário):</strong> Menú <strong>Atribuir formulário (1 usuário)</strong> → escolher Cliente (se admin), Usuário, Formulário, e opcionalmente Período e Data limite → marcar “Enviar e-mail de convite” se quiser → Atribuir.</li>
            <li><strong>Pelo usuário:</strong> Menú <strong>Usuários</strong> → editar o usuário → secção “Questionários asignados” → adicionar formulário, período e data limite → Asignar.</li>
            <li><strong>Em massa:</strong> Menú <strong>Atribuição em massa</strong> → escolher Cliente, Formulário, Período (opcional), Data limite → escolher filtros (Unidade, Área, etc.) <strong>ou</strong> um <strong>Grupo</strong> → Atribuir. Pode marcar “Enviar e-mail de convite aos usuários atribuídos”.</li>
          </ol>
        </section>

        <section>
          <h2 className="text-lg font-semibold text-emotive-gray-header mb-2">3. Grupos: adicionar usuários e lançar a encuesta ao grupo</h2>
          <p className="text-sm mb-2">Um <strong>Grupo</strong> é uma lista explícita de usuários. Você adiciona usuários ao grupo e depois pode atribuir a encuesta a esse grupo (em massa) ou ver no Monitor só os respondentes desse grupo.</p>
          <ul className="list-disc pl-5 space-y-1 text-sm">
            <li>Crie o grupo em <strong>Grupos</strong> → Novo grupo → preencha Nome (e opcionalmente filtros como Unidade, Área para referência).</li>
            <li>Em <strong>Editar</strong> o grupo, use <strong>Adicionar usuários ao grupo</strong>: seleccione os usuários do cliente que pertencem ao grupo e confirme. Pode remover usuários com &quot;Remover&quot;.</li>
            <li>Em <strong>Atribuição em massa</strong>, escolha “por grupo” e seleccione esse grupo: o sistema atribui o formulário a todos os usuários que estão no grupo.</li>
            <li>No <strong>Monitor de respondentes</strong>, em “Por grupo”, pode ver só os respondentes que pertencem ao grupo seleccionado.</li>
          </ul>
        </section>

        <section>
          <h2 className="text-lg font-semibold text-emotive-gray-header mb-2">4. Fluxo típico (gestor)</h2>
          <ol className="list-decimal pl-5 space-y-1 text-sm">
            <li>Criar ou importar <strong>Usuários</strong> e preencher Unidade, Área, etc.</li>
            <li>Criar um <strong>Período</strong> (onda) em Períodos / Ondas.</li>
            <li>Atribuir questionários: <strong>Atribuir formulário (1 usuário)</strong> para poucos, ou <strong>Atribuição em massa</strong> (com filtros ou grupo) para muitos.</li>
            <li>Em <strong>Monitor de respondentes</strong>, ver pendentes e enviar recordatórios.</li>
            <li>Em <strong>Relatório corporativo</strong>, ver KPIs, setores críticos e evolução por período.</li>
          </ol>
        </section>

        <section>
          <h2 className="text-lg font-semibold text-emotive-gray-header mb-2">5. Dúvidas ou problemas</h2>
          <p className="text-sm">Consulte a documentação em <code className="bg-gray-100 px-1 rounded">migrations/docs/COMO_FUNCIONA_LA_APP.md</code> ou entre em contacto com o suporte.</p>
        </section>
      </div>
    </div>
  );
}
