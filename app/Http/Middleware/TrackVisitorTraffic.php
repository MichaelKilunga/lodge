<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\MarketingTrafficLog;

class TrackVisitorTraffic
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only track GET requests that return HTML or 200 OK, exclude admin/api/asset requests
        if ($request->isMethod('GET') && 
            !$request->is('admin*') && 
            !$request->is('login*') && 
            !$request->is('logout*') && 
            !$request->is('marketing*') && 
            !$request->is('api*') && 
            !$request->ajax()) {
            
            try {
                $url = $request->fullUrl();
                $routeName = $request->route() ? $request->route()->getName() : '';

                // Determine Page Type
                $pageType = 'Other';
                if ($request->is('/') || $routeName === 'public.home') $pageType = 'Home';
                elseif ($request->is('rooms*') || str_contains((string)$routeName, 'room')) $pageType = 'Rooms';
                elseif ($request->is('checkout*')) $pageType = 'Checkout';
                elseif ($request->is('blog*')) $pageType = 'Blog';
                elseif ($request->is('location*')) $pageType = 'Location';

                // Detect Source
                $referrer = $request->headers->get('referer');
                $source = 'Direct';
                if ($request->has('utm_source')) {
                    $source = ucfirst($request->get('utm_source'));
                } elseif ($referrer) {
                    $refLower = strtolower($referrer);
                    if (str_contains($refLower, 'facebook.com') || str_contains($refLower, 'fb.me')) $source = 'Facebook';
                    elseif (str_contains($refLower, 'instagram.com')) $source = 'Instagram';
                    elseif (str_contains($refLower, 'tiktok.com')) $source = 'TikTok';
                    elseif (str_contains($refLower, 'google.com')) $source = 'Google Search';
                    elseif (str_contains($refLower, 'whatsapp.com')) $source = 'WhatsApp';
                    else $source = 'Referral';
                }

                // Detect Device
                $userAgent = $request->headers->get('User-Agent');
                $deviceType = 'Desktop';
                if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower((string)$userAgent))) {
                    $deviceType = 'Tablet';
                } elseif (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower((string)$userAgent))) {
                    $deviceType = 'Mobile';
                }

                // Prevent logging every reload in a single second by checking if exact same log exists in past 30 seconds
                $sessionId = $request->session()->getId() ?: md5($request->ip() . '_' . date('Y-m-d'));
                $recentLog = MarketingTrafficLog::where('session_id', $sessionId)
                    ->where('url', substr($url, 0, 255))
                    ->where('event_type', 'page_view')
                    ->where('created_at', '>=', now()->subSeconds(30))
                    ->exists();

                if (!$recentLog) {
                    MarketingTrafficLog::create([
                        'session_id'  => $sessionId,
                        'ip_address'  => $request->ip(),
                        'url'         => substr($url, 0, 255),
                        'page_type'   => substr($pageType, 0, 100),
                        'referrer'    => $referrer ? substr($referrer, 0, 500) : null,
                        'source'      => substr($source, 0, 100),
                        'device_type' => $deviceType,
                        'event_type'  => 'page_view',
                        'utm_source'  => $request->get('utm_source'),
                        'utm_medium'  => $request->get('utm_medium'),
                        'utm_campaign'=> $request->get('utm_campaign'),
                    ]);
                }
            } catch (\Exception $e) {
                // Fail silently so web visitors are never impacted by tracking errors
            }
        }

        return $response;
    }
}
