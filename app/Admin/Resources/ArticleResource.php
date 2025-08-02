<?php

namespace App\Admin\Resources;

use App\Admin\Resources\ArticleResource\Pages;
use App\Admin\Resources\ArticleResource\RelationManagers;
use App\Models\Articles;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ArticleResource extends Resource
{
    protected static ?string $model = Articles::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationLabel = '文章管理';
    
    protected static ?string $modelLabel = '文章';
    
    protected static ?string $pluralModelLabel = '文章';
    
    protected static ?string $navigationGroup = '内容管理';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('标题')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('link')
                    ->label('链接')
                    ->helperText('文章访问链接，留空将自动生成')
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('category')
                    ->label('分类')
                    ->datalist(function () {
                        return Articles::query()
                            ->select('category')->distinct()
                            ->pluck('category')
                            ->filter()->values()->toArray();
                    }),
                
                Forms\Components\RichEditor::make('content')
                    ->label('内容')
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
                
                Tables\Columns\TextColumn::make('title')
                    ->label('标题')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('link')
                    ->label('链接')
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('category')
                    ->label('分类')
                    ->badge(),
                
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'view' => Pages\ViewArticle::route('/{record}'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
