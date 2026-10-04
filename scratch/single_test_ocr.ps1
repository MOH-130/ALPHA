[Windows.Globalization.Language, Windows.Globalization, ContentType = WindowsRuntime] | Out-Null
[Windows.Media.Ocr.OcrEngine, Windows.Media.Ocr, ContentType = WindowsRuntime] | Out-Null
[Windows.Graphics.Imaging.BitmapDecoder, Windows.Graphics.Imaging, ContentType = WindowsRuntime] | Out-Null
[Windows.Storage.StorageFile, Windows.Storage, ContentType = WindowsRuntime] | Out-Null

$langEn = [Windows.Globalization.Language]::new("en-US")
$ocrEn = [Windows.Media.Ocr.OcrEngine]::TryCreateFromLanguage($langEn)
$filePath = "C:\Users\elnou\Downloads\New folder\Offre Netflix 4K Premium.png"

$fileTask = [Windows.Storage.StorageFile]::GetFileFromPathAsync($filePath)
$asTaskMethod = [System.WindowsRuntimeSystemExtensions].GetMethods() | Where-Object { $_.Name -eq 'AsTask' -and $_.GetParameters().Count -eq 1 } | Select-Object -First 1

Write-Host "File exists: $(Test-Path $filePath)"
Write-Host "OcrEn created: $($ocrEn -ne $null)"
