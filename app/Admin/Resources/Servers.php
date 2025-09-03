<?php

namespace App\Admin\Resources;

use App\Admin\Resources\Servers\Pages;
use App\Models\RemoteServer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class Servers extends Resource
{
    protected static ?string $model = RemoteServer::class;

    protected static ?string $navigationIcon = 'heroicon-o-server';

    protected static ?string $navigationGroup = '其他配置';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('remote-server.labels.remote_server');
    }

    public static function getModelLabel(): string
    {
        return __('remote-server.labels.remote_server');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('remote-server.fields.name'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('type')
                    ->label(__('remote-server.fields.type'))
                    ->options(RemoteServer::getServerTypeMap())
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('config', null)),

                Forms\Components\TextInput::make('url')
                    ->label(__('remote-server.fields.url'))
                    ->url()
                    ->visible(fn (Forms\Get $get) => $get('type') == RemoteServer::HTTP_SERVER)
                    ->required(fn (Forms\Get $get) => $get('type') == RemoteServer::HTTP_SERVER),

                Forms\Components\TextInput::make('host')
                    ->label(__('remote-server.fields.host'))
                    ->visible(fn (Forms\Get $get) => in_array($get('type'), [RemoteServer::RCON_COMMAND, RemoteServer::SQL_EXECUTE]))
                    ->required(fn (Forms\Get $get) => in_array($get('type'), [RemoteServer::RCON_COMMAND, RemoteServer::SQL_EXECUTE])),

                Forms\Components\TextInput::make('port')
                    ->label(__('remote-server.fields.port'))
                    ->numeric()
                    ->visible(fn (Forms\Get $get) => in_array($get('type'), [RemoteServer::RCON_COMMAND, RemoteServer::SQL_EXECUTE]))
                    ->required(fn (Forms\Get $get) => in_array($get('type'), [RemoteServer::RCON_COMMAND, RemoteServer::SQL_EXECUTE])),

                Forms\Components\TextInput::make('username')
                    ->label(__('remote-server.fields.username'))
                    ->visible(fn (Forms\Get $get) => $get('type') == RemoteServer::SQL_EXECUTE),

                Forms\Components\TextInput::make('password')
                    ->label(__('remote-server.fields.password'))
                    ->password()
                    ->visible(fn (Forms\Get $get) => in_array($get('type'), [RemoteServer::RCON_COMMAND, RemoteServer::SQL_EXECUTE])),

                Forms\Components\TextInput::make('database')
                    ->label(__('remote-server.fields.database'))
                    ->visible(fn (Forms\Get $get) => $get('type') == RemoteServer::SQL_EXECUTE),

                Forms\Components\Textarea::make('command')
                    ->label(__('remote-server.fields.command'))
                    ->rows(3)
                    ->visible(fn (Forms\Get $get) => in_array($get('type'), [RemoteServer::RCON_COMMAND, RemoteServer::SQL_EXECUTE]))
                    ->helperText(__('remote-server.helps.command')),

                Forms\Components\KeyValue::make('headers')
                    ->label(__('remote-server.fields.headers'))
                    ->visible(fn (Forms\Get $get) => $get('type') == RemoteServer::HTTP_SERVER)
                    ->helperText(__('remote-server.helps.headers')),

                Forms\Components\Textarea::make('body')
                    ->label(__('remote-server.fields.body'))
                    ->rows(5)
                    ->visible(fn (Forms\Get $get) => $get('type') == RemoteServer::HTTP_SERVER)
                    ->helperText(__('remote-server.helps.body')),

                Forms\Components\Toggle::make('is_active')
                    ->label(__('remote-server.fields.is_active'))
                    ->default(true),

                Forms\Components\Textarea::make('description')
                    ->label(__('remote-server.fields.description'))
                    ->rows(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('remote-server.fields.name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label(__('remote-server.fields.type'))
                    ->formatStateUsing(fn (string $state): string => RemoteServer::getServerTypeMap()[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        (string) RemoteServer::HTTP_SERVER => 'success',
                        (string) RemoteServer::RCON_COMMAND => 'warning',
                        (string) RemoteServer::SQL_EXECUTE => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('url')
                    ->label(__('remote-server.fields.url'))
                    ->limit(30)
                    ->visible(fn ($record) => $record?->type == RemoteServer::HTTP_SERVER),

                Tables\Columns\TextColumn::make('host')
                    ->label(__('remote-server.fields.host'))
                    ->visible(fn ($record) => in_array($record?->type, [RemoteServer::RCON_COMMAND, RemoteServer::SQL_EXECUTE])),

                Tables\Columns\TextColumn::make('port')
                    ->label(__('remote-server.fields.port'))
                    ->visible(fn ($record) => in_array($record?->type, [RemoteServer::RCON_COMMAND, RemoteServer::SQL_EXECUTE])),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label(__('remote-server.fields.is_active')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('admin.updated_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('remote-server.fields.type'))
                    ->options(RemoteServer::getServerTypeMap()),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('remote-server.fields.is_active')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRemoteServers::route('/'),
            'create' => Pages\CreateRemoteServer::route('/create'),
            'edit' => Pages\EditRemoteServer::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
    {
        return auth('admin')->user()?->can('manage_servers') || auth('admin')->user()?->hasRole('super-admin') || false;
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit($record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete($record): bool
    {
        return static::canViewAny();
    }
}
