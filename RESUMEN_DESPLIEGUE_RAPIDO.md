# ⚡ Resumen Rápido - Despliegue EC2

## 🎯 Objetivo
Desplegar `emotive.g3nia.com` en EC2 con SSL y despliegue automático.

---

## 📋 Checklist Rápido

### 1️⃣ AWS EC2 (15 min)
- [ ] Crear instancia EC2 (Amazon Linux 2023 o Ubuntu 22.04)
- [ ] Configurar Security Group (puertos 22, 80, 443)
- [ ] Obtener IP pública
- [ ] Conectarse: `ssh -i key.pem ec2-user@IP`

### 2️⃣ GoDaddy DNS (5 min)
- [ ] Ir a GoDaddy → DNS de `g3nia.com`
- [ ] Agregar registro A:
  - **Nombre**: `emotive`
  - **Valor**: `IP_DE_EC2`
  - **TTL**: `600`
- [ ] Esperar 5-30 min para propagación

### 3️⃣ Instalación en Servidor (20 min)
```bash
# En el servidor EC2
cd ~
git clone https://github.com/TU_USUARIO/TU_REPO.git temp
cd temp
chmod +x install-ec2-amazon-linux.sh
./install-ec2-amazon-linux.sh
```

### 4️⃣ Base de Datos (5 min)
```bash
sudo mysql_secure_installation
sudo mysql -u root -p
```
```sql
CREATE DATABASE laravel_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'laravel_user'@'localhost' IDENTIFIED BY 'PASSWORD_SEGURO';
GRANT ALL PRIVILEGES ON laravel_db.* TO 'laravel_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 5️⃣ Clonar Repositorio (2 min)
```bash
sudo mkdir -p /var/www/laravel
sudo chown -R ec2-user:ec2-user /var/www/laravel
cd /var/www/laravel
git clone https://github.com/TU_USUARIO/TU_REPO.git .
```

### 6️⃣ Configurar Nginx y SSL (10 min)
```bash
cd /var/www/laravel
chmod +x configurar-nginx-ssl.sh
sudo ./configurar-nginx-ssl.sh

# Instalar Certbot
sudo dnf install -y certbot python3-certbot-nginx  # Amazon Linux
# O: sudo apt install -y certbot python3-certbot-nginx  # Ubuntu

# Obtener SSL (espera a que DNS propague primero)
sudo certbot --nginx -d emotive.g3nia.com
```

### 7️⃣ Configurar .env (5 min)
```bash
cd /var/www/laravel
cp .env.example .env
nano .env
```
```env
APP_URL=https://emotive.g3nia.com
APP_ENV=production
APP_DEBUG=false

DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=PASSWORD_SEGURO
```
```bash
php artisan key:generate
chmod -R 775 storage bootstrap/cache
php artisan storage:link
```

### 8️⃣ GitHub Actions (10 min)
```bash
# En el servidor, generar clave SSH
ssh-keygen -t rsa -b 4096 -C "github-actions" -f ~/.ssh/github_deploy -N ""
cat ~/.ssh/github_deploy.pub >> ~/.ssh/authorized_keys
cat ~/.ssh/github_deploy  # Copiar clave PRIVADA completa
```

**En GitHub**:
1. Settings → Secrets → Actions
2. Agregar:
   - `SSH_HOST`: `emotive.g3nia.com` (o IP)
   - `SSH_USER`: `ec2-user`
   - `SSH_KEY`: (clave privada copiada)
   - `SSH_PORT`: `22`

### 9️⃣ Primer Despliegue (5 min)
```bash
cd /var/www/laravel
chmod +x deploy.sh
./deploy.sh
```

El script ejecutará automáticamente:
- ✅ Migrations
- ✅ Seeders
- ✅ Factories (solo en primer despliegue)
- ✅ Optimizaciones

### 🔟 Verificar (2 min)
- [ ] Abrir: https://emotive.g3nia.com
- [ ] Verificar SSL (candado verde)
- [ ] Probar login
- [ ] Hacer push a `main` y verificar despliegue automático

---

## 🚀 Despliegue Automático

Cada vez que hagas:
```bash
git push origin main
```

GitHub Actions ejecutará automáticamente:
1. Pull del código
2. Instalación de dependencias
3. Compilación de assets
4. Migrations
5. Seeders
6. Optimizaciones
7. Reinicio de servicios

---

## 📚 Documentación Completa

Ver `GUIA_DESPLIEGUE_COMPLETA_EC2.md` para detalles completos.

---

## ⚠️ Problemas Comunes

**502 Bad Gateway**:
```bash
sudo systemctl restart php-fpm nginx
```

**Permisos**:
```bash
sudo chown -R ec2-user:ec2-user /var/www/laravel
chmod -R 775 storage bootstrap/cache
```

**SSL no funciona**:
- Verifica que DNS propague: `dig emotive.g3nia.com`
- Espera 30 minutos y vuelve a intentar `certbot`

---

## ✅ ¡Listo!

Tu aplicación se desplegará automáticamente con cada push a `main`. 🎉

