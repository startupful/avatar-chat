<?php

namespace Filament\AvatarChat\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Filament\AvatarChat\Resources\AvatarChatResource;

class CreateAvatarChat extends CreateRecord
{
    protected static string $resource = AvatarChatResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}