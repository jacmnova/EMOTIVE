#!/usr/bin/env bash
set -euo pipefail

# Script para limpiar la base de datos y restaurar solo usuarios iniciales
# Uso: chmod +x limpiar-base-datos.sh && ./limpiar-base-datos.sh

echo "🗑️  Limpiando base de datos y restaurando usuarios iniciales..."
echo ""

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Verificar que estamos en el directorio correcto
APP_DIR="/var/www/laravel"
if [ ! -d "$APP_DIR" ]; then
    echo -e "${RED}❌ Directorio $APP_DIR no existe${NC}"
    echo "   Ejecuta este script desde el servidor EC2"
    exit 1
fi

cd "$APP_DIR" || exit 1

# Verificar que existe .env
if [ ! -f ".env" ]; then
    echo -e "${RED}❌ Archivo .env no existe${NC}"
    exit 1
fi

# Confirmación
echo -e "${YELLOW}⚠️  ADVERTENCIA: Este script eliminará TODOS los datos de la base de datos${NC}"
echo "   Solo se conservarán los usuarios iniciales con contraseña admin1234"
echo ""
read -p "¿Estás seguro de que quieres continuar? (escribe 'SI' para confirmar): " -r
if [ "$REPLY" != "SI" ]; then
    echo "Operación cancelada"
    exit 0
fi

echo ""
echo "🔄 Limpiando base de datos..."

# Obtener credenciales de la base de datos desde .env
DB_DATABASE=$(grep "^DB_DATABASE=" .env | cut -d '=' -f2 | tr -d '"' | tr -d "'")
DB_USERNAME=$(grep "^DB_USERNAME=" .env | cut -d '=' -f2 | tr -d '"' | tr -d "'")
DB_PASSWORD=$(grep "^DB_PASSWORD=" .env | cut -d '=' -f2 | tr -d '"' | tr -d "'")
DB_HOST=$(grep "^DB_HOST=" .env | cut -d '=' -f2 | tr -d '"' | tr -d "'" || echo "127.0.0.1")

if [ -z "$DB_DATABASE" ] || [ -z "$DB_USERNAME" ]; then
    echo -e "${RED}❌ No se pudieron leer las credenciales de la base de datos desde .env${NC}"
    exit 1
fi

# Verificar que MySQL está instalado
if ! command -v mysql &> /dev/null; then
    echo -e "${RED}❌ MySQL client no está instalado${NC}"
    echo "   Instala MySQL client: sudo dnf install mysql -y (Amazon Linux) o sudo apt install mysql-client -y (Ubuntu)"
    exit 1
fi

# Lista de tablas a limpiar (excluyendo migrations y otras tablas del sistema)
TABLAS_LIMPIAR=(
    "users"
    "clientes"
    "participantes"
    "formularios"
    "variaveis"
    "perguntas"
    "variavel_pergunta"
    "usuario_formulario"
    "calculos"
    "form_burnout"
    "var_burnout"
    "perguntas_burnout"
    "var_pergunta_burnout"
    "etapas"
    "password_reset_tokens"
    "sessions"
    "jobs"
    "failed_jobs"
    "cache"
    "cache_locks"
)

echo "📋 Tablas que se limpiarán:"
for tabla in "${TABLAS_LIMPIAR[@]}"; do
    echo "   - $tabla"
done
echo ""

# Función para ejecutar comandos MySQL
execute_mysql() {
    local query="$1"
    MYSQL_PWD="$DB_PASSWORD" mysql -u"$DB_USERNAME" -h"$DB_HOST" "$DB_DATABASE" -e "$query" 2>/dev/null || true
}

# Desactivar verificación de claves foráneas temporalmente
echo "🔓 Desactivando verificación de claves foráneas..."
execute_mysql "SET FOREIGN_KEY_CHECKS=0;"

