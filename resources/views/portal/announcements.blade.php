@extends('layouts.portal')
@section('title', '公告')
@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">公告</h1>
<div class="space-y-4">
    @forelse($announcements as $ann)
    <a href="{{ route('portal.announcements.show', $ann) }}" class="block bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
        <div class="flex items-start gap-3">
            @if($ann->is_pinned)<span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded mt-0.5 flex-shrink-0">置頂</span>@endif
            <div class="flex-1">
                <h2 class="font-bold text-gray-800">{{ $ann->title }}</h2>
                <p class="text-gray-500 text-sm mt-1 line-clamp-2">{!! strip_tags($ann->content) !!}</p>
                @if($ann->publish_date)<p class="text-xs text-gray-400 mt-2">{{ $ann->publish_date->format('Y/m/d') }}</p>@endif
            </div>
        </div>
    </a>
    @empty<div class="text-center text-gray-400 py-16">目前尚無公告</div>@endforelse
</div>
<div class="mt-6">{{ $announcements->links() }}</div>
@endsection
