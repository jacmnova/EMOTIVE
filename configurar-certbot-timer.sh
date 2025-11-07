#!/usr/bin/env bash
set -euo pipefail

# Script para configurar timer de renovación automática de Certbot
# Ejecutar: chmod +x configurar-certbot-timer.sh && sudo ./configurar-certbot-timer.sh

echo "🔧 Configurando timer de renovación automática de Certbot..."

# Crear directorio si no existe
sudo mkdir -p /etc/systemd/system/certbot.timer.d

# Crear archivo de timer
sudo tee /etc/systemd/system/certbot.timer > /dev/null <<'EOF'
[Unit]
Description=Certbot Renewal Timer
Documentation=man:certbot(1)

[Timer]
OnCalendar=*-*-* 00,12:00:00
RandomizedDelaySec=3600
Persistent=true

[Install]
WantedBy=timers.target
EOF

# Crear archivo de servicio
sudo tee /etc/systemd/system/certbot.service > /dev/null <<'EOF'
[Unit]
Description=Certbot Renewal Service
Documentation=man:certbot(1)

[Service]
Type=oneshot
ExecStart=/usr/bin/certbot renew --quiet --no-self-upgrade
PrivateTmp=true
EOF

# Recargar systemd
sudo systemctl daemon-reload

# Habilitar y activar el timer
sudo systemctl enable certbot.timer
sudo systemctl start certbot.timer

# Verificar estado
echo ""
echo "✅ Timer configurado"
echo ""
echo "📊 Estado del timer:"
sudo systemctl status certbot.timer --no-pager -l

echo ""
echo "📅 Próxima ejecución programada:"
sudo systemctl list-timers certbot.timer --no-pager

echo ""
echo "✅ Certbot renovará automáticamente los certificados dos veces al día"
echo "   (a las 00:00 y 12:00 con un delay aleatorio de hasta 1 hora)"