# Limpiar tablas
echo "🗑️  Eliminando datos de las tablas..."
for tabla in "${TABLAS_LIMPIAR[@]}"; do
    echo "   Limpiando: $tabla"
    if execute_mysql "TRUNCATE TABLE \`$tabla\`;"; then
        echo -e "${GREEN}     ✅ $tabla limpiada${NC}"
    else
        echo -e "${YELLOW}     ⚠️  No se pudo limpiar $tabla (puede que no exista)${NC}"
    fi
done

# Reactivar verificación de claves foráneas
echo "🔒 Reactivando verificación de claves foráneas..."
execute_mysql "SET FOREIGN_KEY_CHECKS=1;"

echo ""
echo "🌱 Restaurando datos iniciales..."

# Crear seeder temporal con contraseña admin1234
cat > database/seeders/UsuariosInicialesSeeder.php <<'SEEDER_EOF'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuariosInicialesSeeder extends Seeder
{
    public function run()
    {
        // Contraseña: admin1234
        $password = Hash::make('admin1234');

        $usuarios = [
            [
                'name' => 'Arley Humberto Rueda Rincon', 
                'email' => 'wheelkorner@gmail.com',
                'password' => $password, 
                'email_verified_at' => now(), 
                'created_at' => now(), 
                'updated_at' => now(),
                'sa' => 1, 
                'admin' => 1,
                'usuario' => 1,
                'gestor' => 1, 
                'ativo' => 1,
                'cliente_id' => null
            ],
            [
                'name' => 'Administrador', 
                'email' => 'desenvolvedor@fellipelli.com.br', 
                'password' => $password, 
                'email_verified_at' => now(), 
                'created_at' => now(), 
                'updated_at' => now(),
                'sa' => 0, 
                'admin' => 1, 
                'usuario' => 1, 
                'gestor' => 1, 
                'ativo' => 1, 
                'cliente_id' => null
            ],
            [
                'name' => 'Gestor', 
                'email' => 'arley.rincon@fellipelli.com.br', 
                'password' => $password, 
                'email_verified_at' => now(), 
                'created_at' => now(), 
                'updated_at' => now(),
                'sa' => 0, 
                'admin' => 0, 
                'usuario' => 1, 
                'gestor' => 1, 
                'ativo' => 1, 
                'cliente_id' => 1
            ],
        ];

        DB::table('users')->insert($usuarios);
        
        // Crear cliente inicial
        DB::table('clientes')->insert([
            'usuario_id' => 3, 
            'tipo' => 'cnpj',
            'cpf_cnpj' => '07792897000182',
            'nome_fantasia' => 'FELLIPELLI',
            'razao_social' => 'FELLIPELLI INSTRUMENTOS DE DIAGNOSTICO LTDA.',
            'email' => 'adriana.fellipelli@fellipelli.com.br',
            'contato' => 'Adriana Fellipelli',
            'telefone' => '1142807100', 
            'created_at' => now(),
            'updated_at' => now(),
            'ativo' => 1
        ]);
    }
}
SEEDER_EOF

# Ejecutar seeders necesarios
echo "   Ejecutando seeders..."
php artisan db:seed --class=UsuariosInicialesSeeder --force
php artisan db:seed --class=CalculoSeeder --force
php artisan db:seed --class=FormBurnOutSeeder --force
php artisan db:seed --class=VarBurnOutSeeder --force
php artisan db:seed --class=PerguntasBurnOutSeeder --force
php artisan db:seed --class=VarPerguntaBurnOutSeeder --force
php artisan db:seed --class=EtapaSeeder --force

# Eliminar seeder temporal
rm -f database/seeders/UsuariosInicialesSeeder.php

echo ""
echo -e "${GREEN}✅ Base de datos limpiada y usuarios iniciales restaurados${NC}"
echo ""
echo "📋 Usuarios creados (contraseña: admin1234):"
echo "   1. Arley Humberto Rueda Rincon (wheelkorner@gmail.com) - SA/Admin"
echo "   2. Administrador (desenvolvedor@fellipelli.com.br) - Admin"
echo "   3. Gestor (arley.rincon@fellipelli.com.br) - Gestor"
echo ""
echo "🔐 Todos los usuarios tienen la contraseña: admin1234"
echo ""

