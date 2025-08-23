<?php

namespace App\Admin\Resources\AdminUsers\Pages;

use App\Admin\Resources\AdminUsers;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdminUsers extends ListRecords
{
    protected static string $resource = AdminUsers::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}