@echo off
REM ============================================================
REM Script PRO d'optimisation d'images pour Wayo Academy
REM AVIF + WebP haute qualité
REM Necessite: cwebp et avifenc
REM ============================================================

echo.
echo ========================================
echo   OPTIMISATION PRO IMAGES WAYO ACADEMY
echo   AVIF + WebP Haute Qualite
echo ========================================
echo.

REM Verifier si cwebp est installe
where cwebp >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERREUR] cwebp n'est pas installe!
    echo Installation: choco install webp
    pause
    exit /b 1
)

REM Verifier si avifenc est installe
where avifenc >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [AVERTISSEMENT] avifenc n'est pas installe.
    echo Installation: choco install libavif
    echo.
    echo Je vais generer uniquement les WebP haute qualite.
    set AVIF_AVAILABLE=0
) else (
    set AVIF_AVAILABLE=1
)

echo [INFO] Demarrage de l'optimisation haute qualite...
echo.

REM Creer le dossier optimized si necessaire
if not exist "optimized" mkdir optimized

REM ============================================================
REM CONFIGURATION QUALITE PRO
REM ============================================================
REM WebP: qualite 90 = quasi-identique a l'original
REM AVIF: qualite 80 = equivalent WebP 90 (meilleure compression)
REM Pas de redimensionnement = qualite maximale
REM ============================================================

echo.
echo === WEBP HAUTE QUALITE (q90) ===
echo.

REM ============================================================
REM IMAGES MENTORS (PNG -> WebP q90, taille originale)
REM ============================================================
echo [1/9] WebP mentor01-home.png...
cwebp -q 90 "mentor01-home.png" -o "optimized/mentor01-home.webp"

echo [2/9] WebP mentor02-home.png...
cwebp -q 90 "mentor02-home.png" -o "optimized/mentor02-home.webp"

echo [3/9] WebP mentor03-home.png...
cwebp -q 90 "mentor03-home.png" -o "optimized/mentor03-home.webp"

echo [4/9] WebP logo-hwe.png...
cwebp -q 90 "logo-hwe.png" -o "optimized/logo-hwe.webp"

REM ============================================================
REM IMAGES COMPARAISON (PNG -> WebP q90)
REM ============================================================
echo [5/9] WebP without-wayo.png...
cwebp -q 90 "without-wayo.png" -o "optimized/without-wayo.webp"

echo [6/9] WebP with-wayo.png...
cwebp -q 90 "with-wayo.png" -o "optimized/with-wayo.webp"

REM ============================================================
REM IMAGES FEATURES (PNG -> WebP q90)
REM ============================================================
echo [7/9] WebP Ai.png...
cwebp -q 90 "Ai.png" -o "optimized/Ai.webp"

echo [8/9] WebP quiz_recent.png...
cwebp -q 90 "quiz_recent.png" -o "optimized/quiz_recent.webp"

echo [9/9] Copie online_course.webp...
copy "online_course.webp" "optimized\online_course.webp" >nul

REM ============================================================
REM VERSIONS MOBILE WebP (reduit pour mobile)
REM ============================================================
echo.
echo === WEBP MOBILE (q88, reduit) ===
echo.

echo [Mobile] mentor01-home...
cwebp -q 88 -resize 400 0 "mentor01-home.png" -o "optimized/mentor01-home-mobile.webp"

echo [Mobile] mentor02-home...
cwebp -q 88 -resize 400 0 "mentor02-home.png" -o "optimized/mentor02-home-mobile.webp"

echo [Mobile] mentor03-home...
cwebp -q 88 -resize 400 0 "mentor03-home.png" -o "optimized/mentor03-home-mobile.webp"

echo [Mobile] logo-hwe...
cwebp -q 88 -resize 400 0 "logo-hwe.png" -o "optimized/logo-hwe-mobile.webp"

echo [Mobile] Ai...
cwebp -q 88 -resize 800 0 "Ai.png" -o "optimized/Ai-mobile.webp"

echo [Mobile] quiz_recent...
cwebp -q 88 -resize 800 0 "quiz_recent.png" -o "optimized/quiz_recent-mobile.webp"

echo [Mobile] online_course...
cwebp -q 88 -resize 800 0 "online_course.webp" -o "optimized/online_course-mobile.webp"

