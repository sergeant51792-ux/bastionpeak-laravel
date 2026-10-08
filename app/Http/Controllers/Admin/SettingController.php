<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use App\Services\NotificationService;

class SettingController extends Controller
{
    public function __construct(protected NotificationService $notifications) {}

    public function __invoke(Request $request): \Illuminate\View\View
    {
        $tab = $request->query('tab', 'general');

        return view('admin.settings', [
            'tab'     => $tab,
            'settings' => SystemSetting::all()->groupBy('group'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'group'   => ['required', 'in:general,security,transactions,currencies'],
            'key'     => ['required', 'string', 'max:100'],
            'value'   => ['required', 'array'],
        ]);

        $setting = SystemSetting::where('group', $validated['group'])
            ->where('key', $validated['key'])
            ->firstOrFail();

        $oldValue = $setting->value;

        $setting->update(['value' => $validated['value']]);

        // Log to audit trail
        \App\Models\AuditLog::create([
            'admin_id'    => auth()->id(),
            'action'      => 'setting_updated',
            'target_type' => 'system_setting',
            'target_id'   => $setting->id,
            'old_value'   => $oldValue,
            'new_value'   => $validated['value'],
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);

        return back()->with('status', 'Settings saved.');
    }
}
