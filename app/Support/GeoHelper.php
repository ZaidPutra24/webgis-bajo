<?php

namespace App\Support;

/**
 * GeoHelper — kumpulan fungsi analisis spasial sederhana (point-in-polygon)
 * tanpa dependency eksternal (murni PHP), dipakai untuk menghitung berapa
 * banyak titik sekolah yang berada di dalam ROI (Region of Interest) suatu
 * wilayah kecamatan/desa berbentuk GeoJSON (Polygon / MultiPolygon).
 */
class GeoHelper
{
    /**
     * Cek apakah satu titik koordinat (lat, lng) berada di dalam GeoJSON.
     * Mendukung Feature, FeatureCollection, Polygon, dan MultiPolygon.
     *
     * @param  string|array  $geojson
     */
    public static function isPointInGeoJson(float $lat, float $lng, $geojson): bool
    {
        $data = is_string($geojson) ? json_decode($geojson, true) : $geojson;

        if (!is_array($data)) {
            return false;
        }

        foreach (self::extractGeometries($data) as $geometry) {
            if (self::isPointInGeometry($lat, $lng, $geometry)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Hitung jumlah titik (mis. daftar sekolah) yang berada di dalam suatu GeoJSON ROI.
     * $points berupa array/Collection berisi object atau array dengan properti
     * latitude & longitude.
     */
    public static function countPointsInGeoJson($geojson, $points): int
    {
        return self::filterPointsInGeoJson($geojson, $points)->count();
    }

    /**
     * Filter titik-titik (mis. daftar sekolah) yang berada di dalam suatu GeoJSON ROI.
     * Mengembalikan Illuminate Collection yang sudah difilter.
     */
    public static function filterPointsInGeoJson($geojson, $points)
    {
        $data = is_string($geojson) ? json_decode($geojson, true) : $geojson;
        $geometries = is_array($data) ? self::extractGeometries($data) : [];

        return collect($points)->filter(function ($point) use ($geometries) {
            [$lat, $lng] = self::extractLatLng($point);

            if ($lat === null || $lng === null) {
                return false;
            }

            foreach ($geometries as $geometry) {
                if (self::isPointInGeometry((float) $lat, (float) $lng, $geometry)) {
                    return true;
                }
            }

            return false;
        })->values();
    }

    protected static function extractLatLng($point): array
    {
        if (is_array($point)) {
            return [$point['latitude'] ?? null, $point['longitude'] ?? null];
        }

        return [$point->latitude ?? null, $point->longitude ?? null];
    }

    /**
     * Ambil semua geometry (Polygon/MultiPolygon) dari struktur GeoJSON apa pun.
     */
    protected static function extractGeometries(array $data): array
    {
        $type = $data['type'] ?? null;
        $result = [];

        if ($type === 'FeatureCollection') {
            foreach ($data['features'] ?? [] as $feature) {
                if (isset($feature['geometry'])) {
                    $result[] = $feature['geometry'];
                }
            }
        } elseif ($type === 'Feature') {
            if (isset($data['geometry'])) {
                $result[] = $data['geometry'];
            }
        } elseif (in_array($type, ['Polygon', 'MultiPolygon'], true)) {
            $result[] = $data;
        }

        return $result;
    }

    protected static function isPointInGeometry(float $lat, float $lng, array $geometry): bool
    {
        $type = $geometry['type'] ?? null;
        $coordinates = $geometry['coordinates'] ?? null;

        if (!$coordinates) {
            return false;
        }

        if ($type === 'Polygon') {
            return self::isPointInPolygonCoords($lat, $lng, $coordinates);
        }

        if ($type === 'MultiPolygon') {
            foreach ($coordinates as $polygonCoords) {
                if (self::isPointInPolygonCoords($lat, $lng, $polygonCoords)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * $polygonCoords formatnya: [ ring_luar, ring_lubang_1, ring_lubang_2, ... ]
     * Sesuai spesifikasi GeoJSON, tiap ring berisi array [lng, lat].
     */
    protected static function isPointInPolygonCoords(float $lat, float $lng, array $polygonCoords): bool
    {
        if (empty($polygonCoords)) {
            return false;
        }

        // Ring pertama = batas luar. Titik wajib berada di dalamnya.
        if (!self::isPointInRing($lat, $lng, $polygonCoords[0])) {
            return false;
        }

        // Ring selanjutnya = lubang (holes). Jika titik ada di dalam salah satu
        // lubang, berarti titik tersebut sebenarnya di LUAR wilayah polygon.
        for ($i = 1, $n = count($polygonCoords); $i < $n; $i++) {
            if (self::isPointInRing($lat, $lng, $polygonCoords[$i])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Algoritma ray-casting standar untuk point-in-polygon pada satu ring.
     * $ring: array of [lng, lat].
     */
    protected static function isPointInRing(float $lat, float $lng, array $ring): bool
    {
        $inside = false;
        $n = count($ring);

        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $xi = $ring[$i][0];
            $yi = $ring[$i][1];
            $xj = $ring[$j][0];
            $yj = $ring[$j][1];

            $denominator = ($yj - $yi) ?: 1e-12;
            $intersect = (($yi > $lat) !== ($yj > $lat))
                && ($lng < ($xj - $xi) * ($lat - $yi) / $denominator + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }
}
