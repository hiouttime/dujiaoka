<?php

namespace App\Admin\Resources;

use App\Admin\Resources\Coupons\Pages;
use App\Models\Coupon;
use App\Models\Goods;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class Coupons extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    
    protected static ?string $navigationLabel = '优惠券管理';
    
    protected static ?string $modelLabel = '优惠券';
    
    protected static ?string $pluralModelLabel = '优惠券';
    
    protected static ?string $navigationGroup = '商店管理';
    
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('coupon')
                    ->label('优惠券代码')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                
                Forms\Components\Select::make('type')
                    ->label('优惠类型')
                    ->options(Coupon::getTypeMap())
                    ->required(),
                
                Forms\Components\TextInput::make('discount')
                    ->label('优惠值')
                    ->helperText('百分比填写小数(如0.9表示9折)，固定金额直接填写数值')
                    ->numeric()
                    ->required()
                    ->step(0.01),
                
                Forms\Components\TextInput::make('limit')
                    ->label('使用次数限制')
                    ->helperText('0表示无限制')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                
                Forms\Components\Select::make('goods')
                    ->label('适用商品')
                    ->multiple()
                    ->relationship('goods', 'gd_name')
                    ->options(Goods::pluck('gd_name', 'id'))
                    ->helperText('不选择表示适用于所有商品'),
                
                Forms\Components\DateTimePicker::make('start_time')
                    ->label('开始时间')
                    ->helperText('不填表示立即生效'),
                
                Forms\Components\DateTimePicker::make('end_time')
                    ->label('结束时间')
                    ->helperText('不填表示永不过期'),
                
                Forms\Components\Toggle::make('status')
                    ->label('启用状态')
                    ->default(true),
                
                Forms\Components\Textarea::make('remark')
                    ->label('备注')
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
                
                Tables\Columns\TextColumn::make('coupon')
                    ->label('优惠券代码')
                    ->searchable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('type')
                    ->label('类型')
                    ->formatStateUsing(fn ($state) => Coupon::getTypeMap()[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        Coupon::TYPE_PERCENT => 'primary',
                        Coupon::TYPE_FIXED => 'success',
                        Coupon::TYPE_EACH => 'warning',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('discount')
                    ->label('优惠值')
                    ->formatStateUsing(function ($state, $record) {
                        return match($record->type) {
                            Coupon::TYPE_PERCENT => ($state * 100) . '%',
                            default => '¥' . $state
                        };
                    }),
                
                Tables\Columns\TextColumn::make('limit')
                    ->label('使用限制')
                    ->formatStateUsing(fn ($state) => $state == 0 ? '无限制' : $state . '次'),
                
                Tables\Columns\TextColumn::make('ret_limit')
                    ->label('剩余次数')
                    ->formatStateUsing(fn ($state, $record) => 
                        $record->limit == 0 ? '无限' : $state
                    ),
                
                Tables\Columns\TextColumn::make('goods_count')
                    ->label('适用商品数')
                    ->counts('goods')
                    ->formatStateUsing(fn ($state) => $state == 0 ? '全部商品' : $state . '个'),
                
                Tables\Columns\IconColumn::make('status')
                    ->label('状态')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                
                Tables\Columns\TextColumn::make('start_time')
                    ->label('开始时间')
                    ->dateTime()
                    ->placeholder('立即生效'),
                
                Tables\Columns\TextColumn::make('end_time')
                    ->label('结束时间')
                    ->dateTime()
                    ->placeholder('永不过期'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('优惠类型')
                    ->options(Coupon::getTypeMap()),
                
                Tables\Filters\TernaryFilter::make('status')
                    ->label('启用状态')
                    ->placeholder('全部')
                    ->trueLabel('已启用')
                    ->falseLabel('已禁用'),
                
                Tables\Filters\Filter::make('valid_time')
                    ->label('有效期')
                    ->form([
                        Forms\Components\DatePicker::make('valid_from')
                            ->label('有效期从'),
                        Forms\Components\DatePicker::make('valid_until')
                            ->label('有效期到'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['valid_from'],
                                fn (Builder $query, $date): Builder => $query->where(function ($query) use ($date) {
                                    $query->whereNull('start_time')
                                          ->orWhere('start_time', '>=', $date);
                                }),
                            )
                            ->when(
                                $data['valid_until'],
                                fn (Builder $query, $date): Builder => $query->where(function ($query) use ($date) {
                                    $query->whereNull('end_time')
                                          ->orWhere('end_time', '<=', $date);
                                }),
                            );
                    }),
                
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
            ->defaultSort('id', 'desc');
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
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
