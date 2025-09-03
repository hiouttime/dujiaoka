<?php

namespace App\Admin\Resources;

use App\Admin\Resources\Orders\Pages;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Goods;
use App\Models\Pay;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class Orders extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    
    protected static ?string $navigationLabel = '订单管理';
    
    protected static ?string $modelLabel = '订单';
    
    protected static ?string $pluralModelLabel = '订单';
    
    protected static ?string $navigationGroup = '商店管理';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('订单基本信息')
                    ->schema([
                        Forms\Components\TextInput::make('order_sn')
                            ->label('订单号')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('邮箱')
                            ->email()
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('total_price')
                            ->label('总价')
                            ->numeric()
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('actual_price')
                            ->label('实际价格')
                            ->numeric()
                            ->disabled(),
                        
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
                            ->maxLength(255)
                            ->default('')
                            ->visible(fn () => cfg('is_open_search_pwd', 0) == 1),
                        
                        Forms\Components\TextInput::make('trade_no')
                            ->label('交易号')
                            ->maxLength(255)
                            ->default(''),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('订单商品')
                    ->schema([
                        Forms\Components\Repeater::make('orderItems')
                            ->label('')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('goods_name')
                                    ->label('商品名称')
                                    ->disabled(),
                                Forms\Components\TextInput::make('unit_price')
                                    ->label('单价')
                                    ->numeric()
                                    ->disabled(),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('数量')
                                    ->numeric()
                                    ->disabled(),
                                Forms\Components\TextInput::make('subtotal')
                                    ->label('小计')
                                    ->numeric()
                                    ->disabled(),
                                Forms\Components\Textarea::make('info')
                                    ->label('商品信息/卡密')
                                    ->rows(3),
                            ])
                            ->columns(2)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false),
                    ]),
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
                
                Tables\Columns\TextColumn::make('goods_summary')
                    ->label('商品摘要')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (Order $record): string {
                        return $record->orderItems->map(function ($item) {
                            return $item->goods_name . ' x' . $item->quantity;
                        })->join("\n");
                    }),
                
                Tables\Columns\TextColumn::make('total_quantity')
                    ->label('商品数量')
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('邮箱')
                    ->searchable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('total_price')
                    ->label('总价')
                    ->money('USD')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('actual_price')
                    ->label('实际价格')
                    ->money('USD')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('pay.pay_name')
                    ->label('支付方式'),
                
                Tables\Columns\TextColumn::make('buy_ip')
                    ->label('购买IP')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('search_pwd')
                    ->label('查询密码')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn () => cfg('is_open_search_pwd', 0) == 1),
                
                Tables\Columns\TextColumn::make('trade_no')
                    ->label('交易号')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('订单状态')
                    ->options(Order::getStatusMap()),
                
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
            ->with(['orderItems', 'pay'])
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
    public static function canViewAny(): bool
    {
        return auth('admin')->user()?->can('manage_orders') || auth('admin')->user()?->can('view_orders') || auth('admin')->user()?->hasRole('super-admin') || false;
    }

    public static function canCreate(): bool
    {
        return auth('admin')->user()?->can('manage_orders') || auth('admin')->user()?->hasRole('super-admin') || false;
    }

    public static function canEdit($record): bool
    {
        return auth('admin')->user()?->can('manage_orders') || auth('admin')->user()?->hasRole('super-admin') || false;
    }

    public static function canDelete($record): bool
    {
        return auth('admin')->user()?->can('manage_orders') || auth('admin')->user()?->hasRole('super-admin') || false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::whereIn('status', [Order::STATUS_PENDING, Order::STATUS_PROCESSING])->count();
        return $count > 0 ? (string) $count : null;
    }
}
