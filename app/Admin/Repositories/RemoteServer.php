<?php

namespace App\Admin\Repositories;

use App\Models\RemoteServer as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class RemoteServer extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
