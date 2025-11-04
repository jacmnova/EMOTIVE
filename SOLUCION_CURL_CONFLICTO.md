# ⚠️ Solución: Conflicto con curl-minimal vs curl

## 🔍 El Problema

En Amazon Linux 2023, `curl-minimal` viene **preinstalado** y es suficiente para la mayoría de casos. Cuando el script intenta instalar `curl`, entra en conflicto con `curl-minimal`.

## ✅ Solución Inmediata

**Opción 1: Continuar sin instalar curl** (recomendado)

El `curl-minimal` que ya tienes instalado funciona perfectamente. Simplemente **omite la instalación de curl**:

```bash
# Instalar herramientas básicas (SIN curl)
sudo dnf install -y wget git unzip

# Continuar con el resto de la instalación...
```

**Opción 2: Remover curl-minimal y instalar curl completo** (solo si realmente necesitas las funciones adicionales)

```bash
# Remover curl-minimal
sudo dnf remove -y curl-minimal

# Instalar curl completo
sudo dnf install -y curl
```

⚠️ **Nota**: Esto generalmente NO es necesario. `curl-minimal` funciona para todas las operaciones comunes.

## 🚀 Continuar la Instalación

Después de resolver el conflicto, continúa con:

```bash
# Instalar PHP
sudo dnf install -y php php-fpm php-cli php-common php-mysqlnd \
    php-zip php-gd php-mbstring php-curl php-xml php-intl \
    php-bcmath php-opcache php-json

# Verificar que curl funciona
curl --version

# Continuar con Composer, Node.js, etc.
```

## 📋 Comandos Completos Corregidos

```bash
# 1. Instalar herramientas básicas (sin curl, ya viene preinstalado)
sudo dnf install -y wget git unzip

# 2. Verificar curl
curl --version  # Debería funcionar con curl-minimal

# 3. Continuar con PHP
sudo dnf install -y php php-fpm php-cli php-common php-mysqlnd \
    php-zip php-gd php-mbstring php-curl php-xml php-intl \
    php-bcmath php-opcache php-json

# 4. Instalar Composer (usará curl-minimal, funciona perfectamente)
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

## 🔧 El Script Ya Está Corregido

El script `install-ec2-amazon-linux.sh` ya está actualizado para:
- ✅ Verificar si curl está disponible antes de intentar instalarlo
- ✅ Continuar sin error si curl-minimal ya está instalado
- ✅ No intentar instalar curl si ya existe

Simplemente ejecuta el script nuevamente:

```bash
./install-ec2-amazon-linux.sh
```

O continúa manualmente con los comandos de arriba (omitiendo curl de la instalación).

