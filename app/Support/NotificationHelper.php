<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class NotificationHelper
{
    /**
     * Roles that should receive email fallback in addition to database inbox.
     * Can be extended to read from config('notifications.email_roles') later.
     */
    protected static array $emailRoles = ['admin', 'pengurus', 'bendahara'];

    /**
     * Kirim notifikasi ke seluruh user dengan role tertentu.
     * Menyimpan ke tabel inbox_entries, mengirim notification database, dan
     * mengirim email fallback untuk peran tertentu.
     */
    public static function sendToRole(string $role, string $title, string $message, array $extra = []): void
    {
        try {
            $users = User::where('role', $role)->get();
            foreach ($users as $user) {
                // Simpan ke inbox_entries jika tabel tersedia
                try {
                    DB::table('inbox_entries')->insert([
                        'user_id'    => $user->id,
                        'title'      => $title,
                        'message'    => $message,
                        'data'       => json_encode($extra),
                        'is_read'    => 0,
                        'created_at' => Carbon::now(),
                    ]);
                } catch (\Exception $e) {
                    Log::debug('Gagal menyimpan inbox_entries untuk user '.$user->id.': '.$e->getMessage());
                }

                // Notifikasi via sistem Laravel (database channel)
                try {
                    $user->notify(new \App\Notifications\GenericNotification($title, $message, $extra));
                } catch (\Exception $e) {
                    Log::warning('Gagal mengirim notifikasi database ke user ' . $user->id . ': ' . $e->getMessage());
                }

                // Email fallback untuk role tertentu
                if (in_array($role, self::$emailRoles, true) && !empty($user->email)) {
                    try {
                        Mail::to($user->email)->send(new \App\Mail\GenericNotificationMail($title, $message, $extra));
                    } catch (\Exception $e) {
                        Log::warning('Gagal mengirim email notifikasi ke '.$user->email.': '.$e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Gagal mengirim notifikasi ke role ' . $role . ': ' . $e->getMessage());
        }
    }

    /**
     * Kirim notifikasi ke satu user.
     * Menyimpan ke inbox_entries, mengirim notification database, dan
     * mengirim email jika diminta atau jika user adalah admin/pengurus.
     */
    public static function sendToUser(User $user, string $title, string $message, array $extra = []): void
    {
        try {
            try {
                DB::table('inbox_entries')->insert([
                    'user_id'    => $user->id,
                    'title'      => $title,
                    'message'    => $message,
                    'data'       => json_encode($extra),
                    'is_read'    => 0,
                    'created_at' => Carbon::now(),
                ]);
            } catch (\Exception $e) {
                Log::debug('Gagal menyimpan inbox_entries untuk user '.$user->id.': '.$e->getMessage());
            }

            try {
                $user->notify(new \App\Notifications\GenericNotification($title, $message, $extra));
            } catch (\Exception $e) {
                Log::warning('Gagal mengirim notifikasi database ke user ' . $user->id . ': ' . $e->getMessage());
            }

            $role = $user->role ?? null;
            if ((!empty($extra['force_email']) || in_array($role, self::$emailRoles, true)) && !empty($user->email)) {
                try {
                    Mail::to($user->email)->send(new \App\Mail\GenericNotificationMail($title, $message, $extra));
                } catch (\Exception $e) {
                    Log::warning('Gagal mengirim email notifikasi ke '.$user->email.': '.$e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::warning('Gagal mengirim notifikasi ke user ' . ($user->id ?? '[unknown]') . ': ' . $e->getMessage());
        }
    }
}

