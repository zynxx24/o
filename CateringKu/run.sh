#!/usr/bin/env bash
# ╔══════════════════════════════════════════════════════════╗
# ║  CateringKu — Script Menjalankan Aplikasi               ║
# ║  Penggunaan: bash run.sh [command]                       ║
# ║                                                          ║
# ║  Commands:                                               ║
# ║    dev      → Jalankan dev server (PHP + Vite)           ║
# ║    install  → Install semua dependency                   ║
# ║    setup    → Setup awal (install + migrate + seed)      ║
# ║    migrate  → Jalankan migrasi database                  ║
# ║    seed     → Seed database dengan data test             ║
# ║    fresh    → Fresh migrate + seed (HAPUS SEMUA DATA)    ║
# ║    build    → Build production assets                    ║
# ║    clear    → Clear semua cache                          ║
# ║    test     → Jalankan test suite                        ║
# ║    stop     → Hentikan semua proses                      ║
# ╚══════════════════════════════════════════════════════════╝

set -e

# ─── Auto-cd ke folder project ───────────────────────────
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

# ─── Colors ──────────────────────────────────────────────
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color
BOLD='\033[1m'

# ─── Helpers ─────────────────────────────────────────────
info()    { echo -e "${BLUE}[INFO]${NC} $1"; }
success() { echo -e "${GREEN}[✓]${NC} $1"; }
warn()    { echo -e "${YELLOW}[!]${NC} $1"; }
error()   { echo -e "${RED}[✗]${NC} $1"; }

banner() {
    echo -e "${CYAN}"
    echo "  ╔═══════════════════════════════════════╗"
    echo "  ║     🍽️  CateringKu Dev Server          ║"
    echo "  ║     Platform Katering Online           ║"
    echo "  ╚═══════════════════════════════════════╝"
    echo -e "${NC}"
}

# ─── Check Prerequisites ────────────────────────────────
check_php() {
    if ! command -v php &> /dev/null; then
        error "PHP tidak ditemukan! Install PHP 8.2+ terlebih dahulu."
        exit 1
    fi
}

check_node() {
    if ! command -v node &> /dev/null; then
        error "Node.js tidak ditemukan! Install Node.js 18+ terlebih dahulu."
        exit 1
    fi
}

check_composer() {
    if ! command -v composer &> /dev/null; then
        error "Composer tidak ditemukan! Install Composer terlebih dahulu."
        exit 1
    fi
}

check_env() {
    if [ ! -f .env ]; then
        warn "File .env tidak ditemukan. Membuat dari .env.example..."
        if [ -f .env.example ]; then
            cp .env.example .env
            php artisan key:generate --force
            success ".env berhasil dibuat dengan APP_KEY baru"
        else
            error ".env.example tidak ditemukan!"
            exit 1
        fi
    fi
}

# ─── Commands ────────────────────────────────────────────

cmd_install() {
    info "📦 Installing dependencies..."
    echo ""

    check_composer
    check_node

    info "Installing PHP dependencies (Composer)..."
    composer install --no-interaction --prefer-dist
    success "Composer dependencies installed"
    echo ""

    info "Installing Node.js dependencies (NPM)..."
    npm install
    success "NPM dependencies installed"
    echo ""

    check_env

    success "Semua dependency berhasil diinstall! 🎉"
}

cmd_setup() {
    banner
    info "🔧 Setup awal CateringKu..."
    echo ""

    cmd_install
    echo ""

    info "🗄️  Menjalankan migrasi database..."
    php artisan migrate --force
    success "Migrasi selesai"
    echo ""

    info "🌱 Seeding database dengan data test..."
    php artisan db:seed --class=FullTestSeeder --force
    success "Seeding selesai"
    echo ""

    echo -e "${GREEN}${BOLD}"
    echo "  ╔═══════════════════════════════════════════════════╗"
    echo "  ║  ✅ Setup Selesai!                                ║"
    echo "  ║                                                   ║"
    echo "  ║  Akun Test:                                       ║"
    echo "  ║  👤 Admin  : admin@cateringku.com / admin123      ║"
    echo "  ║  🏪 Vendor : vendor@cateringku.com / vendor123    ║"
    echo "  ║  🙋 User   : user@cateringku.com / user123       ║"
    echo "  ║                                                   ║"
    echo "  ║  Jalankan: bash run.sh dev                        ║"
    echo "  ╚═══════════════════════════════════════════════════╝"
    echo -e "${NC}"
}

