@echo off
title MyCampus Dev Server

cd /d "C:\laragon\www\project-mobile\project1"

:: Jalankan composer dev (artisan serve + queue + pail + vite)
start "MyCampus Dev" cmd /c composer dev

:: Tunggu artisan serve jalan
timeout /t 5 /nobreak >nul

:: Jalankan ngrok tunnel
start "Ngrok Tunnel" cmd /c "C:\laragon\bin\ngrok\ngrok.exe" http 8000

:: Tunggu ngrok siap
timeout /t 8 /nobreak >nul

:: Buka browser
start http://localhost:8000

cls
echo ============================================
echo   MyCampus Dev Server - Siap!
echo ============================================
echo.
echo   Web:        http://localhost:8000
echo   Ngrok URL:  http://127.0.0.1:4040
echo.
echo   Buka 127.0.0.1:4040 untuk lihat URL publik
echo   yang bisa dishare ke teman.
echo.
echo   Tutup window ini untuk mematikan semua.
echo ============================================
pause >nul

:: Matikan semua proses saat exit
taskkill /f /fi "WINDOWTITLE eq MyCampus Dev" >nul 2>&1
taskkill /f /fi "WINDOWTITLE eq Ngrok Tunnel" >nul 2>&1
