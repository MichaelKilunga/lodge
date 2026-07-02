<?php

namespace App\Http\Controllers;

use App\Models\MarketingTrafficLog;
use App\Models\MarketingReport;
use App\Models\MarketingStrategyItem;
use App\Models\Transaction;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketingController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'trends');
        $period = $request->get('period', '30days');

        // Date range filtering
        $now = Carbon::now();
        switch ($period) {
            case 'today':
                $from = $now->copy()->startOfDay();
                $to = $now->copy()->endOfDay();
                $prevFrom = $now->copy()->subDay()->startOfDay();
                $prevTo = $now->copy()->subDay()->endOfDay();
                break;
            case '7days':
                $from = $now->copy()->subDays(6)->startOfDay();
                $to = $now->copy()->endOfDay();
                $prevFrom = $now->copy()->subDays(13)->startOfDay();
                $prevTo = $now->copy()->subDays(7)->endOfDay();
                break;
            case 'this_month':
                $from = $now->copy()->startOfMonth();
                $to = $now->copy()->endOfMonth();
                $prevFrom = $now->copy()->subMonth()->startOfMonth();
                $prevTo = $now->copy()->subMonth()->endOfMonth();
                break;
            default: // 30days
                $from = $now->copy()->subDays(29)->startOfDay();
                $to = $now->copy()->endOfDay();
                $prevFrom = $now->copy()->subDays(59)->startOfDay();
                $prevTo = $now->copy()->subDays(30)->endOfDay();
                break;
        }

        // 1. KPI Metrics
        $logs = MarketingTrafficLog::whereBetween('created_at', [$from, $to]);
        $prevLogs = MarketingTrafficLog::whereBetween('created_at', [$prevFrom, $prevTo]);

        $totalVisits = (clone $logs)->where('event_type', 'page_view')->count();
        $prevVisits = (clone $prevLogs)->where('event_type', 'page_view')->count();
        $visitsGrowth = $prevVisits > 0 ? round((($totalVisits - $prevVisits) / $prevVisits) * 100, 1) : 100;

        $uniqueVisitors = (clone $logs)->where('event_type', 'page_view')->distinct('session_id')->count('session_id');
        
        $whatsappClicks = (clone $logs)->where('event_type', 'whatsapp_click')->count();
        $phoneClicks = (clone $logs)->where('event_type', 'phone_click')->count();
        $contactForms = (clone $logs)->where('event_type', 'contact_form_submit')->count();

        // Actual bookings during period
        $transactions = Transaction::with('payment')
            ->whereBetween('created_at', [$from, $to])
            ->get();
        $totalBookings = $transactions->count();
        $totalRevenue = $transactions->sum(fn ($t) => optional($t->payment)->total ?? 0);

        // Conversion rate
        $conversionRate = $totalVisits > 0 ? round(($totalBookings / $totalVisits) * 100, 2) : 0;

        // 2. Acquisition Channels Breakdown (Sources)
        $sourcesBreakdown = (clone $logs)->where('event_type', 'page_view')
            ->select('source', DB::raw('count(*) as count'))
            ->groupBy('source')
            ->orderByDesc('count')
            ->get();

        // 3. Device Breakdown
        $devicesBreakdown = (clone $logs)->where('event_type', 'page_view')
            ->select('device_type', DB::raw('count(*) as count'))
            ->groupBy('device_type')
            ->get();

        // 4. Daily Chart Trend Data (Last 14 days or period days)
        $chartDays = $period === 'today' ? 24 : ($period === '7days' ? 7 : 30);
        $dailyDates = [];
        $dailyVisitsSeries = [];
        $dailyInteractionsSeries = [];

        if ($period === 'today') {
            for ($i = 0; $i < 24; $i++) {
                $hourStr = sprintf('%02d:00', $i);
                $dailyDates[] = $hourStr;
                $hourStart = $from->copy()->addHours($i);
                $hourEnd = $hourStart->copy()->endOfHour();
                
                $dailyVisitsSeries[] = MarketingTrafficLog::whereBetween('created_at', [$hourStart, $hourEnd])
                    ->where('event_type', 'page_view')->count();
                $dailyInteractionsSeries[] = MarketingTrafficLog::whereBetween('created_at', [$hourStart, $hourEnd])
                    ->whereIn('event_type', ['whatsapp_click', 'phone_click', 'contact_form_submit', 'booking_click'])->count();
            }
        } else {
            for ($i = $chartDays - 1; $i >= 0; $i--) {
                $dayDate = Carbon::now()->subDays($i);
                $dailyDates[] = $dayDate->format('M d');
                
                $dayStart = $dayDate->copy()->startOfDay();
                $dayEnd = $dayDate->copy()->endOfDay();
                
                $dailyVisitsSeries[] = MarketingTrafficLog::whereBetween('created_at', [$dayStart, $dayEnd])
                    ->where('event_type', 'page_view')->count();
                $dailyInteractionsSeries[] = MarketingTrafficLog::whereBetween('created_at', [$dayStart, $dayEnd])
                    ->whereIn('event_type', ['whatsapp_click', 'phone_click', 'contact_form_submit', 'booking_click'])->count();
            }
        }

        // Top Viewed Pages
        $topPages = (clone $logs)->where('event_type', 'page_view')
            ->select('page_type', 'url', DB::raw('count(*) as views'))
            ->groupBy('page_type', 'url')
            ->orderByDesc('views')
            ->limit(8)
            ->get();

        // TAB 2: Weekly Digital Marketing & Growth Report Data (Document 2)
        $latestReport = MarketingReport::latest()->first();
        $allReports = MarketingReport::orderByDesc('id')->get();

        // If no report exists or we want default report values:
        $defaultReport = [
            'week_number' => 'Week ' . $now->weekOfYear,
            'reporting_period' => $now->copy()->startOfWeek()->format('M d, Y') . ' - ' . $now->copy()->endOfWeek()->format('M d, Y'),
            'prepared_by' => auth()->user()->name ?? 'Digital Marketing Lead',
            'department' => 'MEDIA & ICT',
            'date_submitted' => $now->format('Y-m-d'),
            'reviewed_by' => 'General Manager',
            'tasks_data' => [
                ['no' => 1, 'task' => 'Publish 3 Social Media Posters & 1 Reel on Facebook/Instagram', 'status' => 'Completed', 'remarks' => 'High engagement on room showcase'],
                ['no' => 2, 'task' => 'Update Google Business Profile photos and reply to recent reviews', 'status' => 'Completed', 'remarks' => 'Uploaded 7 new exterior photos'],
                ['no' => 3, 'task' => 'Optimize Website Booking Form & SEO meta tags', 'status' => 'In Progress', 'remarks' => 'Improved mobile page speed'],
                ['no' => 4, 'task' => 'Monitor WhatsApp inquiries and follow up on pending leads', 'status' => 'Completed', 'remarks' => 'All inquiries answered within 15 mins'],
                ['no' => 5, 'task' => 'Review Meta Ads campaign CTR and optimize ad copy', 'status' => 'Completed', 'remarks' => 'Cost per lead reduced by 12%'],
            ],
            'kpi_data' => [
                ['kpi' => 'Planned Tasks Completed', 'target' => '100%', 'actual' => '80%', 'status' => 'On Track'],
                ['kpi' => 'Social Media Posts', 'target' => '3', 'actual' => '3', 'status' => 'Achieved'],
                ['kpi' => 'Reels Published', 'target' => '1', 'actual' => '1', 'status' => 'Achieved'],
                ['kpi' => 'Videos Produced', 'target' => '1', 'actual' => '1', 'status' => 'Achieved'],
                ['kpi' => 'Photos Captured', 'target' => '7', 'actual' => '8', 'status' => 'Exceeded'],
                ['kpi' => 'Website Visitors', 'target' => '250', 'actual' => strval($totalVisits), 'status' => 'Measured via Website'],
                ['kpi' => 'WhatsApp Inquiries', 'target' => '20', 'actual' => strval($whatsappClicks), 'status' => 'Tracked Button Clicks'],
                ['kpi' => 'Phone Calls', 'target' => '15', 'actual' => strval($phoneClicks), 'status' => 'Tracked Call Clicks'],
                ['kpi' => 'Booking Requests', 'target' => '10', 'actual' => strval($totalBookings), 'status' => 'Confirmed Online'],
                ['kpi' => 'New Guests', 'target' => '8', 'actual' => strval(Customer::count()), 'status' => 'Total Registered'],
                ['kpi' => 'Google Reviews', 'target' => '5', 'actual' => '4', 'status' => 'Good Rating'],
                ['kpi' => 'Meta Ads CTR', 'target' => '2.5%', 'actual' => '3.1%', 'status' => 'Exceeded'],
                ['kpi' => 'Cost per Lead', 'target' => '$5.00', 'actual' => '$3.80', 'status' => 'Optimized'],
            ],
            'social_media_data' => [
                ['platform' => 'Facebook', 'planned' => '8 (3 posters, 1 reel, 4 stories)', 'posted' => '8', 'reels' => '1', 'stories' => '4', 'status' => 'On Target'],
                ['platform' => 'Instagram', 'planned' => '8 (3 posters, 1 reel, 4 stories)', 'posted' => '8', 'reels' => '1', 'stories' => '4', 'status' => 'On Target'],
                ['platform' => 'TikTok', 'planned' => '1 reel', 'posted' => '1', 'reels' => '1', 'stories' => '0', 'status' => 'Completed'],
            ],
            'website_performance_data' => [
                ['metric' => 'Website Visitors', 'value' => number_format($totalVisits)],
                ['metric' => 'Unique Visitors', 'value' => number_format($uniqueVisitors)],
                ['metric' => 'Online Bookings Generated', 'value' => number_format($totalBookings)],
                ['metric' => 'WhatsApp Button Clicks', 'value' => number_format($whatsappClicks)],
                ['metric' => 'Phone Call Clicks', 'value' => number_format($phoneClicks)],
                ['metric' => 'Average Conversion Rate', 'value' => $conversionRate . '%'],
            ],
            'google_business_data' => [
                ['metric' => 'Searches', 'value' => '412'],
                ['metric' => 'Profile Views', 'value' => '680'],
                ['metric' => 'Website Clicks', 'value' => '145'],
                ['metric' => 'Phone Calls', 'value' => '32'],
                ['metric' => 'Direction Requests', 'value' => '84'],
                ['metric' => 'New Reviews', 'value' => '4'],
                ['metric' => 'Average Rating', 'value' => '4.8 ★'],
            ],
            'bookings_leads_data' => [
                ['source' => 'Facebook', 'leads' => 18, 'bookings' => 4, 'revenue' => '$520'],
                ['source' => 'Instagram', 'leads' => 24, 'bookings' => 6, 'revenue' => '$780'],
                ['source' => 'TikTok', 'leads' => 5, 'bookings' => 1, 'revenue' => '$120'],
                ['source' => 'Google Search', 'leads' => 35, 'bookings' => 12, 'revenue' => '$1,650'],
                ['source' => 'Google Business Profile', 'leads' => 14, 'bookings' => 5, 'revenue' => '$640'],
                ['source' => 'Website Direct', 'leads' => 20, 'bookings' => 8, 'revenue' => '$980'],
                ['source' => 'WhatsApp', 'leads' => 28, 'bookings' => 10, 'revenue' => '$1,350'],
                ['source' => 'Phone Calls', 'leads' => 12, 'bookings' => 4, 'revenue' => '$480'],
                ['source' => 'Walk-ins', 'leads' => 10, 'bookings' => 7, 'revenue' => '$890'],
            ],
            'paid_ads_data' => [
                ['campaign' => 'Meta Ads (Bella Vista Getaway)', 'budget' => '$30 / month ($1/day)', 'reach' => '14,250', 'clicks' => '440', 'leads' => '38', 'cost_lead' => '$0.78', 'status' => 'Active & Performing'],
            ],
            'content_created_data' => [
                ['type' => 'Graphics Designed', 'quantity' => '6 Promo Posters'],
                ['type' => 'Reels Produced', 'quantity' => '2 Video Walkthroughs'],
                ['type' => 'Videos Produced', 'quantity' => '1 Room Highlights HD'],
                ['type' => 'Health Articles / Blog Posts', 'quantity' => '1 Blog Post on Local Attractions'],
                ['type' => 'Photos Captured', 'quantity' => '15 High-Res Room & Garden Shots'],
            ],
            'challenges_data' => [
                ['challenge' => 'Slow mobile network loading speed for high-res gallery images', 'impact' => 'Moderate bounce rate on mobile devices', 'action' => 'Implemented WebP compression & lazy loading on images'],
            ],
            'achievements_data' => [
                ['achievement' => 'Increased direct website bookings by 18% compared to previous month'],
                ['achievement' => 'Maintained 4.8 star average rating on Google Business Profile'],
                ['achievement' => 'Reduced cost per lead on Meta Ads below $1.00 through targeted audience refinement'],
            ],
            'next_week_plan_data' => [
                ['no' => 1, 'activity' => 'Launch Weekend Special Package Promotion on Instagram & WhatsApp', 'priority' => 'High', 'responsible' => 'MEDIA & ICT Team'],
                ['no' => 2, 'activity' => 'Upload drone footage of lodge exterior and gardens to website gallery', 'priority' => 'Medium', 'responsible' => 'Photography Lead'],
                ['no' => 3, 'activity' => 'Optimize Google Business Profile Q&A section with top guest FAQs', 'priority' => 'High', 'responsible' => 'Marketing Specialist'],
            ]
        ];

        // TAB 3: Digital Growth Strategy Checklist (Document 1)
        $strategyItems = MarketingStrategyItem::orderBy('area_number')->orderBy('id')->get()->groupBy('area_name');

        return view('marketing.index', compact(
            'tab', 'period', 'from', 'to',
            'totalVisits', 'visitsGrowth', 'uniqueVisitors',
            'whatsappClicks', 'phoneClicks', 'contactForms',
            'totalBookings', 'totalRevenue', 'conversionRate',
            'sourcesBreakdown', 'devicesBreakdown',
            'dailyDates', 'dailyVisitsSeries', 'dailyInteractionsSeries',
            'topPages',
            'latestReport', 'allReports', 'defaultReport',
            'strategyItems'
        ));
    }

    public function trackInteraction(Request $request)
    {
        $eventType = $request->input('event_type', 'page_view');
        $url = $request->input('url', $request->header('referer') ?: url('/'));
        $pageType = $request->input('page_type', 'Home');
        
        // Detect source from referrer or query params
        $referrer = $request->input('referrer', $request->header('referer'));
        $source = 'Direct';
        if ($request->has('utm_source') && !empty($request->input('utm_source'))) {
            $source = ucfirst($request->input('utm_source'));
        } elseif ($referrer) {
            $refLower = strtolower($referrer);
            if (str_contains($refLower, 'facebook.com') || str_contains($refLower, 'fb.me')) $source = 'Facebook';
            elseif (str_contains($refLower, 'instagram.com')) $source = 'Instagram';
            elseif (str_contains($refLower, 'tiktok.com')) $source = 'TikTok';
            elseif (str_contains($refLower, 'google.com')) $source = 'Google Search';
            elseif (str_contains($refLower, 'whatsapp.com')) $source = 'WhatsApp';
            else $source = 'Referral';
        }

        // Detect device
        $userAgent = $request->header('User-Agent');
        $deviceType = 'Desktop';
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($userAgent))) {
            $deviceType = 'Tablet';
        } elseif (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($userAgent))) {
            $deviceType = 'Mobile';
        }

        MarketingTrafficLog::create([
            'session_id'  => $request->session()->getId() ?: md5($request->ip() . '_' . date('Y-m-d')),
            'ip_address'  => $request->ip(),
            'url'         => substr($url, 0, 255),
            'page_type'   => substr($pageType, 0, 100),
            'referrer'    => $referrer ? substr($referrer, 0, 500) : null,
            'source'      => substr($source, 0, 100),
            'device_type' => $deviceType,
            'event_type'  => $eventType,
            'utm_source'  => $request->input('utm_source'),
            'utm_medium'  => $request->input('utm_medium'),
            'utm_campaign'=> $request->input('utm_campaign'),
        ]);

        return response()->json(['status' => 'success']);
    }

    public function saveReport(Request $request)
    {
        $data = $request->validate([
            'week_number'      => 'required|string',
            'reporting_period' => 'required|string',
            'prepared_by'      => 'nullable|string',
            'department'       => 'nullable|string',
            'date_submitted'   => 'nullable|date',
            'reviewed_by'      => 'nullable|string',
            'tasks_data'       => 'nullable|array',
            'kpi_data'         => 'nullable|array',
            'social_media_data'=> 'nullable|array',
            'website_performance_data' => 'nullable|array',
            'google_business_data'     => 'nullable|array',
            'bookings_leads_data'      => 'nullable|array',
            'paid_ads_data'            => 'nullable|array',
            'content_created_data'     => 'nullable|array',
            'challenges_data'          => 'nullable|array',
            'achievements_data'        => 'nullable|array',
            'next_week_plan_data'      => 'nullable|array',
        ]);

        MarketingReport::create($data);

        return redirect()->route('marketing.index', ['tab' => 'report'])
            ->with('success', '✅ Weekly Digital Marketing & Growth Report saved successfully!');
    }

    public function updateStrategyItem(Request $request, MarketingStrategyItem $item)
    {
        $request->validate([
            'status' => 'required|string',
            'cost'   => 'nullable|string',
            'notes'  => 'nullable|string',
        ]);

        $item->update([
            'status' => $request->status,
            'cost'   => $request->cost,
            'notes'  => $request->notes,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'item' => $item]);
        }

        return redirect()->back()->with('success', 'Strategy task updated successfully!');
    }
}
