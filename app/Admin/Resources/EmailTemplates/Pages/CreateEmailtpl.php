<?php

namespace App\Admin\Resources\EmailTemplates\Pages;

use App\Admin\Resources\EmailTemplates;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEmailtpl extends CreateRecord
{
    protected static string $resource = EmailTemplates::class;
}
