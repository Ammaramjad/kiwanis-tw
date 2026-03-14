<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>公告</title></head>
<body style="font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2>親愛的 {{ $member->name_zh }} 會員您好：</h2>
    <h3>{{ $announcement->title }}</h3>
    <div>{!! nl2br(e($announcement->content)) !!}</div>
    @if($announcement->publish_date)
    <p style="color:#888;">發布日期：{{ $announcement->publish_date->format('Y/m/d') }}</p>
    @endif
    <hr>
    <p style="color:#888; font-size:12px;">此郵件由系統自動發送，請勿直接回覆。</p>
</body>
</html>
