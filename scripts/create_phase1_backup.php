<?php
/**
 * PHASE 1 BACKUP SCRIPT FOR ALPHA GAMING SHOP
 * Backs up database.sqlite, produces full SQL export, and backs up all product images.
 */

$rootDir = realpath(__DIR__ . '/..');
$timestamp = date('Ymd_His');
$backupBaseDir = $rootDir . '/backups';
$dbBackupDir = $backupBaseDir . '/database';
$imagesBackupDir = $backupBaseDir . '/products_images';
$uploadsBackupDir = $backupBaseDir . '/all_uploads';

@mkdir($dbBackupDir, 0777, true);
@mkdir($imagesBackupDir, 0777, true);
@mkdir($uploadsBackupDir, 0777, true);

echo "====================================================\n";
echo "   ALPHA GAMING SHOP - PHASE 1 BACKUP EXECUTION     \n";
echo "====================================================\n\n";

// 1. BACKUP SQLITE DATABASE
$sourceDb = $rootDir . '/storage/data/database.sqlite';
if (!file_exists($sourceDb)) {
    die("FATAL: Source database not found at $sourceDb\n");
}

$destDb = $dbBackupDir . "/database_backup_{$timestamp}.sqlite";
$destDbLatest = $dbBackupDir . "/database_backup_latest.sqlite";

if (!copy($sourceDb, $destDb)) {
    die("FATAL: Failed to copy database file!\n");
}
copy($destDb, $destDbLatest);

echo "[1/4] SQLite database file copied successfully:\n";
echo "      -> $destDb (" . number_format(filesize($destDb)) . " bytes)\n";

// 2. VERIFY BACKUP INTEGRITY & RECORD COUNTS
$pdoSource = new PDO("sqlite:" . $sourceDb);
$pdoSource->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdoBackup = new PDO("sqlite:" . $destDb);
$pdoBackup->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$tablesStmt = $pdoSource->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");
$tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);

$manifest = [
    'created_at' => date('c'),
    'source_file' => $sourceDb,
    'backup_file' => $destDb,
    'file_size_bytes' => filesize($destDb),
    'sha256' => hash_file('sha256', $destDb),
    'tables_count' => count($tables),
    'tables' => []
];

echo "\n[2/4] Verifying Table Row Counts between Source & Backup:\n";
echo sprintf("      %-25s | %-12s | %-12s | %s\n", "Table Name", "Source Rows", "Backup Rows", "Status");
echo "      --------------------------------------------------------------\n";

$allVerified = true;
foreach ($tables as $table) {
    $cSource = (int)$pdoSource->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
    $cBackup = (int)$pdoBackup->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();

    $status = ($cSource === $cBackup) ? "MATCH [OK]" : "MISMATCH [FAIL]";
    if ($cSource !== $cBackup) $allVerified = false;

    $manifest['tables'][$table] = [
        'rows' => $cSource,
        'verified' => ($cSource === $cBackup)
    ];

    echo sprintf("      %-25s | %-12d | %-12d | %s\n", $table, $cSource, $cBackup, $status);
}

if (!$allVerified) {
    die("\nFATAL: Row count mismatch between source and backup!\n");
}
echo "      -> All tables verified 100% matched.\n";

// 3. GENERATE SQL DUMP (SCHEMA + DATA) FOR CROSS-DATABASE PORTABILITY
$dumpFile = $dbBackupDir . "/database_dump_{$timestamp}.sql";
$dumpLatest = $dbBackupDir . "/database_dump_latest.sql";
$sqlHandle = fopen($dumpFile, 'w');

fwrite($sqlHandle, "-- ALPHA GAMING SHOP SQL DUMP\n");
fwrite($sqlHandle, "-- Generated at: " . date('Y-m-d H:i:s') . "\n");
fwrite($sqlHandle, "-- Tables: " . count($tables) . "\n\n");

foreach ($tables as $table) {
    // Schema
    $schemaStmt = $pdoSource->query("SELECT sql FROM sqlite_master WHERE type='table' AND name='{$table}'");
    $createSql = $schemaStmt->fetchColumn();
    fwrite($sqlHandle, "-- Table: {$table}\n");
    fwrite($sqlHandle, "DROP TABLE IF EXISTS `{$table}`;\n");
    fwrite($sqlHandle, $createSql . ";\n\n");

    // Data
    $rows = $pdoSource->query("SELECT * FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($rows)) {
        foreach ($rows as $row) {
            $cols = array_keys($row);
            $colList = implode('`, `', $cols);
            $vals = array_map(function($v) use ($pdoSource) {
                if ($v === null) return 'NULL';
                return $pdoSource->quote((string)$v);
            }, array_values($row));
            $valList = implode(', ', $vals);
            fwrite($sqlHandle, "INSERT INTO `{$table}` (`{$colList}`) VALUES ({$valList});\n");
        }
        fwrite($sqlHandle, "\n");
    }
}
fclose($sqlHandle);
copy($dumpFile, $dumpLatest);

echo "\n[3/4] SQL dump created successfully:\n";
echo "      -> $dumpFile (" . number_format(filesize($dumpFile)) . " bytes)\n";

// 4. BACKUP PRODUCT IMAGES AND ALL UPLOADS
echo "\n[4/4] Backing up Product Images:\n";
$productImagesSource = $rootDir . '/public/uploads/products';
$productFiles = glob($productImagesSource . '/*.*');
$copiedImages = 0;
$manifest['images'] = [];

foreach ($productFiles as $file) {
    if (is_file($file)) {
        $fileName = basename($file);
        $destFile = $imagesBackupDir . '/' . $fileName;
        copy($file, $destFile);
        $copiedImages++;
        $manifest['images'][$fileName] = [
            'size' => filesize($destFile),
            'sha256' => hash_file('sha256', $destFile)
        ];
    }
}
echo "      -> Copied $copiedImages product images to $imagesBackupDir\n";

// Backup entire uploads directory as well
function copyDirRecursively($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst, 0777, true);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                copyDirRecursively($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}
copyDirRecursively($rootDir . '/public/uploads', $uploadsBackupDir);
echo "      -> Entire uploads directory backed up to $uploadsBackupDir\n";

// Save Manifest
file_put_contents($dbBackupDir . "/backup_manifest_{$timestamp}.json", json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
file_put_contents($dbBackupDir . "/backup_manifest_latest.json", json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "\n====================================================\n";
echo "   PHASE 1 BACKUP COMPLETED AND VERIFIED 100%       \n";
echo "====================================================\n";
