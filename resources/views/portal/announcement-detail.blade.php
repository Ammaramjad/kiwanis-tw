@extends('layouts.portal')
@section('title', $announcement->title)
@section('content')
<a href="{{ route('portal.announcements') }}" class="text-blue-600 hover:underline text-sm mb-4 inline-block">← 返回公告列表</a>
<div class="bg-white rounded-xl shadow p-8">
    @if($announcement->is_pinned)<span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded">置頂</span>@endif
    <h1 class="text-2xl font-bold text-gray-800 mt-3 mb-2">{{ $announcement->title }}</h1>
    @if($announcement->publish_date)<p class="text-sm text-gray-400 mb-6">{{ $announcement->publish_date->format('Y年m月d日') }}</p>@endif
    <div class="prose max-w-none text-gray-700">{!! $announcement->content !!}</div>
    @if($announcement->attachments && count($announcement->attachments))
    <div class="mt-6 border-t pt-4">
        <h3 class="font-semibold text-gray-700 mb-3">附件</h3>
        @foreach($announcement->attachments as $att)
        <a href="{{ Storage::url($att) }}" class="text-blue-600 hover:underline text-sm block">📎 {{ basename($att) }}</a>
        @endforeach
    </div>
    @endif
</div>
@endsection
