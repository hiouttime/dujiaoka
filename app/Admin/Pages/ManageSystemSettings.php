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
    
    protected static ?string $navigationLabel = '店铺设置';
    
    protected static string $settings = SystemSettings::class;
    
    protected static ?string $navigationGroup = '店铺设置';
    
    protected static ?int $navigationSort = 1;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('SystemSettings')
                    ->tabs([
                        
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
                                
                                Grid::make(2)
                                    ->schema([
                                        Toggle::make('is_open_img_code')
                                            ->label('开启图片验证码')
                                            ->default(false),
                                        
                                        Select::make('contact_required')
                                            ->label('下单信息要求')
                                            ->options([
                                                'email' => '必须填写邮箱',
                                                'any' => '任意6位以上字符串',
                                                'null' => '无需填写',
                                            ])
                                            ->default('email')
                                            ->required(),
                                    ]),
                                
                                Select::make('stock_mode')
                                    ->label('下单库存模式')
                                    ->options([
                                        1 => '下单即减库存',
                                        2 => '发货时减库存',
                                    ])
                                    ->default(2)
                                    ->required()
                                    ->helperText('下单即减库存适合秒杀商品场景，避免高并发下单时造成部分订单无法兑现，但可能存在被恶意占用库存的情况，建议设置商品下单需登录使用。库存会在订单过期后恢复。')
                                    ->columnSpanFull(),
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