<?php

namespace App\Admin\Resources;

use App\Admin\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Goods;
use App\Models\Coupon;
use App\Models\Pay;
use App\Models\GoodsSub;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    
    protected static ?string $navigationLabel = '订单列表';
    
    protected static ?string $modelLabel = '订单';
    
    protected static ?string $pluralModelLabel = '订单';
    
    protected static ?string $navigationGroup = '订单管理';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order_sn')
                    ->label('订单号')
                    ->disabled(),
                
                Forms\Components\TextInput::make('title')
                    ->label('订单标题')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('email')
                    ->label('邮箱')
                    ->email()
                    ->disabled(),
                
                Forms\Components\Select::make('goods_id')
                    ->label('商品')
                    ->options(Goods::pluck('gd_name', 'id'))
                    ->disabled(),
                
                Forms\Components\TextInput::make('sub_id')
                    ->label('子商品')
                    ->formatStateUsing(function ($state) {
                        if ($state == 0) {
                            return '无子商品';
                        }
                        $goodsSub = GoodsSub::find($state);
                        return $goodsSub ? $goodsSub->name : $state;
                    })
                    ->disabled(),
                
                Forms\Components\TextInput::make('goods_price')
                    ->label('商品价格')
                    ->numeric()
                    ->disabled(),
                
                Forms\Components\TextInput::make('buy_amount')
                    ->label('购买数量')
                    ->numeric()
                    ->disabled(),
                
                Forms\Components\TextInput::make('total_price')
                    ->label('总价')
                    ->numeric()
                    ->disabled(),
                
                Forms\Components\TextInput::make('actual_price')
                    ->label('实际价格')
                    ->numeric()
                    ->disabled(),
                
                Forms\Components\Textarea::make('info')
                    ->label('订单信息')
                    ->rows(5),
                
                Forms\Components\TextInput::make('buy_ip')
                    ->label('购买IP')
                    ->disabled(),
                
                Forms\Components\Select::make('pay_id')
                    ->label('支付方式')
                    ->options(Pay::pluck('pay_name', 'id'))
                    ->disabled(),
                
                Forms\Components\Select::make('status')
                    ->label('订单状态')
                    ->options(Order::getStatusMap())
                    ->required(),
                
                Forms\Components\TextInput::make('search_pwd')
                    ->label('查询密码')
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('trade_no')
                    ->label('交易号')
                    ->maxLength(255),
                
                Forms\Components\Select::make('type')
                    ->label('订单类型')
                    ->options(Order::getTypeMap())
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('order_sn')
                    ->label('订单号')
                    ->searchable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('标题')
                    ->searchable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('type')
                    ->label('类型')
                    ->formatStateUsing(fn ($state) => Order::getTypeMap()[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        Order::AUTOMATIC_DELIVERY => 'success',
                        Order::MANUAL_PROCESSING => 'warning',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('邮箱')
                    ->searchable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('goods.gd_name')
                    ->label('商品')
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
                
                Tables\Columns\TextColumn::make('goods_price')
                    ->label('商品价格')
                    ->money('CNY'),
                
                Tables\Columns\TextColumn::make('buy_amount')
                    ->label('数量'),
                
                Tables\Columns\TextColumn::make('actual_price')
                    ->label('实际价格')
                    ->money('CNY'),
                
                Tables\Columns\TextColumn::make('pay.pay_name')
                    ->label('支付方式'),
                
                Tables\Columns\TextColumn::make('buy_ip')
                    ->label('购买IP'),
                
                Tables\Columns\TextColumn::make('search_pwd')
                    ->label('查询密码')
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('trade_no')
                    ->label('交易号')
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('状态')
                    ->formatStateUsing(fn ($state) => Order::getStatusMap()[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        Order::STATUS_WAIT_PAY => 'warning',
                        Order::STATUS_PENDING => 'info',
                        Order::STATUS_PROCESSING => 'primary',
                        Order::STATUS_COMPLETED => 'success',
                        Order::STATUS_FAILURE, Order::STATUS_ABNORMAL, Order::STATUS_EXPIRED => 'danger',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新时间')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('订单状态')
                    ->options(Order::getStatusMap()),
                
                Tables\Filters\SelectFilter::make('type')
                    ->label('订单类型')
                    ->options(Order::getTypeMap()),
                
                Tables\Filters\SelectFilter::make('goods_id')
                    ->label('商品')
                    ->options(Goods::pluck('gd_name', 'id')),
                
                Tables\Filters\SelectFilter::make('pay_id')
                    ->label('支付方式')
                    ->options(Pay::pluck('pay_name', 'id')),
                
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('创建时间从'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('创建时间到'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListOrders::route('/'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}