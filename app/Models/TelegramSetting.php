<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class TelegramSetting extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'telegram_setting';
    public $timestamps = false;

    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = ['name','describe','u_address','publish_channel'];

}
