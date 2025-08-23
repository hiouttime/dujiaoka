<?php

namespace App\Admin\Resources;

use App\Admin\Resources\Products\Pages;
use App\Admin\Resources\Products\RelationManagers;
use App\Models\Goods;
use App\Models\GoodsGroup;
use App\Models\GoodsSub;
use App\Models\Pay;
use App\Models\RemoteServer;
use App\Models\Carmis;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class Products extends Resource
{
    protected static ?string $model = Goods::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = '商品管理';
    protected static ?string $modelLabel = '商品';
    protected static ?string $pluralModelLabel = '商品';
    protected static ?string $navigationGroup = '商店管理';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'gd_name';
    protected static int $globalSearchResultsLimit = 20;
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['gd_name', 'gd_description', 'gd_keywords'];
    }
    
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->gd_name;
    }
    
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $prices = $record->goods_sub->pluck('price');
        return [
            '分组' => $record->group?->gp_name,
            '价格' => $prices->isEmpty() ? '¥0' : '¥' . $prices->min(),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('基本信息')
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\FileUpload::make('picture')
                                    ->label('上传商品图片')
                                    ->image()
                                    ->directory('goods')
                                    ->visibility('public')
                                    ->maxFiles(1),
                                Forms\Components\TextInput::make('picture_url')
                                    ->label('使用远程商品图片URL')
                                    ->url()
                                    ->helperText('商品图片将会优先使用远程商品图片URL'),
                            ])
                            ->columnSpan(1),
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('gd_name')
                                    ->label('商品名称')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Select::make('group_id')
                                    ->label('商品分组')
                                    ->options(GoodsGroup::pluck('gp_name', 'id'))
                                    ->required(),
                                Forms\Components\TextInput::make('gd_description')
                                    ->label('商品描述')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('gd_keywords')
                                    ->label('商品关键词')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->columnSpan(1),
                    ])->columns(2),

                Forms\Components\Section::make('商品配置')
                    ->schema([
                        Forms\Components\ToggleButtons::make('type')
                            ->label('商品类型')
                            ->options(Goods::getGoodsTypeMap())
                            ->default(Goods::AUTOMATIC_DELIVERY)
                            ->required()
                            ->inline()
                            ->columnSpanFull(),
                        
                        Forms\Components\Repeater::make('goods_sub')
                            ->label('商品规格设置')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('规格名称')
                                    ->required()
                                    ->default('默认规格')
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('price')
                                    ->label('售价')
                                    ->numeric()
                                    ->prefix('¥')
                                    ->required()
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('stock')
                                    ->label('库存')
                                    ->numeric()
                                    ->default(0)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('sales_volume')
                                    ->label('销量')
                                    ->numeric()
                                    ->default(0)
                                    ->columnSpan(1),
                            ])
                            ->columns(5)
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->addActionLabel('添加规格')
                            ->minItems(1)
                            ->defaultItems(1)
                            ->columnSpanFull(),

                    ])
                    ->description('自动发货商品的库存由卡密数量决定'),

                Forms\Components\Section::make('购买限制')
                    ->schema([
                        Forms\Components\Select::make('payment_limit')
                            ->label('支付方式限制')
                            ->multiple()
                            ->options(Pay::where('enable', Pay::ENABLED)->pluck('pay_name', 'id'))
                            ->placeholder('选择允许的支付方式')
                            ->helperText('留空则不限制')
                            ->columnSpanFull(),
                        
                        Forms\Components\Toggle::make('require_login')
                            ->label('需要登录才能购买')
                            ->default(false)
                            ->columnSpanFull(),
                        
                        Forms\Components\Group::make([
                            Forms\Components\TextInput::make('buy_min_num')
                                ->label('最小下单数量')
                                ->numeric()
                                ->default(1)
                                ->minValue(1),
                            Forms\Components\TextInput::make('buy_limit_num')
                                ->label('最大下单数量')
                                ->numeric()
                                ->default(0)
                                ->helperText('如果商品需要登录才能购买，则最大数量为单用户最大购买数量；0为不限制'),
                        ])->columns(2),
                        
                        Forms\Components\TextInput::make('preselection')
                            ->label('预选加价')
                            ->numeric()
                            ->prefix('¥')
                            ->default(0)
                            ->helperText('自动发货商品预选卡密加价')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('商品详情')
                    ->schema([
                        Forms\Components\RichEditor::make('buy_prompt')
                            ->label('购买提示')
                            ->helperText('在访问商品页弹窗显示')
                            ->columnSpanFull(),
                        
                        Forms\Components\RichEditor::make('description')
                            ->label('商品详细')
                            ->columnSpanFull(),
                        
                        Forms\Components\RichEditor::make('usage_instructions')
                            ->label('使用说明')
                            ->helperText('购买后在卡密页提供的说明')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('高级配置')
                    ->schema([
                        Forms\Components\Repeater::make('customer_form_fields')
                            ->label('客户输入表单')
                            ->schema([
                                Forms\Components\TextInput::make('field_key')
                                    ->label('字段键')
                                    ->required(),
                                
                                Forms\Components\Select::make('field_type')
                                    ->label('字段类型')
                                    ->options([
                                        'input' => '输入框',
                                        'switch' => '开关'
                                    ])
                                    ->required(),
                                
                                Forms\Components\TextInput::make('field_description')
                                    ->label('字段说明')
                                    ->required(),
                            ])
                            ->columns(3)
                            ->addActionLabel('添加字段')
                            ->collapsible()
                            ->columnSpanFull()
                            ->defaultItems(0)
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                return $data;
                            })
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                return $data;
                            }),
                        
                        Forms\Components\Repeater::make('wholesale_prices')
                            ->label('批发价设置')
                            ->schema([
                                Forms\Components\TextInput::make('min_quantity')
                                    ->label('购买数量大于')
                                    ->numeric()
                                    ->suffix('件时')
                                    ->required(),
                                
                                Forms\Components\TextInput::make('unit_price')
                                    ->label('每件价格')
                                    ->numeric()
                                    ->prefix('¥')
                                    ->suffix('元')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->addActionLabel('添加批发价')
                            ->collapsible()
                            ->columnSpanFull()
                            ->defaultItems(0)
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                return $data;
                            })
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                return $data;
                            }),
                        
                        Forms\Components\Group::make([
                            Forms\Components\Select::make('api_hook')
                                ->label('回调服务器')
                                ->options(RemoteServer::pluck('name', 'id')),
                            
                            Forms\Components\TextInput::make('ord')
                                ->label('排序')
                                ->numeric()
                                ->default(1)
                                ->helperText('数字越小越靠前'),
                            
                            Forms\Components\Toggle::make('is_open')
                                ->label('是否显示')
                                ->default(true),
                        ])->columns(3),
                    ])->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                
                Tables\Columns\ImageColumn::make('picture')
                    ->label('商品图片')
                    ->size(60),
                
                Tables\Columns\TextColumn::make('gd_name')
                    ->label('商品名称')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Goods $record): string => '规格数量: ' . $record->goods_sub()->count() . ' 个'),
                
                Tables\Columns\TextColumn::make('group.gp_name')
                    ->label('商品分组')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('type')
                    ->label('商品类型')
                    ->formatStateUsing(fn (string $state): string => Goods::getGoodsTypeMap()[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        Goods::AUTOMATIC_DELIVERY => 'success',
                        Goods::MANUAL_PROCESSING => 'info',
                        Goods::AUTOMATIC_PROCESSING => 'warning',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('price')
                    ->label('价格')
                    ->money('CNY')
                    ->sortable()
                    ->formatStateUsing(function (Goods $record) {
                        $prices = $record->goods_sub()->pluck('price');
                        return $prices->isEmpty() ? '¥0.00' : '¥' . $prices->min();
                    }),
                
                Tables\Columns\TextColumn::make('stock')
                    ->label('库存')
                    ->formatStateUsing(function (Goods $record) {
                        return $record->type == Goods::AUTOMATIC_DELIVERY
                            ? Carmis::whereIn('sub_id', $record->goods_sub()->pluck('id'))->where('status', Carmis::STATUS_UNSOLD ?? 1)->count()
                            : $record->goods_sub()->sum('stock');
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('sales_volume')
                    ->label('销量')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('ord')
                    ->label('排序')
                    ->sortable(),
                
                Tables\Columns\ToggleColumn::make('is_open')
                    ->label('状态')
                    ->onColor('success')
                    ->offColor('gray'),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新时间')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('商品类型')
                    ->options(Goods::getGoodsTypeMap()),
                
                Tables\Filters\SelectFilter::make('group_id')
                    ->label('商品分组')
                    ->options(GoodsGroup::pluck('gp_name', 'id')),
                
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
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CarmisRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGoods::route('/'),
            'create' => Pages\CreateGoods::route('/create'),
            'edit' => Pages\EditGoods::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
    public static function canViewAny(): bool
    {
        return auth()->user()?->can('manage_products') || auth()->user()?->hasRole('super-admin') || false;
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
