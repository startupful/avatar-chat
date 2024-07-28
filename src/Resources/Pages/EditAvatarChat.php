<?php

namespace Startupful\AvatarChat\Resources\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Startupful\AvatarChat\Resources\AvatarChatResource;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Storage;

class EditAvatarChat extends EditRecord
{
    protected static string $resource = AvatarChatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!isset($data['profile_image']) || $data['profile_image'] === null) {
            // 새 이미지가 업로드되지 않았다면 기존 이미지 유지
            $data['profile_image'] = $this->record->getRawOriginal('profile_image');
        }
        return $data;
    }
}