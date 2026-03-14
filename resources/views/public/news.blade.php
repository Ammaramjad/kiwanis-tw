@extends('layouts.public')
@section('title', '最新消息')
@section('content')
<div class="max-w-5xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">最新消息</h1>
    <div class="space-y-6">
        @forelse($announcements as $ann)
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
            @if($ann->is_pinned)<span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded mr-2">置頂</span>@endif
            <h2 class="text-xl font-bold text-gray-800 mb-2">{{ $ann->title }}</h2>
            <div class="text-gray-600 line-clamp-3 text-sm">{!! strip_tags($ann->content) !!}</div>
            @if($ann->publish_date)<p class="text-xs text-gray-400 mt-3">{{ $ann->publish_date->format('Y/m/d') }}</p>@endif
        </div>
        @empty
        <div class="text-center text-gray-400 py-16">目前尚無公告</div>
        @endforelse
    </div>
    <div class="mt-8">{{ $announcements->links() }}</div>
</div>
@endsection
