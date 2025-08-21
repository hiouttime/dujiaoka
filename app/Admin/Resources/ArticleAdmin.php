<?php

namespace App\Admin\Resources;

use App\Admin\Resources\ArticleAdmin\Pages;
use App\Admin\Resources\ArticleAdmin\RelationManagers;
use App\Models\Articles;
use App\Models\ArticleCategory;
use App\Models\Goods;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ArticleAdmin extends Resource
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
                Forms\Components\Section::make('基本信息')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('标题')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('link')
                            ->label('链接')
                            ->helperText('文章访问链接，留空将自动生成')
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('category_id')
                            ->label('文章分类')
                            ->relationship('category', 'name')
                            ->options(ArticleCategory::active()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('分类名称')
                                    ->required(),
                                Forms\Components\TextInput::make('slug')
                                    ->label('分类标识'),
                                Forms\Components\Textarea::make('description')
                                    ->label('分类描述')
                                    ->rows(2),
                                Forms\Components\TextInput::make('sort')
                                    ->label('排序')
                                    ->numeric()
                                    ->default(0),
                            ]),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('文章内容')
                    ->schema([
                        Forms\Components\RichEditor::make('content')
                            ->label('内容')
                            ->required()
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('关联商品')
                    ->schema([
                        Forms\Components\CheckboxList::make('goods')
                            ->relationship('goods', 'gd_name')
                            ->options(Goods::where('is_open', true)->pluck('gd_name', 'id'))
                            ->searchable()
                            ->columns(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
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
                    ->searchable()
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('category.name')
                    ->label('分类')
                    ->badge()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('link')
                    ->label('链接')
                    ->copyable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('goods_count')
                    ->label('关联商品')
                    ->counts('goods'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('m-d H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新时间')
                    ->dateTime('m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('分类')
                    ->relationship('category', 'name')
                    ->preload(),
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