cmd_dev() {
    banner
    check_php
    check_node
    check_env

    info "🚀 Menjalankan CateringKu development server..."
    echo ""
    echo -e "  ${CYAN}Laravel${NC}  → ${BOLD}http://localhost:8000${NC}"
    echo -e "  ${CYAN}Vite HMR${NC} → ${BOLD}http://localhost:5173${NC}"
    echo ""
    info "Tekan ${BOLD}Ctrl+C${NC} untuk menghentikan kedua server"
    echo ""

    # Clear cache sebelum dev
    php artisan config:clear 2>/dev/null || true
    php artisan route:clear 2>/dev/null || true
    php artisan view:clear 2>/dev/null || true

    # Jalankan PHP + Vite secara bersamaan
    # Jika 'concurrently' tersedia, gunakan itu
    if npx --yes concurrently --version &>/dev/null; then
        npx concurrently \
            --names "PHP,Vite" \
            --prefix-colors "magenta,cyan" \
            --kill-others \
            "php artisan serve --host=0.0.0.0 --port=8000" \
            "npm run dev"
    else
        # Fallback: jalankan di background
        php artisan serve --host=0.0.0.0 --port=8000 &
        PHP_PID=$!

        npm run dev &
        VITE_PID=$!

        # Trap Ctrl+C untuk kill kedua proses
        trap "kill $PHP_PID $VITE_PID 2>/dev/null; echo ''; success 'Server dihentikan.'; exit 0" INT TERM

        wait $PHP_PID $VITE_PID
    fi
}

cmd_migrate() {
    check_php
    info "🗄️  Menjalankan migrasi..."
    php artisan migrate
    success "Migrasi selesai"
}

cmd_seed() {
    check_php
    info "🌱 Seeding database..."
    php artisan db:seed --class=FullTestSeeder
    success "Seeding selesai"
    echo ""
    echo -e "  ${CYAN}Akun Test:${NC}"
    echo -e "  👤 Admin  : admin@cateringku.com / admin123"
    echo -e "  🏪 Vendor : vendor@cateringku.com / vendor123"
    echo -e "  🙋 User   : user@cateringku.com / user123"
}

cmd_fresh() {
    check_php
    warn "⚠️  Ini akan MENGHAPUS SEMUA DATA dan membuat ulang database!"
    read -p "Lanjutkan? (y/N): " confirm
    if [ "$confirm" != "y" ] && [ "$confirm" != "Y" ]; then
        info "Dibatalkan."
        exit 0
    fi
    info "🔄 Fresh migrate + seed..."
    php artisan migrate:fresh --seed --seeder=FullTestSeeder --force
    success "Database berhasil di-reset dan di-seed"
}

cmd_build() {
    check_node
    info "🏗️  Building production assets..."
    npm run build
    success "Build selesai! Assets tersedia di public/build/"
}

cmd_clear() {
    check_php
    info "🧹 Membersihkan semua cache..."
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    php artisan cache:clear
    php artisan event:clear
    success "Semua cache berhasil dihapus"
}

cmd_test() {
    check_php
    info "🧪 Menjalankan test suite..."
    php artisan test
}

cmd_stop() {
    info "🛑 Menghentikan proses..."
    # Kill PHP artisan serve
    pkill -f "artisan serve" 2>/dev/null && success "PHP server dihentikan" || warn "PHP server tidak berjalan"
    # Kill Vite
    pkill -f "vite" 2>/dev/null && success "Vite server dihentikan" || warn "Vite server tidak berjalan"
}

cmd_help() {
    banner
    echo -e "${BOLD}Penggunaan:${NC} bash run.sh [command]"
    echo ""
    echo -e "${BOLD}Commands:${NC}"
    echo -e "  ${GREEN}dev${NC}       Jalankan dev server (PHP + Vite)"
    echo -e "  ${GREEN}install${NC}   Install semua dependency (Composer + NPM)"
    echo -e "  ${GREEN}setup${NC}     Setup awal lengkap (install + migrate + seed)"
    echo -e "  ${GREEN}migrate${NC}   Jalankan migrasi database"
    echo -e "  ${GREEN}seed${NC}      Seed database dengan akun test"
    echo -e "  ${GREEN}fresh${NC}     Fresh migrate + seed ${RED}(HAPUS SEMUA DATA)${NC}"
    echo -e "  ${GREEN}build${NC}     Build production assets"
    echo -e "  ${GREEN}clear${NC}     Clear semua cache Laravel"
    echo -e "  ${GREEN}test${NC}      Jalankan test suite"
    echo -e "  ${GREEN}stop${NC}      Hentikan semua server"
    echo -e "  ${GREEN}help${NC}      Tampilkan bantuan ini"
    echo ""
    echo -e "${BOLD}Contoh:${NC}"
    echo "  bash run.sh setup    # Setup pertama kali"
    echo "  bash run.sh dev      # Jalankan development server"
    echo ""
}

# ─── Main ────────────────────────────────────────────────
COMMAND=${1:-help}

case "$COMMAND" in
    dev)     cmd_dev ;;
    install) cmd_install ;;
    setup)   cmd_setup ;;
    migrate) cmd_migrate ;;
    seed)    cmd_seed ;;
    fresh)   cmd_fresh ;;
    build)   cmd_build ;;
    clear)   cmd_clear ;;
    test)    cmd_test ;;
    stop)    cmd_stop ;;
    help)    cmd_help ;;
    *)
        error "Command tidak dikenal: $COMMAND"
        cmd_help
        exit 1
        ;;
esac
