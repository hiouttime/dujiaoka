<?php

namespace App\Admin\Resources;

use App\Admin\Resources\Categories\Pages;
use App\Admin\Resources\Categories\RelationManagers;
use App\Models\GoodsGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class Categories extends Resource
{
    protected static ?string $model = GoodsGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?string $navigationLabel = '商品分类';
    
    protected static ?string $modelLabel = '商品分类';
    
    protected static ?string $pluralModelLabel = '商品分类';
    
    protected static ?string $navigationGroup = '商店管理';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('gp_name')
                    ->label('分类名称')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\Toggle::make('is_open')
                    ->label('状态')
                    ->default(true)
                    ->helperText('开启后分类将在前台显示'),
                
                Forms\Components\TextInput::make('ord')
                    ->label('排序')
                    ->numeric()
                    ->default(1)
                    ->helperText('数字越小越靠前'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('gp_name')
                    ->label('分类名称')
                    ->searchable(),
                
                Tables\Columns\ToggleColumn::make('is_open')
                    ->label('状态'),
                
                Tables\Columns\TextColumn::make('ord')
                    ->label('排序')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新时间')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('ord', 'asc')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGoodsGroups::route('/'),
            'create' => Pages\CreateGoodsGroup::route('/create'),
            'edit' => Pages\EditGoodsGroup::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
    {
        return auth('admin')->user()?->can('manage_categories') || auth('admin')->user()?->hasRole('super-admin') || false;
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
