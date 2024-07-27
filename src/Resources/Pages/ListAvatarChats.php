<?php

namespace Filament\AvatarChat\Resources\Pages;

use Filament\AvatarChat\Resources\AvatarChatResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListAvatarChats extends ListRecords
{
    protected static string $resource = AvatarChatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}