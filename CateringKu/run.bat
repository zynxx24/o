@echo off
chcp 65001 >nul 2>&1
setlocal enabledelayedexpansion

REM ╔══════════════════════════════════════════════════════════╗
REM ║  CateringKu — Script Menjalankan Aplikasi (Windows)     ║
REM ║  Penggunaan: run.bat [command]                           ║
REM ║                                                          ║
REM ║  Commands:                                               ║
REM ║    dev      → Jalankan dev server (PHP + Vite)           ║
REM ║    install  → Install semua dependency                   ║
REM ║    setup    → Setup awal (install + migrate + seed)      ║
REM ║    migrate  → Jalankan migrasi database                  ║
REM ║    seed     → Seed database dengan data test             ║
REM ║    fresh    → Fresh migrate + seed (HAPUS SEMUA DATA)    ║
REM ║    build    → Build production assets                    ║
REM ║    clear    → Clear semua cache                          ║
REM ║    test     → Jalankan test suite                        ║
REM ║    stop     → Hentikan semua proses                      ║
REM ╚══════════════════════════════════════════════════════════╝

REM ─── Auto-cd ke folder project ───────────────────────────
cd /d "%~dp0"

REM ─── Main ────────────────────────────────────────────────
set "COMMAND=%~1"
if "%COMMAND%"=="" set "COMMAND=help"

if "%COMMAND%"=="dev"     goto cmd_dev
if "%COMMAND%"=="install" goto cmd_install
if "%COMMAND%"=="setup"   goto cmd_setup
if "%COMMAND%"=="migrate" goto cmd_migrate
if "%COMMAND%"=="seed"    goto cmd_seed
if "%COMMAND%"=="fresh"   goto cmd_fresh
if "%COMMAND%"=="build"   goto cmd_build
if "%COMMAND%"=="clear"   goto cmd_clear
if "%COMMAND%"=="test"    goto cmd_test
if "%COMMAND%"=="stop"    goto cmd_stop
if "%COMMAND%"=="help"    goto cmd_help

echo [X] Command tidak dikenal: %COMMAND%
goto cmd_help

REM ═══════════════════════════════════════════════════════════
REM  BANNER
REM ═══════════════════════════════════════════════════════════
:banner
echo.
echo   ╔═══════════════════════════════════════╗
echo   ║     CateringKu Dev Server             ║
echo   ║     Platform Katering Online           ║
echo   ╚═══════════════════════════════════════╝
echo.
goto :eof

REM ═══════════════════════════════════════════════════════════
REM  CHECK PREREQUISITES
REM ═══════════════════════════════════════════════════════════
:check_php
where php >nul 2>&1
if errorlevel 1 (
    echo [X] PHP tidak ditemukan! Install PHP 8.2+ terlebih dahulu.
    exit /b 1
)
goto :eof

:check_node
where node >nul 2>&1
if errorlevel 1 (
    echo [X] Node.js tidak ditemukan! Install Node.js 18+ terlebih dahulu.
    exit /b 1
)
goto :eof

:check_composer
where composer >nul 2>&1
if errorlevel 1 (
    echo [X] Composer tidak ditemukan! Install Composer terlebih dahulu.
    exit /b 1
)
goto :eof

:check_env
if not exist .env (
    echo [!] File .env tidak ditemukan. Membuat dari .env.example...
    if exist .env.example (
        copy .env.example .env >nul
        php artisan key:generate --force
        echo [OK] .env berhasil dibuat dengan APP_KEY baru
    ) else (
        echo [X] .env.example tidak ditemukan!
        exit /b 1
    )
)
goto :eof

REM ═══════════════════════════════════════════════════════════
REM  INSTALL
REM ═══════════════════════════════════════════════════════════
:cmd_install
echo [INFO] Installing dependencies...
echo.

call :check_composer
call :check_node

echo [INFO] Installing PHP dependencies (Composer)...
call composer install --no-interaction --prefer-dist
if errorlevel 1 (
    echo [X] Composer install gagal!
    exit /b 1
)
echo [OK] Composer dependencies installed
echo.

echo [INFO] Installing Node.js dependencies (NPM)...
call npm install
if errorlevel 1 (
    echo [X] NPM install gagal!
    exit /b 1
)
echo [OK] NPM dependencies installed
echo.

call :check_env

echo [OK] Semua dependency berhasil diinstall!
goto :eof

REM ═══════════════════════════════════════════════════════════
REM  SETUP
REM ═══════════════════════════════════════════════════════════
:cmd_setup
call :banner
echo [INFO] Setup awal CateringKu...
echo.

call :cmd_install
echo.

echo [INFO] Menjalankan migrasi database...
php artisan migrate --force
if errorlevel 1 (
    echo [X] Migrasi gagal!
    exit /b 1
)
echo [OK] Migrasi selesai
echo.

echo [INFO] Seeding database dengan data test...
php artisan db:seed --class=FullTestSeeder --force
if errorlevel 1 (
    echo [X] Seeding gagal!
    exit /b 1
)
echo [OK] Seeding selesai
echo.

echo.
echo   ╔═══════════════════════════════════════════════════╗
echo   ║  Setup Selesai!                                   ║
echo   ║                                                   ║
echo   ║  Akun Test:                                       ║
echo   ║  Admin  : admin@cateringku.com / admin123         ║
echo   ║  Vendor : vendor@cateringku.com / vendor123       ║
echo   ║  User   : user@cateringku.com / user123           ║
echo   ║                                                   ║
echo   ║  Jalankan: run.bat dev                            ║
echo   ╚═══════════════════════════════════════════════════╝
echo.
goto :eof

