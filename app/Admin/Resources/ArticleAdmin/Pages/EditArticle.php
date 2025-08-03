<?php

namespace App\Admin\Resources\ArticleAdmin\Pages;

use App\Admin\Resources\ArticleAdmin;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleAdmin::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
