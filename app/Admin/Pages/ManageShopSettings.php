<?php

namespace App\Admin\Pages;

use App\Settings\ShopSettings;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageShopSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';
    
    protected static ?string $navigationLabel = '店铺装修';
    
    protected static string $settings = ShopSettings::class;
    
    protected static ?string $navigationGroup = '店铺设置';
    
    protected static ?int $navigationSort = 2;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('ShopDecorationSettings')
                    ->tabs([
                        // 基础设置
                        Tabs\Tab::make('基础设置')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('网站标题')
                                            ->required()
                                            ->default('独角数卡')
                                            ->maxLength(255),
                                        
                                        TextInput::make('text_logo')
                                            ->label('文字Logo')
                                            ->maxLength(255),
                                    ]),
                                
                                FileUpload::make('img_logo')
                                    ->label('图片Logo')
                                    ->image()
                                    ->directory('logos')
                                    ->maxSize(1024),
                                
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('keywords')
                                            ->label('关键词')
                                            ->maxLength(255)
                                            ->columnSpan(2),
                                    ]),
                                
                                Textarea::make('description')
                                    ->label('网站描述')
                                    ->rows(3),
                                
                                Grid::make(3)
                                    ->schema([
                                        Select::make('template')
                                            ->label('模板')
                                            ->options(config('dujiaoka.templates', []))
                                            ->default('morpho')
                                            ->required(),
                                        
                                        Select::make('language')
                                            ->label('语言')
                                            ->options(config('dujiaoka.language', []))
                                            ->default('zh_CN')
                                            ->required(),
                                        
                                        Select::make('currency')
                                            ->label('货币')
                                            ->options(config('dujiaoka.currencies', []))
                                            ->default('cny')
                                            ->required(),
                                    ]),
                                
                                Grid::make(2)
                                    ->schema([
                                        Toggle::make('is_open_anti_red')
                                            ->label('开启防红')
                                            ->default(false),
                                        
                                        Toggle::make('is_cn_challenge')
                                            ->label('中国大陆验证')
                                            ->default(true),
                                        
                                        Toggle::make('is_open_search_pwd')
                                            ->label('开启查询密码')
                                            ->default(false),
                                        
                                        Toggle::make('is_open_google_translate')
                                            ->label('开启谷歌翻译')
                                            ->default(false),
                                    ]),
                                
                                RichEditor::make('notice')
                                    ->label('公告')
                                    ->columnSpanFull(),
                                
                                Textarea::make('footer')
                                    ->label('页脚')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),
                        
                        // 导航栏设置
                        Tabs\Tab::make('导航栏设置')
                            ->schema([
                                Section::make('导航栏配置')
                                    ->description('配置网站顶部导航栏菜单项，支持二级菜单')
                                    ->schema([
                                        Repeater::make('nav_items')
                                            ->label('导航菜单')
                                            ->default([
                                                [
                                                    'name' => '主页',
                                                    'url' => '/',
                                                    'target_blank' => false,
                                                    'children' => []
                                                ]
                                            ])
                                            ->schema([
                                                Grid::make(3)
                                                    ->schema([
                                                        TextInput::make('name')
                                                            ->label('菜单名称')
                                                            ->required()
                                                            ->maxLength(50),
                                                        
                                                        TextInput::make('url')
                                                            ->label('链接地址')
                                                            ->maxLength(255)
                                                            ->default('#'),
                                                        
                                                        Toggle::make('target_blank')
                                                            ->label('新窗口打开')
                                                            ->default(false),
                                                    ]),
                                                
                                                Repeater::make('children')
                                                    ->label('子菜单')
                                                    ->schema([
                                                        Grid::make(3)
                                                            ->schema([
                                                                TextInput::make('name')
                                                                    ->label('菜单名称')
                                                                    ->maxLength(50),
                                                                
                                                                TextInput::make('url')
                                                                    ->label('链接地址')
                                                                    ->maxLength(255),
                                                                
                                                                Toggle::make('target_blank')
                                                                    ->label('新窗口打开')
                                                                    ->default(false),
                                                            ]),
                                                    ])
                                                    ->collapsible()
                                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                                    ->maxItems(5)
                                                    ->columnSpanFull(),
                                            ])
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                            ->maxItems(10)
                                            ->defaultItems(4)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }
}