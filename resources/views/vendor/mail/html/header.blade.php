<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
                {{-- <img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo"> --}}
                @php
                    $logoUrl = !empty($global_settings['logo_path']) ? asset($global_settings['logo_path']) : (!empty($global_settings['favicon_path']) ? asset($global_settings['favicon_path']) : asset('img/logo/sip.png'));
                @endphp
                <img src="{{ $logoUrl }}" alt="Logo" style="max-height: 70px; max-width: 150px; height: auto;"
                    class="d-inline-block align-text-top">
            @else
                {{ $slot }}
            @endif
        </a>
    </td>
</tr>
