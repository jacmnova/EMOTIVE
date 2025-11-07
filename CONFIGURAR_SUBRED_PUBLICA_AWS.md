# 🌐 Configurar Subred Pública en AWS VPC

Guía paso a paso para configurar una subred pública en AWS VPC para tu instancia EC2.

---

## 📋 ¿Qué es una Subred Pública?

Una **subred pública** es una subred que tiene acceso directo a Internet a través de un **Internet Gateway**. Esto permite que tu instancia EC2 tenga una IP pública y pueda comunicarse con Internet.

---

## 🎯 Opción 1: Usar VPC por Defecto (Más Fácil - Recomendado para Principiantes)

Si es tu primera vez con AWS, la forma más fácil es usar el VPC por defecto que AWS crea automáticamente.

### Paso 1: Verificar VPC por Defecto

1. **Ve a AWS Console**: https://console.aws.amazon.com/vpc/
2. En el menú lateral, haz clic en **"Your VPCs"**
3. Busca un VPC que tenga el nombre **"default"** o que tenga **"172.31.0.0/16"** como CIDR
4. **Anota el ID del VPC** (ejemplo: `vpc-0123456789abcdef0`)

### Paso 2: Verificar Subredes

1. En el menú lateral, haz clic en **"Subnets"**
2. Busca subredes que pertenezcan al VPC por defecto
3. Verifica que tengan **"Auto-assign public IPv4 address"** habilitado:
   - Selecciona una subred
   - Ve a la pestaña **"Actions"** → **"Edit subnet settings"**
   - Asegúrate de que **"Enable auto-assign public IPv4 address"** esté marcado
   - Si no está marcado, márcalo y guarda

### Paso 3: Verificar Internet Gateway

1. En el menú lateral, haz clic en **"Internet Gateways"**
2. Debe haber un Internet Gateway conectado al VPC por defecto
3. Si no existe, crea uno (ver Opción 2, Paso 3)

### Paso 4: Verificar Route Table

1. En el menú lateral, haz clic en **"Route Tables"**
2. Busca la tabla de rutas asociada al VPC por defecto
3. Debe tener una ruta como:
   - **Destination**: `0.0.0.0/0`
   - **Target**: `igw-xxxxx` (Internet Gateway)
4. Si no existe esta ruta, agrégala (ver Opción 2, Paso 4)

**✅ Listo**: Ya puedes usar este VPC y sus subredes al crear tu instancia EC2.

---

## 🛠️ Opción 2: Crear VPC y Subred Pública desde Cero

Si prefieres crear tu propia configuración o no tienes un VPC por defecto:

### Paso 1: Crear VPC

1. **Ve a AWS Console**: https://console.aws.amazon.com/vpc/
2. Haz clic en **"Create VPC"**
3. **Configuración**:
   - **Name tag**: `emotive-vpc` (o el nombre que prefieras)
   - **IPv4 CIDR block**: `10.0.0.0/16` (o el rango que prefieras)
   - **IPv6 CIDR block**: No IPv6 (o déjalo como está)
   - **Tenancy**: Default
4. Haz clic en **"Create VPC"**
5. **Anota el ID del VPC** (ejemplo: `vpc-0123456789abcdef0`)

### Paso 2: Crear Subred Pública

1. En el menú lateral, haz clic en **"Subnets"**
2. Haz clic en **"Create subnet"**
3. **Configuración**:
   - **VPC ID**: Selecciona el VPC que acabas de crear
   - **Subnet name**: `emotive-public-subnet` 
   - **Availability Zone**: Selecciona una zona (ej: `us-east-1a`)
   - **IPv4 CIDR block**: `10.0.1.0/24` (dentro del rango del VPC)
4. Haz clic en **"Create subnet"**
5. **Habilitar IP pública automática**:
   - Selecciona la subred que acabas de crear
   - Ve a **"Actions"** → **"Edit subnet settings"**
   - Marca **"Enable auto-assign public IPv4 address"**
   - Haz clic en **"Save"**

### Paso 3: Crear y Conectar Internet Gateway

1. En el menú lateral, haz clic en **"Internet Gateways"**
2. Haz clic en **"Create internet gateway"**
3. **Configuración**:
   - **Name tag**: `emotive-igw`
4. Haz clic en **"Create internet gateway"**
5. **Conectar al VPC**:
   - Selecciona el Internet Gateway que acabas de crear
   - Haz clic en **"Actions"** → **"Attach to VPC"**
   - Selecciona tu VPC (`emotive-vpc`)
   - Haz clic en **"Attach internet gateway"**

### Paso 4: Configurar Route Table Pública

1. En el menú lateral, haz clic en **"Route Tables"**
2. Busca la tabla de rutas asociada a tu VPC (puede tener un nombre como `rtb-xxxxx`)
3. Selecciónala y haz clic en **"Edit routes"**
4. Haz clic en **"Add route"**
5. **Configuración**:
   - **Destination**: `0.0.0.0/0`
   - **Target**: Selecciona el Internet Gateway que creaste (`igw-xxxxx`)
6. Haz clic en **"Save changes"**
7. **Asociar subred pública**:
   - En la misma tabla de rutas, ve a la pestaña **"Subnet associations"**
   - Haz clic en **"Edit subnet associations"**
   - Marca la subred pública que creaste (`emotive-public-subnet`)
   - Haz clic en **"Save associations"**

