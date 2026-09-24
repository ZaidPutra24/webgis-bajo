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

    /**
     * Hitung luas suatu GeoJSON (Polygon/MultiPolygon) dalam km².
     * Memakai proyeksi equirectangular sederhana berdasarkan lintang rata-rata
     * tiap ring — cukup akurat untuk area sekelas desa (beberapa km²) dan tidak
     * butuh dependency eksternal (mis. geoPHP/GEOS).
     * Mengembalikan null jika GeoJSON tidak valid / tidak berisi geometry area.
     */
    public static function polygonAreaKm2($geojson): ?float
    {
        $data = is_string($geojson) ? json_decode($geojson, true) : $geojson;

        if (!is_array($data)) {
            return null;
        }

        $totalM2 = 0.0;
        foreach (self::extractGeometries($data) as $geometry) {
            $totalM2 += self::geometryAreaM2($geometry);
        }

        return $totalM2 > 0 ? round($totalM2 / 1_000_000, 4) : null;
    }

    /**
     * Hitung titik centroid (rata-rata sederhana vertex ring terluar) suatu
     * GeoJSON. Dipakai sebagai default awal "titik balai desa" sebelum
     * dioverride manual oleh admin dengan koordinat balai desa sebenarnya.
     * Mengembalikan ['latitude' => ..., 'longitude' => ...] atau null.
     */
    public static function centroid($geojson): ?array
    {
        $data = is_string($geojson) ? json_decode($geojson, true) : $geojson;

        if (!is_array($data)) {
            return null;
        }

        $sumLat = 0.0;
        $sumLng = 0.0;
        $count = 0;

        foreach (self::extractGeometries($data) as $geometry) {
            $type = $geometry['type'] ?? null;
            $coordinates = $geometry['coordinates'] ?? null;

            if (!$coordinates) {
                continue;
            }

            if ($type === 'Polygon') {
                $rings = [$coordinates[0] ?? []];
            } elseif ($type === 'MultiPolygon') {
                $rings = array_map(fn ($poly) => $poly[0] ?? [], $coordinates);
            } else {
                $rings = [];
            }

            foreach ($rings as $ring) {
                foreach ($ring as $pt) {
                    if (!isset($pt[0], $pt[1])) {
                        continue;
                    }
                    $sumLng += $pt[0];
                    $sumLat += $pt[1];
                    $count++;
                }
            }
        }

        if ($count === 0) {
            return null;
        }

        return [
            'latitude'  => round($sumLat / $count, 8),
            'longitude' => round($sumLng / $count, 8),
        ];
    }

    /**
     * Jarak great-circle (garis lurus) antara dua titik koordinat, dalam km.
     * Dipakai sebagai cross-check sekunder terhadap waktu tempuh riil
     * (walk_mnt/drive_mnt/boat_mnt) yang sudah diinput manual di jaraksekolahlokasi.
     */
    public static function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $r = 6371.0; // radius bumi rata-rata, km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($r * $c, 3);
    }

    protected static function geometryAreaM2(array $geometry): float
    {
        $type = $geometry['type'] ?? null;
        $coordinates = $geometry['coordinates'] ?? null;

        if (!$coordinates) {
            return 0.0;
        }

        if ($type === 'Polygon') {
            return self::polygonCoordsAreaM2($coordinates);
        }

        if ($type === 'MultiPolygon') {
            $sum = 0.0;
            foreach ($coordinates as $polygonCoords) {
                $sum += self::polygonCoordsAreaM2($polygonCoords);
            }
            return $sum;
        }

        return 0.0;
    }

    /**
     * $polygonCoords formatnya: [ ring_luar, ring_lubang_1, ring_lubang_2, ... ]
     * Ring pertama dijumlah, ring lubang (holes) dikurangkan.
     */
    protected static function polygonCoordsAreaM2(array $polygonCoords): float
    {
        if (empty($polygonCoords) || empty($polygonCoords[0])) {
            return 0.0;
        }

        // Skala proyeksi equirectangular berdasarkan lintang rata-rata ring luar.
        $refLat = self::averageLat($polygonCoords[0]);
        $refLatRad = deg2rad($refLat);

        $mPerDegLat = 111_132.92 - 559.82 * cos(2 * $refLatRad) + 1.175 * cos(4 * $refLatRad);
        $mPerDegLng = 111_412.84 * cos($refLatRad) - 93.5 * cos(3 * $refLatRad);

        $area = self::shoelaceAreaM2($polygonCoords[0], $mPerDegLat, $mPerDegLng);

        for ($i = 1, $n = count($polygonCoords); $i < $n; $i++) {
            $area -= self::shoelaceAreaM2($polygonCoords[$i], $mPerDegLat, $mPerDegLng);
        }

        return abs($area);
    }

    protected static function shoelaceAreaM2(array $ring, float $mPerDegLat, float $mPerDegLng): float
    {
        if (count($ring) < 3) {
            return 0.0;
        }

        $projected = array_map(
            fn ($pt) => [($pt[0] ?? 0) * $mPerDegLng, ($pt[1] ?? 0) * $mPerDegLat],
            $ring
        );

        $sum = 0.0;
        $n = count($projected);
        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $sum += ($projected[$j][0] + $projected[$i][0]) * ($projected[$j][1] - $projected[$i][1]);
        }

        return $sum / 2;
    }

    protected static function averageLat(array $ring): float
    {
        $sum = 0.0;
        $n = count($ring);
        foreach ($ring as $pt) {
            $sum += $pt[1] ?? 0;
        }
        return $n > 0 ? $sum / $n : 0.0;
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
