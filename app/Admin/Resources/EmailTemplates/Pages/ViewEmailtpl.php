<?php

namespace App\Admin\Resources\EmailTemplates\Pages;

use App\Admin\Resources\EmailTemplates;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEmailtpl extends ViewRecord
{
    protected static string $resource = EmailTemplates::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
