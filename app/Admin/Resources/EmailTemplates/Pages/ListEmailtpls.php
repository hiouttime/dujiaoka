<?php

namespace App\Admin\Resources\EmailTemplates\Pages;

use App\Admin\Resources\EmailTemplates;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmailtpls extends ListRecords
{
    protected static string $resource = EmailTemplates::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
