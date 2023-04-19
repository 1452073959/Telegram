<?php

namespace App\Admin\Repositories;

use App\Models\TelegramAdvertise as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class TelegramAdvertise extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
