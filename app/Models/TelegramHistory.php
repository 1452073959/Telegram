<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class TelegramHistory extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'telegram_history';
    public $timestamps = false;

}
