<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\CalonSiswa;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CalonSiswaChart extends ChartWidget
{
    protected static string $chart = 'line'; // Bisa 'line', 'bar', 'pie'
    protected static ?string $heading = 'Calon Siswa (SPMB)';
    protected static ?int $sort = -3; 
    protected int | string | array $columnSpan = 'full'; 

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user && $user->role !== User::ROLE_SISWA;
    }

    protected function getData(): array
    {
        $user = Auth::user();

        // Hanya tampilkan chart ini untuk peran yang relevan (misal: Admin, TU, Staff PPDB)
        if (!in_array($user->role, [User::ROLE_ADMIN, User::ROLE_KEPSEK, User::ROLE_TATA_USAHA, User::ROLE_STAFF_PPDB, User::ROLE_GURU])) {
            return [
                'labels' => [],
                'datasets' => [],
            ];
        }

        $statuses = CalonSiswa::select('status', DB::raw('count(*) as total'))
                              ->groupBy('status')
                              ->pluck('total', 'status')
                              ->toArray();

        $labels = array_keys($statuses);
        $data = array_values($statuses);

        // Warna untuk setiap status (sesuaikan dengan tema Anda)
        $colors = [
            'menunggu' => '#FBBF24', // yellow-500
            'disetujui' => '#10B981', // green-500
            'ditolak' => '#EF4444', // red-500
        ];

        $backgroundColors = [];
        foreach ($labels as $label) {
            $backgroundColors[] = $colors[$label] ?? '#CCCCCC'; // Warna abu-abu jika tidak ada definisi
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Jumlah Calon Siswa',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}