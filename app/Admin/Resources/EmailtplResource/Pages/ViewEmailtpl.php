<?php

namespace App\Admin\Resources\EmailtplResource\Pages;

use App\Admin\Resources\EmailtplResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEmailtpl extends ViewRecord
{
    protected static string $resource = EmailtplResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
