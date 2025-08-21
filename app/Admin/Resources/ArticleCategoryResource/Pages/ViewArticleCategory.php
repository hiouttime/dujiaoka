<?php

namespace App\Admin\Resources\ArticleCategoryResource\Pages;

use App\Admin\Resources\ArticleCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewArticleCategory extends ViewRecord
{
    protected static string $resource = ArticleCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
