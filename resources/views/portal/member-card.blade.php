@extends('layouts.portal')
@section('title', '會員卡')
@section('content')
<div class="max-w-sm mx-auto">
    <a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline text-sm mb-4 inline-block">← 返回儀表板</a>
    <div class="bg-gradient-to-br from-blue-800 to-blue-600 text-white rounded-2xl shadow-xl p-6">
        <div class="text-xs text-blue-200 mb-4">基扶社聯合會 | Taiwan Kiwanis Association</div>
        <div class="flex items-center gap-4 mb-4">
            @if($member->profile_photo)<img src="{{ Storage::url($member->profile_photo) }}" class="w-16 h-16 rounded-full border-2 border-white object-cover">@else<div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-2xl font-bold">{{ mb_substr($member->name_zh, 0, 1) }}</div>@endif
            <div>
                <div class="text-xl font-bold">{{ $member->name_zh }}</div>
                @if($member->name_en)<div class="text-sm text-blue-200">{{ $member->name_en }}</div>@endif
            </div>
        </div>
        <div class="text-sm space-y-1 mb-4">
            <div>會員編號：{{ $member->member_id }}</div>
            @if($member->club)<div>社團：{{ $member->club->name }}</div>@endif
            @if($member->district)<div>地區：{{ $member->district->name }}</div>@endif
            @if($member->join_date)<div>入會日期：{{ $member->join_date->format('Y/m/d') }}</div>@endif
        </div>
        <div class="bg-white rounded-xl p-3 inline-block">
            {!! $qrCode !!}
        </div>
    </div>
    <div class="mt-4 text-center">
        <p class="text-xs text-gray-400">此 QR Code 可用於活動報到掃描</p>
    </div>
</div>
@endsection
