@extends('layouts.public')
@section('title', '加入我們')
@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold text-gray-800 mb-4">加入我們</h1>
    <p class="text-gray-500 mb-8">填寫以下表單，我們的工作人員將與您聯繫，協助您加入適合的社團。</p>
    @if(session('success'))
    <div class="bg-green-50 border border-green-300 text-green-700 rounded-lg p-4 mb-6">{{ session('success') }}</div>
    @endif
    <form method="POST" action="{{ route('join.store') }}" class="bg-white rounded-xl shadow p-8 space-y-5">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">姓名 *</label>
                <input type="text" name="name" required value="{{ old('name') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none @error('name') border-red-400 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">電話 *</label>
                <input type="tel" name="phone" required value="{{ old('phone') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none @error('phone') border-red-400 @enderror">
                @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">電子郵件 *</label>
                <input type="email" name="email" required value="{{ old('email') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none @error('email') border-red-400 @enderror">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">城市</label>
                <input type="text" name="city" value="{{ old('city') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">職業</label>
                <input type="text" name="profession" value="{{ old('profession') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">意向社團</label>
                <select name="preferred_club_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">請選擇...</option>
                    @foreach($clubs as $club)<option value="{{ $club->id }}" {{ old('preferred_club_id') == $club->id ? 'selected' : '' }}>{{ $club->name }}</option>@endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">申請說明</label>
            <textarea name="message" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('message') }}</textarea>
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-3 rounded-lg hover:bg-blue-700 transition">送出申請</button>
    </form>
</div>
@endsection
