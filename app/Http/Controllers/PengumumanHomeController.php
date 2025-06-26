<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengumumanHomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengumuman::where('tanggal_publikasi', '<=', Carbon::now())
                           ->orderByDesc('tanggal_publikasi');

        $user = Auth::user();

        if ($user) {
            // Logged-in user: show announcements for their role and 'semua'
            $userRole = $user->role;
            $query->where(function ($q) use ($userRole) {
                $q->where('target_peran', $userRole)
                  ->orWhere('target_peran', 'semua');
            });
        } else {
            // Guest user: show announcements only for 'semua'
            $query->where('target_peran', 'semua');
        }

        // Ambil 5 pengumuman terbaru yang relevan
        $announcements = $query->limit(5)->get();

        // Jumlah yang ditampilkan di badge akan menjadi jumlah pengumuman yang baru saja diambil
        $announcementCount = $announcements->count();

        return response()->json([
            'announcements' => $announcements,
            'announcementCount' => $announcementCount,
        ]);
    }
    
    public function show($id)
    {
        $announcement = Pengumuman::findOrFail($id);

        // Optional: Implement access control for the API endpoint as well
        $user = Auth::user();
        if ($announcement->target_peran !== 'semua' && (!$user || $user->role !== $announcement->target_peran)) {
            return response()->json(['message' => 'Unauthorized access to this announcement.'], 403);
        }

        return response()->json($announcement);
    }
}
