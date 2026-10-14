<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DeployFonts extends Command
{
    protected $signature = 'bastion:deploy-fonts';
    protected $description = 'Copy font CSS files from public/fonts/ to the web root fonts directory';

    public function handle(): int
    {
        $src = public_path('fonts');
        $dst = '/home/bastionpeaks/public_html/fonts';

        if (!is_dir($src)) {
            $this->error("Source directory not found: $src");
            return self::FAILURE;
        }

        if (!is_dir($dst)) {
            @mkdir($dst, 0755, true);
        }

        $files = scandir($src) ?: [];
        $copied = 0;
        foreach ($files as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $s = $src . '/' . $item;
            $d = $dst . '/' . $item;
            if (is_dir($s)) {
                $this->deployRecursive($s, $d);
            } else {
                @copy($s, $d);
                $copied++;
                $this->info("Copied: $item");
            }
        }

        $this->info("Total: $copied font files deployed to $dst");
        return self::SUCCESS;
    }

    private function deployRecursive(string $src, string $dst): void
    {
        if (!is_dir($dst)) {
            @mkdir($dst, 0755, true);
        }
        foreach (scandir($src) ?: [] as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $s = $src . '/' . $item;
            $d = $dst . '/' . $item;
            if (is_dir($s)) {
                $this->deployRecursive($s, $d);
            } else {
                @copy($s, $d);
                $this->info("Copied: $d");
            }
        }
    }
}
