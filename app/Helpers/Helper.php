<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Str;

class Helper
{
    public static function convertToRupiah($price)
    {
        return 'TZS '.number_format($price, 2, ',', '.');
    }

    public static function thisMonth()
    {
        return Carbon::parse(Carbon::now())->format('m');
    }

    public static function thisYear()
    {
        return Carbon::parse(Carbon::now())->format('Y');
    }

    public static function dateDayFormat($date)
    {
        return Carbon::parse($date)->isoFormat('dddd, D MMM YYYY');
    }

    public static function dateFormat($date)
    {
        return Carbon::parse($date)->isoFormat('D MMM YYYY');
    }

    public static function dateFormatTime($date)
    {
        return Carbon::parse($date)->isoFormat('D MMM YYYY H:m:s');
    }

    public static function dateFormatTimeNoYear($date)
    {
        return Carbon::parse($date)->isoFormat('D MMM, hh:mm a');
    }

    public static function getDateDifference($check_in, $check_out)
    {
        $check_in = strtotime($check_in);
        $check_out = strtotime($check_out);
        $date_difference = $check_out - $check_in;

        return round($date_difference / (60 * 60 * 24));
    }

    public static function plural($value, $count)
    {
        return Str::plural($value, $count);
    }

    public static function getColorByDay($day)
    {
        $color = '';
        if ($day == 1) {
            $color = 'bg-danger';
        } elseif ($day > 1 && $day < 4) {
            $color = 'bg-warning';
        } else {
            $color = 'bg-success';
        }

        return $color;
    }

    public static function getTotalPayment($day, $price)
    {
        return $day * $price;
    }

    public static function cleanEmbedUrl($url, $type = null)
    {
        if (empty($url)) {
            return $url;
        }

        // 1. If user pasted an entire <iframe src="..."> HTML tag, extract the src attribute
        if (preg_match('/src=["\']([^"\']+)["\']/i', $url, $matches)) {
            $url = $matches[1];
        }

        $url = trim($url, " '\"\t\n\r\0\x0B");

        // 2. Clean YouTube URL
        if ($type === 'youtube' || str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            if (preg_match('/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/i', $url, $matches)) {
                return 'https://www.youtube.com/embed/' . $matches[1];
            }
        }

        // 3. Clean Google Maps URL
        if ($type === 'map' || str_contains($url, 'google.com/maps') || str_contains($url, 'maps.google.com')) {
            if (!str_contains($url, '/embed') && !str_contains($url, 'output=embed')) {
                if (preg_match('/\/place\/([^\/\?@]+)/i', $url, $matches)) {
                    $place = urldecode($matches[1]);
                    return 'https://www.google.com/maps?q=' . urlencode($place) . '&output=embed';
                }
                if (preg_match('/[\?&]q=([^&]+)/i', $url, $matches)) {
                    return 'https://www.google.com/maps?q=' . $matches[1] . '&output=embed';
                }
                if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/i', $url, $matches)) {
                    return 'https://www.google.com/maps?q=' . $matches[1] . ',' . $matches[2] . '&output=embed';
                }
                $separator = str_contains($url, '?') ? '&' : '?';
                return $url . $separator . 'output=embed';
            }
        }

        return $url;
    }
}
