# 🔐 Configuración de GitHub Secrets para Despliegue Automático

Guía rápida para configurar los secrets en GitHub Actions.

---

## 📋 Secrets a Configurar

Ve a tu repositorio en GitHub:
1. **Settings** → **Secrets and variables** → **Actions**
2. Haz clic en **"New repository secret"** para cada uno

---

## 🔑 Secret 1: SSH_HOST

**Name:** `SSH_HOST`

**Value:** 
```
emotive.g3nia.com
```

*O usa la IP pública si el dominio aún no apunta:*
```
54.123.45.67
```

---

## 🔑 Secret 2: SSH_USER

**Name:** `SSH_USER`

**Value:**
```
ec2-user
```

*Si usas Ubuntu, cambia a:*
```
ubuntu
```

---

## 🔑 Secret 3: SSH_KEY

**Name:** `SSH_KEY`

**Value:** *(Pega aquí la clave privada completa)*

**Para obtener la clave privada, ejecuta en el servidor EC2:**
```bash
cat ~/.ssh/github_deploy
```

**Copia TODO el contenido, incluyendo:**
- `-----BEGIN OPENSSH PRIVATE KEY-----`
- Todas las líneas del medio
- `-----END OPENSSH PRIVATE KEY-----`

**Ejemplo de formato (NO uses este, usa el tuyo):**
```
-----BEGIN OPENSSH PRIVATE KEY-----
b3BlbnNzaC1rZXktdjEAAAAABG5vbmUAAAAEbm9uZQAAAAAAAAABAAABlwAAAAdzc2gtcn
NhAAAAAwEAAQAAAYEAy8... (muchas líneas más) ...
-----END OPENSSH PRIVATE KEY-----
```

---

## 🔑 Secret 4: SSH_PORT

**Name:** `SSH_PORT`

**Value:**
```
22
```

---

## ✅ Checklist de Configuración

- [ ] Secret `SSH_HOST` configurado
- [ ] Secret `SSH_USER` configurado
- [ ] Secret `SSH_KEY` configurado (clave privada completa)
- [ ] Secret `SSH_PORT` configurado
- [ ] Clave pública agregada a `authorized_keys` en el servidor

---

## 🔧 Verificar en el Servidor

Antes de probar el despliegue, asegúrate de que la clave pública esté autorizada:

```bash
# En el servidor EC2
cat ~/.ssh/github_deploy.pub >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```

---

## 🧪 Probar el Despliegue

1. Haz un cambio pequeño en tu código
2. Commit y push:
   ```bash
   git add .
   git commit -m "Test deploy automático"
   git push origin main
   ```
3. Ve a GitHub → **Actions** → Verifica que el workflow se ejecute
4. Espera 2-3 minutos y verifica que el cambio esté en producción

---

## 📝 Resumen Rápido

| Secret Name | Value | Ejemplo |
|-------------|-------|---------|
| `SSH_HOST` | Dominio o IP | `emotive.g3nia.com` |
| `SSH_USER` | Usuario SSH | `ec2-user` |
| `SSH_KEY` | Clave privada completa | `-----BEGIN OPENSSH...` |
| `SSH_PORT` | Puerto SSH | `22` |

---

## ⚠️ Notas Importantes

1. **SSH_KEY**: Debe ser la clave **PRIVADA** completa, no la pública
2. **SSH_HOST**: Puedes usar el dominio o la IP. Si usas dominio, asegúrate de que apunte a tu EC2
3. **Seguridad**: Los secrets están encriptados en GitHub y solo se usan durante la ejecución del workflow
4. **Permisos**: La clave privada debe tener permisos 600 en el servidor: `chmod 600 ~/.ssh/github_deploy`

---

## 🆘 Si el Despliegue Falla

1. Verifica los logs en GitHub Actions
2. Verifica que la clave SSH sea correcta
3. Prueba conexión SSH manualmente desde tu máquina
4. Verifica que el usuario tenga permisos en `/var/www/laravel`

---

¡Listo! Una vez configurados estos 4 secrets, cada push a `main` desplegará automáticamente. 🚀

