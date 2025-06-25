<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Absensi; // Asumsi model Absensi Anda
use App\Models\AbsensiSiswa;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AbsensiChart extends ChartWidget
{
    protected static string $chart = 'bar'; // Bisa 'line', 'bar', 'pie'
    protected static ?string $heading = 'Rekap Absensi Saya (30 Hari Terakhir)';
    protected static ?int $sort = -1; // Tampilkan di posisi atas
    protected int | string | array $columnSpan = 'full'; // Agar full width

    /**
     * Determine if the widget should be visible to the current user.
     * Only visible for students (ROLE_SISWA).
     *
     * @return bool
     */
    public static function canView(): bool
    {
        $user = Auth::user();
        // Pastikan hanya siswa yang bisa melihat chart ini
        return $user && $user->role === User::ROLE_SISWA;
    }

    protected function getData(): array
    {
        $user = Auth::user();
        $findSiswa = Siswa::where('user_id', $user->id)->first();
        $siswaId = $findSiswa->id;

        // Ambil data absensi untuk siswa yang login dalam 30 hari terakhir
        $startDate = Carbon::now()->subDays(29)->startOfDay(); // 30 hari termasuk hari ini
        $endDate = Carbon::now()->endOfDay();

        $absensiData = AbsensiSiswa::where('siswa_id', $siswaId)
            ->whereBetween('tanggal_absensi', [$startDate, $endDate])
            ->select('status_kehadiran', DB::raw('count(*) as total'))
            ->groupBy('status_kehadiran')
            ->pluck('total', 'status_kehadiran')
            ->toArray();

        // Status absensi yang mungkin
        $allStatuses = ['hadir', 'sakit', 'izin', 'alpha'];
        $colors = [
            'hadir' => '#10B981', // green-500
            'sakit' => '#FBBF24', // yellow-500
            'izin' => '#60A5FA',  // blue-400
            'alpha' => '#EF4444', // red-500
        ];

        $labels = [];
        $data = [];
        $backgroundColors = [];

        foreach ($allStatuses as $status) {
            $labels[] = ucfirst($status); // Ubah jadi Hadir, Sakit, dll.
            $data[] = $absensiData[$status] ?? 0; // Ambil jumlah atau 0 jika tidak ada
            $backgroundColors[] = $colors[$status] ?? '#CCCCCC'; // Warna default jika tidak terdefinisi
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Jumlah Hari',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return static::$chart;
    }

    // Optional: Tambahkan opsi chart untuk tampilan yang lebih baik (misalnya menghilangkan Y-axis)
    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0, // Pastikan nilai Y-axis adalah bilangan bulat
                    ],
                ],
            ],
        ];
    }
}