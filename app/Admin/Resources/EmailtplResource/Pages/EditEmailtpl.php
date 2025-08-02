<?php

namespace App\Admin\Resources\EmailtplResource\Pages;

use App\Admin\Resources\EmailtplResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmailtpl extends EditRecord
{
    protected static string $resource = EmailtplResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
