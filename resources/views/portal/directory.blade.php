@extends('layouts.portal')
@section('title', '會員通訊錄')
@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">會員通訊錄</h1>
<form method="GET" class="bg-white rounded-xl shadow p-4 mb-6 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 items-end">
    <div class="col-span-2">
        <label class="block text-xs text-gray-500 mb-1">搜尋姓名</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="姓名..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">社團</label>
        <select name="club_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            <option value="">全部</option>
            @foreach($clubs as $c)<option value="{{ $c->id }}" {{ request('club_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">地區</label>
        <select name="district_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            <option value="">全部</option>
            @foreach($districts as $d)<option value="{{ $d->id }}" {{ request('district_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>@endforeach
        </select>
    </div>
    <div class="col-span-2 flex gap-2">
        <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 text-sm">搜尋</button>
        <a href="{{ route('portal.directory') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm text-gray-600">清除</a>
    </div>
</form>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @forelse($members as $m)
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
        <div class="flex items-center gap-3 mb-3">
            @if($m->profile_photo)<img src="{{ Storage::url($m->profile_photo) }}" class="w-14 h-14 rounded-full object-cover">@else<div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xl font-bold">{{ mb_substr($m->name_zh, 0, 1) }}</div>@endif
            <div>
                <div class="font-bold text-gray-800">{{ $m->name_zh }}</div>
                @if($m->name_en)<div class="text-xs text-gray-500">{{ $m->name_en }}</div>@endif
                @if($m->profession)<div class="text-xs text-gray-500">{{ $m->profession }}</div>@endif
            </div>
        </div>
        @if($m->club)<p class="text-xs text-gray-500 mb-3">{{ $m->club->name }}</p>@endif
        <div class="flex flex-wrap gap-2 mt-2">
            @if($m->phone)
            <a href="tel:{{ $m->phone_e164 ?? $m->phone }}" onclick="logContact({{ $m->id }}, 'call')" class="flex items-center gap-1 text-xs bg-green-50 text-green-700 px-2 py-1 rounded-full hover:bg-green-100" title="撥打電話">📞 電話</a>
            <a href="sms:{{ $m->phone_e164 ?? $m->phone }}" onclick="logContact({{ $m->id }}, 'sms')" class="flex items-center gap-1 text-xs bg-yellow-50 text-yellow-700 px-2 py-1 rounded-full hover:bg-yellow-100" title="傳送簡訊">💬 簡訊</a>
            @endif
            @if($m->line_id)<a href="{{ $m->line_deep_link }}" onclick="logContact({{ $m->id }}, 'line')" target="_blank" class="flex items-center gap-1 text-xs bg-lime-50 text-lime-700 px-2 py-1 rounded-full hover:bg-lime-100" title="LINE 聊天">LINE</a>@endif
            @if($m->email)<a href="mailto:{{ $m->email }}" onclick="logContact({{ $m->id }}, 'email')" class="flex items-center gap-1 text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded-full hover:bg-blue-100" title="發送郵件">✉️ 郵件</a>@endif
            @if($m->city)<a href="https://maps.google.com/?q={{ urlencode($m->city) }}" target="_blank" class="flex items-center gap-1 text-xs bg-gray-50 text-gray-600 px-2 py-1 rounded-full hover:bg-gray-100" title="地圖">🗺️ 地圖</a>@endif
        </div>
    </div>
    @empty<div class="col-span-4 text-center text-gray-400 py-16">找不到符合條件的會員</div>@endforelse
</div>
<div class="mt-6">{{ $members->appends(request()->query())->links() }}</div>
<script>
function logContact(receiverId, method) {
    fetch('{{ route("portal.contact-log") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
        body: JSON.stringify({receiver_id: receiverId, method: method})
    });
}
</script>
@endsection
