<?php

namespace App\Admin\Resources\Servers\Pages;

use App\Admin\Resources\Servers;
use Filament\Resources\Pages\CreateRecord;

class CreateRemoteServer extends CreateRecord
{
    protected static string $resource = Servers::class;
}