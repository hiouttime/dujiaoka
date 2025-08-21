<?php

namespace App\Admin\Resources;

use App\Admin\Resources\ArticleCategoryResource\Pages;
use App\Admin\Resources\ArticleCategoryResource\RelationManagers;
use App\Models\ArticleCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ArticleCategoryResource extends Resource
{
    protected static ?string $model = ArticleCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';
    
    protected static ?string $navigationLabel = '文章分类';
    
    protected static ?string $modelLabel = '文章分类';
    
    protected static ?string $pluralModelLabel = '文章分类';
    
    protected static ?string $navigationGroup = '内容管理';
    
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('分类名称')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('slug')
                    ->label('分类标识')
                    ->helperText('分类的唯一标识，用于URL')
                    ->maxLength(100),
                Forms\Components\Textarea::make('description')
                    ->label('分类描述')
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('sort')
                    ->label('排序')
                    ->helperText('数值越大越靠前')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->label('是否启用')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('分类名称')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('分类标识')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('描述')
                    ->limit(50),
                Tables\Columns\TextColumn::make('sort')
                    ->label('排序')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('状态')
                    ->boolean(),
                Tables\Columns\TextColumn::make('articles_count')
                    ->label('文章数量')
                    ->counts('articles'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('状态')
                    ->placeholder('全部')
                    ->trueLabel('启用')
                    ->falseLabel('禁用'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListArticleCategories::route('/'),
            'create' => Pages\CreateArticleCategory::route('/create'),
            'view' => Pages\ViewArticleCategory::route('/{record}'),
            'edit' => Pages\EditArticleCategory::route('/{record}/edit'),
        ];
    }
}
