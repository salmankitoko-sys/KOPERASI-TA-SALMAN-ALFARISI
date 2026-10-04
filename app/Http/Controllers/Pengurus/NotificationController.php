<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pembiayaan;
use App\Models\Angsuran;
use App\Models\Produk;
use App\Models\InboxEntry;
use Illuminate\Http\Request;
use App\Support\NotificationHelper;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function index()
    {
        // Hitung data real untuk ringkasan notifikasi
        $anggotaPending = User::where('role', 'anggota')
            ->where(function ($q) {
                $q->where('status', 'Calon')
                  ->orWhereNull('email_verified_at');
            })->count();

        $pengajuanPembiayaan = Pembiayaan::where('status', 'diajukan')->count();

        $angsuranJatuhTempo = Angsuran::whereDate('jatuh_tempo', Carbon::today())
            ->where(function($q){ $q->where('status','belum_bayar')->orWhereNull('status'); })
            ->count();

        $produkModerasi = Produk::where('status', 'pending')->count();

        // Ambil riwayat notifikasi (ringkasan terbaru)
        $recent = InboxEntry::orderBy('created_at', 'desc')->limit(6)->get();

        return view('dashboard.pengurus.notifications', compact('anggotaPending', 'pengajuanPembiayaan', 'angsuranJatuhTempo', 'produkModerasi', 'recent'));
    }

    public function broadcast(Request $request)
    {
        $request->validate([
            'recipient' => 'required|in:semua_anggota,semua_pengurus,semua_admin,semua_user',
            'title'     => 'required|string|max:200',
            'message'   => 'required|string|max:2000',
        ]);

        $recipient = $request->recipient;
        $title = $request->title;
        $message = $request->message;

        // Decide recipients
        if ($recipient === 'semua_anggota') {
            NotificationHelper::sendToRole('anggota', $title, $message);
        } elseif ($recipient === 'semua_pengurus') {
            NotificationHelper::sendToRole('pengurus', $title, $message);
        } elseif ($recipient === 'semua_admin') {
            NotificationHelper::sendToRole('admin', $title, $message);
        } elseif ($recipient === 'semua_user') {
            // send to all users
            $roles = ['admin','pengurus','anggota','dps'];
            foreach ($roles as $r) {
                NotificationHelper::sendToRole($r, $title, $message);
            }
        } else {
            // Fallback: treat as role name
            NotificationHelper::sendToRole($recipient, $title, $message);
        }

        return redirect()->back()->with('success', 'Broadcast berhasil dikirim.');
    }
}
