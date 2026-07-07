<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MarketingStrategyItem;
use App\Models\MarketingTrafficLog;
use App\Models\MarketingReport;
use Carbon\Carbon;

class MarketingSeeder extends Seeder
{
    public function run()
    {
        // 1. Seed Digital Growth Strategy Items (Document 1)
        if (MarketingStrategyItem::count() === 0) {
            $strategyItems = [
                // Area 1
                [1, 'AREA 1: Understanding Bella Vista Lodge', 'Vision', '0/=', 'Completed'],
                [1, 'AREA 1: Understanding Bella Vista Lodge', 'Mission', '0/=', 'Completed'],
                [1, 'AREA 1: Understanding Bella Vista Lodge', 'Core Values', '0/=', 'Completed'],
                [1, 'AREA 1: Understanding Bella Vista Lodge', 'Room Types', '0/=', 'Completed'],
                [1, 'AREA 1: Understanding Bella Vista Lodge', 'Amenities', '0/=', 'Completed'],
                [1, 'AREA 1: Understanding Bella Vista Lodge', 'Location', '0/=', 'Completed'],
                [1, 'AREA 1: Understanding Bella Vista Lodge', 'Pricing', '0/=', 'Completed'],
                [1, 'AREA 1: Understanding Bella Vista Lodge', 'Unique Selling Proposition', '0/=', 'In Progress'],
                [1, 'AREA 1: Understanding Bella Vista Lodge', 'Target Market', '0/=', 'In Progress'],
                [1, 'AREA 1: Understanding Bella Vista Lodge', 'Competitors', '0/=', 'Not Started'],
                // Area 2
                [2, 'AREA 2: Branding', 'Logo', '0/=', 'Completed'],
                [2, 'AREA 2: Branding', 'Brand Colours', '0/=', 'Completed'],
                [2, 'AREA 2: Branding', 'Typography', '0/=', 'Completed'],
                [2, 'AREA 2: Branding', 'Brand Guidelines', '0/=', 'In Progress'],
                [2, 'AREA 2: Branding', 'Business Cards', '50,000/=', 'Not Started'],
                [2, 'AREA 2: Branding', 'Email Signature', '0/=', 'Completed'],
                [2, 'AREA 2: Branding', 'Social Media Templates', '0/=', 'In Progress'],
                [2, 'AREA 2: Branding', 'WhatsApp Branding', '0/=', 'Completed'],
                // Area 3
                [3, 'AREA 3: Professional Photography & Videography', 'Exterior', '70,000/=', 'Completed'],
                [3, 'AREA 3: Professional Photography & Videography', 'Reception', '0/=', 'Completed'],
                [3, 'AREA 3: Professional Photography & Videography', 'Lobby', '0/=', 'Completed'],
                [3, 'AREA 3: Professional Photography & Videography', 'Rooms', '0/=', 'Completed'],
                [3, 'AREA 3: Professional Photography & Videography', 'Restaurant', '0/=', 'In Progress'],
                [3, 'AREA 3: Professional Photography & Videography', 'Conference Hall', '0/=', 'In Progress'],
                [3, 'AREA 3: Professional Photography & Videography', 'Gardens', '0/=', 'Completed'],
                [3, 'AREA 3: Professional Photography & Videography', 'Food', '0/=', 'In Progress'],
                [3, 'AREA 3: Professional Photography & Videography', 'Staff', '0/=', 'Not Started'],
                [3, 'AREA 3: Professional Photography & Videography', 'Drone Footage', '150,000/=', 'Not Started'],
                [3, 'AREA 3: Professional Photography & Videography', 'Guest Experience', '0/=', 'In Progress'],
                [3, 'AREA 3: Professional Photography & Videography', 'Testimonials', '0/=', 'Not Started'],
                // Area 4
                [4, 'AREA 4: Website & Booking', 'Homepage', '180,000/=', 'Completed'],
                [4, 'AREA 4: Website & Booking', 'Rooms', '0/=', 'Completed'],
                [4, 'AREA 4: Website & Booking', 'Gallery', '0/=', 'Completed'],
                [4, 'AREA 4: Website & Booking', 'Restaurant', '0/=', 'Completed'],
                [4, 'AREA 4: Website & Booking', 'Conference', '0/=', 'Completed'],
                [4, 'AREA 4: Website & Booking', 'Contact', '0/=', 'Completed'],
                [4, 'AREA 4: Website & Booking', 'Booking Form', '0/=', 'Completed'],
                [4, 'AREA 4: Website & Booking', 'WhatsApp', '0/=', 'Completed'],
                [4, 'AREA 4: Website & Booking', 'SEO', '0/=', 'In Progress'],
                [4, 'AREA 4: Website & Booking', 'Google Maps', '0/=', 'Completed'],
                // Area 5
                [5, 'AREA 5: Google Business Profile', 'Photos', '0/=', 'Completed'],
                [5, 'AREA 5: Google Business Profile', 'Services', '0/=', 'Completed'],
                [5, 'AREA 5: Google Business Profile', 'Working Hours', '0/=', 'Completed'],
                [5, 'AREA 5: Google Business Profile', 'Reviews', '0/=', 'In Progress'],
                [5, 'AREA 5: Google Business Profile', 'FAQs', '0/=', 'In Progress'],
                [5, 'AREA 5: Google Business Profile', 'Directions', '0/=', 'Completed'],
                // Area 6
                [6, 'AREA 6: Social Media', 'Facebook', '0/=', 'In Progress'],
                [6, 'AREA 6: Social Media', 'Instagram', '0/=', 'In Progress'],
                [6, 'AREA 6: Social Media', 'TikTok', '0/=', 'Not Started'],
                // Area 7
                [7, 'AREA 7: Paid Advertising', 'Meta Ads', '$1 per day', 'In Progress'],
                // Area 8
                [8, 'AREA 8: Analytics', 'Website Visitors', '0/=', 'Completed'],
                [8, 'AREA 8: Analytics', 'Bookings', '0/=', 'Completed'],
                [8, 'AREA 8: Analytics', 'WhatsApp Leads', '0/=', 'Completed'],
                [8, 'AREA 8: Analytics', 'Phone Calls', '0/=', 'Completed'],
                [8, 'AREA 8: Analytics', 'Occupancy', '0/=', 'Completed'],
                [8, 'AREA 8: Analytics', 'Revenue', '0/=', 'Completed'],
                [8, 'AREA 8: Analytics', 'Reviews', '0/=', 'In Progress'],
            ];

            foreach ($strategyItems as $item) {
                MarketingStrategyItem::create([
                    'area_number' => $item[0],
                    'area_name' => $item[1],
                    'task' => $item[2],
                    'cost' => $item[3],
                    'status' => $item[4],
                ]);
            }
        }
    }
}
