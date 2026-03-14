@extends('layouts.portal')
@section('title', '個人設定')
@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">個人設定</h1>

@if(session('status') === 'profile-updated')
<div class="bg-green-50 border border-green-300 text-green-700 rounded-lg p-3 mb-4 text-sm">個人資料已更新</div>
@endif

<div class="space-y-6">
    <div class="bg-white rounded-xl shadow p-6">
        <livewire:profile.update-profile-information-form />
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <livewire:profile.update-password-form />
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <livewire:profile.delete-user-form />
    </div>
</div>
@endsection
