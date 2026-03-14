@extends('layouts.public')
@section('title', '組織架構')
@section('content')
<div class="max-w-6xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">組織架構</h1>
    <div class="space-y-8">
        @foreach($districts as $district)
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-bold text-blue-700 mb-4">{{ $district->name }} ({{ $district->district_code }})</h2>
            @if($district->clubs->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($district->clubs as $club)
                <a href="{{ route('clubs.show', $club) }}" class="border border-gray-200 rounded-lg p-3 hover:bg-blue-50 hover:border-blue-300 transition">
                    <div class="font-medium text-gray-800">{{ $club->name }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $club->club_code }}{{ $club->city ? ' · '.$club->city : '' }}</div>
                </a>
                @endforeach
            </div>
            @else
            <p class="text-gray-400 text-sm">此地區尚無社團</p>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endsection
