# Lo que falta para que la app esté 100% operativa como la de PHP

Lista de funcionalidades presentes en la aplicación Laravel (php_old_app) que aún no están migradas o completadas en el stack **Next.js + FastAPI**.

---

## Prioridad alta (impacto directo en uso)

### 1. Lógica de cálculos (interpretaciones)
- **Estado**: ~70% en `migrations/backend-api/app/services/calculos.py`
- **Falta**: Completar las interpretaciones de ejes analíticos (actualmente simplificadas)
- **Impacto**: Calidad y precisión del reporte

### 2. Generación automática de análisis (OpenAI)
- **Estado**: El backend puede generar análisis bajo demanda; no se genera automáticamente al consultar el reporte si no existe
- **Falta**: Al obtener el relatorio, si no hay análisis guardado, generarlo automáticamente (o opción clara “Generar análisis”)
- **Impacto**: El usuario puede ver reporte sin análisis

### 3. Páginas estáticas / informativas
- **PHP**: `/termos`, `/tutorial`, `/faqs` (públicas)
- **Falta en Next.js**: Crear rutas y contenido equivalente
  - `app/termos/page.tsx` (o `/termos`)
  - `app/tutorial/page.tsx`
  - `app/faqs/page.tsx` (incluye formulario de contacto, ver punto 4)

### 4. Formulario de contacto
- **PHP**: `POST /contato/enviar` → envía email al soporte (ContatoController)
- **Falta**:
  - **Backend**: Endpoint `POST /api/v1/contato/enviar` (recibe nombre, email, mensaje; envía email vía servicio existente)
  - **Frontend**: Formulario en página FAQs (o página Contacto) que llame a ese endpoint

---

## Prioridad media (funciones de admin/gestor)

### 5. Impersonación de usuarios
- **PHP**: `POST /impersonate/start/{id}` (admin/gestor se hace pasar por otro usuario)
- **Falta**:
  - **Backend**: Endpoint para iniciar/finalizar impersonación (token o sesión especial)
  - **Frontend**: Botón “Personificar” en listados de usuarios (gestão, usuarios) que llame al endpoint y recargue la sesión como ese usuario

### 6. Importación de usuarios (CSV)
- **PHP**: `GET /gestor/importar` (formulario), `POST /gestor/importar` (procesar CSV)
- **Falta**:
  - **Backend**: Endpoint `POST /api/v1/usuarios/importar` (recibe CSV, crea usuarios en lote para el cliente del gestor)
  - **Frontend**: Página “Importar usuarios” para gestor (subir CSV, resultado de importación)

### 7. Gestión de etapas de formulario
- **PHP**: `POST /etapas/adicionar`, `DELETE /etapas/{id}/remover` (FormularioEtapaController)
- **Falta**:
  - **Backend**: Endpoints para listar/adicionar/remover etapas del formulario (o incluir en CRUD de formularios)
  - **Frontend**: En la edición de formulario, sección “Etapas” (lista + agregar + quitar)

### 8. Gestión de “Cálculos” (Tipo de cálculo)
- **PHP**: Recurso completo `calculos` (CalculosController) + en formularios se elige `calculo_id` (TipoCalculo)
- **Falta**:
  - **Backend**: CRUD de tipos de cálculo si se desea administrarlos desde la app (listar ya existe vía modelo; falta API explícita si se usa en formularios)
  - **Frontend**: En crear/editar formulario, selector de “Tipo de cálculo”;
  - Opcional: pantalla admin “Cálculos” (listar/crear/editar/eliminar) como en PHP

### 9. Gestión de medias (Midias)
- **PHP**: Recurso `midias` (CRUD): título, tipo url/video, formulario_id, archivo; se asocia a formulario y se muestra en “Mis cuestionarios” (ver video/url antes de responder)
- **Falta**:
  - **Backend**: CRUD completo de medias (listar, crear, editar, eliminar) y asociación a formulario; el modelo existe
  - **Frontend**: Página “Medias” (listar, crear, editar) y en la vista de cuestionarios del usuario, botón para abrir video/url de la media del formulario

