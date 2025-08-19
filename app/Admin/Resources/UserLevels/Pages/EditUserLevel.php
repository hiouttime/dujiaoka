<?php

namespace App\Admin\Resources\UserLevels\Pages;

use App\Admin\Resources\UserLevels;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserLevel extends EditRecord
{
    protected static string $resource = UserLevels::class;

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
}