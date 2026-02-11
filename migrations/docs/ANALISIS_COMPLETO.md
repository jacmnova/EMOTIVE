# Análisis Completo de la Aplicación E.MO.TI.VE

## 📋 Resumen Ejecutivo

Aplicación Laravel para evaluación de salud emocional y bienestar en el trabajo mediante cuestionarios E.MO.TI.VE (Exaustão Emocional, Realização Profissional, Despersonalização/Cinismo, Fatores Psicossociais, Excesso de Trabalho, Assédio Moral).

## 🏗️ Arquitectura Actual

### Stack Tecnológico
- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade Templates + AdminLTE 3 + Bootstrap 5 + Vite
- **Base de Datos**: SQLite/MySQL
- **Autenticación**: Laravel Auth (Session-based)
- **PDF**: DomPDF + Browsershot
- **IA**: OpenAI API (GPT-4o)
- **Notificaciones**: Laravel Notifications

## 👥 Sistema de Usuarios y Roles

### Roles Implementados
1. **SA (Super Admin)**: Acceso completo al sistema
2. **Admin**: Gestión de usuarios, clientes, formularios, preguntas, variables
3. **Gestor**: Gestión de usuarios y formularios de su cliente
4. **Usuario**: Participante que responde cuestionarios

### Modelo de Usuario
```php
- id
- name
- email (unique)
- password (hashed)
- avatar
- email_verified_at
- verification_token
- sa (boolean)
- admin (boolean)
- gestor (boolean)
- usuario (boolean)
- cliente_id (nullable, FK a clientes)
- ativo (boolean)
- deleted_at (soft deletes)
- deleted_by
- timestamps
```

### Permisos y Gates
- `Gate::define('sa')` - Super Admin
- `Gate::define('admin')` - Administrador
- `Gate::define('gestor')` - Gestor de cliente
- `Gate::define('usuario')` - Usuario participante

## 🗄️ Modelo de Datos

### Tablas Principales

#### 1. **users**
Usuarios del sistema con roles y relaciones.

#### 2. **clientes**
```sql
- id
- usuario_id (FK a users - gestor del cliente)
- tipo (empresa, etc)
- cpf_cnpj
- nome_fantasia
- razao_social
- logo_url
- email
- contato
- telefone
- ativo (boolean)
- deleted_at (soft deletes)
- deleted_by
- timestamps
```

#### 3. **formularios**
```sql
- id
- nome
- label
- descricao
- instrucoes
- score_ini (integer)
- score_fim (integer)
- calculo_id (FK a tipo_calculo)
- status (boolean)
- timestamps
```

#### 4. **tipo_calculo**
Tipos de cálculo disponibles para formularios.

#### 5. **perguntas**
```sql
- id
- formulario_id (FK)
- numero_da_pergunta (integer)
- pergunta (text)
- timestamps
```

#### 6. **variaveis** (Dimensiones)
```sql
- id
- formulario_id (FK)
- nome
- descricao
- tag (EXEM, REPR, DECI, FAPS, EXTR, ASMO)
- B (integer) - Límite inferior faixa baja
- M (integer) - Límite inferior faixa media
- A (integer) - Límite inferior faixa alta
- baixa, moderada, alta (text) - Descripciones
- r_baixa, r_moderada, r_alta (text) - Recomendaciones
- d_baixa, d_moderada, d_alta (text nullable) - Dicas
- timestamps
```

#### 7. **pergunta_variavel** (Pivot)
Relación many-to-many entre preguntas y variables.

#### 8. **respostas**
```sql
- id
- user_id (FK)
- pergunta_id (FK)
- valor_resposta (integer 0-6)
- timestamps
```

#### 9. **cliente_formulario** (Pivot)
Relación entre clientes y formularios disponibles.

#### 10. **usuario_formulario** (Pivot)
```sql
- id
- usuario_id (FK)
- formulario_id (FK)
- status (pendente, completo)
- midia_id (FK nullable)
- assistido (boolean)
- timestamps
```

#### 11. **formulario_etapas**
```sql
- id
- formulario_id (FK)
- etapa (integer)
- de (integer) - ID pregunta inicio
- ate (integer) - ID pregunta fin
- timestamps
```

