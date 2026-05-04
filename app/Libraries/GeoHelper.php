<?php

namespace App\Libraries;

class GeoHelper
{
    /**
     * Hitung jarak antara dua titik koordinat menggunakan Haversine Formula (km)
     */
    public static function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    /**
     * Validasi koordinat latitude
     */
    public static function isValidLatitude($lat): bool
    {
        return is_numeric($lat) && $lat >= -90 && $lat <= 90;
    }

    /**
     * Validasi koordinat longitude
     */
    public static function isValidLongitude($lon): bool
    {
        return is_numeric($lon) && $lon >= -180 && $lon <= 180;
    }
}
