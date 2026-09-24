<?php

namespace App\Support;

/**
 * KMeans — implementasi ringan algoritma K-Means clustering (murni PHP,
 * tanpa dependency eksternal seperti Rubix ML/php-ml) dipakai untuk
 * mengelompokkan desa berdasarkan kedekatan geografis (koordinat desa +
 * koordinat sekolah terdekat) dan beban siswa, sebagai dasar analisis
 * usulan lokasi sekolah baru.
 *
 * Cara pakai singkat:
 *   $result = KMeans::fit($vectors, $k);
 *   $result['assignments'] -> index cluster (0..k-1) untuk tiap baris $vectors
 *   $result['centroids']   -> titik pusat tiap cluster (dalam skala yang sama
 *                              dengan $vectors yang dikirim, jadi kalau kirim
 *                              data yang sudah distandarisasi, centroid-nya
 *                              juga dalam skala standar tsb)
 *   $result['iterations']  -> jumlah iterasi sampai konvergen
 *   $result['inertia']     -> total within-cluster sum of squares (WCSS),
 *                              dipakai untuk elbow method
 */
class KMeans
{
    /**
     * Jalankan K-Means pada kumpulan feature vector.
     *
     * @param  array<int, array<int, float>>  $vectors  Daftar baris data, tiap baris array numerik dengan dimensi sama.
     * @param  int  $k  Jumlah cluster yang diinginkan.
     * @param  int  $maxIterations  Batas maksimum iterasi (untuk jaga-jaga supaya tidak infinite loop).
     * @param  int  $seed  Seed random deterministik, supaya hasil clustering konsisten antar request
     *                      (tidak berubah-ubah setiap kali halaman di-refresh) selama datanya sama.
     * @return array{assignments: array<int,int>, centroids: array<int,array<int,float>>, iterations: int, inertia: float}
     */
    public static function fit(array $vectors, int $k, int $maxIterations = 200, int $seed = 42): array
    {
        $n = count($vectors);

        if ($n === 0) {
            return ['assignments' => [], 'centroids' => [], 'iterations' => 0, 'inertia' => 0.0];
        }

        // Tidak masuk akal punya cluster lebih banyak dari titik data.
        $k = max(1, min($k, $n));

        $centroids = self::initCentroidsPlusPlus($vectors, $k, $seed);

        $assignments = array_fill(0, $n, -1);
        $iterations = 0;

        for ($iter = 0; $iter < $maxIterations; $iter++) {
            $iterations = $iter + 1;
            $changed = false;

            // --- Assignment step: setiap titik masuk ke centroid terdekat ---
            $newAssignments = [];
            foreach ($vectors as $i => $vector) {
                $bestCluster = 0;
                $bestDist = INF;
                foreach ($centroids as $c => $centroid) {
                    $dist = self::squaredEuclidean($vector, $centroid);
                    if ($dist < $bestDist) {
                        $bestDist = $dist;
                        $bestCluster = $c;
                    }
                }
                $newAssignments[$i] = $bestCluster;
                if ($newAssignments[$i] !== $assignments[$i]) {
                    $changed = true;
                }
            }
            $assignments = $newAssignments;

            // --- Update step: pindahkan tiap centroid ke rata-rata anggotanya ---
            $newCentroids = self::recomputeCentroids($vectors, $assignments, $k, $centroids);

            if (!$changed) {
                $centroids = $newCentroids;
                break;
            }

            $centroids = $newCentroids;
        }

        $inertia = 0.0;
        foreach ($vectors as $i => $vector) {
            $inertia += self::squaredEuclidean($vector, $centroids[$assignments[$i]]);
        }

        return [
            'assignments' => $assignments,
            'centroids'   => $centroids,
            'iterations'  => $iterations,
            'inertia'     => round($inertia, 4),
        ];
    }

