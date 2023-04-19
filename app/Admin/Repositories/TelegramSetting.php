<?php

namespace App\Admin\Repositories;

use App\Models\TelegramSetting as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class TelegramSetting extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
