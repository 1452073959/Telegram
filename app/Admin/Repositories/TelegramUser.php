<?php

namespace App\Admin\Repositories;

use App\Models\TelegramUser as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class TelegramUser extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
