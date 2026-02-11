#!/bin/bash
# Ejecutar tests del frontend
# Uso: ./run-tests.sh   o   bash run-tests.sh

set -e
cd "$(dirname "$0")"

# Instalar dependencias si no hay node_modules
if [ ! -d node_modules ]; then
  echo "Instalando dependencias..."
  npm install
fi

echo "Tests unitarios (Jest)..."
npm test -- --passWithNoTests --watchAll=false "$@"

echo ""
echo "Para tests E2E: npm run test:e2e"
echo "  (requiere: npx playwright install)"
