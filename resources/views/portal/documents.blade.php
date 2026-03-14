@extends('layouts.portal')
@section('title', '文件中心')
@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">文件中心</h1>
<form method="GET" class="bg-white rounded-xl shadow p-4 mb-6 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-48">
        <label class="block text-xs text-gray-500 mb-1">搜尋</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="文件標題..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">類別</label>
        <select name="category" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">全部</option>
            <option value="minutes" {{ request('category')=='minutes'?'selected':'' }}>會議記錄</option>
            <option value="newsletter" {{ request('category')=='newsletter'?'selected':'' }}>社訊</option>
            <option value="report" {{ request('category')=='report'?'selected':'' }}>報告</option>
            <option value="policy" {{ request('category')=='policy'?'selected':'' }}>政策文件</option>
        </select>
    </div>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">搜尋</button>
</form>
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50"><tr class="text-left text-gray-500 text-xs"><th class="px-4 py-3">標題</th><th class="px-4 py-3">類別</th><th class="px-4 py-3">所屬</th><th class="px-4 py-3">下載次數</th><th class="px-4 py-3">日期</th><th class="px-4 py-3">操作</th></tr></thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($documents as $doc)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-800">{{ $doc->title }}</td>
                <td class="px-4 py-3 text-gray-500">{{ match($doc->category) { 'minutes'=>'會議記錄','newsletter'=>'社訊','report'=>'報告','policy'=>'政策',default=>'其他' } }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $doc->club?->name ?? $doc->district?->name ?? '總部' }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $doc->download_count }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $doc->created_at->format('Y/m/d') }}</td>
                <td class="px-4 py-3"><a href="{{ route('portal.documents.download', $doc) }}" class="text-blue-600 hover:underline text-xs">下載</a></td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">目前尚無文件</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $documents->appends(request()->query())->links() }}</div>
@endsection
