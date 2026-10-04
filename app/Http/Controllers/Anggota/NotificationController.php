<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pesanan;
use App\Models\Angsuran;
use App\Models\InboxEntry;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Pesanan aktif (sederhana: semua pesanan pembeli yang belum selesai atau dibatalkan)
        $pesananAktif = Pesanan::where('pembeli_id', $user->id)
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->count();

        // Wishlist: belum diimplementasikan, kembalikan 0 untuk sekarang
        $wishlistCount = 0;

        // Angsuran jatuh tempo hari ini untuk user
        $angsuranJatuhTempo = Angsuran::whereHas('pembiayaan', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->whereRaw('DATE(jatuh_tempo) = ?', [Carbon::today()->toDateString()])
          ->where(function($q){ $q->where('status','belum_bayar')->orWhereNull('status'); })
          ->count();

        // Ambil inbox user (page)
        $entries = InboxEntry::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('dashboard.anggota.notifications', compact('pesananAktif', 'wishlistCount', 'angsuranJatuhTempo', 'entries'));
    }

    /**
     * API endpoint untuk polling notifikasi real-time.
     * Mengembalikan JSON berisi notifikasi terbaru + jumlah belum dibaca.
     */
    public function apiNotifications(Request $request): JsonResponse
    {
        $user = Auth::user();

        $notifications = InboxEntry::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->map(fn ($entry) => [
                'id' => $entry->id,
                'title' => $entry->title,
                'message' => $entry->message,
                'data' => $entry->data,
                'is_read' => $entry->is_read,
                'created_at' => $entry->created_at->toISOString(),
            ]);

        $unreadCount = InboxEntry::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }
}
