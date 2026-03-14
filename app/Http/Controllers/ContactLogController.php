<?php
namespace App\Http\Controllers;

use App\Models\ContactLog;
use App\Models\Member;
use Illuminate\Http\Request;

class ContactLogController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:members,id',
            'method' => 'required|in:call,sms,line,email',
        ]);

        $sender = auth()->user()->member;
        if (!$sender) {
            return response()->json(['error' => 'Member profile not found'], 403);
        }

        ContactLog::create([
            'sender_id' => $sender->id,
            'receiver_id' => $validated['receiver_id'],
            'method' => $validated['method'],
            'metadata' => ['user_agent' => $request->userAgent(), 'ip' => $request->ip()],
        ]);

        return response()->json(['success' => true]);
    }
}
