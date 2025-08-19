<?php

namespace App\Admin\Resources\Users\Pages;

use App\Admin\Resources\Users;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = Users::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}