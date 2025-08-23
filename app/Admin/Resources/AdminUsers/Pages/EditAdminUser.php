<?php

namespace App\Admin\Resources\AdminUsers\Pages;

use App\Admin\Resources\AdminUsers;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditAdminUser extends EditRecord
{
    protected static string $resource = AdminUsers::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('管理员更新成功')
            ->body('管理员信息已经更新。');
    }
}