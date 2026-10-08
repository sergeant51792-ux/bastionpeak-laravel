<?php

$pattern = '/<x-icon name="([^"]+)" <svg ([^>]+) \/>/';
$replacement = '<x-icon name="$1" $2 />';

$files = glob('resources/views/*.blade.php');
$files = array_merge($files, glob('resources/views/**/*.blade.php'));
$files = array_merge($files, glob('resources/views/**/**/*.blade.php'));

foreach ($files as $file) {
    $content = file_get_contents($file);
    $newContent = preg_replace($pattern, $replacement, $content);
    if ($newContent !== $content) {
        file_put_contents($file, $newContent);
        echo 'Fixed: ' . $file . PHP_EOL;
    }
}
