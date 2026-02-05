@echo off
REM ============================================================
REM Script d'optimisation d'images pour Wayo Academy
REM Necessite cwebp: https://developers.google.com/speed/webp/download
REM ============================================================

echo.
echo ========================================
echo   OPTIMISATION IMAGES WAYO ACADEMY
echo ========================================
echo.

REM Verifier si cwebp est installe
where cwebp >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERREUR] cwebp n'est pas installe!
    echo.
    echo Installation:
    echo 1. Telechargez libwebp: https://developers.google.com/speed/webp/download
    echo 2. Extrayez le fichier ZIP
    echo 3. Ajoutez le dossier bin au PATH de Windows
    echo.
    echo Ou utilisez Chocolatey: choco install webp
    echo.
    pause
    exit /b 1
)

echo [INFO] cwebp trouve. Demarrage de l'optimisation...
echo.

REM Creer le dossier optimized si necessaire
if not exist "optimized" mkdir optimized

REM ============================================================
REM IMAGES MENTORS (les plus lourdes - 1.2-1.5 MB -> ~50 KB)
REM ============================================================
echo [1/9] Optimisation mentor01-home.png...
cwebp -q 85 -resize 600 0 "mentor01-home.png" -o "optimized/mentor01-home.webp"

echo [2/9] Optimisation mentor02-home.png...
cwebp -q 85 -resize 600 0 "mentor02-home.png" -o "optimized/mentor02-home.webp"

echo [3/9] Optimisation mentor03-home.png...
cwebp -q 85 -resize 600 0 "mentor03-home.png" -o "optimized/mentor03-home.webp"

echo [4/9] Optimisation logo-hwe.png...
cwebp -q 85 -resize 600 0 "logo-hwe.png" -o "optimized/logo-hwe.webp"

REM ============================================================
REM IMAGES COMPARAISON (230-280 KB -> ~30 KB)
REM ============================================================
echo [5/9] Optimisation without-wayo.png...
cwebp -q 85 -resize 800 0 "without-wayo.png" -o "optimized/without-wayo.webp"

echo [6/9] Optimisation with-wayo.png...
cwebp -q 85 -resize 800 0 "with-wayo.png" -o "optimized/with-wayo.webp"

REM ============================================================
REM IMAGES FEATURES (32-133 KB -> ~20 KB)
REM ============================================================
echo [7/9] Optimisation Ai.png...
cwebp -q 85 -resize 1200 0 "Ai.png" -o "optimized/Ai.webp"

echo [8/9] Optimisation quiz_recent.png...
cwebp -q 85 -resize 1200 0 "quiz_recent.png" -o "optimized/quiz_recent.webp"

REM ============================================================
REM COPIER les WebP existants deja optimises
REM ============================================================
echo [9/9] Copie des WebP existants...
if exist "online_course.webp" copy "online_course.webp" "optimized\online_course.webp" >nul

REM ============================================================
REM VERSIONS MOBILE (plus petites pour mobile)
REM ============================================================
echo.
echo [MOBILE] Creation des versions mobiles...

REM Mentors mobile (300px)
cwebp -q 80 -resize 300 0 "mentor01-home.png" -o "optimized/mentor01-home-mobile.webp"
cwebp -q 80 -resize 300 0 "mentor02-home.png" -o "optimized/mentor02-home-mobile.webp"
cwebp -q 80 -resize 300 0 "mentor03-home.png" -o "optimized/mentor03-home-mobile.webp"
cwebp -q 80 -resize 300 0 "logo-hwe.png" -o "optimized/logo-hwe-mobile.webp"

REM Features mobile (600px)
cwebp -q 80 -resize 600 0 "Ai.png" -o "optimized/Ai-mobile.webp"
cwebp -q 80 -resize 600 0 "quiz_recent.png" -o "optimized/quiz_recent-mobile.webp"
cwebp -q 85 -resize 600 0 "online_course.webp" -o "optimized/online_course-mobile.webp"

REM Comparison mobile (400px)
cwebp -q 80 -resize 400 0 "without-wayo.png" -o "optimized/without-wayo-mobile.webp"
cwebp -q 80 -resize 400 0 "with-wayo.png" -o "optimized/with-wayo-mobile.webp"

REM ============================================================
REM RESUME
REM ============================================================
echo.
echo ========================================
echo   OPTIMISATION TERMINEE!
echo ========================================
echo.
echo Configuration:
echo   Desktop: qualite 85, largeur adaptee
echo   Mobile:  qualite 80, largeur reduite
echo.
echo Fichiers optimises:
echo.
echo DESKTOP:
for %%f in (optimized\*-home.webp optimized\Ai.webp optimized\quiz_recent.webp optimized\online_course.webp optimized\with*.webp optimized\without*.webp) do (
    if not "%%~nf"=="%%~nf" if not "%%~nf:~-7%"=="-mobile" (
        for %%A in ("%%f") do echo   %%~nxA: %%~zA bytes
    )
)
dir /b optimized\*.webp 2>nul | findstr /v mobile
echo.
echo MOBILE:
dir /b optimized\*-mobile.webp 2>nul
echo.
echo Comparaison des tailles:
echo.
echo   AVANT (PNG):
echo   - mentor01-home.png: ~1.2 MB
echo   - mentor02-home.png: ~1.5 MB
echo   - mentor03-home.png: ~1.2 MB
echo   - without-wayo.png:  ~278 KB
echo   - with-wayo.png:     ~231 KB
echo   - quiz_recent.png:   ~133 KB
echo   - Ai.png:            ~32 KB
echo   TOTAL AVANT: ~4.5 MB
echo.
echo   APRES (WebP optimise):
for /f %%a in ('powershell -command "(Get-ChildItem optimized\*.webp | Measure-Object -Property Length -Sum).Sum / 1KB"') do echo   TOTAL APRES: ~%%a KB
echo.
pause
