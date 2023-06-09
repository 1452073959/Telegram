<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class TelegramAdvertise extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'telegram_advertise';
//    public $timestamps = false;
    const CREATED_AT = 'advertise_createtime';
    const UPDATED_AT = 'advertise_updatetime';
    //关联用户
    public function user()
    {
        return $this->belongsTo(TelegramUser::class,'user_id','id');
    }
}
