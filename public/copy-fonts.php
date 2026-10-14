<?php
declare(strict_types=1);
// Standalone font deployment helper for Bastion Peak
// This file is copied to public_html by deploy.php's case 'assets' block
// via the file list extension

$key = $_GET['key'] ?? '';
$expectedKey = 'bastionpeak-deploy-secret-737940260';

if (!hash_equals($expectedKey, $key)) {
    http_response_code(403);
    exit('Forbidden. Add ?key=YOUR_KEY to the URL.');
}

// Auto-detect app dir
$srcDir = null;
foreach ([__DIR__ . '/../bastionpeak-laravel/public/fonts', __DIR__ . '/fonts', dirname(__DIR__) . '/bastionpeak-laravel/public/fonts'] as $candidate) {
    $real = realpath($candidate);
    if ($real && is_dir($real)) {
        $srcDir = $real;
        break;
    }
}

if (!$srcDir) {
    exit("ERROR: Could not find source fonts directory.\n");
}

$dstDir = '/home/bastionpeaks/public_html/fonts';

if (!is_dir($dstDir)) {
    mkdir($dstDir, 0755, true);
}

$files = scandir($srcDir) ?: [];
$copied = 0;
foreach ($files as $item) {
    if ($item === '.' || $item === '..') continue;
    $src = $srcDir . '/' . $item;
    $dst = $dstDir . '/' . $item;
    if (is_file($src)) {
        if (copy($src, $dst)) {
            echo "Copied: $item (" . filesize($dst) . " bytes)\n";
            $copied++;
        } else {
            echo "FAILED to copy: $item\n";
        }
    }
}

echo "\nTotal: $copied files deployed to $dstDir\n";

// Verify
echo "\nVerification:\n";
foreach (['schibsted.css', 'ibm-plex.css'] as $f) {
    $path = $dstDir . '/' . $f;
    echo "  $f: " . (file_exists($path) ? "EXISTS (" . filesize($path) . " bytes)" : "MISSING") . "\n";
}
