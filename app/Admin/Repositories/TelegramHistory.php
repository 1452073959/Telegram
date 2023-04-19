<?php

namespace App\Admin\Repositories;

use App\Models\TelegramHistory as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class TelegramHistory extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
