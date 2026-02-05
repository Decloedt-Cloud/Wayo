@echo off
REM ============================================================
REM Script d'optimisation vidéo pour Wayo Academy
REM Nécessite FFmpeg: https://ffmpeg.org/download.html
REM ============================================================

echo.
echo ========================================
echo   OPTIMISATION VIDEO WAYO ACADEMY
echo ========================================
echo.

REM Vérifier si FFmpeg est installé
where ffmpeg >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERREUR] FFmpeg n'est pas installe!
    echo Telechargez-le ici: https://ffmpeg.org/download.html
    echo Puis ajoutez-le au PATH de Windows
    pause
    exit /b 1
)

echo [INFO] FFmpeg trouve. Demarrage de l'optimisation...
echo.

REM Créer les dossiers si nécessaires
if not exist "optimized" mkdir optimized
if not exist "posters" mkdir posters

REM ============================================================
REM 1. FRANÇAIS - Desktop (1080p, CRF 20, avec audio)
REM ============================================================
echo [1/6] Desktop HD Francais (1080p, qualite haute)...
ffmpeg -y -i "V5 Wayo Promo Video francais.mp4" ^
    -vcodec libx264 -crf 20 -preset slow ^
    -vf "scale=1920:-2" ^
    -c:a aac -b:a 128k ^
    -movflags +faststart ^
    "optimized/promo_fr.mp4"

echo [2/6] Mobile Francais (720p, leger)...
ffmpeg -y -i "V5 Wayo Promo Video francais.mp4" ^
    -vcodec libx264 -crf 28 -preset slow ^
    -vf "scale=720:-2" ^
    -c:a aac -b:a 96k ^
    -movflags +faststart ^
    "optimized/promo_fr_mobile.mp4"

echo [POSTER] Extraction poster Francais...
ffmpeg -y -i "V5 Wayo Promo Video francais.mp4" ^
    -ss 00:00:02 -vframes 1 ^
    -vf "scale=1280:-2" ^
    "posters/poster_fr.webp"

REM ============================================================
REM 2. ARABE - Desktop (1080p, CRF 20, avec audio)
REM ============================================================
echo [3/6] Desktop HD Arabe (1080p, qualite haute)...
ffmpeg -y -i "v2_Wayo_Academy_Promo_Video.mp4" ^
    -vcodec libx264 -crf 20 -preset slow ^
    -vf "scale=1920:-2" ^
    -c:a aac -b:a 128k ^
    -movflags +faststart ^
    "optimized/promo_ar.mp4"

echo [4/6] Mobile Arabe (720p, leger)...
ffmpeg -y -i "v2_Wayo_Academy_Promo_Video.mp4" ^
    -vcodec libx264 -crf 28 -preset slow ^
    -vf "scale=720:-2" ^
    -c:a aac -b:a 96k ^
    -movflags +faststart ^
    "optimized/promo_ar_mobile.mp4"

echo [POSTER] Extraction poster Arabe...
ffmpeg -y -i "v2_Wayo_Academy_Promo_Video.mp4" ^
    -ss 00:00:02 -vframes 1 ^
    -vf "scale=1280:-2" ^
    "posters/poster_ar.webp"

REM ============================================================
REM 3. ANGLAIS - Desktop (1080p, CRF 20, avec audio)
REM ============================================================
echo [5/6] Desktop HD Anglais (1080p, qualite haute)...
ffmpeg -y -i "Final En Wayo Promo Video anglais.mp4" ^
    -vcodec libx264 -crf 20 -preset slow ^
    -vf "scale=1920:-2" ^
    -c:a aac -b:a 128k ^
    -movflags +faststart ^
    "optimized/promo_en.mp4"

echo [6/6] Mobile Anglais (720p, leger)...
ffmpeg -y -i "Final En Wayo Promo Video anglais.mp4" ^
    -vcodec libx264 -crf 28 -preset slow ^
    -vf "scale=720:-2" ^
    -c:a aac -b:a 96k ^
    -movflags +faststart ^
    "optimized/promo_en_mobile.mp4"

echo [POSTER] Extraction poster Anglais...
ffmpeg -y -i "Final En Wayo Promo Video anglais.mp4" ^
    -ss 00:00:02 -vframes 1 ^
    -vf "scale=1280:-2" ^
    "posters/poster_en.webp"

REM ============================================================
REM RÉSUMÉ
REM ============================================================
echo.
echo ========================================
echo   OPTIMISATION TERMINEE!
echo ========================================
echo.
echo Configuration:
echo   Desktop: 1080p, CRF 20 (qualite haute), audio 128kbps
echo   Mobile:  720p,  CRF 28 (leger), audio 96kbps
echo.
echo Fichiers generes:
dir /b optimized\*.mp4 2>nul
echo.
echo Posters generes:
dir /b posters\*.webp 2>nul
echo.
pause
