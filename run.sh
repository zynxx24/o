#!/bin/bash

# ═══════════════════════════════════════════════════
# CateringKu — Launcher Script
# Jalankan dari mana saja: bash run.sh [command]
# ═══════════════════════════════════════════════════

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
PROJECT_DIR="$SCRIPT_DIR/CateringKu"

# Cek apakah folder CateringKu ada
if [ ! -d "$PROJECT_DIR" ]; then
    echo "❌ Folder CateringKu tidak ditemukan di: $PROJECT_DIR"
    exit 1
fi

# Forward semua command ke CateringKu/run.sh
exec bash "$PROJECT_DIR/run.sh" "${@:-dev}"