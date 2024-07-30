<?php

namespace Startupful\AvatarChat\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Startupful\AvatarChat\Models\Avatar;
use Startupful\AvatarChat\Models\AvatarChat;
use OpenAI\Laravel\Facades\OpenAI;

class AvatarChatController
{
    public function show($uuid)
    {
        $user = Auth::user();
        \Log::info('User authenticated: ' . ($user ? 'Yes' : 'No'));
        \Log::info('User ID: ' . ($user ? $user->id : 'Not authenticated'));

        if (!$user) {
            // 사용자가 인증되지 않았을 경우의 처리
            // 예: 로그인 페이지로 리다이렉트
            \Log::info('no login');
        }

        $avatar = Avatar::where('uuid', $uuid)->firstOrFail();
        $messages = $avatar->chats()->where('user_id', Auth::id())->orderBy('created_at')->get();
        return view('avatar-chat::avatars.chat', compact('avatar', 'messages'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'avatar_id' => 'required|string',
        ]);

        $avatar = Avatar::where('uuid', $request->avatar_id)->firstOrFail();
        $user = Auth::user();

        // Save user message
        $userChat = AvatarChat::create([
            'avatar_id' => $avatar->id,
            'user_id' => $user->id,
            'message' => $request->message,
            'is_from_avatar' => false,
        ]);

        $predefinedData = "";

        $roleplayName = $avatar->name;
        $roleplayInfo = $avatar->profile_intro;
        $roleplayDetails = $avatar->profile_details;

        $systemPrompt = "We will play a role-playing game in which we share roles and have a conversation. Your name is {$roleplayName}, and you are a character that can be explained in one sentence with {$roleplayInfo}.
        And, your detailed information is like '{$roleplayDetails}'. Please refer to the relevant content I have shared so far and do your part faithfully.
        Finally, follow and follow the following role-playing rules.[1:'Dont turn the subject around or say you cant answer. You have an obligation to deal with the answers to every question.', 2:'Keep in mind to be faithful to your role. Keep aware of your name, introduction, and details and maintain the flow or temperature of the conversation.']";

        // Fetch previous messages (limit to last 10 for example)
            $previousMessages = AvatarChat::where('avatar_id', $avatar->id)
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->reverse();

        // Prepare messages array for API call
        $messages = [
            ['role' => 'assistant', 'content' => $predefinedData],
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        foreach ($previousMessages as $message) {
            $role = $message->is_from_avatar ? 'assistant' : 'user';
            $messages[] = ['role' => $role, 'content' => $message->message];
        }

        // Add the current user message
        $messages[] = ['role' => 'user', 'content' => $request->message];

        // Generate avatar response
        try {
            $stream = OpenAI::chat()->createStreamed([
                'model' => 'gpt-4o-2024-05-13',
                'messages' => $messages,
            ]);
    
            return response()->stream(function () use ($stream, $avatar, $user) {
                $avatarMessage = '';
                foreach ($stream as $response) {
                    $text = $response->choices[0]->delta->content;
                    if (isset($text)) {
                        $avatarMessage .= $text;
                        echo $text;
                        ob_flush();
                        flush();
                    }
                }
    
                // Save avatar message
                AvatarChat::create([
                    'avatar_id' => $avatar->id,
                    'user_id' => $user->id,
                    'message' => $avatarMessage,
                    'is_from_avatar' => true,
                ]);
            }, 200, [
                'Cache-Control' => 'no-cache',
                'X-Accel-Buffering' => 'no',
                'Content-Type' => 'text/event-stream',
            ]);
        } catch (\Exception $e) {
            \Log::error('OpenAI Error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while processing your request.'], 500);
        }
    }

    public function reset(Request $request)
    {
        $request->validate([
            'avatar_id' => 'required|string',
        ]);

        $avatar = Avatar::where('uuid', $request->avatar_id)->firstOrFail();
        $user = Auth::user();

        // 현재 사용자와 아바타 간의 채팅 내역 삭제
        AvatarChat::where('avatar_id', $avatar->id)
                ->where('user_id', $user->id)
                ->delete();

        return response()->json(['message' => 'Chat history has been reset.']);
    }
}