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
        $res = TelegramAdvertise::with('user')->find($this->getKey());
//         dump($res->toarray());
        if ($res) {
            if ($res['send_status'] == '3') {
                return $this->response()->error('数据已取消');
            }
            $res->send_status = '3';
            $res->save();
            //通知发送失败
            send_message($res['user']['user_no'],'您的广告信息未审核通过,费用已退回账户!');
            //获取用户,返还扣除金额
            $user = TelegramUser::where('id', $res['user_id'])->first();
            $user->balance += $res['deduction_money'];
            $user->save();
            $response= send_message($res['user']['user_no'],
                "
账户余额变动:
+$res->deduction_money
备注:审核失败退回!
            ");
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

    protected function script()
    {
        return <<<JS
(function () {
    var _this = this; // 将 self 替换为 this
    // 点击取消按钮时的处理
    this.rowActionElement.on('click', '.cancel', function () {
        // 创建确认框
        var modal = new Dcat.modal({
            title: '确认取消',
            content: '<input type="text" id="input_text" class="form-control" placeholder="请输入内容">',
            okText: '确认',
            cancelText: '取消',
            onOk: function() {
                // 点击确认按钮时的回调函数
                var inputText = $('#input_text').val();
                // 在这里处理输入框中的内容
                console.log('输入框内容：', inputText);
                // 调用 handle 方法进行后续操作
                _this.handle(); // 将 self.handle() 替换为 this.handle()
            }
        });
        // 打开确认框
        modal.show();
    });
}).call(this);
JS;
    }


}
