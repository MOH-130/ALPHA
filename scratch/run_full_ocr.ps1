Add-Type -AssemblyName System.Drawing
[Windows.Globalization.Language, Windows.Globalization, ContentType = WindowsRuntime] | Out-Null
[Windows.Media.Ocr.OcrEngine, Windows.Media.Ocr, ContentType = WindowsRuntime] | Out-Null
[Windows.Graphics.Imaging.BitmapDecoder, Windows.Graphics.Imaging, ContentType = WindowsRuntime] | Out-Null
[Windows.Storage.StorageFile, Windows.Storage, ContentType = WindowsRuntime] | Out-Null

$langAr = [Windows.Globalization.Language]::new("ar-SA")
$langEn = [Windows.Globalization.Language]::new("en-US")
$ocrAr = [Windows.Media.Ocr.OcrEngine]::TryCreateFromLanguage($langAr)
$ocrEn = [Windows.Media.Ocr.OcrEngine]::TryCreateFromLanguage($langEn)

function Get-OcrText($filePath, $ocrEngine) {
    try {
        $fileTask = [Windows.Storage.StorageFile]::GetFileFromPathAsync($filePath)
        $storageFile = $fileTask.AsTask().GetAwaiter().GetResult()
        $streamTask = $storageFile.OpenAsync([Windows.Storage.FileAccessMode]::Read)
        $stream = $streamTask.AsTask().GetAwaiter().GetResult()
        
        $decoderTask = [Windows.Graphics.Imaging.BitmapDecoder]::CreateAsync($stream)
        $decoder = $decoderTask.AsTask().GetAwaiter().GetResult()
        
        $bmpTask = $decoder.GetSoftwareBitmapAsync()
        $softwareBitmap = $bmpTask.AsTask().GetAwaiter().GetResult()
        
        $ocrTask = $ocrEngine.RecognizeAsync($softwareBitmap)
        $ocrResult = $ocrTask.AsTask().GetAwaiter().GetResult()
        
        $lines = @()
        foreach ($line in $ocrResult.Lines) {
            $lines += $line.Text
        }
        return ($lines -join "`n")
    } catch {
        return ""
    }
}

$uniqueJson = Get-Content "scratch/unique_images.json" -Raw -Encoding UTF8 | ConvertFrom-Json
$folder = "C:\Users\elnou\Downloads\New folder"
$results = @()

$total = $uniqueJson.Count
$idx = 0

foreach ($item in $uniqueJson) {
    $idx++
    $filePath = Join-Path $folder $item.chosen_name
    Write-Host "[$idx/$total] Running OCR: $($item.chosen_name)..."
    
    $textAr = Get-OcrText -filePath $filePath -ocrEngine $ocrAr
    $textEn = Get-OcrText -filePath $filePath -ocrEngine $ocrEn
    
    $combined = "$textAr`n$textEn"
    
    # Simple regex to catch numbers followed or preceded by DA, DA., د.ج, DA/دج
    $prices = @()
    if ($combined -match '(\d{3,6})\s*(DA|D\.A|د\.ج|دج|دينار|DZ|DZD)') {
        $prices += $matches[0]
    }
    
    $results += [PSCustomObject]@{
        group_id = $item.group_id
        chosen_name = $item.chosen_name
        all_names = $item.all_names
        file_path = $filePath
        size = $item.size
        ocr_ar = $textAr
        ocr_en = $textEn
        detected_prices = $prices
    }
}

$results | ConvertTo-Json -Depth 5 | Set-Content "scratch/ocr_results.json" -Encoding UTF8
Write-Host "OCR Completed for all $total unique images! Saved to scratch/ocr_results.json"
