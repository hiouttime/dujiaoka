<?php

namespace App\Admin\Resources;

use App\Models\UserLevel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ColorColumn;

class UserLevels extends Resource
{
    protected static ?string $model = UserLevel::class;
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationLabel = '用户等级';
    protected static ?string $modelLabel = '用户等级';
    protected static ?string $pluralModelLabel = '用户等级';
    protected static ?string $navigationGroup = '用户管理';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('等级名称')
                    ->required()
                    ->maxLength(50),
                
                TextInput::make('min_spent')
                    ->label('最低消费金额')
                    ->numeric()
                    ->step(0.01)
                    ->prefix('¥')
                    ->required()
                    ->helperText('用户累计消费达到此金额自动升级到该等级'),
                
                TextInput::make('discount_rate')
                    ->label('折扣率')
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0.01)
                    ->maxValue(1.00)
                    ->default(1.00)
                    ->required()
                    ->helperText('1.00表示无折扣，0.95表示95折'),
                
                ColorPicker::make('color')
                    ->label('等级颜色')
                    ->default('#6b7280')
                    ->helperText('用于前台显示的颜色标识'),
                
                TextInput::make('sort')
                    ->label('排序')
                    ->numeric()
                    ->default(0)
                    ->helperText('数字越小排序越靠前'),
                
                Textarea::make('description')
                    ->label('等级描述')
                    ->rows(3)
                    ->columnSpanFull(),
                
                Toggle::make('status')
                    ->label('启用状态')
                    ->default(true)
                    ->helperText('禁用后用户将无法升级到此等级'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort')
                    ->label('排序')
                    ->sortable(),
                
                TextColumn::make('name')
                    ->label('等级名称')
                    ->searchable()
                    ->sortable(),
                
                ColorColumn::make('color')
                    ->label('颜色'),
                
                TextColumn::make('min_spent')
                    ->label('最低消费')
                    ->money('CNY')
                    ->sortable(),
                
                TextColumn::make('discount_rate')
                    ->label('折扣率')
                    ->formatStateUsing(fn ($state) => $state . ' (' . round((1 - $state) * 100, 1) . '%折)')
                    ->sortable(),
                
                TextColumn::make('users_count')
                    ->label('用户数量')
                    ->counts('users')
                    ->sortable(),
                
                BadgeColumn::make('status')
                    ->label('状态')
                    ->getStateUsing(fn ($record) => $record->status_text)
                    ->colors([
                        'success' => '启用',
                        'secondary' => '禁用',
                    ]),
                
                TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('status')
                    ->label('状态')
                    ->boolean()
                    ->trueLabel('启用')
                    ->falseLabel('禁用')
                    ->native(false),
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
            ->defaultSort('sort')
            ->reorderable('sort');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Admin\Resources\UserLevels\Pages\ListUserLevels::route('/'),
            'create' => \App\Admin\Resources\UserLevels\Pages\CreateUserLevel::route('/create'),
            'edit' => \App\Admin\Resources\UserLevels\Pages\EditUserLevel::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('manage_user_levels') || auth()->user()?->hasRole('super-admin') || false;
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
