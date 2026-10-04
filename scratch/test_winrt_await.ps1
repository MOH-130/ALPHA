Add-Type -AssemblyName System.Runtime.WindowsRuntime
[Windows.Globalization.Language, Windows.Globalization, ContentType = WindowsRuntime] | Out-Null
[Windows.Media.Ocr.OcrEngine, Windows.Media.Ocr, ContentType = WindowsRuntime] | Out-Null
[Windows.Graphics.Imaging.BitmapDecoder, Windows.Graphics.Imaging, ContentType = WindowsRuntime] | Out-Null
[Windows.Storage.StorageFile, Windows.Storage, ContentType = WindowsRuntime] | Out-Null

$asTaskGeneric = [System.WindowsRuntimeSystemExtensions].GetMethods() | Where-Object { 
    $_.Name -eq 'AsTask' -and $_.GetParameters().Count -eq 1 -and $_.GetParameters()[0].ParameterType.Name -eq 'IAsyncOperation`1' 
} | Select-Object -First 1

function Await($asyncOp) {
    $type = $asyncOp.GetType().GetInterfaces() | Where-Object { $_.Name -eq 'IAsyncOperation`1' } | Select-Object -First 1
    $innerType = $type.GetGenericArguments()[0]
    $asTask = $asTaskGeneric.MakeGenericMethod($innerType)
    $task = $asTask.Invoke($null, @($asyncOp))
    $task.Wait()
    return $task.Result
}

$langAr = [Windows.Globalization.Language]::new("ar-SA")
$ocrAr = [Windows.Media.Ocr.OcrEngine]::TryCreateFromLanguage($langAr)
$langEn = [Windows.Globalization.Language]::new("en-US")
$ocrEn = [Windows.Media.Ocr.OcrEngine]::TryCreateFromLanguage($langEn)

$filePath = "C:\Users\elnou\Downloads\New folder\Offre Netflix 4K Premium.png"
$storageFile = Await ([Windows.Storage.StorageFile]::GetFileFromPathAsync($filePath))
$stream = Await ($storageFile.OpenAsync([Windows.Storage.FileAccessMode]::Read))
$decoder = Await ([Windows.Graphics.Imaging.BitmapDecoder]::CreateAsync($stream))
$softwareBitmap = Await ($decoder.GetSoftwareBitmapAsync())

$ocrResultAr = Await ($ocrAr.RecognizeAsync($softwareBitmap))
$ocrResultEn = Await ($ocrEn.RecognizeAsync($softwareBitmap))

Write-Host "=== ARABIC OCR ==="
$ocrResultAr.Lines | ForEach-Object { Write-Host $_.Text }

Write-Host "=== ENGLISH OCR ==="
$ocrResultEn.Lines | ForEach-Object { Write-Host $_.Text }
