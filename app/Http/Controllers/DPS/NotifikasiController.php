<?php

namespace App\Http\Controllers\DPS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class NotifikasiController extends Controller
{
    /**
     * Daftar notifikasi DPS (JSON).
     */
    public function index(Request $request)
    {
        Gate::authorize('dps.view-notifikasi');

        $notifications = auth()->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($n) {
                $data = $n->data ?? [];
                return [
                    'id'         => $n->id,
                    'title'      => $data['title'] ?? 'Notifikasi',
                    'message'    => $data['message'] ?? '',
                    'extra'      => $data['extra'] ?? [],
                    'read'       => !is_null($n->read_at),
                    'created_at' => $n->created_at?->toDateTimeString(),
                    'created_at_human' => $n->created_at?->diffForHumans(),
                ];
            });

        $unread = auth()->user()->unreadNotifications()->count();

        return response()->json([
            'success' => true,
            'data'    => $notifications,
            'unread'  => $unread,
        ]);
    }

    /**
     * Tandai satu notifikasi sebagai dibaca.
     */
    public function markRead(Request $request, $id)
    {
        Gate::authorize('dps.view-notifikasi');

        $notification = auth()->user()->notifications()->findOrFail($id);
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai sudah dibaca.',
        ]);
    }

    /**
     * Tandai semua notifikasi sebagai dibaca.
     */
    public function markAllRead(Request $request)
    {
        Gate::authorize('dps.view-notifikasi');

        auth()->user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi ditandai sudah dibaca.',
        ]);
    }
}

