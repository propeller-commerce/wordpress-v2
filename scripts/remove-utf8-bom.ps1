# Script to remove UTF-8 BOM from PHP files
# UTF-8 BOM (Byte Order Mark) can corrupt PHP applications and causes Plugin Check warnings

$partials_dir = "D:\laragon\www\playground2\wp-content\plugins\propeller-ecommerce-v2\public\partials"
$files = Get-ChildItem -Path $partials_dir -Recurse -Filter "*.php"

$count = 0

foreach ($file in $files) {
    try {
        # Read file as bytes
        $bytes = [System.IO.File]::ReadAllBytes($file.FullName)
        
        # Check if file starts with UTF-8 BOM (EF BB BF)
        if ($bytes.Length -gt 3 -and $bytes[0] -eq 0xEF -and $bytes[1] -eq 0xBB -and $bytes[2] -eq 0xBF) {
            # Remove BOM by keeping everything after the first 3 bytes
            $new_bytes = $bytes[3..($bytes.Length - 1)]
            
            # Write back without BOM
            [System.IO.File]::WriteAllBytes($file.FullName, $new_bytes)
            $count++
            Write-Host "Removed BOM from: $($file.Name)"
        }
    }
    catch {
        Write-Host "Error processing $($file.Name): $_"
    }
}

Write-Host "Total files processed: $count"
