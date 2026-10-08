<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialService;
use App\Models\FinancialServiceApplication;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinancialServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('web');
        $this->middleware('auth');
        $this->middleware('role:Super Admin');
    }

    public function __invoke(Request $request): \Illuminate\View\View
    {
        return $this->index($request);
    }

    public function index(Request $request): \Illuminate\View\View
    {
        $services = FinancialService::orderBy('sort_order')->get();

        return view('admin.financial-services', [
            'services' => $services,
        ]);
    }

    public function applications(Request $request, FinancialService $service): \Illuminate\View\View
    {
        $applications = FinancialServiceApplication::where('financial_service_id', $service->id)
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.financial-service-applications', [
            'service' => $service,
            'applications' => $applications,
        ]);
    }

    public function approveApplication(Request $request, FinancialServiceApplication $application): \Illuminate\Http\RedirectResponse
    {
        $application->update([
            'status' => 'approved',
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
            'review_notes' => $request->input('notes'),
        ]);

        AuditLog::log('financial_service_application_approved', $application, [
            'service' => $application->service->name,
            'amount' => $application->amount,
        ]);

        return back()->with('status', 'Application approved.');
    }

    public function rejectApplication(Request $request, FinancialServiceApplication $application): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'notes' => ['required', 'string', 'max:1000'],
        ]);

        $application->update([
            'status' => 'rejected',
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
            'review_notes' => $request->input('notes'),
        ]);

        AuditLog::log('financial_service_application_rejected', $application, [
            'service' => $application->service->name,
        ]);

        return back()->with('status', 'Application rejected.');
    }
}
