<?php

namespace App\Admin\Pages;

use App\Settings\MailSettings;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageMailSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    
    protected static ?string $navigationLabel = '发件设置';
    
    protected static string $settings = MailSettings::class;
    
    protected static ?string $navigationGroup = '邮件设置';
    
    protected static ?int $navigationSort = 1;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('SMTP配置')
                    ->description('配置邮件发送服务器信息')
                    ->schema([
                        Select::make('driver')
                            ->label('邮件驱动')
                            ->options([
                                'smtp' => 'SMTP',
                                'mail' => 'Mail',
                                'sendmail' => 'Sendmail'
                            ])
                            ->default('smtp')
                            ->required(),
                        
                        Grid::make(2)
                            ->schema([
                                TextInput::make('host')
                                    ->label('SMTP服务器')
                                    ->placeholder('smtp.example.com')
                                    ->required(),
                                
                                TextInput::make('port')
                                    ->label('SMTP端口')
                                    ->numeric()
                                    ->default(465)
                                    ->placeholder('465'),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                TextInput::make('username')
                                    ->label('SMTP用户名')
                                    ->placeholder('your-email@example.com')
                                    ->required(),
                                
                                TextInput::make('password')
                                    ->label('SMTP密码')
                                    ->password()
                                    ->placeholder('您的邮箱密码或应用专用密码')
                                    ->required(),
                            ]),
                        
                        Select::make('encryption')
                            ->label('加密方式')
                            ->options([
                                'ssl' => 'SSL',
                                'tls' => 'TLS',
                                null => '无加密'
                            ])
                            ->default('ssl')
                            ->helperText('通常使用SSL(端口465)或TLS(端口587)'),
                    ])
                    ->columns(2),
                
                Section::make('发件人信息')
                    ->description('配置邮件发送者信息')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('from_address')
                                    ->label('发件人邮箱')
                                    ->email()
                                    ->placeholder('noreply@example.com')
                                    ->required()
                                    ->helperText('必须与SMTP用户名匹配'),
                                
                                TextInput::make('from_name')
                                    ->label('发件人名称')
                                    ->default('独角发卡')
                                    ->placeholder('独角发卡')
                                    ->required(),
                            ]),
                    ])
                    ->columns(1),
                
                Section::make('管理员配置')
                    ->description('配置系统管理员邮箱')
                    ->schema([
                        TextInput::make('manage_email')
                            ->label('管理员邮箱')
                            ->email()
                            ->placeholder('admin@example.com')
                            ->helperText('接收系统通知和订单提醒的管理员邮箱'),
                    ])
                    ->columns(1),
            ]);
    }
}