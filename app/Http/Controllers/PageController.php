<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function support(): \Illuminate\View\View
    {
        return view('pages.support');
    }

    public function submitSupport(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $logDir = storage_path('app/support');
        if (! is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $entry = [
            'received_at' => now()->toIso8601String(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'ip' => $request->ip(),
        ];

        file_put_contents(
            $logDir . '/requests.log',
            json_encode($entry, JSON_UNESCAPED_SLASHES) . PHP_EOL,
            FILE_APPEND
        );

        return back()->with('support_submitted', true);
    }

    public function policies(): \Illuminate\View\View
    {
        return view('pages.policies');
    }

    public function terms(): \Illuminate\View\View
    {
        return view('pages.terms');
    }

    public function privacy(): \Illuminate\View\View
    {
        return view('pages.privacy');
    }

    public function learn(): \Illuminate\View\View
    {
        return view('pages.learn');
    }

    public function recommendations(): \Illuminate\View\View
    {
        return view('pages.recommendations');
    }
}
