<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\AbsensiGuru; // Import model AbsensiGuru Anda
use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GuruAbsensiChart extends ChartWidget
{
    protected static string $chart = 'bar'; // Bar chart untuk perbandingan status
    protected static ?string $heading = 'Rekap Absensi Saya (30 Hari Terakhir)';
    protected static ?int $sort = -1;
    protected int | string | array $columnSpan = 'full'; // Agar full width

    /**
     * Determine if the widget should be visible to the current user.
     * Only visible for users with the 'guru' role.
     *
     * @return bool
     */
    public static function canView(): bool
    {
        $user = Auth::user();
        // Pastikan hanya guru yang bisa melihat chart ini
        return $user && $user->role === User::ROLE_KEPSEK;
    }

    protected function getData(): array
    {
        $user = Auth::user();
        $findSiswa = Guru::where('user_id', $user->id)->first();
        $guruId = $findSiswa->id; // Asumsi guru_id di absensi_guru adalah id dari user yang login (ROLE_GURU)

        // Ambil data absensi untuk guru yang login dalam 30 hari terakhir
        $startDate = Carbon::now()->subDays(29)->startOfDay(); // 30 hari termasuk hari ini
        $endDate = Carbon::now()->endOfDay();

        $absensiData = AbsensiGuru::where('guru_id', $guruId)
            ->whereBetween('tanggal_absensi', [$startDate, $endDate]) // Gunakan tanggal_absensi
            ->select('status_kehadiran', DB::raw('count(*) as total')) // Gunakan status_kehadiran
            ->groupBy('status_kehadiran')
            ->pluck('total', 'status_kehadiran')
            ->toArray();

        // Status absensi guru yang mungkin (sesuai ENUM di DB)
        $allStatuses = ['hadir', 'izin', 'sakit', 'alpha'];
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