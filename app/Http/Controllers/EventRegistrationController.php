<?php
namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventRegistrationController extends Controller
{
    public function register(Event $event)
    {
        $member = auth()->user()->member;
        if (!$member) return back()->with('error', '您尚未建立會員檔案');

        if ($event->is_full) return back()->with('error', '此活動已額滿');

        $existing = EventRegistration::where('event_id', $event->id)->where('member_id', $member->id)->first();
        if ($existing) return back()->with('info', '您已報名此活動');

        EventRegistration::create([
            'event_id' => $event->id,
            'member_id' => $member->id,
            'status' => 'registered',
            'qr_code' => Str::uuid(),
        ]);

        return back()->with('success', '報名成功！');
    }

    public function unregister(Event $event)
    {
        $member = auth()->user()->member;
        if (!$member) return back()->with('error', '您尚未建立會員檔案');

        EventRegistration::where('event_id', $event->id)->where('member_id', $member->id)->delete();
        return back()->with('success', '已取消報名');
    }
}
