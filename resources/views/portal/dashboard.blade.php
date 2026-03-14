@extends('layouts.portal')
@section('title', '儀表板')
@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">歡迎回來，{{ auth()->user()->name }}</h1>
@if($member)
<div class="bg-white rounded-xl shadow p-6 mb-6 flex items-center gap-6">
    @if($member->profile_photo)<img src="{{ Storage::url($member->profile_photo) }}" class="w-20 h-20 rounded-full object-cover">@else<div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-2xl font-bold">{{ mb_substr($member->name_zh, 0, 1) }}</div>@endif
    <div>
        <div class="text-xl font-bold text-gray-800">{{ $member->name_zh }}{{ $member->name_en ? ' ('.$member->name_en.')' : '' }}</div>
        <div class="text-gray-500 text-sm mt-1">{{ $member->club?->name ?? '尚未分配社團' }} · 會員編號 {{ $member->member_id }}</div>
        <div class="mt-2 flex gap-3">
            <a href="{{ route('portal.member.card', $member) }}" class="text-xs bg-blue-50 text-blue-700 px-3 py-1 rounded-full hover:bg-blue-100">📱 會員卡</a>
        </div>
    </div>
</div>
@endif
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-bold text-gray-700 mb-4 flex items-center gap-2">📢 最新公告</h2>
        @forelse($announcements as $ann)
        <div class="border-b border-gray-100 pb-3 mb-3 last:border-0 last:mb-0">
            @if($ann->is_pinned)<span class="text-xs bg-red-100 text-red-600 px-1.5 py-0.5 rounded mr-1">置頂</span>@endif
            <a href="{{ route('portal.announcements.show', $ann) }}" class="text-sm font-medium text-gray-800 hover:text-blue-600">{{ $ann->title }}</a>
            @if($ann->publish_date)<p class="text-xs text-gray-400 mt-0.5">{{ $ann->publish_date->format('Y/m/d') }}</p>@endif
        </div>
        @empty<p class="text-gray-400 text-sm">目前尚無公告</p>@endforelse
        <a href="{{ route('portal.announcements') }}" class="text-blue-600 hover:underline text-sm mt-3 inline-block">查看所有公告 →</a>
    </div>
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-bold text-gray-700 mb-4 flex items-center gap-2">📅 即將舉辦的活動</h2>
        @forelse($events as $event)
        <div class="border-b border-gray-100 pb-3 mb-3 last:border-0 last:mb-0">
            <div class="text-xs text-blue-600 font-medium">{{ $event->start_time->format('Y/m/d H:i') }}</div>
            <a href="{{ route('portal.events.show', $event) }}" class="text-sm font-medium text-gray-800 hover:text-blue-600">{{ $event->title }}</a>
            @if($event->venue)<p class="text-xs text-gray-400 mt-0.5">📍 {{ $event->venue }}</p>@endif
        </div>
        @empty<p class="text-gray-400 text-sm">目前尚無活動</p>@endforelse
        <a href="{{ route('portal.events') }}" class="text-blue-600 hover:underline text-sm mt-3 inline-block">查看所有活動 →</a>
    </div>
</div>
@endsection
