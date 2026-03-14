<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', '基扶社管理系統') }} - @yield('title', '首頁')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans">
    <nav class="bg-blue-800 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-xl font-bold">🌐 基扶社聯合會</a>
                </div>
                <div class="hidden md:flex items-center space-x-4 text-sm">
                    <a href="{{ route('about') }}" class="hover:text-blue-200">關於我們</a>
                    <a href="{{ route('organization') }}" class="hover:text-blue-200">組織架構</a>
                    <a href="{{ route('clubs') }}" class="hover:text-blue-200">社團目錄</a>
                    <a href="{{ route('news') }}" class="hover:text-blue-200">最新消息</a>
                    <a href="{{ route('events') }}" class="hover:text-blue-200">活動</a>
                    <a href="{{ route('join') }}" class="hover:text-blue-200">加入我們</a>
                    <a href="{{ route('contact') }}" class="hover:text-blue-200">聯絡</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-blue-600 hover:bg-blue-500 px-3 py-1 rounded">會員入口</a>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-blue-200">登入</a>
                    @endauth
                </div>
                <div class="md:hidden">
                    <button id="mobile-menu-btn" class="p-2 rounded focus:outline-none focus:ring-2 focus:ring-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div id="mobile-menu" class="hidden md:hidden px-4 pb-3 space-y-1">
            <a href="{{ route('about') }}" class="block py-2 hover:text-blue-200">關於我們</a>
            <a href="{{ route('organization') }}" class="block py-2 hover:text-blue-200">組織架構</a>
            <a href="{{ route('clubs') }}" class="block py-2 hover:text-blue-200">社團目錄</a>
            <a href="{{ route('news') }}" class="block py-2 hover:text-blue-200">最新消息</a>
            <a href="{{ route('events') }}" class="block py-2 hover:text-blue-200">活動</a>
            <a href="{{ route('join') }}" class="block py-2 hover:text-blue-200">加入我們</a>
            <a href="{{ route('contact') }}" class="block py-2 hover:text-blue-200">聯絡</a>
            @auth
                <a href="{{ route('dashboard') }}" class="block py-2 text-blue-200">會員入口</a>
            @else
                <a href="{{ route('login') }}" class="block py-2 hover:text-blue-200">登入</a>
            @endauth
        </div>
    </nav>
    <main>@yield('content')</main>
    <footer class="bg-gray-800 text-gray-300 mt-16 py-10">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-white font-bold text-lg mb-3">基扶社聯合會</h3>
                <p class="text-sm">服務社區，扶助青少年，推動國際友誼。</p>
            </div>
            <div>
                <h3 class="text-white font-bold mb-3">快速連結</h3>
                <ul class="space-y-1 text-sm">
                    <li><a href="{{ route('about') }}" class="hover:text-white">關於我們</a></li>
                    <li><a href="{{ route('clubs') }}" class="hover:text-white">社團目錄</a></li>
                    <li><a href="{{ route('events') }}" class="hover:text-white">活動</a></li>
                    <li><a href="{{ route('join') }}" class="hover:text-white">加入我們</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-white font-bold mb-3">聯絡資訊</h3>
                <p class="text-sm">Taiwan, ROC<br>Email: info@kiwanis.tw</p>
            </div>
        </div>
        <div class="text-center text-xs text-gray-500 mt-8">© {{ date('Y') }} 基扶社聯合會. All rights reserved.</div>
    </footer>
    <script>
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
</body>
</html>
