#!/bin/bash
# Ejecutar tests del backend
# Uso: ./run-tests.sh   o   bash run-tests.sh

set -e
cd "$(dirname "$0")"

# Crear venv si no existe
if [ ! -d .venv ]; then
  echo "Creando entorno virtual..."
  python3 -m venv .venv
fi

# Activar y instalar dependencias
source .venv/bin/activate 2>/dev/null || . .venv/Scripts/activate 2>/dev/null
pip install -q -r requirements.txt

# Ejecutar tests
echo "Ejecutando tests..."
python -m pytest tests/ -v --tb=short "$@"
