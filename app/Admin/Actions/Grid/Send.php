<?php

namespace App\Admin\Actions\Grid;

use App\Models\TelegramAdvertise;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class Send extends RowAction
{
    /**
     * @return string
     */
    protected $title = '发送';

    /**
     * Handle the action request.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
//         dump($this->getKey());
        //获取发送对象
        $res = TelegramAdvertise::find($this->getKey());
//         dump($res->toarray());
        if ($res) {
            $response=  Telegram::sendMessage([
                'chat_id' => $res['send_channel'],
                'text' => $res['advertise_content']]);
            if($response['message_id']){
                $res->send_status='2';
                $res->save();
                return $this->response()
                    ->success('发送成功: ');
            }else{
                return $this->response()->error('发送失败!');
            }
        }else{
            return $this->response()->error('数据不存在!');
        }


    }

    /**
     * @return string|array|void
     */
    public function confirm()
    {
        // return ['Confirm?', 'contents'];
        return '确认发送?';
    }

    /**
     * @param Model|Authenticatable|HasPermissions|null $user
     *
     * @return bool
     */
    protected function authorize($user): bool
    {
        return true;
    }

    /**
     * @return array
     */
    protected function parameters()
    {
        return [];
    }
}