echo [Mobile] without-wayo...
cwebp -q 88 -resize 500 0 "without-wayo.png" -o "optimized/without-wayo-mobile.webp"

echo [Mobile] with-wayo...
cwebp -q 88 -resize 500 0 "with-wayo.png" -o "optimized/with-wayo-mobile.webp"

REM ============================================================
REM AVIF (si disponible) - Meilleure compression
REM ============================================================
if %AVIF_AVAILABLE%==1 (
    echo.
    echo === AVIF HAUTE QUALITE (q80 = equivalent WebP q90) ===
    echo.
    
    echo [AVIF] mentor01-home...
    avifenc --min 20 --max 30 -s 6 "mentor01-home.png" "optimized/mentor01-home.avif"
    
    echo [AVIF] mentor02-home...
    avifenc --min 20 --max 30 -s 6 "mentor02-home.png" "optimized/mentor02-home.avif"
    
    echo [AVIF] mentor03-home...
    avifenc --min 20 --max 30 -s 6 "mentor03-home.png" "optimized/mentor03-home.avif"
    
    echo [AVIF] logo-hwe...
    avifenc --min 20 --max 30 -s 6 "logo-hwe.png" "optimized/logo-hwe.avif"
    
    echo [AVIF] without-wayo...
    avifenc --min 20 --max 30 -s 6 "without-wayo.png" "optimized/without-wayo.avif"
    
    echo [AVIF] with-wayo...
    avifenc --min 20 --max 30 -s 6 "with-wayo.png" "optimized/with-wayo.avif"
    
    echo [AVIF] Ai...
    avifenc --min 20 --max 30 -s 6 "Ai.png" "optimized/Ai.avif"
    
    echo [AVIF] quiz_recent...
    avifenc --min 20 --max 30 -s 6 "quiz_recent.png" "optimized/quiz_recent.avif"
    
    REM Convertir online_course.webp en PNG puis AVIF
    echo [AVIF] online_course (via PNG)...
    dwebp "online_course.webp" -o "optimized/online_course_temp.png" 2>nul
    if exist "optimized/online_course_temp.png" (
        avifenc --min 20 --max 30 -s 6 "optimized/online_course_temp.png" "optimized/online_course.avif"
        del "optimized/online_course_temp.png"
    )
    
    REM AVIF Mobile
    echo.
    echo === AVIF MOBILE ===
    echo.
    
    echo [AVIF Mobile] mentor01-home...
    avifenc --min 25 --max 35 -s 6 --resize 400 400 "mentor01-home.png" "optimized/mentor01-home-mobile.avif" 2>nul
    
    echo [AVIF Mobile] mentor02-home...
    avifenc --min 25 --max 35 -s 6 --resize 400 400 "mentor02-home.png" "optimized/mentor02-home-mobile.avif" 2>nul
    
    echo [AVIF Mobile] mentor03-home...
    avifenc --min 25 --max 35 -s 6 --resize 400 400 "mentor03-home.png" "optimized/mentor03-home-mobile.avif" 2>nul
    
    echo [AVIF Mobile] logo-hwe...
    avifenc --min 25 --max 35 -s 6 --resize 400 400 "logo-hwe.png" "optimized/logo-hwe-mobile.avif" 2>nul
)

REM ============================================================
REM RESUME
REM ============================================================
echo.
echo ========================================
echo   OPTIMISATION PRO TERMINEE!
echo ========================================
echo.
echo Configuration:
echo   WebP Desktop: qualite 90 (haute fidelite)
echo   WebP Mobile:  qualite 88, dimensions reduites
if %AVIF_AVAILABLE%==1 (
    echo   AVIF Desktop: qualite ~80 (equivalent WebP 90)
)
echo.
echo Fichiers generes:
echo.
dir /b optimized\*.webp 2>nul
if %AVIF_AVAILABLE%==1 (
    echo.
    dir /b optimized\*.avif 2>nul
)
echo.
echo Tailles des fichiers:
echo.
for %%f in (optimized\*.webp optimized\*.avif) do (
    for %%A in ("%%f") do echo   %%~nxA: %%~zA bytes
)
echo.
pause
