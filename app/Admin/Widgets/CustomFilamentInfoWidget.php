<?php

namespace App\Admin\Widgets;

use Filament\Widgets\Widget;

class CustomFilamentInfoWidget extends Widget
{
    protected static ?int $sort = -2;

    protected static bool $isLazy = false;

    /**
     * @var view-string
     */
    protected static string $view = 'admin.widgets.custom-filament-info-widget';
}