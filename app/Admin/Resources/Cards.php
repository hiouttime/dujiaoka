<?php

namespace App\Admin\Resources;

use App\Admin\Resources\Cards\Pages;
use App\Models\Carmis;
use App\Models\Goods;
use App\Models\GoodsSub;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class Cards extends Resource
{
    protected static ?string $model = Carmis::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';
    
    protected static ?string $navigationLabel = '卡密管理';
    
    protected static ?string $modelLabel = '卡密';
    
    protected static ?string $pluralModelLabel = '卡密';
    
    protected static ?string $navigationGroup = '商店管理';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('goods_id')
                    ->label('商品')
                    ->options(Goods::where('type', Goods::AUTOMATIC_DELIVERY)->pluck('gd_name', 'id'))
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('sub_id', 0)),
                
                Forms\Components\Select::make('sub_id')
                    ->label('子商品')
                    ->options(function (Forms\Get $get) {
                        $goodsId = $get('goods_id');
                        if (!$goodsId) {
                            return [0 => '无子商品'];
                        }
                        
                        $subs = GoodsSub::where('goods_id', $goodsId)->pluck('name', 'id')->toArray();
                        return [0 => '无子商品'] + $subs;
                    })
                    ->default(0)
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('goods.gd_name')
                    ->label('商品名称')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('sub_id')
                    ->label('子商品')
                    ->formatStateUsing(function ($state) {
                        if ($state == 0) {
                            return '无子商品';
                        }
                        $goodsSub = GoodsSub::find($state);
                        return $goodsSub ? $goodsSub->name : $state;
                    }),
                
                Tables\Columns\SelectColumn::make('status')
                    ->label('状态')
                    ->options(Carmis::getStatusMap()),
                
                Tables\Columns\IconColumn::make('is_loop')
                    ->label('可重复使用')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                
                Tables\Columns\TextColumn::make('carmi')
                    ->label('卡密')
                    ->limit(20)
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('info')
                    ->label('信息')
                    ->limit(20),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新时间')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('goods_id')
                    ->label('商品')
                    ->options(Goods::where('type', Goods::AUTOMATIC_DELIVERY)->pluck('gd_name', 'id')),
                
                Tables\Filters\SelectFilter::make('status')
                    ->label('状态')
                    ->options(Carmis::getStatusMap()),
                
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            ])
            ->defaultSort('id', 'desc')
            ->poll('30s');
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
            'index' => Pages\ListCarmis::route('/'),
            'create' => Pages\CreateCarmis::route('/create'),
            'edit' => Pages\EditCarmis::route('/{record}/edit'),
        ];
    }
}
