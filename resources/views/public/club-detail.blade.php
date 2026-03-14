@extends('layouts.public')
@section('title', $club->name)
@section('content')
<div class="max-w-5xl mx-auto px-4 py-12">
    <div class="bg-white rounded-xl shadow p-8 mb-8">
        <div class="flex items-center gap-6 mb-6">
            @if($club->logo)<img src="{{ Storage::url($club->logo) }}" class="w-20 h-20 rounded-full object-cover" alt="{{ $club->name }}">@else<div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-3xl font-bold">{{ mb_substr($club->name, 0, 1) }}</div>@endif
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $club->name }}</h1>
                @if($club->name_en)<p class="text-gray-500">{{ $club->name_en }}</p>@endif
                <p class="text-sm text-blue-600 mt-1">{{ $club->district?->name ?? '' }} · {{ $club->club_code }}</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="font-bold text-gray-700 mb-3">聯絡資訊</h2>
                @if($club->address)<p class="text-sm text-gray-600 mb-1">📍 {{ $club->address }}</p>@endif
                @if($club->phone)<p class="text-sm text-gray-600 mb-1">📞 <a href="tel:{{ $club->phone }}" class="text-blue-600">{{ $club->phone }}</a></p>@endif
                @if($club->email)<p class="text-sm text-gray-600 mb-1">✉️ <a href="mailto:{{ $club->email }}" class="text-blue-600">{{ $club->email }}</a></p>@endif
                @if($club->website)<p class="text-sm text-gray-600">🌐 <a href="{{ $club->website }}" target="_blank" class="text-blue-600">{{ $club->website }}</a></p>@endif
            </div>
            <div>
                <h2 class="font-bold text-gray-700 mb-3">現任幹部</h2>
                @if($club->president)<p class="text-sm text-gray-600 mb-1">社長：{{ $club->president->name_zh }}</p>@endif
                @if($club->secretary)<p class="text-sm text-gray-600 mb-1">秘書：{{ $club->secretary->name_zh }}</p>@endif
                @if($club->treasurer)<p class="text-sm text-gray-600">財務長：{{ $club->treasurer->name_zh }}</p>@endif
            </div>
        </div>
    </div>
</div>
@endsection
