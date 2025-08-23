<?php

namespace App\Admin\Resources;

use App\Admin\Resources\AdminUsers\Pages;
use App\Models\AdminUser;
use Filament\Forms\{Form, Components\Section, Components\Select, Components\TextInput};
use Filament\Resources\Resource;
use Filament\Tables\{Table, Columns\TextColumn, Actions\Action};
use Filament\{Tables, Notifications\Notification};
use Illuminate\Support\Facades\Hash;

class AdminUsers extends Resource
{
    protected static ?string $model = AdminUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationGroup = '系统管理';
    
    protected static ?string $navigationLabel = '管理员';
    
    protected static ?string $modelLabel = '管理员';
    
    protected static ?string $pluralModelLabel = '管理员管理';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('基本信息')
                    ->schema([
                        TextInput::make('name')->label('姓名')->required(),
                        TextInput::make('username')->label('用户名')->required()->unique(ignoreRecord: true),
                        TextInput::make('password')->label('密码')->password()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->helperText('留空则不修改密码'),
                    ])->columns(2),

                Section::make('角色权限')
                    ->schema([
                        Select::make('roles')->label('角色')->multiple()
                            ->relationship('roles', 'name')->preload()->searchable(),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('name')->label('姓名')->searchable()->sortable(),
                TextColumn::make('username')->label('用户名')->searchable()->sortable(),
                TextColumn::make('roles.name')->label('角色')->badge()->separator(','),
                TextColumn::make('created_at')->label('创建时间')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->label('角色')
                    ->relationship('roles', 'name')
                    ->multiple(),
            ])
            ->actions([
                Action::make('resetPassword')
                    ->label('重置密码')
                    ->icon('heroicon-o-key')
                    ->form([
                        TextInput::make('password')
                            ->label('新密码')
                            ->password()
                            ->required()
                            ->minLength(6),
                            
                        TextInput::make('password_confirmation')
                            ->label('确认密码')
                            ->password()
                            ->required()
                            ->same('password'),
                    ])
                    ->action(function (AdminUser $record, array $data) {
                        $record->update([
                            'password' => Hash::make($data['password'])
                        ]);
                        
                        Notification::make()
                            ->title('密码重置成功')
                            ->success()
                            ->send();
                    }),
                    
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdminUsers::route('/'),
            'create' => Pages\CreateAdminUser::route('/create'),
            'edit' => Pages\EditAdminUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('manage_admins') || auth()->user()?->hasRole('super-admin') || false;
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
