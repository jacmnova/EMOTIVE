# 📋 Cómo Ver Logs - Guía Completa

## 🔍 Logs de Laravel (Aplicación)

### Ver Logs de Laravel
```bash
cd /var/www/laravel/EMOTIVE

# Ver últimas 50 líneas
tail -50 storage/logs/laravel.log

# Ver últimas 100 líneas
tail -100 storage/logs/laravel.log

# Ver en tiempo real (seguimiento)
tail -f storage/logs/laravel.log

# Ver desde el inicio del archivo
head -100 storage/logs/laravel.log

# Ver solo errores
grep -i error storage/logs/laravel.log | tail -50

# Ver solo excepciones
grep -i exception storage/logs/laravel.log | tail -50
```

### Buscar Errores Específicos
```bash
cd /var/www/laravel/EMOTIVE

# Buscar errores de hoy
grep "$(date +%Y-%m-%d)" storage/logs/laravel.log | grep -i error

# Buscar por texto específico
grep "No application encryption key" storage/logs/laravel.log

# Ver errores de las últimas 2 horas
tail -1000 storage/logs/laravel.log | grep -i error
```

## 🌐 Logs de Nginx

### Ver Logs de Nginx
```bash
# Log de errores
sudo tail -50 /var/log/nginx/error.log

# Log de acceso
sudo tail -50 /var/log/nginx/access.log

# Ver en tiempo real
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log

# Ver solo errores recientes
sudo grep -i error /var/log/nginx/error.log | tail -50
```

## 🐘 Logs de PHP-FPM

### Ver Logs de PHP-FPM
```bash
# Log principal
sudo tail -50 /var/log/php-fpm/error.log

# O usando journalctl
sudo journalctl -u php-fpm -n 50

# Ver en tiempo real
sudo tail -f /var/log/php-fpm/error.log

# O
sudo journalctl -u php-fpm -f
```

## 🗄️ Logs de MySQL/MariaDB

### Ver Logs de MySQL
```bash
# Log de errores
sudo tail -50 /var/log/mysqld.log

# O si es MariaDB
sudo tail -50 /var/log/mariadb/mariadb.log

# Ver en tiempo real
sudo tail -f /var/log/mysqld.log

# Ver contraseñas temporales
sudo grep "temporary password" /var/log/mysqld.log
```

## 📊 Ver Todos los Logs Relevantes

### Script para Ver Todos los Logs
```bash
# Crear script
cat > ~/ver-logs.sh <<'EOF'
#!/bin/bash
echo "=== LOGS DE LARAVEL ==="
tail -20 /var/www/laravel/EMOTIVE/storage/logs/laravel.log
echo ""
echo "=== LOGS DE NGINX (ERRORES) ==="
sudo tail -20 /var/log/nginx/error.log
echo ""
echo "=== LOGS DE PHP-FPM ==="
sudo tail -20 /var/log/php-fpm/error.log 2>/dev/null || sudo journalctl -u php-fpm -n 20
echo ""
echo "=== LOGS DE MYSQL ==="
sudo tail -20 /var/log/mysqld.log 2>/dev/null || echo "No disponible"
EOF

chmod +x ~/ver-logs.sh

# Ejecutar
~/ver-logs.sh
```

## 🔍 Comandos Útiles para Logs

### Ver Logs por Fecha
```bash
# Ver logs de hoy
grep "$(date +%Y-%m-%d)" /var/www/laravel/EMOTIVE/storage/logs/laravel.log

# Ver logs de una fecha específica
grep "2025-11-03" /var/www/laravel/EMOTIVE/storage/logs/laravel.log
```

### Ver Tamaño de Logs
```bash
# Ver tamaño de archivos de log
ls -lh /var/www/laravel/EMOTIVE/storage/logs/
ls -lh /var/log/nginx/
ls -lh /var/log/php-fpm/
```

### Limpiar Logs Antiguos
```bash
# Limpiar log de Laravel (mantener últimas 1000 líneas)
cd /var/www/laravel/EMOTIVE
tail -1000 storage/logs/laravel.log > storage/logs/laravel.log.tmp
mv storage/logs/laravel.log.tmp storage/logs/laravel.log
```

### Ver Últimas Errores de Todos los Logs
```bash
echo "=== ÚLTIMOS ERRORES LARAVEL ==="
tail -30 /var/www/laravel/EMOTIVE/storage/logs/laravel.log | grep -i error
echo ""
echo "=== ÚLTIMOS ERRORES NGINX ==="
sudo tail -30 /var/log/nginx/error.log | grep -i error
echo ""
echo "=== ÚLTIMOS ERRORES PHP-FPM ==="
sudo tail -30 /var/log/php-fpm/error.log 2>/dev/null | grep -i error || echo "No disponible"
```

## 📱 Ver Logs en Tiempo Real (Múltiples)

```bash
# Ver logs de Laravel y Nginx simultáneamente
tail -f /var/www/laravel/EMOTIVE/storage/logs/laravel.log /var/log/nginx/error.log
```

## 🎯 Comandos Rápidos Más Usados

```bash
# Ver último error de Laravel
tail -50 /var/www/laravel/EMOTIVE/storage/logs/laravel.log | grep -A 10 -i error | tail -20

# Ver logs mientras pruebas la app
tail -f /var/www/laravel/EMOTIVE/storage/logs/laravel.log

# Ver errores de Nginx
sudo tail -f /var/log/nginx/error.log

# Ver todos los logs importantes
tail -20 /var/www/laravel/EMOTIVE/storage/logs/laravel.log && echo "---" && sudo tail -20 /var/log/nginx/error.log
```

## 📋 Ubicaciones de Logs

| Servicio | Ubicación del Log |
|----------|-------------------|
| Laravel | `/var/www/laravel/EMOTIVE/storage/logs/laravel.log` |
| Nginx Errores | `/var/log/nginx/error.log` |
| Nginx Acceso | `/var/log/nginx/access.log` |
| PHP-FPM | `/var/log/php-fpm/error.log` |
| MySQL | `/var/log/mysqld.log` |
| MariaDB | `/var/log/mariadb/mariadb.log` |
| Sistema | `journalctl -u nombre-servicio` |

## 🔥 Ver Errores en Tiempo Real (Mientras Pruebas)

```bash
# Terminal 1: Ver logs de Laravel
tail -f /var/www/laravel/EMOTIVE/storage/logs/laravel.log

# Terminal 2: Ver logs de Nginx
sudo tail -f /var/log/nginx/error.log

# Terminal 3: Probar la aplicación
curl http://localhost
```

¡Usa estos comandos para diagnosticar problemas! 🔍

