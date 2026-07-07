@extends('template.master')
@section('title', 'System Settings')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0"><i class="fas fa-cogs text-primary me-2"></i>System Settings</h3>
                <p class="text-muted mb-0">Configure every aspect of your hotel website</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('setting.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-4">

                {{-- ========= LEFT COLUMN ========= --}}
                <div class="col-lg-8">

                    {{-- General Info --}}
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-hotel text-primary me-2"></i>General Information</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Hotel Name</label>
                                <input type="text" class="form-control" name="hotel_name" value="{{ $settings['hotel_name'] ?? 'Bella Vista Lodge' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Hotel Tagline <small class="text-muted">(shown under the name in footer & hero)</small></label>
                                <input type="text" class="form-control" name="hotel_tagline" value="{{ $settings['hotel_tagline'] ?? '' }}" placeholder="Experience unparalleled luxury...">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Hotel Address</label>
                                <textarea class="form-control" name="hotel_address" rows="2">{{ $settings['hotel_address'] ?? '' }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Footer Copyright Text <small class="text-muted">(leave blank to auto-generate)</small></label>
                                <input type="text" class="form-control" name="footer_copyright" value="{{ $settings['footer_copyright'] ?? '' }}" placeholder="© 2026 Bella Vista Lodge. All rights reserved.">
                            </div>
                        </div>
                    </div>

                    {{-- Contact Details --}}
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-address-book text-primary me-2"></i>Contact Details</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Contact Email (Public)</label>
                                    <input type="email" class="form-control" name="contact_email" value="{{ $settings['contact_email'] ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Contact Phone</label>
                                    <input type="text" class="form-control" name="contact_phone" value="{{ $settings['contact_phone'] ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">WhatsApp Number <small class="text-muted">(floating button)</small></label>
                                    <input type="text" class="form-control" name="whatsapp_number" value="{{ $settings['whatsapp_number'] ?? '' }}" placeholder="+1234567890">
                                    <small class="text-muted">Include country code. Leave blank to hide the WhatsApp button.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Owner Email <small class="text-muted">(for daily reports)</small></label>
                                    <input type="email" class="form-control" name="owner_email" value="{{ $settings['owner_email'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SMS Notifications --}}
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-sms text-success me-2"></i>SMS Notifications</h5>
                            <small class="text-muted">Configure the Skypush Push SMS gateway. Values set here override <code>.env</code> variables.</small>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">API Key <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="sms_api_key"
                                           value="{{ $settings['sms_api_key'] ?? '' }}"
                                           placeholder="Leave blank to use PUSHSMS_API_KEY from .env"
                                           autocomplete="new-password">
                                    <small class="text-muted">Your secret Skypush <code>X-API-KEY</code>. Contact your SMS gateway administrator to obtain this.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Sender ID</label>
                                    <input type="text" class="form-control" name="sms_sender_id"
                                           value="{{ $settings['sms_sender_id'] ?? '' }}"
                                           placeholder="e.g. BELLA (max 11 chars)"
                                           maxlength="11">
                                    <small class="text-muted">The name or number shown on recipients' phones. Max 11 characters.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Client App Name</label>
                                    <input type="text" class="form-control" name="sms_client_app"
                                           value="{{ $settings['sms_client_app'] ?? 'HMS' }}"
                                           placeholder="e.g. HMS">
                                    <small class="text-muted">Identifier sent to the SMS gateway to track which app sent the message.</small>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold"><i class="fas fa-phone-alt text-success me-1"></i>Admin SMS Recipient</label>
                                    <input type="text" class="form-control" name="admin_sms_recipient"
                                           value="{{ $settings['admin_sms_recipient'] ?? '' }}"
                                           placeholder="255712345678">
                                    <small class="text-muted">Phone number(s) to receive reservation &amp; daily report SMS alerts. Include country code (e.g. <code>255xxxxxxxxx</code>). Comma-separate for multiple numbers.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Social Media --}}
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-share-alt text-primary me-2"></i>Social Media Handles</h5>
                            <small class="text-muted">Enter full URLs. Leave blank to hide the icon.</small>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="fab fa-facebook text-primary me-1"></i> Facebook</label>
                                    <input type="url" class="form-control" name="social_facebook" value="{{ $settings['social_facebook'] ?? '' }}" placeholder="https://facebook.com/yourpage">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="fab fa-instagram text-danger me-1"></i> Instagram</label>
                                    <input type="url" class="form-control" name="social_instagram" value="{{ $settings['social_instagram'] ?? '' }}" placeholder="https://instagram.com/yourpage">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="fab fa-twitter text-info me-1"></i> Twitter / X</label>
                                    <input type="url" class="form-control" name="social_twitter" value="{{ $settings['social_twitter'] ?? '' }}" placeholder="https://twitter.com/yourpage">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="fab fa-tiktok text-dark me-1"></i> TikTok</label>
                                    <input type="url" class="form-control" name="social_tiktok" value="{{ $settings['social_tiktok'] ?? '' }}" placeholder="https://tiktok.com/@yourpage">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="fab fa-youtube text-danger me-1"></i> YouTube</label>
                                    <input type="url" class="form-control" name="social_youtube" value="{{ $settings['social_youtube'] ?? '' }}" placeholder="https://youtube.com/yourchannel">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="fab fa-linkedin text-primary me-1"></i> LinkedIn</label>
                                    <input type="url" class="form-control" name="social_linkedin" value="{{ $settings['social_linkedin'] ?? '' }}" placeholder="https://linkedin.com/company/yourpage">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Operational --}}
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-sliders-h text-primary me-2"></i>Operational Settings</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Receipt Verification Time <small class="text-muted">(shown to clients)</small></label>
                                <input type="text" class="form-control" name="receipt_verify_time" value="{{ $settings['receipt_verify_time'] ?? '1-2 hours' }}" placeholder="e.g. 1-2 hours">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Extra Note in Booking Emails <small class="text-muted">(optional)</small></label>
                                <textarea class="form-control" name="booking_email_note" rows="3" placeholder="Any extra instructions or notes for guests...">{{ $settings['booking_email_note'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Landing Page Content Display Settings --}}
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-desktop text-primary me-2"></i>Landing Page Display Content</h5>
                            <small class="text-muted">Manage headings and descriptions shown on the main public landing page</small>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Hero Section Title</label>
                                    <input type="text" class="form-control" name="home_hero_title" value="{{ $settings['home_hero_title'] ?? 'Welcome to Bella Vista Lodge' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Hero Section Subtitle</label>
                                    <input type="text" class="form-control" name="home_hero_subtitle" value="{{ $settings['home_hero_subtitle'] ?? 'Experience luxury, comfort, and exceptional service.' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Featured Rooms Heading</label>
                                    <input type="text" class="form-control" name="home_featured_title" value="{{ $settings['home_featured_title'] ?? 'Our Featured Rooms' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Featured Rooms Subtitle</label>
                                    <input type="text" class="form-control" name="home_featured_subtitle" value="{{ $settings['home_featured_subtitle'] ?? 'Handpicked accommodations for your perfect stay.' }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Facilities Section Heading</label>
                                    <input type="text" class="form-control" name="home_features_title" value="{{ $settings['home_features_title'] ?? 'Why Choose Us?' }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Rooms Page Content Display Settings --}}
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-bed text-primary me-2"></i>Rooms Guest Page Display Content</h5>
                            <small class="text-muted">Customize the banner title and subtitle shown on the public Rooms & Suites page</small>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Rooms Hero Title</label>
                                    <input type="text" class="form-control" name="rooms_hero_title" value="{{ $settings['rooms_hero_title'] ?? 'Our Rooms & Suites' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Rooms Hero Subtitle</label>
                                    <input type="text" class="form-control" name="rooms_hero_subtitle" value="{{ $settings['rooms_hero_subtitle'] ?? 'Find your perfect sanctuary.' }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Location & Interactive Map Settings --}}
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-map-marked-alt text-primary me-2"></i>Location & Interactive Map Settings</h5>
                            <small class="text-muted">Manage all text descriptions, maps, and Google Earth direction videos</small>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Top Badge Text</label>
                                    <input type="text" class="form-control" name="location_badge" value="{{ $settings['location_badge'] ?? 'Prime Accessibility' }}">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Location Page Heading</label>
                                    <input type="text" class="form-control" name="location_heading" value="{{ $settings['location_heading'] ?? 'Discover Our Prime Luxury Location' }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Main Location Description</label>
                                    <textarea class="form-control" name="location_description" rows="2">{{ $settings['location_description'] ?? 'Nestled amidst breathtaking natural vistas, Bella Vista Lodge offers tranquil seclusion with effortless accessibility from international airports and scenic transport hubs.' }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Google Maps Embed URL / iFrame Src</label>
                                    <input type="text" class="form-control" name="location_map_iframe" value="{{ $settings['location_map_iframe'] ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d101408.21722940251!2d-122.08554284488975!3d37.42199990176881!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808fb7495bec0189%3A0x7c17d44a466baf9b!2sMountain%20View%2C%20CA!5e0!3m2!1sen!2sus!4v1700000000000!5m2!1sen!2sus' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Google Earth Direction Video (YouTube Embed URL)</label>
                                    <input type="text" class="form-control" name="location_youtube_video" value="{{ $settings['location_youtube_video'] ?? 'https://www.youtube.com/embed/ScMzIvxBSi4' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Map Card Title</label>
                                    <input type="text" class="form-control" name="location_map_card_title" value="{{ $settings['location_map_card_title'] ?? 'Interactive Satellite Map' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Video Card Title</label>
                                    <input type="text" class="form-control" name="location_video_card_title" value="{{ $settings['location_video_card_title'] ?? 'Google Earth 3D Animated Route' }}">
                                </div>
                                <div class="col-md-12 border-top pt-3">
                                    <h6 class="fw-bold text-primary mb-2">Transport & Arrival Descriptions</h6>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Arrival Section Heading</label>
                                    <input type="text" class="form-control" name="location_info_title" value="{{ $settings['location_info_title'] ?? 'Effortless Arrival & Connections' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Arrival Section Subtitle</label>
                                    <input type="text" class="form-control" name="location_info_subtitle" value="{{ $settings['location_info_subtitle'] ?? 'Whether arriving by private transfer, helicopter, or scenic drive, reaching our sanctuary is part of your unforgettable journey.' }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Driving Route Description</label>
                                    <textarea class="form-control" name="location_directions" rows="2">{{ $settings['location_directions'] ?? 'From International Airport: Take Highway 101 North for 35 miles, follow scenic Route 9 directly to private lodge gates. Valet and private helicopter transfer available upon reservation.' }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Private Helipad Card Title</label>
                                    <input type="text" class="form-control" name="location_helipad_title" value="{{ $settings['location_helipad_title'] ?? 'Private Helipad & Transfers' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Valet Parking Card Title</label>
                                    <input type="text" class="form-control" name="location_parking_title" value="{{ $settings['location_parking_title'] ?? 'Valet Parking & EV Charging' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Private Helipad Description</label>
                                    <textarea class="form-control" name="location_helipad_text" rows="3">{{ $settings['location_helipad_text'] ?? 'We maintain an on-site private helipad for chartered arrivals. VIP luxury SUV transfers from major airports can also be arranged directly through our concierge team 24 hours prior to arrival.' }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Valet Parking Description</label>
                                    <textarea class="form-control" name="location_parking_text" rows="3">{{ $settings['location_parking_text'] ?? 'Complimentary 24/7 secure valet parking is extended to all registered lodge guests. Fast Level-2 and DC universal electric vehicle charging stations are available upon arrival at our main lodge portico.' }}</textarea>
                                </div>
                                <div class="col-md-6 border-top pt-3">
                                    <label class="form-label fw-semibold">Call to Action Heading</label>
                                    <input type="text" class="form-control" name="location_cta_title" value="{{ $settings['location_cta_title'] ?? 'Ready to Experience Our Sanctuary?' }}">
                                </div>
                                <div class="col-md-6 border-top pt-3">
                                    <label class="form-label fw-semibold">Call to Action Subtitle</label>
                                    <input type="text" class="form-control" name="location_cta_subtitle" value="{{ $settings['location_cta_subtitle'] ?? 'Secure your preferred dates and let our concierge prepare your customized arrival itinerary.' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ========= RIGHT COLUMN ========= --}}
                <div class="col-lg-4">

                    {{-- Branding Colors --}}
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-palette text-primary me-2"></i>Brand Colors</h5>
                            <small class="text-muted">Applied to navbar, buttons & footer</small>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Primary Color <small class="text-muted">(navbar/footer bg)</small></label>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="color" class="form-control form-control-color" id="primary_color_picker" name="primary_color" value="{{ $settings['primary_color'] ?? '#0f172a' }}" style="width: 60px; height: 40px;">
                                    <input type="text" class="form-control" id="primary_color_text" value="{{ $settings['primary_color'] ?? '#0f172a' }}" placeholder="#0f172a" oninput="document.getElementById('primary_color_picker').value=this.value">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Accent Color <small class="text-muted">(buttons & highlights)</small></label>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="color" class="form-control form-control-color" id="accent_color_picker" name="accent_color" value="{{ $settings['accent_color'] ?? '#d4af37' }}" style="width: 60px; height: 40px;">
                                    <input type="text" class="form-control" id="accent_color_text" value="{{ $settings['accent_color'] ?? '#d4af37' }}" placeholder="#d4af37" oninput="document.getElementById('accent_color_picker').value=this.value">
                                </div>
                            </div>
                            {{-- Live preview --}}
                            <div class="mt-3 p-3 rounded text-center" id="color-preview" style="background: {{ $settings['primary_color'] ?? '#0f172a' }};">
                                <span class="fw-bold" style="color: {{ $settings['accent_color'] ?? '#d4af37' }}; font-size: 1rem;">{{ $settings['hotel_name'] ?? 'Bella Vista Lodge' }}</span>
                            </div>
                            <small class="text-muted d-block text-center mt-1">Live Color Preview</small>
                        </div>
                    </div>

                    {{-- Logo & Images --}}
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-image text-primary me-2"></i>Logo & Images</h5>
                        </div>
                        <div class="card-body p-4">
                            {{-- Logo --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Hotel Logo</label>
                                @if(!empty($settings['logo_path']))
                                    <div class="mb-2 p-2 border rounded text-center" style="background: #0f172a;">
                                        <img src="{{ asset($settings['logo_path']) }}" alt="Logo" style="max-height: 60px; max-width: 100%;">
                                    </div>
                                @endif
                                <input type="file" class="form-control" name="logo_path" accept="image/*">
                                <small class="text-muted">PNG/SVG recommended. Used in navbar and emails.</small>
                            </div>

                            {{-- Favicon --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Favicon</label>
                                @if(!empty($settings['favicon_path']))
                                    <div class="mb-2">
                                        <img src="{{ asset($settings['favicon_path']) }}" alt="Favicon" style="height: 32px; width: 32px;">
                                    </div>
                                @endif
                                <input type="file" class="form-control" name="favicon_path" accept="image/*">
                                <small class="text-muted">.ico or small PNG (32×32 px).</small>
                            </div>

                            {{-- Hero Background --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Homepage Hero Background</label>
                                @if(!empty($settings['hero_image_path']))
                                    <div class="mb-2 rounded overflow-hidden" style="height: 80px;">
                                        <img src="{{ asset($settings['hero_image_path']) }}" alt="Hero" style="width: 100%; height: 80px; object-fit: cover;">
                                    </div>
                                @endif
                                <input type="file" class="form-control" name="hero_image_path" accept="image/*">
                                <small class="text-muted">Large landscape image for the homepage banner.</small>
                            </div>

                            {{-- Rooms Page Hero Background --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Rooms Guest Page Banner</label>
                                @if(!empty($settings['rooms_hero_image_path']))
                                    <div class="mb-2 rounded overflow-hidden" style="height: 80px;">
                                        <img src="{{ asset($settings['rooms_hero_image_path']) }}" alt="Rooms Hero" style="width: 100%; height: 80px; object-fit: cover;">
                                    </div>
                                @endif
                                <input type="file" class="form-control" name="rooms_hero_image_path" accept="image/*">
                                <small class="text-muted">Large banner image for the public Rooms & Suites page.</small>
                            </div>
                        </div>
                    </div>

                    {{-- Save Button --}}
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Save All Settings
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

@section('footer')
<script>
    // Sync color pickers ↔ text inputs
    document.getElementById('primary_color_picker').addEventListener('input', function() {
        document.getElementById('primary_color_text').value = this.value;
        document.getElementById('color-preview').style.background = this.value;
    });
    document.getElementById('accent_color_picker').addEventListener('input', function() {
        document.getElementById('accent_color_text').value = this.value;
        document.getElementById('color-preview').querySelector('span').style.color = this.value;
    });
    document.getElementById('primary_color_text').addEventListener('input', function() {
        if (this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
            document.getElementById('primary_color_picker').value = this.value;
            document.getElementById('color-preview').style.background = this.value;
        }
    });
    document.getElementById('accent_color_text').addEventListener('input', function() {
        if (this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
            document.getElementById('accent_color_picker').value = this.value;
            document.getElementById('color-preview').querySelector('span').style.color = this.value;
        }
    });
</script>
@endsection
@endsection
