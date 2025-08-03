<?php

namespace App\Admin\Resources\ArticleAdmin\Pages;

use App\Admin\Resources\ArticleAdmin;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListArticles extends ListRecords
{
    protected static string $resource = ArticleAdmin::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
