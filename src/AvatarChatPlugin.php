<?php

namespace Filament\AvatarChat;

use Filament\Contracts\Plugin;
use Filament\Panel;

class AvatarChatPlugin implements Plugin
{
    public function getId(): string
    {
        return 'avatar-chat';
    }

    public function register(Panel $panel): void
    {
        $panel
        ->resources([
            Resources\AvatarChatResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }
}