<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the user-access administration dashboard.
     */
    public function index(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_admin' => User::where('role', User::ROLE_ADMIN)->count(),
            'total_ketua' => User::where('role', User::ROLE_KETUA)->count(),
            'total_pengurus' => User::where('role', User::ROLE_PENGURUS)->count(),
            'total_dps' => User::where('role', User::ROLE_DPS)->count(),
            'total_anggota' => User::where('role', User::ROLE_ANGGOTA)->count(),
            'total_pelanggan' => User::where('role', User::ROLE_PELANGGAN)->count(),
            'active_users' => User::where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
            'attention_users' => User::where(function ($query): void {
                $query->where('is_active', false)
                    ->orWhereNull('email_verified_at');
            })->count(),
            'new_this_month' => User::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
        ];

        $recentUsers = User::latest()
            ->limit(6)
            ->get();

        return view('dashboard.admin.index', compact('stats', 'recentUsers'));
    }
}
