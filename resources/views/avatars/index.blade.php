@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
<div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">아바타 목록</h1>
        <a href="{{ route('avatar.create') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
            아바타 생성
        </a>
    </div>
    
    @if($avatars->isEmpty())
        <p class="text-gray-500">현재 사용 가능한 아바타가 없습니다.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($avatars as $avatar)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <img src="{{ $avatar->profile_image_url ?? asset('images/default-avatar.png') }}" alt="{{ $avatar->name }}" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h2 class="text-xl font-semibold mb-2">{{ $avatar->name }}</h2>
                        <a href="{{ route('avatar.chat', $avatar->uuid) }}" class="block w-full text-center bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                            {{ $avatar->name }}와 대화하기
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection