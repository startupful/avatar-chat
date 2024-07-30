<?php

namespace Startupful\AvatarChat;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Support\ServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Livewire\Livewire;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Startupful\AvatarChat\Resources\AvatarChatResource;

class AvatarChatServiceProvider extends PackageServiceProvider
{
    public static string $name = 'avatar-chat';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations(['create_avatar_chat_tables'])
            ->runsMigrations();
    }

    public function packageBooted(): void
    {
        Livewire::component('avatar-chat-resource', AvatarChatResource::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'avatar-chat');

        // Asset Registration (필요한 경우)
        // FilamentAsset::register(
        //     assets:[
        //         AlpineComponent::make('avatar-chat', __DIR__ . '/../resources/dist/avatar-chat.js'),
        //     ],
        //     package: 'filament/avatar-chat'
        // );
    }

    protected function bootLivewireComponents(): string
    {
        return '';
    }
}