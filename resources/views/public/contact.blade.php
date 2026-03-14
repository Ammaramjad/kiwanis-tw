@extends('layouts.public')
@section('title', '聯絡我們')
@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">聯絡我們</h1>
    <div class="bg-white rounded-xl shadow p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h2 class="font-bold text-gray-700 mb-4">聯絡資訊</h2>
                <p class="text-gray-600 mb-3">📧 info@kiwanis.tw</p>
                <p class="text-gray-600 mb-3">🌐 Taiwan, ROC</p>
                <p class="text-gray-600">如有任何問題，請透過電子郵件與我們聯繫，我們將盡快回覆。</p>
            </div>
            <div>
                <h2 class="font-bold text-gray-700 mb-4">辦公時間</h2>
                <p class="text-gray-600 mb-2">週一至週五：09:00 - 17:00</p>
                <p class="text-gray-600">週六、週日：休息</p>
            </div>
        </div>
    </div>
</div>
@endsection
