<?php

namespace App\Admin\Resources\AdminUsers\Pages;

use App\Admin\Resources\AdminUsers;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateAdminUser extends CreateRecord
{
    protected static string $resource = AdminUsers::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('管理员创建成功')
            ->body('新的管理员账户已经创建。');
    }
}