#### 12. **analises**
```sql
- id
- user_id (FK)
- formulario_id (FK)
- texto (text) - Análisis generado por OpenAI
- timestamps
```

#### 13. **midias**
Medios asociados a formularios (imágenes, videos, etc).

#### 14. **notifications**
Tabla estándar de Laravel para notificaciones.

## 🔄 Flujos Principales

### 1. Flujo de Autenticación
```
/ → /login → /home (según rol)
- Registro con verificación de email
- Recuperación de contraseña
- Impersonación (admin puede hacerse pasar por otro usuario)
```

### 2. Flujo de Gestión (Admin/SA)
```
Dashboard → Usuarios/Clientes/Formularios/Preguntas/Variables
- CRUD completo de todas las entidades
- Asignación de formularios a clientes
- Importación masiva de usuarios (CSV)
- Gestión de medios
```

### 3. Flujo de Participante (Usuario)
```
Home → Mis Cuestionarios → Responder Formulario → Ver Reporte
- Lista de formularios asignados
- Responder por etapas
- Guardar respuestas (AJAX)
- Ver reporte con gráficos y análisis
- Descargar PDF del reporte
```

### 4. Flujo de Gestor
```
Home → Gestión de Usuarios → Asignar Formularios
- Ver usuarios de su cliente
- Editar usuarios
- Asignar formularios a usuarios
- Ver reportes de usuarios
```

## 📊 Sistema de Cálculos

### Dimensiones (Variables)
1. **EXEM** - Exaustão Emocional
2. **REPR** - Realização Profissional
3. **DECI** - Despersonalização / Cinismo
4. **FAPS** - Fatores Psicossociais
5. **EXTR** - Excesso de Trabalho
6. **ASMO** - Assédio Moral

### Lógica de Cálculo de Puntuaciones

#### 1. Inversión de Preguntas
Algunas preguntas requieren inversión de valores:
- **Preguntas invertidas**: 0→6, 1→5, 2→4, 3→3, 4→2, 5→1, 6→0
- **Identificación**: Por texto de la pregunta (helper `PerguntasInvertidasHelper`)
- **Aplicación**: Uniforme para todas las dimensiones

#### 2. Cálculo por Dimensión
```php
foreach ($variavel->perguntas as $pergunta) {
    $resposta = obtener_respuesta($pergunta);
    $valor = aplicar_inversion_si_corresponde($resposta, $pergunta);
    $pontuacao += $valor;
}
```

#### 3. Clasificación en Faixas
- **Baixa**: valor ≤ B
- **Moderada**: B < valor ≤ M
- **Alta**: valor > M

#### 4. Caso Especial: EXTR
- Una pregunta específica se cuenta dos veces para alcanzar 16 preguntas totales según CSV MAX.

### Índices (EE, PR, SO)

#### Cálculo Directo desde Respuestas
```php
EE = EXEM ∪ REPR (suma de preguntas únicas)
PR = DECI ∪ FAPS (suma de preguntas únicas)
SO = EXTR ∪ ASMO (suma de preguntas únicas)
```

Cada índice:
1. Obtiene variables relacionadas
2. Procesa todas las preguntas de esas variables
3. Aplica inversión cuando corresponde
4. Suma valores (valores absolutos, no porcentajes)

### Ejes Analíticos

#### Eje 1: ENERGIA EMOCIONAL
- **Dimensiones**: EXEM, REPR
- **Cálculo**: Usa índice EE directamente (valor absoluto)
- **Interpretación**: Basada en combinaciones de faixas

#### Eje 2: PROPÓSITO E RELAÇÕES
- **Dimensiones**: DECI, FAPS
- **Cálculo**: Usa índice PR directamente (valor absoluto)
- **Interpretación**: Basada en combinaciones de faixas

#### Eje 3: SUSTENTABILIDADE OCUPACIONAL
- **Dimensiones**: EXTR, ASMO
- **Cálculo**: Usa índice SO directamente (valor absoluto)
- **Interpretación**: Basada en combinaciones de faixas

### IID (Índice Integrado de Descarrilamento)

```php
promedio_indices = (EE + PR + SO) / 3
promedio_maximos = (276 + 234 + 186) / 3 = 232
IID = (promedio_indices / promedio_maximos) * 100
```

