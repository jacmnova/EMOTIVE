# Notificações – Finalidade e funcionamento

## Finalidade

As notificações servem para **informar o utilizador sobre eventos relevantes** dentro da plataforma E.MO.TI.VE, sem depender apenas do e-mail. Objetivos:

1. **Manter o utilizador informado** sobre ações que o afetam (questionários atribuídos, relatórios prontos, alterações de conta).
2. **Alertar gestores e administradores** sobre eventos que exigem atenção (novos utilizadores, formulários completados, mensagens de contacto).
3. **Centralizar avisos** num único sítio (centro de notificações) em vez de depender só de e-mail.

---

## Como podem funcionar

### Fluxo geral

1. **Criação**: Quando ocorre um evento (ex.: questionário atribuído, relatório gerado), o backend cria um registo de notificação associado ao utilizador destinatário.
2. **Listagem**: O frontend chama `GET /notificacoes` e mostra a lista na página “Notificações” (e opcionalmente um contador no ícone do sino no header).
3. **Marcar como lida**: O utilizador marca uma ou todas como lidas; o backend atualiza o estado (ex.: `lida_at`).
4. **Opcional – e-mail**: Para eventos críticos (ex.: recuperação de senha, verificação de e-mail), continuar a enviar e-mail como hoje; a notificação in-app é um complemento.

### Backend (proposto)

- **Modelo** `Notification`: `id`, `user_id`, `tipo`, `titulo`, `mensagem`, `link` (opcional), `lida_at` (null = não lida), `created_at`.
- **Endpoints**:
  - `GET /notificacoes` – lista notificações do utilizador autenticado (paginado, mais recentes primeiro).
  - `POST /notificacoes/:id/marcar-lida` – marca uma como lida.
  - `POST /notificacoes/marcar-todas` – marca todas como lidas (já existe, passaria a atualizar a BD).
- **Criação**: Em pontos do código onde já existem ações (registar utilizador, completar questionário, enviar relatório, etc.), chamar um serviço do tipo `criar_notificacao(user_id, tipo, titulo, mensagem, link=...)`.

### Frontend (proposto)

- A página **Notificações** deixa de usar apenas `localStorage` e passa a carregar a lista via `GET /notificacoes`.
- Opcional: no **header**, mostrar um badge no ícone do sino com o número de notificações não lidas (atualizado ao carregar o layout ou em intervalo).
- Manter ações “Marcar como lida” e “Marcar todas como lidas” ligadas aos novos endpoints.

---

## Eventos que podem gerar notificações

| Quem recebe   | Evento                          | Exemplo de título / mensagem                    |
|---------------|----------------------------------|-------------------------------------------------|
| Utilizador    | Questionário atribuído           | “Novo questionário” – “O formulário X foi atribuído a si.” |
| Utilizador    | Lembrete / prazo                 | “Lembrete” – “Tem questionários pendentes.”      |
| Utilizador    | Relatório disponível             | “Relatório pronto” – “O seu relatório do formulário X está disponível.” |
| Utilizador    | Conta verificada / senha alterada| “E-mail verificado” / “Senha alterada com sucesso.” |
| Gestor/Admin  | Novo utilizador registado        | “Novo utilizador” – “Y registou-se no cliente Z.” |
| Gestor/Admin  | Formulário completado por alguém | “Questionário completado” – “O utilizador Y completou o formulário X.” |
| Admin         | Mensagem de contacto (FAQ)      | “Contacto” – “Nova mensagem de N no formulário de suporte.” |

---

## Resumo

- **Finalidade**: informar o utilizador (e gestores) sobre eventos importantes dentro da aplicação, num centro de notificações e, quando fizer sentido, em e-mail.
- **Funcionamento**: backend persiste notificações por utilizador; frontend lista e marca como lidas; eventos existentes (registos, questionários, relatórios, contacto) passam a criar registos de notificação além das ações que já fazem hoje (ex.: enviar e-mail).
