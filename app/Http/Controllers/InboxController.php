<?php

namespace App\Http\Controllers;

use App\Models\InboxEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InboxController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $perPage = 20;

        $entries = InboxEntry::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return view('inbox.index', compact('entries'));
    }

    public function markRead($id)
    {
        $user = Auth::user();
        $entry = InboxEntry::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        $entry->is_read = true;
        $entry->save();

        // Support AJAX requests
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notifikasi ditandai terbaca.');
    }

    public function markAllRead()
    {
        $user = Auth::user();
        InboxEntry::where('user_id', $user->id)->where('is_read', 0)->update(['is_read' => 1]);

        return redirect()->back()->with('success', 'Semua notifikasi ditandai terbaca.');
    }
}