### Paso 5: Verificar Configuración

1. **Verifica la subred**:
   - Ve a **"Subnets"**
   - Selecciona tu subred pública
   - Debe mostrar:
     - ✅ **Auto-assign public IPv4**: Yes
     - ✅ **Route table**: La tabla con ruta a Internet Gateway

2. **Verifica el Internet Gateway**:
   - Ve a **"Internet Gateways"**
   - Debe mostrar **"Attached"** con el nombre de tu VPC

3. **Verifica la tabla de rutas**:
   - Ve a **"Route Tables"**
   - Debe tener una ruta `0.0.0.0/0` → `igw-xxxxx`

---

## 🚀 Usar la Subred Pública al Crear EC2

### Al Crear Instancia EC2:

1. **En "Configure Instance"**:
   - **Network**: Selecciona tu VPC (`emotive-vpc`)
   - **Subnet**: Selecciona tu subred pública (`emotive-public-subnet`)

2. **En "Configure Security Group"**:
   - Asegúrate de permitir:
     - **SSH (22)**: Tu IP o `0.0.0.0/0`
     - **HTTP (80)**: `0.0.0.0/0`
     - **HTTPS (443)**: `0.0.0.0/0`

3. **En "Review and Launch"**:
   - Verifica que **"Auto-assign Public IP"** esté habilitado
   - Si no está, haz clic en **"Edit network interfaces"** y habilítalo

---

## 🔍 Verificar que la Subred es Pública

### Método 1: Desde la Consola

1. Ve a **"Subnets"**
2. Selecciona tu subred
3. Verifica:
   - ✅ **Auto-assign public IPv4**: `Yes`
   - ✅ **Route table** tiene ruta a Internet Gateway

### Método 2: Desde la CLI

```bash
# Listar subredes
aws ec2 describe-subnets --filters "Name=vpc-id,Values=vpc-xxxxx"

# Verificar si tiene IP pública automática
aws ec2 describe-subnets --subnet-ids subnet-xxxxx \
  --query 'Subnets[0].MapPublicIpOnLaunch'
# Debe retornar: true
```

---

## ⚠️ Problemas Comunes

### Problema 1: Instancia sin IP Pública

**Solución**:
1. Verifica que la subred tenga **"Auto-assign public IPv4"** habilitado
2. Si la instancia ya está creada:
   - Detén la instancia
   - Ve a **"Networking"** → **"Change subnet"**
   - Selecciona una subred pública
   - Inicia la instancia

### Problema 2: No Puedo Conectarme por SSH

**Solución**:
1. Verifica que el Security Group permita SSH desde tu IP
2. Verifica que la instancia tenga IP pública
3. Verifica que el Internet Gateway esté conectado al VPC
4. Verifica que la tabla de rutas tenga ruta a `0.0.0.0/0`

### Problema 3: No Puedo Acceder a Internet desde la Instancia

**Solución**:
1. Verifica que el Internet Gateway esté **"Attached"** al VPC
2. Verifica que la tabla de rutas tenga:
   - **Destination**: `0.0.0.0/0`
   - **Target**: `igw-xxxxx` (tu Internet Gateway)
3. Verifica que la subred esté asociada a la tabla de rutas correcta

---

## 📝 Resumen de Componentes Necesarios

Para que una subred sea pública, necesitas:

1. ✅ **VPC** con rango CIDR (ej: `10.0.0.0/16`)
2. ✅ **Subred** dentro del VPC con **"Auto-assign public IPv4"** habilitado
3. ✅ **Internet Gateway** creado y conectado al VPC
4. ✅ **Route Table** con ruta `0.0.0.0/0` → Internet Gateway
5. ✅ **Subred asociada** a la Route Table correcta

---

## 🎯 Recomendación para tu Caso

**Para `emotive.g3nia.com`**, te recomiendo:

1. **Usar el VPC por defecto** si es tu primera vez (Opción 1)
2. O crear un VPC dedicado si quieres más control (Opción 2)

**Configuración sugerida**:
- **VPC**: `10.0.0.0/16`
- **Subred pública**: `10.0.1.0/24` en `us-east-1a` (o tu región)
- **Internet Gateway**: Conectado al VPC
- **Route Table**: Con ruta a Internet Gateway

---

## ✅ Checklist Final

Antes de crear tu instancia EC2, verifica:

- [ ] VPC creado o identificado
- [ ] Subred pública creada con IP pública automática habilitada
- [ ] Internet Gateway creado y conectado al VPC
- [ ] Route Table configurada con ruta a Internet Gateway
- [ ] Subred asociada a la Route Table correcta
- [ ] Security Group configurado (puertos 22, 80, 443)

---

## 🔗 Recursos Adicionales

- [Documentación oficial de VPC](https://docs.aws.amazon.com/vpc/)
- [Guía de subredes públicas](https://docs.aws.amazon.com/vpc/latest/userguide/vpc-subnets.html)
- [Configuración de Internet Gateway](https://docs.aws.amazon.com/vpc/latest/userguide/VPC_Internet_Gateway.html)

---

¿Listo? Ahora puedes crear tu instancia EC2 en la subred pública y tendrá acceso a Internet. 🚀

