@extends('layouts.public')
@section('title', '社團目錄')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">社團目錄</h1>
    <form method="GET" class="bg-white rounded-xl shadow p-4 mb-8 flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-sm text-gray-600 mb-1">搜尋社團</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="輸入社團名稱..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
        <div class="min-w-40">
            <label class="block text-sm text-gray-600 mb-1">地區</label>
            <select name="district" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">所有地區</option>
                @foreach($districts as $d)
                <option value="{{ $d->id }}" {{ request('district') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 text-sm">搜尋</button>
        <a href="{{ route('clubs') }}" class="text-gray-500 hover:text-gray-700 text-sm py-2">清除</a>
    </form>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($clubs as $club)
        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-6 border border-gray-100">
            <div class="flex items-center gap-3 mb-3">
                @if($club->logo)<img src="{{ Storage::url($club->logo) }}" class="w-12 h-12 rounded-full object-cover" alt="{{ $club->name }}">@else<div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-lg">{{ mb_substr($club->name, 0, 1) }}</div>@endif
                <div>
                    <h3 class="font-bold text-gray-800">{{ $club->name }}</h3>
                    <div class="text-xs text-gray-500">{{ $club->district?->name ?? '' }}</div>
                </div>
            </div>
            @if($club->city)<p class="text-sm text-gray-500 mb-1">📍 {{ $club->city }}</p>@endif
            @if($club->phone)<p class="text-sm text-gray-500 mb-1">📞 {{ $club->phone }}</p>@endif
            <a href="{{ route('clubs.show', $club) }}" class="mt-3 inline-block text-blue-600 hover:underline text-sm">查看詳情 →</a>
        </div>
        @empty
        <div class="col-span-3 text-center text-gray-400 py-16">找不到符合條件的社團</div>
        @endforelse
    </div>
    <div class="mt-8">{{ $clubs->links() }}</div>
</div>
@endsection
