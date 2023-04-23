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
        $res = TelegramAdvertise::with('user')->find($this->getKey());
//         dump($res->toarray());
        if ($res) {
            if ($res['send_status'] == '2') {
                return $this->response()->error('该内容已发送');
            }
            //发送广告到频道
            $response = send_message($res['send_channel'], $res['advertise_content']);
            if ($response['message_id']) {
                $res->send_status = '2';
                $res->save();
                //通知发送成功
                send_message($res['user']['user_no'],'您的广告信息已审核通过,发布成功!');
                return $this->response()
                    ->redirect('/advertise')
                    ->success('发送成功: ');

            } else {
                return $this->response()->error('发送失败!');
            }
        } else {
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
