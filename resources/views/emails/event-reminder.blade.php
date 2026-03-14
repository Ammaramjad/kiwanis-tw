<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>活動提醒</title></head>
<body style="font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2>親愛的 {{ $member->name_zh }} 會員您好：</h2>
    <h3>活動提醒：{{ $event->title }}</h3>
    <table style="width:100%; border-collapse:collapse;">
        <tr><td style="padding:4px 8px; color:#666;">時間：</td><td>{{ $event->start_time->format('Y/m/d H:i') }}</td></tr>
        <tr><td style="padding:4px 8px; color:#666;">地點：</td><td>{{ $event->venue }} {{ $event->address }}</td></tr>
        @if($event->contact_person)
        <tr><td style="padding:4px 8px; color:#666;">聯絡人：</td><td>{{ $event->contact_person }} {{ $event->contact_phone }}</td></tr>
        @endif
    </table>
    @if($event->description)
    <div style="margin-top:16px;">{!! nl2br(e($event->description)) !!}</div>
    @endif
    <hr>
    <p style="color:#888; font-size:12px;">此郵件由系統自動發送，請勿直接回覆。</p>
</body>
</html>
