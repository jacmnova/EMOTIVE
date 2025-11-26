# 🔒 Solucionar Problema SSL - "No seguro"

Si ves "No seguro" en el navegador, significa que el certificado SSL no está configurado. Sigue estos pasos:

## ✅ Solución Rápida (Recomendado)

### Opción 1: Usar el Script Automático

1. **Conéctate al servidor EC2:**
   ```bash
   ssh -i tu-key.pem ec2-user@TU_IP_PUBLICA
   ```

2. **Ve al directorio del proyecto:**
   ```bash
   cd /var/www/laravel
   ```

3. **Copia el script si no está:**
   ```bash
   # Si el script no está en el servidor, cópialo desde tu máquina local
   # O descárgalo desde el repositorio
   ```

4. **Ejecuta el script de configuración SSL:**
   ```bash
   sudo chmod +x configurar-ssl-emotive.sh
   sudo ./configurar-ssl-emotive.sh
   ```

El script automáticamente:
- ✅ Verifica DNS
- ✅ Instala Certbot si es necesario
- ✅ Configura Nginx
- ✅ Obtiene e instala el certificado SSL
- ✅ Configura redirección HTTP → HTTPS

---

## 🔧 Solución Manual (Si el script no funciona)

### Paso 1: Verificar que el dominio apunta al servidor

```bash
# En tu máquina local
dig emotive.fellipelli.com.br
# O
nslookup emotive.fellipelli.com.br
```

Debe mostrar la IP de tu servidor EC2.

### Paso 2: Instalar Certbot

```bash
# En el servidor EC2
# Para Amazon Linux 2023
sudo dnf install -y certbot python3-certbot-nginx

# Para Amazon Linux 2
sudo yum install -y certbot python3-certbot-nginx

# Para Ubuntu/Debian
sudo apt update
sudo apt install -y certbot python3-certbot-nginx
```

### Paso 3: Verificar configuración de Nginx

```bash
# Verificar que Nginx está corriendo
sudo systemctl status nginx

# Ver configuración actual
sudo cat /etc/nginx/conf.d/laravel.conf
# O si es Ubuntu/Debian:
sudo cat /etc/nginx/sites-available/laravel
```

Asegúrate de que tenga:
```nginx
server {
    listen 80;
    server_name emotive.fellipelli.com.br;
    # ... resto de la configuración
}
```

### Paso 4: Obtener certificado SSL

```bash
sudo certbot --nginx -d emotive.fellipelli.com.br
```

Sigue las instrucciones:
- Email: `desenvolvedor@fellipelli.com.br`
- Aceptar términos: `Y`
- Compartir email: `N` (o `Y` si quieres)
- Redirigir HTTP a HTTPS: `2` (recomendado)

### Paso 5: Verificar que funciona

```bash
# Verificar configuración de Nginx
sudo nginx -t

# Recargar Nginx
sudo systemctl reload nginx

# Ver certificado
sudo certbot certificates
```

### Paso 6: Probar en el navegador

1. Abre: `https://emotive.fellipelli.com.br`
2. Debe mostrar un candado verde 🔒
3. HTTP debe redirigir automáticamente a HTTPS

---

## ❌ Problemas Comunes

### Error: "Domain not pointing to this server"

**Solución:** Verifica que el DNS esté configurado correctamente:
```bash
# En tu proveedor de dominio, asegúrate de tener:
# Tipo: A
# Nombre: emotive
# Valor: IP_PUBLICA_DE_TU_EC2
```

### Error: "Port 80 is not open"

**Solución:** Abre el puerto 80 en el Security Group de EC2:
1. AWS Console → EC2 → Security Groups
2. Selecciona tu Security Group
3. Inbound Rules → Add Rule
4. Tipo: HTTP, Puerto: 80, Origen: 0.0.0.0/0

### Error: "Nginx configuration test failed"

**Solución:** Verifica la configuración:
```bash
sudo nginx -t
# Corrige los errores que aparezcan
sudo nano /etc/nginx/conf.d/laravel.conf
```

### El certificado se instaló pero sigue mostrando "No seguro"

**Solución:** 
1. Limpia la caché del navegador (Ctrl+Shift+Delete)
2. Verifica que estés accediendo por HTTPS (no HTTP)
3. Verifica el certificado:
   ```bash
   sudo certbot certificates
   ```

---

## 🔄 Renovación Automática

Certbot configura la renovación automática. Para verificar:

```bash
# Ver estado del timer
sudo systemctl status certbot.timer

# Probar renovación manual
sudo certbot renew --dry-run
```

---

## 📞 Si nada funciona

1. **Ver logs de Certbot:**
   ```bash
   sudo tail -f /var/log/letsencrypt/letsencrypt.log
   ```

2. **Ver logs de Nginx:**
   ```bash
   sudo tail -f /var/log/nginx/error.log
   ```

3. **Verificar que el puerto 443 está abierto:**
   ```bash
   sudo netstat -tlnp | grep 443
   ```

4. **Verificar configuración SSL en Nginx:**
   ```bash
   sudo grep -A 10 "listen 443" /etc/nginx/conf.d/laravel.conf
   ```

---

## ✅ Checklist Final

- [ ] DNS configurado correctamente
- [ ] Puertos 80 y 443 abiertos en Security Group
- [ ] Certbot instalado
- [ ] Nginx configurado con `server_name emotive.fellipelli.com.br`
- [ ] Certificado SSL obtenido e instalado
- [ ] Nginx recargado
- [ ] Sitio accesible en HTTPS con candado verde 🔒


