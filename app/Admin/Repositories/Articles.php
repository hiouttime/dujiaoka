<?php

namespace App\Admin\Repositories;

use App\Models\Articles as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Articles extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
