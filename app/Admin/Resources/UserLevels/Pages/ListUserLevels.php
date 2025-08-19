<?php

namespace App\Admin\Resources\UserLevels\Pages;

use App\Admin\Resources\UserLevels;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserLevels extends ListRecords
{
    protected static string $resource = UserLevels::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}