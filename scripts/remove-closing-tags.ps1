# Script to remove closing PHP tags from the end of partial files
# This is a WordPress best practice - partial/template files should not end with ?>

$partials_dir = "D:\laragon\www\playground2\wp-content\plugins\propeller-ecommerce-v2\public\partials"
$files = Get-ChildItem -Path $partials_dir -Recurse -Filter "*.php" | 
    Where-Object { (Get-Content $_.FullName -Tail 1) -match '^\?>\s*$|^\s*\?>\s*$' }

$count = 0

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    # Remove closing PHP tag from the very end, including any trailing whitespace
    $new_content = $content -replace '\s*\?>\s*$', ''
    
    Set-Content -Path $file.FullName -Value $new_content -NoNewline
    $count++
    Write-Host "Removed closing tag from: $($file.Name)"
}

Write-Host "Total files processed: $count"
