<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AcceptanceCheck extends Command
{
    protected $signature = 'bastion:acceptance';
    protected $description = 'Run Phase 1 acceptance checks';

    public function handle(): int
    {
        $checks = [
            'No emoji in codebase' => $this->checkNoEmoji(),
            'CSS variables used' => $this->checkCssVariables(),
            'No hardcoded hex colors' => $this->checkNoHardcodedHex(),
            'No TODO/FIXME comments' => $this->checkNoTodo(),
            'No console.log' => $this->checkNoConsoleLog(),
            'No href="#" placeholders' => $this->checkNoHashHref(),
            'Semantic HTML present' => $this->checkSemanticHtml(),
            'Tabular-nums on money' => $this->checkTabularNums(),
            'Accessible labels' => $this->checkAccessibleLabels(),
        ];

        $passed = 0;
        $failed = 0;

        foreach ($checks as $name => $result) {
            if ($result) {
                $this->info("PASS: {$name}");
                $passed++;
            } else {
                $this->error("FAIL: {$name}");
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Results: {$passed} passed, {$failed} failed");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function checkNoEmoji(): bool
    {
        $files = File::allFiles(resource_path());
        foreach ($files as $file) {
            if (preg_match('/\p{Extended_Pictographic}/u', $file->getContents())) {
                return false;
            }
        }
        return true;
    }

    private function checkCssVariables(): bool
    {
        return File::exists(public_path('css/tokens.css'));
    }

    private function checkNoHardcodedHex(): bool
    {
        $files = File::allFiles(resource_path('views'));
        foreach ($files as $file) {
            if (preg_match('/#[0-9a-fA-F]{3,6}/', $file->getContents())) {
                return false;
            }
        }
        return true;
    }

    private function checkNoTodo(): bool
    {
        $files = File::allFiles([app_path(), resource_path()]);
        foreach ($files as $file) {
            if (preg_match('/\/\/\s*(TODO|FIXME|HACK)/i', $file->getContents())) {
                return false;
            }
        }
        return true;
    }

    private function checkNoConsoleLog(): bool
    {
        $files = File::allFiles([resource_path('js'), public_path('js')]);
        foreach ($files as $file) {
            if (str_contains($file->getContents(), 'console.log')) {
                return false;
            }
        }
        return true;
    }

    private function checkNoHashHref(): bool
    {
        $files = File::allFiles(resource_path('views'));
        foreach ($files as $file) {
            if (preg_match('/href\s*=\s*["\']#["\']/', $file->getContents())) {
                return false;
            }
        }
        return true;
    }

    private function checkSemanticHtml(): bool
    {
        $files = File::allFiles(resource_path('views'));
        $hasHeader = false;
        $hasMain = false;
        $hasNav = false;

        foreach ($files as $file) {
            $content = $file->getContents();
            if (str_contains($content, '<header')) $hasHeader = true;
            if (str_contains($content, '<main')) $hasMain = true;
            if (str_contains($content, '<nav')) $hasNav = true;
        }

        return $hasHeader && $hasMain && $hasNav;
    }

    private function checkTabularNums(): bool
    {
        $files = File::allFiles(resource_path('views'));
        foreach ($files as $file) {
            if (preg_match('/font-variant-numeric:\s*tabular-nums/', $file->getContents())) {
                return true;
            }
        }
        return false;
    }

    private function checkAccessibleLabels(): bool
    {
        $files = File::allFiles(resource_path('views'));
        foreach ($files as $file) {
            if (preg_match('/aria-label\s*=/', $file->getContents())) {
                return true;
            }
        }
        return false;
    }
}
