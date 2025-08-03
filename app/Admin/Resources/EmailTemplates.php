<?php

namespace App\Admin\Resources;

use App\Admin\Resources\EmailTemplates\Pages;
use App\Admin\Resources\EmailTemplates\RelationManagers;
use App\Models\Emailtpl;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmailTemplates extends Resource
{
    protected static ?string $model = Emailtpl::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    
    protected static ?string $navigationLabel = '邮件模板';
    
    protected static ?string $modelLabel = '邮件模板';
    
    protected static ?string $pluralModelLabel = '邮件模板';
    
    protected static ?string $navigationGroup = '邮件设置';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('tpl_name')
                    ->label('模板名称')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('tpl_token')
                    ->label('模板标识')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->disabled(fn ($context) => $context === 'edit'),
                
                Forms\Components\RichEditor::make('tpl_content')
                    ->label('模板内容')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('tpl_name')
                    ->label('模板名称')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('tpl_token')
                    ->label('模板标识')
                    ->copyable(),
                
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // 邮件模板不允许删除
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailtpls::route('/'),
            'create' => Pages\CreateEmailtpl::route('/create'),
            'view' => Pages\ViewEmailtpl::route('/{record}'),
            'edit' => Pages\EditEmailtpl::route('/{record}/edit'),
        ];
    }
}
