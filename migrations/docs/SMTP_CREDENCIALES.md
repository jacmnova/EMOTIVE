# SMTP – Credenciales para el backend API (E.MO.TI.VE)

El backend en `migrations/backend-api` (FastAPI) envía correos para:

- Verificación de e-mail al registrarse
- Recuperación de contraseña
- Invitación a cuestionario (al atribuir formulário)
- Recordatorio de cuestionário pendiente

## Dónde configurar

Las variables se leen del archivo **`.env`** dentro de `migrations/backend-api/` (no del `.env` de la raíz del proyecto Laravel).

Puedes copiar el bloque de correo desde tu `.env` de Laravel al `.env` del backend. Ejemplo:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_correo@gmail.com
MAIL_PASSWORD=tu_contraseña_de_aplicacion
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu_correo@gmail.com
MAIL_FROM_NAME=E.MO.TI.VE
```

## Variables usadas por la API

| Variable           | Descripción                          | Ejemplo                |
|--------------------|--------------------------------------|------------------------|
| `MAIL_HOST`        | Servidor SMTP                        | `smtp.gmail.com`       |
| `MAIL_PORT`        | Puerto (587 TLS, 465 SSL)            | `587`                  |
| `MAIL_USERNAME`    | Usuario SMTP                         | `correo@gmail.com`     |
| `MAIL_PASSWORD`    | Contraseña (en Gmail, “contraseña de aplicación”) | `xxxx xxxx xxxx xxxx` |
| `MAIL_ENCRYPTION`  | `tls`, `ssl` o `null`                | `tls`                  |
| `MAIL_FROM_ADDRESS`| Remitente visible                    | `correo@gmail.com`     |
| `MAIL_FROM_NAME`   | Nombre del remitente                 | `E.MO.TI.VE`           |
| `FRONTEND_URL`     | URL del frontend (para enlaces en e-mails) | `https://app.emotive.com` |

## Gmail

Si usas Gmail, genera una **contraseña de aplicación** en la cuenta de Google (seguridad → verificación en 2 pasos → contraseñas de aplicación) y usa esa contraseña en `MAIL_PASSWORD`. No uses la contraseña normal de la cuenta.

## Comprobar

Tras guardar `migrations/backend-api/.env`, reinicia el servidor (uvicorn). Al atribuir un formulário con “Enviar e-mail de convite” o al solicitar recuperación de contraseña, el backend usará estas credenciales.
