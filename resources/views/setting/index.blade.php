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
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Hero Background Image</label>
                                @if(!empty($settings['hero_image_path']))
                                    <div class="mb-2 rounded overflow-hidden" style="height: 80px;">
                                        <img src="{{ asset($settings['hero_image_path']) }}" alt="Hero" style="width: 100%; height: 80px; object-fit: cover;">
                                    </div>
                                @endif
                                <input type="file" class="form-control" name="hero_image_path" accept="image/*">
                                <small class="text-muted">Large landscape image for the homepage banner.</small>
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
