<?php

namespace App\Admin\Resources\ArticleAdmin\Pages;

use App\Admin\Resources\ArticleAdmin;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleAdmin::class;
}
