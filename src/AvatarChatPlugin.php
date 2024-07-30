<?php

namespace Startupful\AvatarChat;

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
        $this->publishesMigrations([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ]);
    }

    public static function make(): static
    {
        return app(static::class);
    }
}