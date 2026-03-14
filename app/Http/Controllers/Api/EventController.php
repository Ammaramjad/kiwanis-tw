<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with(['club:id,name'])->where('status', 'published');
        if ($request->search) $query->where('title', 'like', "%{$request->search}%");
        if ($request->upcoming) $query->where('start_time', '>=', now());
        return response()->json($query->orderBy('start_time')->paginate(15));
    }

    public function show(Event $event)
    {
        abort_if($event->status !== 'published', 404);
        return response()->json($event->load(['club', 'district']));
    }

    public function register(Request $request, Event $event)
    {
        $member = $request->user()->member;
        if (!$member) return response()->json(['error' => 'Member profile not found'], 403);
        if ($event->is_full) return response()->json(['error' => '活動已額滿'], 422);
        $existing = EventRegistration::where('event_id', $event->id)->where('member_id', $member->id)->first();
        if ($existing) return response()->json(['message' => '已報名'], 200);
        $reg = EventRegistration::create([
            'event_id' => $event->id,
            'member_id' => $member->id,
            'status' => 'registered',
            'qr_code' => Str::uuid(),
        ]);
        return response()->json($reg, 201);
    }

    public function unregister(Request $request, Event $event)
    {
        $member = $request->user()->member;
        if (!$member) return response()->json(['error' => 'Member profile not found'], 403);
        EventRegistration::where('event_id', $event->id)->where('member_id', $member->id)->delete();
        return response()->json(['message' => '已取消報名']);
    }
}
