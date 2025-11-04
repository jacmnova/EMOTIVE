# ⚠️ Solución: "No match for argument: epel-release"

## 🔍 El Problema

El error indica que estás usando **Amazon Linux 2023**, que usa `dnf` y **NO tiene el paquete `epel-release`** disponible. Este paquete solo existe en Amazon Linux 2.

## ✅ Solución

**El script ya está corregido**, pero si estás ejecutándolo manualmente, simplemente **omite el comando de epel-release**.

### Comandos Corregidos para Amazon Linux 2023:

```bash
# Actualizar sistema
sudo dnf update -y

# Instalar herramientas básicas (SIN epel-release)
sudo dnf install -y wget curl git unzip

# Instalar PHP directamente
sudo dnf install -y php php-fpm php-cli php-common php-mysqlnd \
    php-zip php-gd php-mbstring php-curl php-xml php-intl \
    php-bcmath php-opcache php-json

# Continuar con el resto de la instalación...
```

### Si Estás Usando Amazon Linux 2:

```bash
# Entonces SÍ puedes usar epel-release
sudo yum install -y epel-release
```

## 📋 Diferencias: Amazon Linux 2 vs 2023

| Característica | Amazon Linux 2 | Amazon Linux 2023 |
|----------------|----------------|-------------------|
| Gestor paquetes | `yum` | `dnf` |
| EPEL disponible | ✅ Sí | ❌ No (no necesario) |
| Repositorios | extras, epel | nativos mejorados |

## 🚀 Continuar la Instalación

Si ya ejecutaste el script y falló en ese punto, simplemente **continúa con estos comandos**:

```bash
# Instalar herramientas básicas
sudo dnf install -y wget curl git unzip

# Instalar PHP
sudo dnf install -y php php-fpm php-cli php-common php-mysqlnd \
    php-zip php-gd php-mbstring php-curl php-xml php-intl \
    php-bcmath php-opcache php-json

# Verificar PHP
php -v

# Continuar con Composer, Node.js, etc.
```

El script actualizado ya maneja esto automáticamente. Si ejecutas el script nuevamente, debería funcionar correctamente.

