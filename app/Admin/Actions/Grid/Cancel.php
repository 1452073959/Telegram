<?php

namespace App\Admin\Actions\Grid;

use App\Models\TelegramUser;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\TelegramAdvertise;

class Cancel extends RowAction
{
    /**
     * @return string
     */
    protected $title = '取消';

    /**
     * Handle the action request.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
        //获取发送对象
        $res = TelegramAdvertise::find($this->getKey());
//         dump($res->toarray());
        if ($res) {
            if ($res['send_status'] == '3') {
                return $this->response()->error('数据已取消');
            }
            $res->send_status = '3';
            $res->save();
            //获取用户,返还扣除金额
            $user = TelegramUser::where('id', $res['user_id'])->first();
            $user->balance += $res['deduction_money'];
            $user->save();
            return $this->response()
                ->redirect('/advertise')
                ->success('取消成功');
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
        return '确认取消?';
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
