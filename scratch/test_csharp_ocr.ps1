$csharpCode = @"
using System;
using System.IO;
using System.Threading.Tasks;
using Windows.Storage;
using Windows.Graphics.Imaging;
using Windows.Media.Ocr;
using Windows.Globalization;

public class WinOcr {
    public static string Recognize(string filePath, string langCode) {
        return RecognizeAsync(filePath, langCode).GetAwaiter().GetResult();
    }

    private static async Task<string> RecognizeAsync(string filePath, string langCode) {
        try {
            StorageFile file = await StorageFile.GetFileFromPathAsync(filePath);
            using (var stream = await file.OpenAsync(FileAccessMode.Read)) {
                BitmapDecoder decoder = await BitmapDecoder.CreateAsync(stream);
                SoftwareBitmap bitmap = await decoder.GetSoftwareBitmapAsync();
                Language lang = new Language(langCode);
                OcrEngine engine = OcrEngine.TryCreateFromLanguage(lang);
                if (engine == null) return "ENGINE_NULL";
                OcrResult result = await engine.RecognizeAsync(bitmap);
                return result.Text;
            }
        } catch (Exception ex) {
            return "ERROR: " + ex.Message;
        }
    }
}
"@

$winmdPath = "C:\Windows\System32\WinMetadata\Windows.Foundation.winmd"
$refs = @(
    "System.Runtime.WindowsRuntime",
    "C:\Windows\System32\WinMetadata\Windows.Foundation.winmd",
    "C:\Windows\System32\WinMetadata\Windows.Storage.winmd",
    "C:\Windows\System32\WinMetadata\Windows.Graphics.winmd",
    "C:\Windows\System32\WinMetadata\Windows.Media.winmd",
    "C:\Windows\System32\WinMetadata\Windows.Globalization.winmd"
)

Add-Type -TypeDefinition $csharpCode -ReferencedAssemblies $refs

$testFile = "C:\Users\elnou\Downloads\New folder\Offre Netflix 4K Premium.png"
$resAr = [WinOcr]::Recognize($testFile, "ar-SA")
$resEn = [WinOcr]::Recognize($testFile, "en-US")

Write-Host "AR Result: $resAr"
Write-Host "EN Result: $resEn"
