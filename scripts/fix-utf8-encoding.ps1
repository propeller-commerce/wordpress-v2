# Comprehensive script to ensure all PHP files use UTF-8 without BOM
# This prevents Plugin Check warnings and ensures plugin compatibility

$plugin_dir = "D:\laragon\www\playground2\wp-content\plugins\propeller-ecommerce-v2"
$files = Get-ChildItem -Path $plugin_dir -Recurse -Filter "*.php" -ErrorAction SilentlyContinue

$fixed_count = 0
$bom_count = 0

foreach ($file in $files) {
    try {
        # Read file as bytes
        $bytes = [System.IO.File]::ReadAllBytes($file.FullName)
        
        # Check if file starts with UTF-8 BOM (EF BB BF)
        if ($bytes.Length -gt 3 -and $bytes[0] -eq 0xEF -and $bytes[1] -eq 0xBB -and $bytes[2] -eq 0xBF) {
            # Remove BOM by keeping everything after the first 3 bytes
            $new_bytes = $bytes[3..($bytes.Length - 1)]
            
            # Write back without BOM using UTF8 without BOM encoding
            $UTF8NoBOM = New-Object System.Text.UTF8Encoding $false
            [System.IO.File]::WriteAllBytes($file.FullName, $new_bytes)
            
            $bom_count++
            Write-Host "Removed BOM from: $($file.Name)"
        }
        
        # Also ensure file is written with UTF-8 without BOM
        $content = [System.IO.File]::ReadAllText($file.FullName)
        $UTF8NoBOM = New-Object System.Text.UTF8Encoding $false
        [System.IO.File]::WriteAllText($file.FullName, $content, $UTF8NoBOM)
        $fixed_count++
    }
    catch {
        Write-Host "Error processing $($file.FullName): $_"
    }
}

Write-Host "Files with BOM removed: $bom_count"
Write-Host "Total files processed: $fixed_count"
