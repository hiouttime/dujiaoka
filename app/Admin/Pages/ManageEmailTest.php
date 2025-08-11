<?php

namespace App\Admin\Pages;

use App\Settings\MailSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\MailServiceProvider;

class ManageEmailTest extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static string $view = 'filament.pages.manage-email-test';

    protected static ?string $navigationGroup = '邮件设置';
    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('email-test.labels.email-test');
    }

    public function getTitle(): string
    {
        return __('email-test.labels.email-test');
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'title' => '这是一条测试邮件',
            'body' => '这是一条测试邮件的正文内容<br/><br/>正文比较长<br/><br/>非常长<br/><br/>测试测试测试',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('email-test.labels.send_test_email'))
                    ->description('测试邮件发送功能，确保邮件配置正确')
                    ->schema([
                        Forms\Components\TextInput::make('to')
                            ->label(__('email-test.labels.to'))
                            ->email()
                            ->required()
                            ->helperText('请输入接收测试邮件的邮箱地址'),

                        Forms\Components\TextInput::make('title')
                            ->label(__('email-test.labels.title'))
                            ->required()
                            ->default('这是一条测试邮件'),

                        Forms\Components\RichEditor::make('body')
                            ->label(__('email-test.labels.body'))
                            ->required()
                            ->default('这是一条测试邮件的正文内容<br/><br/>正文比较长<br/><br/>非常长<br/><br/>测试测试测试')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function send(): void
    {
        try {
            $data = $this->form->getState();

            $to = $data['to'];
            $title = $data['title'];
            $body = $data['body'];

            // 获取邮件配置
            $mailSettings = app(MailSettings::class);

            // 临时覆盖邮件配置
            config([
                'mail.default' => $mailSettings->driver ?? 'smtp',
                'mail.mailers.smtp.transport' => 'smtp',
                'mail.mailers.smtp.host' => $mailSettings->host ?? '',
                'mail.mailers.smtp.port' => $mailSettings->port ?? 465,
                'mail.mailers.smtp.encryption' => $mailSettings->encryption ?? 'ssl',
                'mail.mailers.smtp.username' => $mailSettings->username ?? '',
                'mail.mailers.smtp.password' => $mailSettings->password ?? '',
                'mail.from.address' => $mailSettings->from_address ?? '',
                'mail.from.name' => $mailSettings->from_name ?? '独角发卡',
            ]);

            // 重新注册邮件服务
            (new MailServiceProvider(app()))->register();

            // 发送邮件
            Mail::send(['html' => 'email.mail'], ['body' => $body], function ($message) use ($to, $title) {
                $message->to($to)->subject($title);
            });

            Notification::make()
                ->title(__('email-test.labels.success'))
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('发送失败')
                ->body($e->getMessage())
                ->danger()
                ->send();
            
            throw new Halt();
        }
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('send')
                ->label(__('email-test.labels.send_test_email'))
                ->action('send')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary'),
        ];
    }
}