<?php

namespace App\Http\Controllers;

use App\Models\SmsLog;
use App\Services\SmsService;
use Illuminate\Http\Request;

class SmsMonitoringController extends Controller
{
    public function index(Request $request)
    {
        // Authorize access: Super (system admin) and Owner only
        $user = auth()->user();
        $isOwner = ($user->userRole && strcasecmp($user->userRole->name, 'Owner') === 0) || strcasecmp($user->role, 'Owner') === 0;
        
        if (!$user->isSuperAdmin() && !$isOwner) {
            abort(403, 'Unauthorized access. Only Owners and System Administrators are allowed to view this page.');
        }

        $balanceData = SmsService::getBalance();
        
        $totalSent = SmsLog::count();
        $totalSuccess = SmsLog::where('status', 'Success')->count();
        $totalFailed = SmsLog::where('status', 'Failed')->count();
        
        $logs = SmsLog::orderBy('id', 'desc')->paginate(15);

        return view('sms.monitoring', compact('balanceData', 'totalSent', 'totalSuccess', 'totalFailed', 'logs'));
    }
}
