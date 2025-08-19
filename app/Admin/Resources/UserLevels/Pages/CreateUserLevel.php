<?php

namespace App\Admin\Resources\UserLevels\Pages;

use App\Admin\Resources\UserLevels;
use Filament\Resources\Pages\CreateRecord;

class CreateUserLevel extends CreateRecord
{
    protected static string $resource = UserLevels::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}