### 10. Contraseña iniciada por admin
- **PHP**: `POST /admin/password/initiate/{id}` y `POST /admin/password/update/{id}` (PasswordController)
- **Falta**:
  - **Backend**: Endpoints para que admin inicie un flujo de cambio de contraseña para un usuario (ej. enviar email con token) y para actualizar la contraseña de ese usuario (con token o con privilegio admin)
  - **Frontend**: En edición de usuario (vista admin), acciones “Enviar enlace para cambiar contraseña” / “Definir nueva contraseña”

### 11. Reenviar email de verificación
- **PHP**: `POST /verificar-reativar` (reenvía email de verificación al usuario logueado)
- **Falta**:
  - **Backend**: Endpoint `POST /api/v1/auth/reenviar-verificacion` que genere nuevo token y envíe el email
  - **Frontend**: En perfil (o en banner si no verificado), botón “Reenviar email de verificación”

### 12. Notificaciones: marcar todas como leídas
- **PHP**: `GET /notificacoes/marcar-todas` (marca todas las notificaciones del usuario como leídas)
- **Falta**:
  - **Backend**: Si las notificaciones pasan a estar en BD, endpoint `POST /api/v1/notificacoes/marcar-todas`
  - **Frontend**: Botón “Marcar todas como leídas” que llame a ese endpoint (y/o seguir con lógica local si se mantiene solo en cliente)

---

## Prioridad menor (refinamiento y paridad)

### 13. Varias rutas de PDF (PHP)
- **PHP**: `/relatorio/pdf`, `/relatorio/pdf/temp/{token}`, `relatorio/pdf/capture/temp/{token}`, `relatorio/pdf/web/temp/{token}`
- **Estado**: FastAPI tiene endpoint único para generar PDF
- **Falta**: Validar que todos los casos de uso (reporte normal, reporte por token temporal, etc.) estén cubiertos por el endpoint actual o añadir los que falten

### 14. Marcar cuestionario como “assistido”
- **PHP**: `POST /usuario-formulario/{id}/assistido` (marcar asistido)
- **Falta**: Endpoint y uso en frontend si se desea la misma lógica

### 15. Finalizar cuestionario
- **PHP**: `POST /usuario-formulario/finalizar`
- **Comprobar**: Si en la app nueva el “finalizar” se hace al guardar la última respuesta o con un botón explícito; si hace falta, añadir endpoint y botón

---

## Despliegue y operación (Fase 5)

- [ ] Variables de entorno de producción (backend y frontend)
- [ ] Despliegue backend (ej. Gunicorn + Nginx, o contenedor)
- [ ] Despliegue frontend (build Next.js, servir estático o Node)
- [ ] Migración de datos desde Laravel (si se cambia BD o esquema)
- [ ] Monitoreo y logs en producción
- [ ] CI/CD: pipeline de despliegue (además de tests)

---

## Resumen por componente

| Área                    | Estado   | Acción principal                                              |
|-------------------------|----------|----------------------------------------------------------------|
| Cálculos (interpret.)   | 70%      | Completar interpretaciones en `calculos.py`                   |
| OpenAI en reporte       | Parcial  | Auto-generar análisis al consultar reporte si no existe       |
| Páginas termos/tutorial/faqs | No  | Crear en Next.js                                              |
| Contacto                | No       | Endpoint + formulario FAQs                                   |
| Impersonación           | No       | API + botón en listados                                       |
| Importar CSV            | No       | API + página gestor                                           |
| Etapas formulario       | No       | API etapas + UI en edición formulario                         |
| CRUD Cálculos (TipoCalculo) | Parcial | API + selector en formulario + pantalla admin (opcional) |
| Medias (Midias)         | Parcial  | CRUD API + UI Medias + mostrar en cuestionarios               |
| Admin password          | No       | Endpoints initiate/update + UI en edición usuario             |
| Reenviar verificación   | No       | Endpoint + botón en perfil                                    |
| Notificaciones backend  | No       | Marcar todas leídas (si se usan notificaciones en BD)          |
| PDF / finalizar / assistido | Revisar | Validar casos de uso y completar si falta                     |
| Despliegue              | 0%       | Configuración producción y CI/CD                              |

Cuando todo lo anterior esté implementado y desplegado, la app estará en paridad funcional con la aplicación PHP y lista para uso 100% operativo.
