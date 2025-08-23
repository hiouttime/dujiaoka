<?php

namespace App\Admin\Resources\Roles\Pages;

use App\Admin\Resources\Roles;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateRole extends CreateRecord
{
    protected static string $resource = Roles::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('角色创建成功')
            ->body('新的角色已经创建。');
    }

}