    /**
     * Standarisasi (z-score) tiap kolom/dimensi feature matrix, supaya kolom
     * dengan skala besar (mis. koordinat) tidak mendominasi kolom dengan
     * skala kecil (mis. jumlah siswa) secara tidak proporsional saat
     * dihitung jarak Euclidean-nya.
     *
     * @param  array<int, array<int, float>>  $vectors
     * @return array{data: array<int,array<int,float>>, mean: array<int,float>, std: array<int,float>}
     */
    public static function standardize(array $vectors): array
    {
        $n = count($vectors);
        if ($n === 0) {
            return ['data' => [], 'mean' => [], 'std' => []];
        }

        $dims = count($vectors[array_key_first($vectors)]);
        $mean = array_fill(0, $dims, 0.0);
        $std = array_fill(0, $dims, 0.0);

        foreach ($vectors as $vector) {
            foreach ($vector as $d => $val) {
                $mean[$d] += $val;
            }
        }
        foreach ($mean as $d => $sum) {
            $mean[$d] = $sum / $n;
        }

        foreach ($vectors as $vector) {
            foreach ($vector as $d => $val) {
                $std[$d] += ($val - $mean[$d]) ** 2;
            }
        }
        foreach ($std as $d => $sumSq) {
            $std[$d] = sqrt($sumSq / $n);
            if ($std[$d] < 1e-9) {
                $std[$d] = 1.0; // hindari division by zero jika kolom konstan
            }
        }

        $data = array_map(function ($vector) use ($mean, $std) {
            $row = [];
            foreach ($vector as $d => $val) {
                $row[$d] = ($val - $mean[$d]) / $std[$d];
            }
            return $row;
        }, $vectors);

        return ['data' => $data, 'mean' => $mean, 'std' => $std];
    }

    /**
     * Inisialisasi centroid awal memakai strategi K-Means++: titik pertama
     * dipilih acak (dengan seed tetap agar deterministik), titik berikutnya
     * dipilih dengan probabilitas proporsional terhadap kuadrat jarak ke
     * centroid terdekat yang sudah ada — supaya centroid awal tersebar
     * (menghasilkan clustering yang lebih stabil & konvergen lebih cepat
     * dibanding inisialisasi acak murni).
     */
    protected static function initCentroidsPlusPlus(array $vectors, int $k, int $seed): array
    {
        mt_srand($seed);

        $n = count($vectors);
        $centroids = [];

        // Normalisasi index array (jaga-jaga jika $vectors punya key non-sequential)
        $vectorsList = array_values($vectors);
        $firstIndex = mt_rand(0, $n - 1);
        $centroids = [$vectorsList[$firstIndex]];

        while (count($centroids) < $k) {
            $distances = [];
            $totalDist = 0.0;

            foreach ($vectorsList as $vector) {
                $minDist = INF;
                foreach ($centroids as $centroid) {
                    $d = self::squaredEuclidean($vector, $centroid);
                    if ($d < $minDist) {
                        $minDist = $d;
                    }
                }
                $distances[] = $minDist;
                $totalDist += $minDist;
            }

            if ($totalDist <= 0) {
                // Semua titik sudah identik dengan centroid yang ada; ambil titik
                // berikutnya secara berurutan saja supaya tetap ada k centroid.
                $centroids[] = $vectorsList[count($centroids) % $n];
                continue;
            }

            $r = mt_rand() / mt_getrandmax() * $totalDist;
            $cumulative = 0.0;
            $chosen = $vectorsList[0];
            foreach ($vectorsList as $idx => $vector) {
                $cumulative += $distances[$idx];
                if ($cumulative >= $r) {
                    $chosen = $vector;
                    break;
                }
            }
            $centroids[] = $chosen;
        }

        return $centroids;
    }

    protected static function recomputeCentroids(array $vectors, array $assignments, int $k, array $fallbackCentroids): array
    {
        $dims = count($vectors[array_key_first($vectors)]);
        $sums = array_fill(0, $k, null);
        $counts = array_fill(0, $k, 0);

        foreach ($vectors as $i => $vector) {
            $c = $assignments[$i];
            if ($sums[$c] === null) {
                $sums[$c] = array_fill(0, $dims, 0.0);
            }
            foreach ($vector as $d => $val) {
                $sums[$c][$d] += $val;
            }
            $counts[$c]++;
        }

        $centroids = [];
        for ($c = 0; $c < $k; $c++) {
            if ($counts[$c] === 0) {
                // Cluster kosong (tidak ada anggota) -> pertahankan posisi
                // centroid sebelumnya supaya tidak "hilang" dari hasil.
                $centroids[$c] = $fallbackCentroids[$c] ?? array_fill(0, $dims, 0.0);
                continue;
            }
            $centroids[$c] = array_map(fn ($sum) => $sum / $counts[$c], $sums[$c]);
        }

        return $centroids;
    }

    protected static function squaredEuclidean(array $a, array $b): float
    {
        $sum = 0.0;
        foreach ($a as $i => $val) {
            $diff = $val - ($b[$i] ?? 0.0);
            $sum += $diff * $diff;
        }
        return $sum;
    }
}
