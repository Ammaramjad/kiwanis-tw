@extends('layouts.public')
@section('title', $event->title)
@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">
    <a href="{{ route('events') }}" class="text-blue-600 hover:underline text-sm mb-6 inline-block">← 返回活動列表</a>
    <div class="bg-white rounded-xl shadow p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">{{ $event->title }}</h1>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-600 mb-6">
            <div>📅 <strong>時間：</strong>{{ $event->start_time->format('Y/m/d H:i') }}{{ $event->end_time ? ' ~ '.$event->end_time->format('H:i') : '' }}</div>
            @if($event->venue)<div>📍 <strong>地點：</strong>{{ $event->venue }}</div>@endif
            @if($event->address)<div>🗺️ <strong>地址：</strong>{{ $event->address }}
                @if($event->google_maps_url)<a href="{{ $event->google_maps_url }}" target="_blank" class="text-blue-600 ml-1">在地圖查看</a>@endif
            </div>@endif
            @if($event->contact_person)<div>👤 <strong>聯絡人：</strong>{{ $event->contact_person }} {{ $event->contact_phone }}</div>@endif
            @if($event->max_participants)<div>👥 <strong>名額：</strong>{{ $event->max_participants }} 人</div>@endif
        </div>
        @if($event->description)
        <div class="prose max-w-none text-gray-700">{!! $event->description !!}</div>
        @endif
        @auth
        <div class="mt-6">
            <a href="{{ route('portal.events.show', $event) }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">前往會員入口報名</a>
        </div>
        @else
        <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-600">
            請<a href="{{ route('login') }}" class="text-blue-600 hover:underline">登入</a>以報名此活動。
        </div>
        @endauth
    </div>
</div>
@endsection
