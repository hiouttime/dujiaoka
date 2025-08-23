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
    
    protected static ?string $title = '店铺设置';

    public function getTitle(): string 
    {
        return '店铺设置';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('SystemSettings')
                    ->tabs([
                        
                        // 订单设置
                        Tabs\Tab::make('order-settings')
                            ->label('订单设置')
                            ->schema([
                                Section::make('订单基础设置')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('order_expire_time')
                                                    ->label('订单过期时间(分钟)')
                                                    ->numeric()
                                                    ->default(5)
                                                    ->required()
                                                    ->helperText('订单未支付情况下的自动过期时间'),
                                                
                                                TextInput::make('order_ip_limits')
                                                    ->label('单IP限制下单数量')
                                                    ->numeric()
                                                    ->default(1)
                                                    ->required()
                                                    ->helperText('限制同一IP地址可下单数量，防止恶意下单'),
                                            ]),
                                    ]),
                                
                                Section::make('下单验证设置')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Toggle::make('is_open_img_code')
                                                    ->label('开启图片验证码')
                                                    ->default(false)
                                                    ->helperText('在下单页面显示图片验证码'),
                                                
                                                Select::make('contact_required')
                                                    ->label('下单联系方式要求')
                                                    ->options([
                                                        'email' => '必须填写邮箱',
                                                        'any' => '任意6位以上字符串',
                                                        'null' => '无需填写',
                                                    ])
                                                    ->default('email')
                                                    ->required()
                                                    ->helperText('设置用户下单时的联系方式要求'),
                                            ]),
                                    ]),
                                
                                Section::make('库存管理')
                                    ->schema([
                                        Select::make('stock_mode')
                                            ->label('库存扣减模式')
                                            ->options([
                                                1 => '下单即减库存',
                                                2 => '发货时减库存',
                                            ])
                                            ->default(2)
                                            ->required()
                                            ->helperText('下单即减库存：适合秒杀场景，避免超卖但可能被恶意占用；发货时减库存：用户体验更好但可能出现超卖')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        
                        // 推送设置
                        Tabs\Tab::make('notification-settings')
                            ->label('推送设置')
                            ->schema([
                                Section::make('Server酱推送')
                                    ->description('通过Server酱推送订单消息到微信')
                                    ->schema([
                                        Toggle::make('is_open_server_jiang')
                                            ->label('开启Server酱推送')
                                            ->default(false),
                                        
                                        TextInput::make('server_jiang_token')
                                            ->label('Server酱Token')
                                            ->maxLength(255)
                                            ->placeholder('请输入Server酱的SendKey'),
                                    ])->columns(2),
                                
                                Section::make('Telegram推送')
                                    ->description('通过Telegram Bot推送订单消息')
                                    ->schema([
                                        Toggle::make('is_open_telegram_push')
                                            ->label('开启Telegram推送')
                                            ->default(false)
                                            ->columnSpanFull(),
                                        
                                        TextInput::make('telegram_bot_token')
                                            ->label('Telegram Bot Token')
                                            ->maxLength(255)
                                            ->placeholder('请输入Bot Token'),
                                        
                                        TextInput::make('telegram_userid')
                                            ->label('Telegram User ID')
                                            ->maxLength(255)
                                            ->placeholder('请输入接收消息的用户ID'),
                                    ])->columns(2),
                                
                                Section::make('Bark推送')
                                    ->description('通过Bark推送订单消息到iOS设备')
                                    ->schema([
                                        Toggle::make('is_open_bark_push')
                                            ->label('开启Bark推送')
                                            ->default(false),
                                        
                                        Toggle::make('is_open_bark_push_url')
                                            ->label('推送URL链接')
                                            ->default(false)
                                            ->helperText('是否在推送消息中包含订单URL'),
                                        
                                        TextInput::make('bark_server')
                                            ->label('Bark服务器地址')
                                            ->maxLength(255)
                                            ->placeholder('https://api.day.app'),
                                        
                                        TextInput::make('bark_token')
                                            ->label('Bark Device Token')
                                            ->maxLength(255)
                                            ->placeholder('请输入设备Token'),
                                    ])->columns(2),
                                
                                Section::make('企业微信推送')
                                    ->description('通过企业微信群机器人推送订单消息')
                                    ->schema([
                                        Toggle::make('is_open_qywxbot_push')
                                            ->label('开启企业微信推送')
                                            ->default(false),
                                        
                                        TextInput::make('qywxbot_key')
                                            ->label('企业微信Webhook Key')
                                            ->maxLength(255)
                                            ->placeholder('请输入群机器人的Webhook Key'),
                                    ])->columns(2),
                            ]),
                        
                        // 验证码设置
                        Tabs\Tab::make('captcha-settings')
                            ->label('验证码设置')
                            ->schema([
                                Section::make('极验验证码')
                                    ->description('集成极验行为验证码，提供更好的安全防护')
                                    ->schema([
                                        Toggle::make('is_open_geetest')
                                            ->label('开启极验验证码')
                                            ->default(false)
                                            ->helperText('开启后将在下单页面显示极验行为验证码'),
                                        
                                        TextInput::make('geetest_id')
                                            ->label('极验ID')
                                            ->maxLength(255)
                                            ->placeholder('请输入极验验证码的ID'),
                                        
                                        TextInput::make('geetest_key')
                                            ->label('极验Key')
                                            ->maxLength(255)
                                            ->placeholder('请输入极验验证码的Key'),
                                    ])->columns(2),
                            ]),
                        
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }
}