**Niveles de Riesgo**:
- **Baixo** (≤40): Zona de Equilíbrio Emocional
- **Médio** (41-65): Zona de Atenção Preventiva
- **Atenção** (66-89): Zona de Vulnerabilidade
- **Alto** (≥90): Zona Crítica

## 🛣️ Rutas Principales

### Autenticación
- `GET /` → Redirige a login
- `GET /login` → Formulario de login
- `POST /login` → Procesar login
- `GET /register` → Formulario de registro
- `POST /register` → Procesar registro
- `GET /verificar-email?token=...` → Verificar email
- `POST /verificar-reativar` → Reenviar email verificación

### Dashboard y Home
- `GET /home` → Home según rol
- `GET /dashboard` → Alias de home
- `GET /dashadmin` → Dashboard admin

### Gestión de Datos Personales
- `GET /dados` → Ver/editar perfil
- `PUT /meusuario/{id}` → Actualizar usuario
- `POST /changepass/{id}` → Cambiar contraseña
- `POST /upload/imagem/usuario` → Subir avatar
- `POST /upload/imagem/cliente` → Subir logo cliente

### Formularios y Cuestionarios
- `GET /meus-questionarios` → Lista de cuestionarios del usuario
- `GET /meusquestionarios/editar/{id}` → Responder formulario
- `POST /respostas/salvar` → Guardar respuestas (AJAX)
- `POST /usuario-formulario/finalizar` → Finalizar formulario
- `POST /formulario/verificar-status` → Verificar estado

### Reportes
- `GET /meurelatorio/show` → Ver reporte (con parámetros)
- `GET /relatorio/pdf` → Generar PDF
- `GET /relatorio/pdf/temp/{token}` → Ver reporte con token temporal
- `POST /relatorio/regenerar` → Regenerar análisis IA
- `POST /relatorio/analise/{usuarioId}` → Generar análisis IA

### Gestión Admin
- `GET /usuarios` → Lista usuarios (admin)
- `POST /usuarios` → Crear usuario
- `PUT /usuarios/{id}` → Actualizar usuario
- `DELETE /usuarios/{id}` → Eliminar usuario
- `PUT /usuarios/status/{id}` → Activar/desactivar
- `POST /impersonate/start/{id}` → Impersonar usuario

- `GET /clientes` → Lista clientes
- `POST /clientes` → Crear cliente
- `PUT /clientes/{id}` → Actualizar cliente
- `DELETE /clientes/{id}` → Eliminar cliente
- `PUT /clientes/status/{id}` → Activar/desactivar

- `GET /formularios` → Lista formularios
- `POST /formularios` → Crear formulario
- `PUT /formularios/{id}` → Actualizar formulario
- `DELETE /formularios/{id}` → Eliminar formulario
- `PUT /formularios/status/{id}` → Activar/desactivar

- `GET /perguntas` → Lista preguntas
- `POST /perguntas` → Crear pregunta
- `PUT /perguntas/{id}` → Actualizar pregunta
- `DELETE /perguntas/{id}` → Eliminar pregunta

- `GET /variaveis` → Lista variables
- `POST /variaveis` → Crear variable
- `PUT /variaveis/{id}` → Actualizar variable
- `DELETE /variaveis/{id}` → Eliminar variable
- `GET /variaveis/formulario/{id}` → Variables de un formulario

- `GET /calculos` → Lista cálculos
- `POST /calculos` → Crear cálculo
- `PUT /calculos/{id}` → Actualizar cálculo
- `DELETE /calculos/{id}` → Eliminar cálculo
- `PUT /calculos/status/{id}` → Activar/desactivar

- `GET /midias` → Lista medios
- `POST /midias` → Crear medio
- `PUT /midias/{id}` → Actualizar medio
- `DELETE /midias/{id}` → Eliminar medio

### Gestión Gestor
- `GET /gestao-usuarios` → Usuarios del cliente
- `GET /editar-usuario/{id}` → Editar usuario del cliente
- `GET /lista-formularios` → Formularios del cliente
- `GET /gestor/create-cliente` → Crear cliente (gestor)
- `POST /gestor/store-cliente` → Guardar cliente
- `GET /gestor/importar` → Formulario importar usuarios
- `POST /gestor/importar` → Importar usuarios CSV

