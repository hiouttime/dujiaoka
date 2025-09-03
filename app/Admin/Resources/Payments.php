<?php

namespace App\Admin\Resources;

use App\Admin\Resources\Payments\Pages;
use App\Models\Pay;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class Payments extends Resource
{
    protected static ?string $model = Pay::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = '支付配置';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('pay.labels.pay');
    }

    public static function getModelLabel(): string
    {
        return __('pay.labels.pay');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('pay_name')
                    ->label(__('pay.fields.pay_name'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('pay_check')
                    ->label(__('pay.fields.pay_check'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('pay_fee')
                    ->label(__('pay.fields.pay_fee'))
                    ->numeric()
                    ->step(0.01)
                    ->default(0),

                Forms\Components\Select::make('pay_method')
                    ->label(__('pay.fields.pay_method'))
                    ->options(Pay::getMethodMap())
                    ->required(),

                Forms\Components\TextInput::make('merchant_id')
                    ->label(__('pay.fields.merchant_id'))
                    ->maxLength(255),

                Forms\Components\Select::make('pay_client')
                    ->label(__('pay.fields.pay_client'))
                    ->options(Pay::getClientMap())
                    ->required(),

                Forms\Components\TextInput::make('pay_handleroute')
                    ->label(__('pay.fields.pay_handleroute'))
                    ->maxLength(255),

                Forms\Components\Toggle::make('china_only')
                    ->label(__('pay.fields.china_only'))
                    ->default(false),

                Forms\Components\Toggle::make('enable')
                    ->label(__('pay.fields.enable'))
                    ->default(true),

                Forms\Components\Textarea::make('merchant_key')
                    ->label(__('pay.fields.merchant_key'))
                    ->rows(3),

                Forms\Components\Textarea::make('merchant_pem')
                    ->label(__('pay.fields.merchant_pem'))
                    ->rows(5),

                Forms\Components\Textarea::make('merchant_key_64')
                    ->label(__('pay.fields.merchant_key_64'))
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

                Tables\Columns\TextColumn::make('pay_name')
                    ->label(__('pay.fields.pay_name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('pay_check')
                    ->label(__('pay.fields.pay_check'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('pay_fee')
                    ->label(__('pay.fields.pay_fee'))
                    ->money('CNY'),

                Tables\Columns\TextColumn::make('pay_method')
                    ->label(__('pay.fields.pay_method'))
                    ->formatStateUsing(fn (string $state): string => Pay::getMethodMap()[$state] ?? $state),

                Tables\Columns\TextColumn::make('merchant_id')
                    ->label(__('pay.fields.merchant_id'))
                    ->limit(20),

                Tables\Columns\TextColumn::make('pay_client')
                    ->label(__('pay.fields.pay_client'))
                    ->formatStateUsing(fn (string $state): string => Pay::getClientMap()[$state] ?? $state),

                Tables\Columns\TextColumn::make('pay_handleroute')
                    ->label(__('pay.fields.pay_handleroute')),

                Tables\Columns\ToggleColumn::make('china_only')
                    ->label(__('pay.fields.china_only')),

                Tables\Columns\ToggleColumn::make('enable')
                    ->label(__('pay.fields.enable')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('pay_method')
                    ->label(__('pay.fields.pay_method'))
                    ->options(Pay::getMethodMap()),

                Tables\Filters\SelectFilter::make('pay_client')
                    ->label(__('pay.fields.pay_client'))
                    ->options(Pay::getClientMap()),

                Tables\Filters\TernaryFilter::make('china_only')
                    ->label(__('pay.fields.china_only')),

                Tables\Filters\TernaryFilter::make('enable')
                    ->label(__('pay.fields.enable')),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPays::route('/'),
            'create' => Pages\CreatePay::route('/create'),
            'edit' => Pages\EditPay::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
    {
        return auth('admin')->user()?->can('manage_payments') || auth('admin')->user()?->hasRole('super-admin') || false;
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
