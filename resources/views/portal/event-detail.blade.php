@extends('layouts.portal')
@section('title', $event->title)
@section('content')
<a href="{{ route('portal.events') }}" class="text-blue-600 hover:underline text-sm mb-4 inline-block">← 返回活動列表</a>
<div class="bg-white rounded-xl shadow p-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">{{ $event->title }}</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-600 mb-6">
        <div>📅 開始：{{ $event->start_time->format('Y/m/d H:i') }}</div>
        @if($event->end_time)<div>🕐 結束：{{ $event->end_time->format('Y/m/d H:i') }}</div>@endif
        @if($event->venue)<div>📍 地點：{{ $event->venue }}</div>@endif
        @if($event->address)<div>🗺️ 地址：{{ $event->address }}@if($event->google_maps_url)<a href="{{ $event->google_maps_url }}" target="_blank" class="text-blue-600 ml-1 text-xs">[地圖]</a>@endif</div>@endif
        @if($event->contact_person)<div>👤 聯絡人：{{ $event->contact_person }} {{ $event->contact_phone }}</div>@endif
        @if($event->max_participants)<div>👥 名額：{{ $event->max_participants }} 人</div>@endif
    </div>
    @if($event->description)<div class="prose max-w-none text-gray-700 mb-6">{!! $event->description !!}</div>@endif
    @if($event->registration_required)
        @if($isRegistered)
        <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg p-4 mb-4">✅ 您已報名此活動</div>
        <form method="POST" action="{{ route('portal.events.unregister', $event) }}">
            @csrf @method('DELETE')
            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 text-sm">取消報名</button>
        </form>
        @elseif($event->is_full)
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-lg p-4">⚠️ 此活動已額滿</div>
        @else
        <form method="POST" action="{{ route('portal.events.register', $event) }}">
            @csrf
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">立即報名</button>
        </form>
        @endif
    @else
    <div class="bg-blue-50 border border-blue-200 text-blue-700 rounded-lg p-4">此活動無需事先報名，歡迎直接參加。</div>
    @endif
</div>
@endsection