### Otros
- `GET /chat` → Chat con ChatGPT
- `POST /chat` → Enviar mensaje a ChatGPT
- `GET /faqs` → Preguntas frecuentes
- `GET /termos` → Términos y condiciones
- `GET /tutorial` → Tutorial
- `POST /contato/enviar` → Enviar mensaje de contacto
- `POST /etapas/adicionar` → Agregar etapa a formulario
- `DELETE /etapas/{id}/remover` → Eliminar etapa
- `POST /usuario-formulario/{id}/assistido` → Marcar como visto
- `GET /notificacoes/marcar-todas` → Marcar notificaciones como leídas

## 🎨 Frontend

### Tecnologías
- **Templates**: Blade (Laravel)
- **UI Framework**: AdminLTE 3
- **CSS**: Bootstrap 5 + Tailwind CSS 4
- **JS**: Vanilla JS + Axios
- **Build**: Vite
- **Gráficos**: Chart.js (en reportes)

### Estructura de Vistas
```
resources/views/
├── layouts/
│   ├── app.blade.php (AdminLTE)
│   └── partials/
├── auth/ (login, register, etc)
├── dashboard/ (home)
├── usuarios/ (CRUD)
├── clientes/ (CRUD)
├── formularios/ (CRUD)
├── perguntas/ (CRUD)
├── variaveis/ (CRUD)
├── calculos/ (CRUD)
├── midias/ (CRUD)
├── participante/ (cuestionarios, formulario, reporte)
├── dados/ (perfil)
├── gestao/ (gestión gestor)
├── analise/ (análisis)
├── pdf/ (vistas PDF)
└── emails/ (plantillas email)
```

## 🔐 Seguridad

### Autenticación
- Session-based authentication
- Email verification
- Password reset tokens
- Remember me functionality

### Autorización
- Gates para roles (sa, admin, gestor, usuario)
- Middleware de autenticación
- Validación de acceso a recursos (ej: usuario solo ve sus reportes)

### Validación
- Validación de formularios en backend
- CSRF protection
- XSS protection (Blade escaping)

## 📄 Generación de PDFs

### Método Actual
1. Servicio externo de conversión (API en puerto 8080)
2. Genera URL temporal con token
3. Servicio navega a URL y convierte HTML a PDF
4. Retorna PDF para descarga

### Vistas PDF
- `participante.relatorio_emotive_pdf` - Vista optimizada para PDF
- `participante.relatorio_emotive_capture` - Vista sin layout para captura
- `participante.relatorio_emotive` - Vista web completa

## 🤖 Integración con OpenAI

### Generación de Análisis
- **Modelo**: GPT-4o
- **Prompt**: Incluye nombre usuario, puntuaciones por dimensión, faixas
- **Output**: Texto motivacional de ~500 palabras
- **Almacenamiento**: Tabla `analises`
- **Regeneración**: Admin puede regenerar análisis

## 📧 Notificaciones

### Tipos
- Verificación de email
- Reset de contraseña
- Nuevo usuario cadastrado
- Usuario cadastrado (notificación al usuario)

## 🔧 Comandos Artisan

Múltiples comandos para:
- Actualizar relaciones pregunta-variable
- Calcular rangos
- Diagnosticar cálculos
- Probar lógica de inversión
- Exportar datos para comparación
- Y más...

## 📦 Dependencias Principales

### Backend
- `laravel/framework: ^12.0`
- `barryvdh/laravel-dompdf: ^3.1`
- `jeroennoten/laravel-adminlte: ^3.15`
- `openai-php/laravel: ^0.13.0`
- `spatie/browsershot: ^5.0`

### Frontend
- `bootstrap: ^5.2.3`
- `tailwindcss: ^4.0.0`
- `axios: ^1.8.2`
- `vite: ^6.2.4`

## 🚀 Próximos Pasos para Migración

1. **Análisis de dependencias**: Identificar todas las dependencias y funcionalidades
2. **Diseño de API REST**: Definir endpoints para Python
3. **Migración de base de datos**: Crear esquemas SQLAlchemy
4. **Migración de lógica de negocio**: Portar cálculos a Python
5. **Migración de frontend**: Recrear en Next.js/Angular
6. **Testing**: Asegurar 100% de funcionalidad equivalente
