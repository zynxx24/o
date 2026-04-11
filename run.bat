@echo off
chcp 65001 >nul 2>&1
REM ══════════════════════════════════════════════════════
REM  CateringKu — Launcher Script (Windows)
REM  Jalankan dari folder ini: run.bat [command]
REM ══════════════════════════════════════════════════════

set "SCRIPT_DIR=%~dp0"
set "PROJECT_DIR=%SCRIPT_DIR%CateringKu"

REM Cek apakah folder CateringKu ada
if not exist "%PROJECT_DIR%" (
    echo [X] Folder CateringKu tidak ditemukan di: %PROJECT_DIR%
    exit /b 1
)

REM Forward semua command ke CateringKu\run.bat
set "CMD=%~1"
if "%CMD%"=="" set "CMD=dev"
call "%PROJECT_DIR%\run.bat" %CMD%
