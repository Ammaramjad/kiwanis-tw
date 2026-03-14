@extends('layouts.public')
@section('title', '首頁')
@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-800 to-blue-600 text-white py-24 px-4 text-center">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">基扶社聯合會</h1>
        <p class="text-xl mb-2">Taiwan Kiwanis-Style Association</p>
        <p class="text-blue-200 mb-8">服務社區 · 扶助青少年 · 推動國際友誼</p>
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="{{ route('about') }}" class="bg-white text-blue-800 font-semibold px-6 py-3 rounded-lg hover:bg-blue-50">了解更多</a>
            <a href="{{ route('join') }}" class="border-2 border-white text-white font-semibold px-6 py-3 rounded-lg hover:bg-white hover:text-blue-800">加入我們</a>
        </div>
    </div>
</section>

<!-- Stats -->
<section class="bg-white py-12">
    <div class="max-w-5xl mx-auto px-4 grid grid-cols-3 gap-8 text-center">
        <div><div class="text-4xl font-bold text-blue-700">{{ $stats['districts'] }}</div><div class="text-gray-500">地區</div></div>
        <div><div class="text-4xl font-bold text-blue-700">{{ $stats['clubs'] }}</div><div class="text-gray-500">社團</div></div>
        <div><div class="text-4xl font-bold text-blue-700">{{ $stats['members'] }}</div><div class="text-gray-500">會員</div></div>
    </div>
</section>

<!-- Latest Announcements -->
@if($announcements->count())
<section class="max-w-7xl mx-auto px-4 py-12">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">最新公告</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($announcements as $ann)
        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-5 border border-gray-100">
            @if($ann->is_pinned)<span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded mr-2">置頂</span>@endif
            <h3 class="font-semibold text-gray-800 mt-2 mb-2 line-clamp-2">{{ $ann->title }}</h3>
            <p class="text-gray-500 text-sm line-clamp-3">{!! strip_tags($ann->content) !!}</p>
            @if($ann->publish_date)<p class="text-xs text-gray-400 mt-2">{{ $ann->publish_date->format('Y/m/d') }}</p>@endif
        </div>
        @endforeach
    </div>
</section>
@endif

<!-- Upcoming Events -->
@if($events->count())
<section class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">即將舉辦的活動</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($events as $event)
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-5 border border-gray-100">
                <div class="text-xs text-blue-600 font-semibold mb-2">{{ $event->start_time->format('Y/m/d H:i') }}</div>
                <h3 class="font-semibold text-gray-800 mb-1">{{ $event->title }}</h3>
                @if($event->venue)<p class="text-sm text-gray-500">📍 {{ $event->venue }}{{ $event->city ? ', '.$event->city : '' }}</p>@endif
                <a href="{{ route('events.show', $event) }}" class="text-blue-600 text-sm mt-2 inline-block hover:underline">查看詳情 →</a>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-6"><a href="{{ route('events') }}" class="text-blue-600 hover:underline">查看所有活動 →</a></div>
    </div>
</section>
@endif
@endsection
