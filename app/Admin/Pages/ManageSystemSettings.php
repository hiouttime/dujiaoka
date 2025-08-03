<?php

namespace App\Admin\Pages;

use App\Settings\SystemSettings;
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
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageSystemSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    
    protected static ?string $navigationLabel = '系统设置';
    
    protected static string $settings = SystemSettings::class;
    
    protected static ?string $navigationGroup = '系统设置';
    
    protected static ?int $navigationSort = 1;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('SystemSettings')
                    ->tabs([
                        // 基础设置
                        Tabs\Tab::make('基础设置')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('网站标题')
                                            ->required()
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
                                            ->required(),
                                        
                                        Select::make('language')
                                            ->label('语言')
                                            ->options(config('dujiaoka.language', []))
                                            ->required(),
                                        
                                        Select::make('currency')
                                            ->label('货币')
                                            ->options(config('dujiaoka.currencies', []))
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
                        
                        // 订单设置
                        Tabs\Tab::make('订单设置')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('order_expire_time')
                                            ->label('订单过期时间(分钟)')
                                            ->numeric()
                                            ->default(5)
                                            ->required(),
                                        
                                        TextInput::make('order_ip_limits')
                                            ->label('IP限制')
                                            ->numeric()
                                            ->default(1)
                                            ->required(),
                                    ]),
                                
                                Toggle::make('is_open_img_code')
                                    ->label('开启图片验证码')
                                    ->default(false),
                            ]),
                        
                        // 推送设置
                        Tabs\Tab::make('推送设置')
                            ->schema([
                                Section::make('Server酱推送')
                                    ->schema([
                                        Toggle::make('is_open_server_jiang')
                                            ->label('开启Server酱推送')
                                            ->default(false),
                                        
                                        TextInput::make('server_jiang_token')
                                            ->label('Server酱Token')
                                            ->maxLength(255),
                                    ]),
                                
                                Section::make('Telegram推送')
                                    ->schema([
                                        Toggle::make('is_open_telegram_push')
                                            ->label('开启Telegram推送')
                                            ->default(false),
                                        
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('telegram_bot_token')
                                                    ->label('Telegram Bot Token')
                                                    ->maxLength(255),
                                                
                                                TextInput::make('telegram_userid')
                                                    ->label('Telegram User ID')
                                                    ->maxLength(255),
                                            ]),
                                    ]),
                                
                                Section::make('Bark推送')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Toggle::make('is_open_bark_push')
                                                    ->label('开启Bark推送')
                                                    ->default(false),
                                                
                                                Toggle::make('is_open_bark_push_url')
                                                    ->label('Bark推送URL')
                                                    ->default(false),
                                            ]),
                                        
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('bark_server')
                                                    ->label('Bark服务器')
                                                    ->maxLength(255),
                                                
                                                TextInput::make('bark_token')
                                                    ->label('Bark Token')
                                                    ->maxLength(255),
                                            ]),
                                    ]),
                                
                                Section::make('企业微信推送')
                                    ->schema([
                                        Toggle::make('is_open_qywxbot_push')
                                            ->label('开启企业微信推送')
                                            ->default(false),
                                        
                                        TextInput::make('qywxbot_key')
                                            ->label('企业微信Key')
                                            ->maxLength(255),
                                    ]),
                            ]),
                        
                        // 验证码设置
                        Tabs\Tab::make('验证码设置')
                            ->schema([
                                Toggle::make('is_open_geetest')
                                    ->label('开启极验验证码')
                                    ->default(false),
                                
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('geetest_id')
                                            ->label('极验ID')
                                            ->maxLength(255),
                                        
                                        TextInput::make('geetest_key')
                                            ->label('极验Key')
                                            ->maxLength(255),
                                    ]),
                            ]),
                        
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }
}