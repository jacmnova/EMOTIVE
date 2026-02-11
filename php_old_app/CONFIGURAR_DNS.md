# 🌐 Configurar DNS para emotive.fellipelli.com.br

## ⚠️ Problema Detectado

El dominio `emotive.fellipelli.com.br` está apuntando a una IP incorrecta:
- **IP actual del DNS**: `54.90.186.25` ❌
- **IP correcta del servidor**: `18.188.218.143` ✅

## 📋 Pasos para Corregir DNS

### Opción 1: GoDaddy

1. **Accede a GoDaddy:**
   - Ve a https://www.godaddy.com/
   - Inicia sesión
   - Ve a "Mis Productos" → "Dominios"
   - Busca `fellipelli.com.br`

2. **Editar DNS:**
   - Clic en "DNS" o "Administrar DNS"
   - Busca el registro tipo **A** con nombre `emotive`
   - Si existe, edítalo
   - Si no existe, crea uno nuevo

3. **Configurar registro:**
   - **Tipo**: `A`
   - **Nombre/Host**: `emotive`
   - **Valor/Points to**: `18.188.218.143`
   - **TTL**: `600` (10 minutos) o `3600` (1 hora)
   - **Guardar**

### Opción 2: Registro.br

1. **Accede a Registro.br:**
   - Ve a https://registro.br/
   - Inicia sesión
   - Busca el dominio `fellipelli.com.br`

2. **Editar DNS:**
   - Ve a "DNS" o "Zona DNS"
   - Busca el registro tipo **A** con nome `emotive`
   - Edita o crea el registro

3. **Configurar:**
   - **Tipo**: `A`
   - **Nome**: `emotive`
   - **Valor**: `18.188.218.143`
   - **TTL**: `3600`
   - **Salvar**

### Opción 3: Cloudflare

1. **Accede a Cloudflare:**
   - Ve a https://dash.cloudflare.com/
   - Selecciona el dominio `fellipelli.com.br`

2. **Editar DNS:**
   - Ve a "DNS" → "Records"
   - Busca el registro tipo **A** con nombre `emotive`
   - Edita o crea

3. **Configurar:**
   - **Type**: `A`
   - **Name**: `emotive`
   - **IPv4 address**: `18.188.218.143`
   - **Proxy status**: Desactivado (nube gris) para SSL
   - **TTL**: Auto
   - **Save**

### Opción 4: AWS Route 53

1. **Accede a Route 53:**
   - Ve a https://console.aws.amazon.com/route53/
   - Selecciona "Hosted zones"
   - Busca `fellipelli.com.br`

2. **Editar registro:**
   - Busca el registro tipo **A** con nombre `emotive`
   - Edita o crea

3. **Configurar:**
   - **Record name**: `emotive`
   - **Record type**: `A`
   - **Value**: `18.188.218.143`
   - **TTL**: `300`
   - **Save**

---

## ⏱️ Tiempo de Propagación

Después de cambiar el DNS:
- **Propagación mínima**: 5-10 minutos
- **Propagación típica**: 30 minutos - 2 horas
- **Propagación máxima**: 24-48 horas (raro)

---

## ✅ Verificar que el DNS está Correcto

### Desde tu máquina local:

```bash
# Opción 1: dig
dig emotive.fellipelli.com.br

# Opción 2: nslookup
nslookup emotive.fellipelli.com.br

# Opción 3: host
host emotive.fellipelli.com.br
```

**Resultado esperado:**
```
emotive.fellipelli.com.br has address 18.188.218.143
```

### Desde el servidor:

```bash
# En el servidor EC2
dig emotive.fellipelli.com.br
```

Debe mostrar: `18.188.218.143`

---

## 🔍 Verificar en Tiempo Real

Puedes usar herramientas online:
- https://www.whatsmydns.net/#A/emotive.fellipelli.com.br
- https://dnschecker.org/#A/emotive.fellipelli.com.br

---

## ⚠️ Importante

1. **No ejecutes el script de SSL hasta que el DNS esté correcto**
   - Certbot necesita verificar que el dominio apunta al servidor
   - Si el DNS no apunta correctamente, la verificación fallará

2. **Verifica que el puerto 80 esté abierto**
   - En AWS EC2 → Security Groups
   - Debe permitir tráfico HTTP (puerto 80) desde `0.0.0.0/0`

3. **Espera la propagación DNS antes de continuar**
   - Usa `dig` o `nslookup` para verificar
   - Solo cuando muestre `18.188.218.143`, continúa con SSL

---

## 📝 Checklist

- [ ] Accedí al panel DNS de mi proveedor
- [ ] Encontré o creé el registro A para `emotive`
- [ ] Cambié el valor a `18.188.218.143`
- [ ] Guardé los cambios
- [ ] Esperé 10-30 minutos
- [ ] Verifiqué con `dig emotive.fellipelli.com.br`
- [ ] Confirmé que muestra `18.188.218.143`
- [ ] Ahora puedo ejecutar el script de SSL

---

## 🚀 Después de Corregir DNS

Una vez que el DNS esté correcto:

```bash
# En el servidor EC2
cd /var/www/laravel
sudo ./configurar-ssl-emotive.sh
```

Ahora debería funcionar sin problemas. ✅

