<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class TelegramUser extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'telegram_user';
    
}
