<?php

namespace App\Admin\Resources;

use App\Admin\Resources\GoodsResource\Pages;
use App\Admin\Resources\GoodsResource\RelationManagers;
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

class GoodsResource extends Resource
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
        return [
            '分组' => $record->group?->gp_name,
            '价格' => '¥' . $record->price,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('基本信息')
                    ->schema([
                        Forms\Components\TextInput::make('gd_name')
                            ->label('商品名称')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('gd_description')
                            ->label('商品描述')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('gd_keywords')
                            ->label('商品关键词')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('group_id')
                            ->label('商品分组')
                            ->options(GoodsGroup::pluck('gp_name', 'id'))
                            ->required(),
                    ])->columns(2),
                
                Forms\Components\Section::make('商品图片')
                    ->schema([
                        Forms\Components\FileUpload::make('picture')
                            ->label('商品图片')
                            ->image()
                            ->directory('goods')
                            ->visibility('public'),
                        
                        Forms\Components\TextInput::make('picture_url')
                            ->label('图片URL')
                            ->url()
                            ->helperText('如果设置了图片URL，将覆盖上传的图片'),
                    ])->columns(2),
                
                Forms\Components\Section::make('商品配置')
                    ->schema([
                        Forms\Components\Radio::make('type')
                            ->label('商品类型')
                            ->options(Goods::getGoodsTypeMap())
                            ->default(Goods::AUTOMATIC_DELIVERY)
                            ->required(),
                        
                        Forms\Components\Radio::make('is_sub')
                            ->label('是否为子规格商品')
                            ->options([
                                Goods::STATUS_CLOSE => '否',
                                Goods::STATUS_OPEN => '是'
                            ])
                            ->default(Goods::STATUS_CLOSE)
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                if ($state == Goods::STATUS_OPEN) {
                                    $set('price', 0);
                                    $set('stock', 0);
                                    $set('sales_volume', 0);
                                }
                            }),
                        
                        Forms\Components\TextInput::make('price')
                            ->label('商品价格')
                            ->numeric()
                            ->prefix('¥')
                            ->default(0)
                            ->visible(fn (Forms\Get $get) => $get('is_sub') == Goods::STATUS_CLOSE),
                        
                        Forms\Components\TextInput::make('stock')
                            ->label('库存')
                            ->numeric()
                            ->helperText('自动发货商品的库存由卡密数量决定')
                            ->visible(fn (Forms\Get $get) => $get('is_sub') == Goods::STATUS_CLOSE),
                        
                        Forms\Components\TextInput::make('sales_volume')
                            ->label('销量')
                            ->numeric()
                            ->default(0)
                            ->visible(fn (Forms\Get $get) => $get('is_sub') == Goods::STATUS_CLOSE),
                    ])->columns(3),
                
                Forms\Components\Section::make('购买限制')
                    ->schema([
                        Forms\Components\Select::make('payment_limit')
                            ->label('支付方式限制')
                            ->multiple()
                            ->options(Pay::where('is_open', Pay::STATUS_OPEN)->pluck('pay_name', 'id'))
                            ->helperText('留空则不限制支付方式'),
                        
                        Forms\Components\TextInput::make('buy_limit_num')
                            ->label('购买限制数量')
                            ->numeric()
                            ->helperText('每个用户最多购买数量，0为不限制'),
                        
                        Forms\Components\TextInput::make('preselection')
                            ->label('预选价格')
                            ->numeric()
                            ->prefix('¥')
                            ->default(0)
                            ->helperText('预选购买时的默认价格'),
                    ])->columns(3),
                
                Forms\Components\Section::make('商品详情')
                    ->schema([
                        Forms\Components\RichEditor::make('buy_prompt')
                            ->label('购买提示')
                            ->columnSpanFull(),
                        
                        Forms\Components\RichEditor::make('description')
                            ->label('商品详情')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('商品规格管理')
                    ->schema([
                        Forms\Components\Repeater::make('goods_sub')
                            ->label('商品规格')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('规格名称')
                                    ->required()
                                    ->columnSpan(2),
                                
                                Forms\Components\TextInput::make('price')
                                    ->label('规格价格')
                                    ->numeric()
                                    ->prefix('¥')
                                    ->required()
                                    ->columnSpan(1),
                                
                                Forms\Components\TextInput::make('stock')
                                    ->label('规格库存')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('自动发货商品的库存由对应卡密数量决定')
                                    ->columnSpan(1),
                                
                                Forms\Components\TextInput::make('sales_volume')
                                    ->label('销量')
                                    ->numeric()
                                    ->default(0)
                                    ->columnSpan(1),
                            ])
                            ->columns(5)
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->addActionLabel('添加规格')
                            ->reorderableWithButtons()
                            ->collapsible()
                    ])
                    ->visible(fn (Forms\Get $get) => $get('is_sub') == Goods::STATUS_OPEN)
                    ->collapsed(),
                
                Forms\Components\Section::make('高级配置')
                    ->schema([
                        Forms\Components\Textarea::make('other_ipu_cnf')
                            ->label('其他输入配置')
                            ->helperText('JSON格式的其他输入配置'),
                        
                        Forms\Components\Textarea::make('wholesale_price_cnf')
                            ->label('批发价格配置')
                            ->helperText('JSON格式的批发价格配置'),
                        
                        Forms\Components\Select::make('api_hook')
                            ->label('API钩子')
                            ->options(RemoteServer::pluck('name', 'id'))
                            ->helperText('关联的远程服务器'),
                        
                        Forms\Components\TextInput::make('ord')
                            ->label('排序')
                            ->numeric()
                            ->default(1)
                            ->helperText('数字越小越靠前'),
                        
                        Forms\Components\Toggle::make('is_open')
                            ->label('是否启用')
                            ->default(true),
                    ])->columns(2),
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
                    ->description(fn (Goods $record): string => 
                        $record->is_sub == Goods::STATUS_OPEN 
                            ? '多规格商品 (' . $record->goods_sub()->count() . ' 个规格)' 
                            : ''
                    ),
                
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
                        if ($record->is_sub == Goods::STATUS_OPEN) {
                            $prices = $record->goods_sub()->pluck('price');
                            if ($prices->isEmpty()) return '¥0.00';
                            $min = $prices->min();
                            $max = $prices->max();
                            return $min == $max ? "¥{$min}" : "¥{$min} - ¥{$max}";
                        }
                        return "¥{$record->price}";
                    }),
                
                Tables\Columns\TextColumn::make('stock')
                    ->label('库存')
                    ->formatStateUsing(function (Goods $record) {
                        if ($record->is_sub == Goods::STATUS_OPEN) {
                            // 多规格商品显示总库存
                            if ($record->type == Goods::AUTOMATIC_DELIVERY) {
                                return Carmis::whereIn('sub_id', $record->goods_sub()->pluck('id'))
                                    ->where('status', Carmis::STATUS_UNSOLD ?? 1)
                                    ->count();
                            } else {
                                return $record->goods_sub()->sum('stock');
                            }
                        }
                        
                        // 单规格商品
                        if ($record->type == Goods::AUTOMATIC_DELIVERY) {
                            return Carmis::where('goods_id', $record->id)
                                ->where('status', Carmis::STATUS_UNSOLD ?? 1)
                                ->count();
                        }
                        return $record->in_stock ?? $record->stock;
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
}
