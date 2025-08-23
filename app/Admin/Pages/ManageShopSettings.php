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

    public function getTitle(): string 
    {
        return '店铺装修';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('ShopDecorationSettings')
                    ->tabs([
                        // 基础设置  
                        Tabs\Tab::make('basic-settings')
                            ->label('基础设置')
                            ->schema([
                                Section::make('网站标识')
                                    ->description('设置网站的Logo和标题，将在网站头部显示')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                FileUpload::make('img_logo')
                                                    ->label('图片Logo')
                                                    ->image()
                                                    ->directory('logos')
                                                    ->maxSize(1024)
                                                    ->imagePreviewHeight('80')
                                                    ->helperText('推荐尺寸：200x80像素，支持PNG/JPG格式'),
                                                
                                                Grid::make(1)
                                                    ->schema([
                                                        TextInput::make('title')
                                                            ->label('网站标题')
                                                            ->required()
                                                            ->default('独角数卡')
                                                            ->maxLength(255)
                                                            ->helperText('显示在网站标题栏和搜索结果中'),
                                                        
                                                        TextInput::make('text_logo')
                                                            ->label('文字Logo')
                                                            ->maxLength(255)
                                                            ->helperText('当图片Logo不可用时的备用文字'),
                                                    ])
                                                    ->columnSpan(2),
                                            ]),
                                    ]),
                                
                                Section::make('SEO设置')
                                    ->description('优化搜索引擎展示效果')
                                    ->schema([
                                        TextInput::make('keywords')
                                            ->label('关键词')
                                            ->maxLength(255)
                                            ->helperText('用逗号分隔多个关键词，有助于SEO优化')
                                            ->placeholder('例如：数字卡密,虚拟商品,在线购买'),
                                        
                                        Textarea::make('description')
                                            ->label('网站描述')
                                            ->rows(3)
                                            ->helperText('网站简介，会显示在搜索引擎结果中')
                                            ->placeholder('请简洁描述您的网站...'),
                                    ]),
                                
                                Section::make('基本配置')
                                    ->description('网站运行的基础参数设置')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Select::make('template')
                                                    ->label('网站模板')
                                                    ->options(config('dujiaoka.templates', []))
                                                    ->default('morpho')
                                                    ->required()
                                                    ->helperText('选择网站前端显示模板'),
                                                
                                                Select::make('language')
                                                    ->label('默认语言')
                                                    ->options(config('dujiaoka.language', []))
                                                    ->default('zh_CN')
                                                    ->required()
                                                    ->helperText('网站默认显示语言'),
                                                
                                                Select::make('currency')
                                                    ->label('货币单位')
                                                    ->options(config('dujiaoka.currencies', []))
                                                    ->default('cny')
                                                    ->required()
                                                    ->helperText('商品价格显示的货币单位'),
                                            ]),
                                    ]),
                                
                                Section::make('功能开关')
                                    ->description('控制网站的各种功能特性')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Toggle::make('is_open_anti_red')
                                                    ->label('开启防红功能')
                                                    ->default(false)
                                                    ->helperText('启用微信防红页面跳转'),
                                                
                                                Toggle::make('is_cn_challenge')
                                                    ->label('中国大陆验证')
                                                    ->default(true)
                                                    ->helperText('验证访客是否来自中国大陆'),
                                                
                                                Toggle::make('is_open_search_pwd')
                                                    ->label('开启查询密码')
                                                    ->default(false)
                                                    ->helperText('用户查询订单时需要输入密码'),
                                                
                                                Toggle::make('is_open_google_translate')
                                                    ->label('开启谷歌翻译')
                                                    ->default(false)
                                                    ->helperText('在网站中集成谷歌翻译功能'),
                                            ]),
                                    ]),
                                
                                Section::make('页面内容')
                                    ->description('设置网站的公告和页脚信息')
                                    ->schema([
                                        RichEditor::make('notice')
                                            ->label('网站公告')
                                            ->columnSpanFull()
                                            ->helperText('显示在网站首页的重要公告信息，支持HTML格式'),
                                        
                                        Textarea::make('footer')
                                            ->label('页脚自定义代码')
                                            ->rows(3)
                                            ->columnSpanFull()
                                            ->helperText('可用于添加统计代码、客服系统等')
                                    ]),
                            ]),
                        
                        // 导航栏设置
                        Tabs\Tab::make('navigation-settings')
                            ->label('导航栏设置')
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