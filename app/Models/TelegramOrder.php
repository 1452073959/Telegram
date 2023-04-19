<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class TelegramOrder extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'telegram_order';


    const CREATED_AT = 'order_createtime';
    const UPDATED_AT = 'order_updatetime';
}
