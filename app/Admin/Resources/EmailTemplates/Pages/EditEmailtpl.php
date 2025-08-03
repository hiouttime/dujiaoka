<?php

namespace App\Admin\Resources\EmailTemplates\Pages;

use App\Admin\Resources\EmailTemplates;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmailtpl extends EditRecord
{
    protected static string $resource = EmailTemplates::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
