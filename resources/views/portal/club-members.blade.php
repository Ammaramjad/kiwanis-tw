@extends('layouts.portal')
@section('title', '我的社團')
@section('content')
@if(!$club)
<div class="bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-lg p-4">您尚未加入任何社團</div>
@else
<div class="flex items-center gap-4 mb-6">
    @if($club->logo)<img src="{{ Storage::url($club->logo) }}" class="w-16 h-16 rounded-full object-cover">@else<div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-2xl font-bold">{{ mb_substr($club->name, 0, 1) }}</div>@endif
    <div>
        <h1 class="text-2xl font-bold text-gray-800">{{ $club->name }}</h1>
        <p class="text-gray-500 text-sm">{{ $club->district?->name ?? '' }}</p>
    </div>
</div>
<div class="bg-white rounded-xl shadow p-5 mb-6">
    <h2 class="font-bold text-gray-700 mb-3">現任幹部</h2>
    <div class="grid grid-cols-3 gap-4 text-sm">
        @if($club->president)<div><div class="text-xs text-gray-400">社長</div><div class="font-medium">{{ $club->president->name_zh }}</div></div>@endif
        @if($club->secretary)<div><div class="text-xs text-gray-400">秘書</div><div class="font-medium">{{ $club->secretary->name_zh }}</div></div>@endif
        @if($club->treasurer)<div><div class="text-xs text-gray-400">財務長</div><div class="font-medium">{{ $club->treasurer->name_zh }}</div></div>@endif
    </div>
</div>
<h2 class="text-xl font-bold text-gray-800 mb-4">社團會員 ({{ $members->count() }}人)</h2>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @foreach($members as $m)
    <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
        <div class="flex items-center gap-3 mb-2">
            @if($m->profile_photo)<img src="{{ Storage::url($m->profile_photo) }}" class="w-10 h-10 rounded-full object-cover">@else<div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">{{ mb_substr($m->name_zh, 0, 1) }}</div>@endif
            <div>
                <div class="font-medium text-gray-800 text-sm">{{ $m->name_zh }}</div>
                @if($m->profession)<div class="text-xs text-gray-500">{{ $m->profession }}</div>@endif
            </div>
        </div>
        <div class="flex gap-2 flex-wrap">
            @if($m->phone)<a href="tel:{{ $m->phone }}" class="text-xs text-green-600 hover:underline">📞</a>@endif
            @if($m->email)<a href="mailto:{{ $m->email }}" class="text-xs text-blue-600 hover:underline">✉️</a>@endif
            @if($m->line_id)<a href="{{ $m->line_deep_link }}" target="_blank" class="text-xs text-lime-600 hover:underline">LINE</a>@endif
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
