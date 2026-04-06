# SCRIPT DE DIAGNOSTIC - Lance depuis D:\Own project\projet_Mssa
# PowerShell: .\find_bug.ps1

Write-Host "=== RECHERCHE DU BUG BLADE ===" -ForegroundColor Cyan

$viewsPath = "resources\views"
$files = Get-ChildItem -Path $viewsPath -Filter "*.blade.php" -Recurse

foreach ($file in $files) {
    $lines = Get-Content $file.FullName
    $ifCount = 0
    $lineNum = 0
    $openLines = @()
    
    foreach ($line in $lines) {
        $lineNum++
        $stripped = $line.Trim()
        
        # Skip comments
        if ($stripped.StartsWith("{{--")) { continue }
        
        # Count @if (not @endif)
        $ifMatches = ([regex]::Matches($stripped.Replace("@endif","~~"), "@if\b")).Count
        $endifMatches = ([regex]::Matches($stripped, "@endif\b")).Count
        
        $net = $ifMatches - $endifMatches
        if ($net -gt 0) {
            for ($x = 0; $x -lt $net; $x++) {
                $openLines += $lineNum
            }
        } elseif ($net -lt 0) {
            for ($x = 0; $x -lt [Math]::Abs($net); $x++) {
                if ($openLines.Count -gt 0) {
                    $openLines = $openLines[0..($openLines.Count-2)]
                }
            }
        }
    }
    
    if ($openLines.Count -gt 0) {
        Write-Host "ERREUR: $($file.FullName)" -ForegroundColor Red
        Write-Host "  @if non ferme aux lignes: $($openLines -join ', ')" -ForegroundColor Yellow
    }
    
    # Check for @auth...@else...@endauth
    $content = $lines -join "`n"
    if ($content -match "@auth[\s\S]*?@else[\s\S]*?@endauth") {
        Write-Host "ERREUR @auth/@else: $($file.FullName)" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "=== FICHIERS AVEC CRLF ===" -ForegroundColor Cyan
foreach ($file in $files) {
    $bytes = [System.IO.File]::ReadAllBytes($file.FullName)
    $hasCRLF = $false
    for ($i = 0; $i -lt $bytes.Length - 1; $i++) {
        if ($bytes[$i] -eq 13 -and $bytes[$i+1] -eq 10) { $hasCRLF = $true; break }
    }
    if ($hasCRLF) {
        Write-Host "  CRLF: $($file.Name)" -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "=== TAILLE DES FICHIERS CLES ===" -ForegroundColor Cyan
$keyFiles = @(
    "resources\views\shop\products\show.blade.php",
    "resources\views\layouts\app.blade.php",
    "resources\views\shop\products\credit-block.blade.php",
    "resources\views\components\notification-bell.blade.php"
)
foreach ($f in $keyFiles) {
    if (Test-Path $f) {
        $count = (Get-Content $f).Count
        Write-Host "  $f : $count lignes"
    }
}

Write-Host ""
Write-Host "=== TERMINE ===" -ForegroundColor Green
