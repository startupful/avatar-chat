<?php

namespace Startupful\AvatarChat\Http\Controllers;

use Illuminate\Http\Request;
use Startupful\AvatarChat\Models\Avatar;

class AvatarController
{
    public function index()
    {
        $avatars = Avatar::where('is_public', true)->get();
        return view('avatar-chat::avatars.index', compact('avatars'));
    }

    public function create()
    {
        return view('avatar-chat::avatars.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'profile_intro' => 'required|string',
            'profile_image' => 'nullable|image|max:2048',
            'is_public' => 'boolean',
            'categories' => 'nullable|string',
            'hashtags' => 'nullable|string',
            'first_message' => 'required|string',
            'profile_details' => 'required|string',
            'fine_tuning_data' => 'required|string',
        ]);
    
        $avatar = new Avatar();
        $avatar->name = $validatedData['name'];
        $avatar->profile_intro = $validatedData['profile_intro'];
        $avatar->is_public = $request->has('is_public');
        $avatar->categories = explode(',', $validatedData['categories']);
        $avatar->hashtags = explode(',', $validatedData['hashtags']);
        $avatar->first_message = ['content' => $validatedData['first_message']];
        $avatar->profile_details = $validatedData['profile_details'];
        $avatar->fine_tuning_data = ['data' => $validatedData['fine_tuning_data']];
    
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('avatars', 'public');
            $avatar->profile_image = $path;
        }
    
        $avatar->save();
    
        return redirect()->route('avatar.index')->with('success', '아바타가 성공적으로 생성되었습니다.');
    }
}