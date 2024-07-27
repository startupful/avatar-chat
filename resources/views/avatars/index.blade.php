@extends('layouts.app')

@section('content')
    <h1>Avatar List</h1>
    <div class="avatar-grid">
        @foreach($avatars as $avatar)
            <div class="avatar-item">
                <img src="{{ $avatar->profile_image }}" alt="{{ $avatar->name }}">
                <h2>{{ $avatar->name }}</h2>
                <a href="{{ route('avatar.chat', $avatar->uuid) }}">Chat with {{ $avatar->name }}</a>
            </div>
        @endforeach
    </div>
@endsection