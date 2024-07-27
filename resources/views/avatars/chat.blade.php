@extends('layouts.app')

@section('content')
@csrf
<script src="https://cdn.tailwindcss.com"></script>
<div class="flex flex-col max-w-3xl h-[80vh] mx-auto bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h1 class="text-lg font-semibold text-gray-900">{{ $avatar->name }} 와의 대화</h1>
            <button id="new-chat-btn" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                새로운 대화하기
            </button>
        </div>
    </header>

    <!-- Chat area -->
    <div class="flex-1 overflow-y-auto px-4 py-6" id="chat-messages">
        @if(isset($messages) && $messages->count() > 0)
            @foreach ($messages as $message)
                <div class="mb-4 {{ $message->is_from_avatar ? 'text-left' : 'text-right' }}">
                    @if($message->is_from_avatar)
                        <div class="flex items-start">
                        <img src="{{ $avatar->profile_image_url ?? asset('images/default-avatar.png') }}" alt="{{ $avatar->name }}" class="w-8 h-8 rounded-full mr-2">
                            <div>
                                <div class="text-sm text-gray-600 mb-1">{{ $avatar->name }}</div>
                                <div class="inline-block max-w-xl bg-white rounded-b-lg rounded-tr-lg px-4 py-2">
                                    {{ $message->message }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="inline-block max-w-xl bg-blue-500 text-white rounded-b-lg rounded-tl-lg px-4 py-2">
                            {{ $message->message }}
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <p class="text-center text-gray-500">대화를 시작해보세요!</p>
        @endif
    </div>

    <!-- Input area -->
    <div class="bg-white border-t border-gray-200 px-4 py-4">
        <form id="chat-form" class="flex space-x-3">
            <input type="text" id="user-input" class="flex-1 rounded-full border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="메시지를 입력하세요...">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                전송
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', (event) => {
    const form = document.getElementById('chat-form');
    const input = document.getElementById('user-input');
    const chatMessages = document.getElementById('chat-messages');
    const newChatBtn = document.getElementById('new-chat-btn');

    scrollToBottom();

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const message = input.value.trim();
        if (!message) return;

        addMessageToChat(message, false);
        input.value = '';
        scrollToBottom();

        try {
            const response = await fetch('{{ route("avatar.chat.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message, avatar_id: '{{ $avatar->uuid }}' })
            });

            if (!response.ok) {
                if (response.status === 401) {
                    // 인증되지 않은 경우
                    window.location.href = '{{ route("login") }}'; // 로그인 페이지로 리다이렉트
                    return;
                }
                const errorData = await response.json();
                throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
            }

            const reader = response.body.getReader();
            let avatarMessage = '';

            while (true) {
                const { done, value } = await reader.read();
                if (done) break;
                const chunk = new TextDecoder().decode(value);
                avatarMessage += chunk;
                scrollToBottom();
            }
            updateAvatarMessage(avatarMessage);
        } catch (error) {
            console.error('Error:', error);
            addMessageToChat('죄송합니다. 오류가 발생했습니다: ' + error.message, true);
            scrollToBottom();
        }
    });

    newChatBtn.addEventListener('click', async () => {
        try {
            const response = await fetch('{{ route("avatar.chat.reset") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ avatar_id: '{{ $avatar->uuid }}' })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // 페이지 새로고침
            window.location.reload();
        } catch (error) {
            console.error('Error:', error);
            alert('대화 초기화 중 오류가 발생했습니다.');
        }
    });

    function addMessageToChat(message, isFromAvatar) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `mb-4 ${isFromAvatar ? 'text-left' : 'text-right'}`;
        
        if (isFromAvatar) {
            messageDiv.innerHTML = `
                <div class="flex items-start">
                    <img src="{{ $avatar->profile_image_url ?? asset('images/default-avatar.png') }}" alt="{{ $avatar->name }}" class="w-8 h-8 rounded-full mr-2">
                    <div>
                        <div class="text-sm text-gray-600 mb-1">{{ $avatar->name }}</div>
                        <div class="inline-block max-w-xl bg-white rounded-b-lg rounded-tr-lg px-4 py-2">
                            ${message}
                        </div>
                    </div>
                </div>
            `;
        } else {
            messageDiv.innerHTML = `
                <div class="inline-block max-w-xl bg-blue-500 text-white rounded-b-lg rounded-tl-lg px-4 py-2">
                    ${message}
                </div>
            `;
        }
        
        chatMessages.appendChild(messageDiv);
        scrollToBottom();
    }

    function updateAvatarMessage(message) {
        const avatarMessageDiv = document.createElement('div');
        avatarMessageDiv.className = 'mb-4 text-left avatar-message';
        avatarMessageDiv.innerHTML = `
            <div class="flex items-start">
                <img src="{{ $avatar->profile_image_url ?? asset('images/default-avatar.png') }}" alt="{{ $avatar->name }}" class="w-8 h-8 rounded-full mr-2">
                <div>
                    <div class="text-sm text-gray-600 mb-1">{{ $avatar->name }}</div>
                    <div class="inline-block max-w-xl bg-white rounded-b-lg rounded-tr-lg px-4 py-2">
                        ${message}
                    </div>
                </div>
            </div>
        `;
        chatMessages.appendChild(avatarMessageDiv);
        scrollToBottom();
    }

    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
});
</script>
@endsection