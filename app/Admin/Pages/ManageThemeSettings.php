<?php

namespace App\Admin\Pages;

use App\Settings\ThemeSettings;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageThemeSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-swatch';
    
    protected static ?string $navigationLabel = '主题设置';
    
    protected static string $settings = ThemeSettings::class;
    
    protected static ?string $navigationGroup = '店铺设置';
    
    protected static ?int $navigationSort = 3;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('ThemeSettings')
                    ->tabs([
                        // 轮播文案设置
                        Tabs\Tab::make('轮播文案')
                            ->schema([
                                Section::make('顶部轮播文案设置')
                                    ->description('配置主题顶部的轮播公告文案，每行一个文案')
                                    ->schema([
                                        Textarea::make('notices')
                                            ->label('轮播文案')
                                            ->helperText('每行输入一个文案，将自动轮播显示')
                                            ->rows(6)
                                            ->placeholder('欢迎使用我们的服务！
限时优惠，立即购买享受折扣
24小时客服在线，随时为您服务
优质产品，值得信赖')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        
                        // 轮播图设置
                        Tabs\Tab::make('轮播图设置')
                            ->schema([
                                Section::make('首页轮播图配置')
                                    ->description('配置主题首页的轮播图片、文案和按钮')
                                    ->schema([
                                        Repeater::make('banners')
                                            ->label('轮播图')
                                            ->schema([
                                                Grid::make(2)
                                                    ->schema([
                                                        FileUpload::make('image')
                                                            ->label('轮播图片')
                                                            ->image()
                                                            ->disk('admin')
                                                            ->directory('images')
                                                            ->maxSize(2048)
                                                            ->required(),
                                                        
                                                        Grid::make(1)
                                                            ->schema([
                                                                TextInput::make('title')
                                                                    ->label('主标题')
                                                                    ->maxLength(100),
                                                                
                                                                TextInput::make('subtitle')
                                                                    ->label('副标题')
                                                                    ->maxLength(200),
                                                                
                                                                TextInput::make('button_text')
                                                                    ->label('按钮文字')
                                                                    ->maxLength(50),
                                                                
                                                                TextInput::make('button_url')
                                                                    ->label('按钮链接')
                                                                    ->url()
                                                                    ->maxLength(255),
                                                            ]),
                                                    ]),
                                                
                                                Toggle::make('target_blank')
                                                    ->label('新窗口打开链接')
                                                    ->default(false),
                                            ])
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? '轮播图')
                                            ->maxItems(5)
                                            ->defaultItems(1)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        
                        // 外观设置
                        Tabs\Tab::make('外观设置')
                            ->schema([
                                Section::make('Logo设置')
                                    ->schema([
                                        Toggle::make('invert_logo')
                                            ->label('Logo适配模式')
                                            ->helperText('开启后，在浅色模式下保持Logo原始亮度，深色模式下不应用滤镜。适用于深色Logo需要在不同主题下正确显示的场景')
                                            ->default(false),
                                    ]),
                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }
}