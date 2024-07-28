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
}