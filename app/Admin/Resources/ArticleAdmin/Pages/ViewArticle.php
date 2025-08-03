<?php

namespace App\Admin\Resources\ArticleAdmin\Pages;

use App\Admin\Resources\ArticleAdmin;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewArticle extends ViewRecord
{
    protected static string $resource = ArticleAdmin::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
