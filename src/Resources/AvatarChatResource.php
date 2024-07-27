<?php

namespace Filament\AvatarChat\Resources;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms;
use Filament\AvatarChat\Models\Avatar;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\Storage;

class AvatarChatResource extends Resource
{
    protected static ?string $model = Avatar::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Avatar Chat';

    protected static ?string $pluralModelLabel = 'Avatar Chats';

    public static function getNavigationGroup(): ?string
    {
        return __('AI');  // 또는 원하는 그룹 이름
    }

    public static function getNavigationSort(): ?int
    {
        return 1;  // 네비게이션 순서
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();  // 선택사항: 배지 표시
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\Toggle::make('is_public')
                ->required(),
            Forms\Components\FileUpload::make('profile_image')
                ->image()
                ->disk('public')
                ->directory('avatars')
                ->visibility('public'),
            Forms\Components\TagsInput::make('categories'),
            Forms\Components\Textarea::make('first_message')
                ->columnSpan('full'),
            Forms\Components\Textarea::make('profile_intro')
                ->columnSpan('full'),
            Forms\Components\TagsInput::make('hashtags'),
            Forms\Components\Textarea::make('profile_details')
                ->columnSpan('full'),
            Forms\Components\KeyValue::make('fine_tuning_data')
                ->columnSpan('full'),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_public')
                    ->boolean(),
                Tables\Columns\ImageColumn::make('profile_image_url')
                    ->circular(),
                Tables\Columns\TagsColumn::make('categories'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_public')
                    ->options([
                        '1' => 'Public',
                        '0' => 'Private',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAvatarChats::route('/'),
            'create' => Pages\CreateAvatarChat::route('/create'),
            'edit' => Pages\EditAvatarChat::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('chats');
    }
}