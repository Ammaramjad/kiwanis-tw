<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', '基扶社管理系統') }} - 會員入口 - @yield('title', '儀表板')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 font-sans">
<div class="min-h-screen flex">
    <!-- Sidebar -->
    <aside class="hidden md:flex flex-col w-64 bg-blue-900 text-white">
        <div class="p-5 border-b border-blue-800">
            <a href="{{ route('home') }}" class="text-lg font-bold">基扶社管理系統</a>
            <p class="text-xs text-blue-300 mt-1">會員入口</p>
        </div>
        <nav class="flex-1 p-4 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-800 {{ request()->routeIs('dashboard') ? 'bg-blue-800' : '' }}">🏠 儀表板</a>
            <a href="{{ route('portal.directory') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-800 {{ request()->routeIs('portal.directory') ? 'bg-blue-800' : '' }}">👥 會員通訊錄</a>
            <a href="{{ route('portal.club-members') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-800">🏢 我的社團</a>
            <a href="{{ route('portal.events') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-800">📅 活動</a>
            <a href="{{ route('portal.announcements') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-800">📢 公告</a>
            <a href="{{ route('portal.documents') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-800">📄 文件中心</a>
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-800">⚙️ 個人設定</a>
        </nav>
        <div class="p-4 border-t border-blue-800">
            <div class="text-sm text-blue-300">{{ auth()->user()->name }}</div>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="text-xs text-blue-400 hover:text-white">登出</button>
            </form>
        </div>
    </aside>
    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow-sm px-6 py-4 flex items-center justify-between md:hidden">
            <a href="{{ route('home') }}" class="font-bold text-blue-800">基扶社管理系統</a>
            <a href="{{ route('dashboard') }}" class="text-sm text-blue-600">儀表板</a>
        </header>
        <main class="flex-1 p-6">
            @if(session('success'))<div class="bg-green-50 border border-green-300 text-green-700 rounded-lg p-3 mb-4 text-sm">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="bg-red-50 border border-red-300 text-red-700 rounded-lg p-3 mb-4 text-sm">{{ session('error') }}</div>@endif
            @if(session('info'))<div class="bg-blue-50 border border-blue-300 text-blue-700 rounded-lg p-3 mb-4 text-sm">{{ session('info') }}</div>@endif
            @yield('content')
        </main>
    </div>
</div>
@livewireScripts
</body>
</html>
