# Checklist para 100% operativo (como la app PHP)

- [x] **1. Páginas estáticas** – `/termos`, `/tutorial`, `/faqs` en Next.js + enlaces en inicio
- [x] **2. Formulario de contacto** – Endpoint `POST /api/v1/contato/enviar` + formulario en página FAQs
- [x] **3. Reenviar email de verificación** – `POST /auth/reenviar-verificacion` + botón en Perfil
- [x] **4. Notificaciones: marcar todas leídas** – `POST /notificacoes/marcar-todas` + botón en Notificações
- [x] **5. Impersonación** – `POST /impersonate/start/{id}` y `POST /impersonate/stop` + botón "Personificar" en listado usuarios + banner "Sair da personificação"
- [x] **6. Importar usuarios CSV** – `POST /users/importar` (gestor) + página `/dashboard/usuarios/importar`
- [x] **7. Etapas de formulario** – `GET/POST/DELETE /formularios/{id}/etapas` + sección Etapas en edición de formulario
- [x] **8. Tipos de cálculo** – API CRUD `/calculos` + selector en formulario (crear/editar) + página Cálculos (admin)
- [x] **9. Medias (Midias)** – CRUD API `/midias` + UI (list/new/edit) + mostrar en Meus questionários (Assistir vídeo / Abrir mídia)
- [x] **10. Admin: iniciar/cambiar contraseña** – `POST /users/{id}/password/initiate` y `password/update` + acciones en edición usuario
- [x] **11. Análisis automático al consultar reporte** – Al obtener relatório, si no hay análisis se genera con OpenAI y se guarda
- [x] **12. Interpretaciones de ejes** – Completadas en `calculos.py` (interpretar_eixo1/2/3 con todas las combinaciones Laravel)
- [x] **13. Pruebas finales** – Ejecutar tests backend + frontend; validar flujos manualmente

## Cómo ejecutar las pruebas finales

- **Backend**: `cd migrations/backend-api && pip install -r requirements.txt && pytest`
- **Frontend**: `cd migrations/frontend && npm install && npm test` (unit) y `npx playwright test` (E2E)
- Validar manualmente: login, registro, recuperar contraseña, reporte con análisis, PDF, impersonación, importar CSV, etapas, cálculos, mídias, admin contraseña.
