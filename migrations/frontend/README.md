# Frontend E.MO.TI.VE (Next.js)

Frontend da aplicação E.MO.TI.VE em Next.js 14 (App Router), consumindo a API FastAPI.

## Pré-requisitos

- Node.js 18+
- npm ou yarn

## Instalação

```bash
cd migrations/frontend
npm install
cp .env.local.example .env.local
# Edite .env.local e defina NEXT_PUBLIC_API_URL (ex: http://localhost:8000)
```

## Executar

```bash
npm run dev
```

A aplicação estará em `http://localhost:3000`.

Certifique-se de que o backend FastAPI está rodando em `http://localhost:8000` (ou na URL configurada em `NEXT_PUBLIC_API_URL`).

## Estrutura

- `app/` – App Router (páginas e layouts)
  - `auth/` – Login, registro, recuperação de senha, verificação de email
  - `dashboard/` – Área autenticada (início, meus questionários, usuários, clientes, formulários)
- `lib/` – API client, auth helpers
- `types/` – Tipos TypeScript

## Autenticação

- Login: JWT armazenado em `localStorage`.
- Após login, o token é enviado no header `Authorization: Bearer <token>` em todas as chamadas à API.
- Páginas em `/dashboard/*` redirecionam para `/auth/login` se não houver token.

## Build

```bash
npm run build
npm start
```

## Funcionalidades implementadas (Fase 3 completa)

- **Auth**: login, registro, recuperação de senha, verificação de email
- **Dashboard**: início, meus questionários, chat, perfil, notificações
- **Admin**: CRUD usuários, clientes, formulários; CRUD perguntas e variáveis por formulário
- **Gestor**: listar e editar usuários do seu cliente (sem criar usuários)
- **Usuário**: listar questionários asignados, responder perguntas (0–6), salvar respostas
- **Relatório**: pontuações, índices, nível de risco, plano, análise; botão baixar PDF
- **Chat**: mensagens com ChatGPT
- **Perfil**: ver e editar nome e email
- **Notificações**: página com lista local (localStorage) e estado vazio
