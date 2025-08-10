<?php

namespace App\Admin\Resources\Products\RelationManagers;

use App\Models\Carmis;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CarmisRelationManager extends RelationManager
{
    protected static string $relationship = 'carmis';
    
    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->type === \App\Models\BaseModel::AUTOMATIC_DELIVERY;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('sub_id')
                    ->label('商品规格')
                    ->options(fn ($livewire) => $livewire->ownerRecord->goods_sub()->pluck('name', 'id'))
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('状态')
                    ->options(Carmis::getStatusMap())
                    ->default(Carmis::STATUS_UNSOLD)
                    ->required(),
                Forms\Components\Toggle::make('is_loop')
                    ->label('是否可重复使用')
                    ->default(false),
                Forms\Components\Textarea::make('carmi')
                    ->label('卡密内容')
                    ->required()
                    ->rows(3),
                Forms\Components\Textarea::make('info')
                    ->label('卡密信息')
                    ->helperText('卡密的使用说明或相关信息')
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('carmi')
            ->columns([
                Tables\Columns\TextColumn::make('goodsSub.name')
                    ->label('商品规格')
                    ->sortable(),
                Tables\Columns\TextColumn::make('carmi')
                    ->label('卡密内容')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('status')
                    ->label('状态')
                    ->formatStateUsing(fn (string $state): string => Carmis::getStatusMap()[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        Carmis::STATUS_UNSOLD => 'success',
                        Carmis::STATUS_SOLD => 'warning', 
                        Carmis::STATUS_USED => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\ToggleColumn::make('is_loop')
                    ->label('可重复使用'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('状态')
                    ->options(Carmis::getStatusMap()),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
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
}
