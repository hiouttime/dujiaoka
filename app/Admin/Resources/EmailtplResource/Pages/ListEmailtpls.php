<?php

namespace App\Admin\Resources\EmailtplResource\Pages;

use App\Admin\Resources\EmailtplResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmailtpls extends ListRecords
{
    protected static string $resource = EmailtplResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
