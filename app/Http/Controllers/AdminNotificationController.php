<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    /**
     * Display admin notifications page.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Database notifications (Laravel built-in)
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadCount = $user->unreadNotifications()->count();

        // Inbox entries (custom notification system)
        $inboxEntries = \App\Models\InboxEntry::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadInbox = \App\Models\InboxEntry::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        // System stats for context
        $pendingDps = \App\Models\Pembiayaan::where('status_validasi_dps', 'menunggu')->count();
        $pendingToko = \App\Models\Toko::where('status', 'pending')->count();
        $pendingProduk = \App\Models\Produk::where('status', 'pending')->count();
        $newUsers = \App\Models\User::where('created_at', '>=', now()->subDays(7))->count();

        return view('dashboard.admin.notifications', compact(
            'notifications',
            'unreadCount',
            'inboxEntries',
            'unreadInbox',
            'pendingDps',
            'pendingToko',
            'pendingProduk',
            'newUsers'
        ));
    }

    /**
     * Mark a notification as read.
     */
    public function markRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