REM ═══════════════════════════════════════════════════════════
REM  DEV
REM ═══════════════════════════════════════════════════════════
:cmd_dev
call :banner
call :check_php
call :check_node
call :check_env

echo [INFO] Menjalankan CateringKu development server...
echo.
echo   Laravel  : http://localhost:8000
echo   Vite HMR : http://localhost:5173
echo.
echo [INFO] Tekan Ctrl+C untuk menghentikan kedua server
echo.

REM Clear cache sebelum dev
php artisan config:clear >nul 2>&1
php artisan route:clear >nul 2>&1
php artisan view:clear >nul 2>&1

REM Coba gunakan concurrently (lebih bagus output-nya)
where npx >nul 2>&1
if errorlevel 1 goto dev_fallback

call npx --yes concurrently --version >nul 2>&1
if errorlevel 1 goto dev_fallback

call npx concurrently --names "PHP,Vite" --prefix-colors "magenta,cyan" --kill-others "php artisan serve --host=0.0.0.0 --port=8000" "npm run dev"
goto :eof

:dev_fallback
REM Fallback: jalankan PHP di background dengan start, lalu Vite di foreground
echo [INFO] Menjalankan PHP server di background...
start "CateringKu-PHP" /min cmd /c "php artisan serve --host=0.0.0.0 --port=8000"

echo [INFO] Menjalankan Vite dev server...
echo [INFO] Tutup window ini atau tekan Ctrl+C untuk menghentikan Vite.
echo [INFO] Jangan lupa jalankan "run.bat stop" untuk menghentikan PHP server.
echo.
call npm run dev
goto :eof

REM ═══════════════════════════════════════════════════════════
REM  MIGRATE
REM ═══════════════════════════════════════════════════════════
:cmd_migrate
call :check_php
echo [INFO] Menjalankan migrasi...
php artisan migrate
echo [OK] Migrasi selesai
goto :eof

REM ═══════════════════════════════════════════════════════════
REM  SEED
REM ═══════════════════════════════════════════════════════════
:cmd_seed
call :check_php
echo [INFO] Seeding database...
php artisan db:seed --class=FullTestSeeder
echo [OK] Seeding selesai
echo.
echo   Akun Test:
echo   Admin  : admin@cateringku.com / admin123
echo   Vendor : vendor@cateringku.com / vendor123
echo   User   : user@cateringku.com / user123
goto :eof

REM ═══════════════════════════════════════════════════════════
REM  FRESH
REM ═══════════════════════════════════════════════════════════
:cmd_fresh
call :check_php
echo [!] Ini akan MENGHAPUS SEMUA DATA dan membuat ulang database!
set /p "CONFIRM=Lanjutkan? (y/N): "
if /i not "%CONFIRM%"=="y" (
    echo [INFO] Dibatalkan.
    goto :eof
)
echo [INFO] Fresh migrate + seed...
php artisan migrate:fresh --seed --seeder=FullTestSeeder --force
echo [OK] Database berhasil di-reset dan di-seed
goto :eof

REM ═══════════════════════════════════════════════════════════
REM  BUILD
REM ═══════════════════════════════════════════════════════════
:cmd_build
call :check_node
echo [INFO] Building production assets...
call npm run build
echo [OK] Build selesai! Assets tersedia di public\build\
goto :eof

REM ═══════════════════════════════════════════════════════════
REM  CLEAR
REM ═══════════════════════════════════════════════════════════
:cmd_clear
call :check_php
echo [INFO] Membersihkan semua cache...
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan event:clear
echo [OK] Semua cache berhasil dihapus
goto :eof

REM ═══════════════════════════════════════════════════════════
REM  TEST
REM ═══════════════════════════════════════════════════════════
:cmd_test
call :check_php
echo [INFO] Menjalankan test suite...
php artisan test
goto :eof

REM ═══════════════════════════════════════════════════════════
REM  STOP
REM ═══════════════════════════════════════════════════════════
:cmd_stop
echo [INFO] Menghentikan proses...

REM Kill PHP artisan serve
taskkill /f /fi "WINDOWTITLE eq CateringKu-PHP*" >nul 2>&1
tasklist /fi "IMAGENAME eq php.exe" | find "php.exe" >nul 2>&1
if not errorlevel 1 (
    taskkill /f /im php.exe >nul 2>&1
    echo [OK] PHP server dihentikan
) else (
    echo [!] PHP server tidak berjalan
)

REM Kill Node/Vite
tasklist /fi "IMAGENAME eq node.exe" | find "node.exe" >nul 2>&1
if not errorlevel 1 (
    taskkill /f /im node.exe >nul 2>&1
    echo [OK] Vite server dihentikan
) else (
    echo [!] Vite server tidak berjalan
)
goto :eof

REM ═══════════════════════════════════════════════════════════
REM  HELP
REM ═══════════════════════════════════════════════════════════
:cmd_help
call :banner
echo Penggunaan: run.bat [command]
echo.
echo Commands:
echo   dev       Jalankan dev server (PHP + Vite)
echo   install   Install semua dependency (Composer + NPM)
echo   setup     Setup awal lengkap (install + migrate + seed)
echo   migrate   Jalankan migrasi database
echo   seed      Seed database dengan akun test
echo   fresh     Fresh migrate + seed (HAPUS SEMUA DATA)
echo   build     Build production assets
echo   clear     Clear semua cache Laravel
echo   test      Jalankan test suite
echo   stop      Hentikan semua server
echo   help      Tampilkan bantuan ini
echo.
echo Contoh:
echo   run.bat setup    ^(Setup pertama kali^)
echo   run.bat dev      ^(Jalankan development server^)
echo.
goto :eof
