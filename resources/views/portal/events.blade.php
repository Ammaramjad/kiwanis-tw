@extends('layouts.portal')
@section('title', '活動')
@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">活動</h1>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($events as $event)
    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-6 border border-gray-100">
        <div class="text-blue-600 text-xs font-semibold mb-2">📅 {{ $event->start_time->format('Y/m/d H:i') }}</div>
        <h3 class="font-bold text-gray-800 mb-2">{{ $event->title }}</h3>
        @if($event->venue)<p class="text-sm text-gray-500 mb-1">📍 {{ $event->venue }}</p>@endif
        @if($event->max_participants)<p class="text-sm text-gray-500">👥 名額：{{ $event->max_participants }} 人</p>@endif
        <a href="{{ route('portal.events.show', $event) }}" class="mt-4 inline-block text-blue-600 hover:underline text-sm">查看詳情與報名 →</a>
    </div>
    @empty<div class="col-span-3 text-center text-gray-400 py-16">目前尚無活動</div>@endforelse
</div>
<div class="mt-6">{{ $events->appends(request()->query())->links() }}</div>
@endsection
